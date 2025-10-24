<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\UserFolder;
use App\Models\UserFile;
use App\Models\UserFileActivity;
use App\Models\UserStorageQuota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FileManagerController extends Controller
{
    // Index - Luôn bắt đầu từ root folder
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Lấy root folder của user
        $rootFolder = UserFolder::where('user_id', $user->id)
            ->where('is_root', true)
            ->first();
        
        if (!$rootFolder) {
            // Tạo nếu chưa có (fallback)
            $rootFolder = $this->createRootFolder($user);
        }
        
        // Nếu không có folder_id trong request, dùng root
        $folderId = $request->folder ?: $rootFolder->id;

        // Lấy thông tin storage quota
        $quota = UserStorageQuota::firstOrCreate(
            ['user_id' => $user->id],
            ['quota_limit' => 1073741824]
        );

        // Lấy folder hiện tại
        $currentFolder = UserFolder::where('user_id', $user->id)
            ->findOrFail($folderId);

        // Lấy danh sách folders con
        $folders = UserFolder::where('user_id', $user->id)
            ->where('parent_id', $folderId)
            ->orderBy('name')
            ->get();

        // Lấy danh sách files
        $files = UserFile::where('user_id', $user->id)
            ->where('folder_id', $folderId)
            ->latest()
            ->get();

        // ✅ Lấy tất cả folders để hiển thị trong modal di chuyển
        $allFolders = UserFolder::where('user_id', $user->id)
            ->where('is_root', false)
            ->orderBy('path')
            ->get();

        // Breadcrumb - Bỏ root khỏi breadcrumb để gọn hơn
        $breadcrumb = [];
        if (!$currentFolder->is_root) {
            $breadcrumbFull = $currentFolder->breadcrumb;
            // Lọc bỏ root folder
            $breadcrumb = $breadcrumbFull->filter(function($folder) {
                return !$folder->is_root;
            })->values();
        }

        return view('customer.file_manager.index', compact(
            'folders', 
            'files', 
            'currentFolder',
            'rootFolder',
            'breadcrumb',
            'quota',
            'allFolders' // ← ✅ QUAN TRỌNG: Thêm biến này
        ));
    }

    // Tạo folder mới - KHÔNG cho tạo nếu parent_id = null
    public function createFolder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'required|exists:user_folders,id',
            'description' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        
        // Kiểm tra parent folder thuộc về user
        $parent = UserFolder::where('user_id', $user->id)
            ->findOrFail($request->parent_id);

        // Không cho tạo folder trong root nếu parent là root
        if ($parent->is_root) {
            // Cho phép tạo subfolder level 1 trong root
            // Nhưng không cho tạo folder có tên trùng với user_X
        }

        // Kiểm tra trùng tên trong cùng parent
        $exists = UserFolder::where('user_id', $user->id)
            ->where('parent_id', $request->parent_id)
            ->where('name', $request->name)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Tên thư mục đã tồn tại!');
        }

        // Tạo path dựa trên parent
        $slugName = Str::slug($request->name);
        $path = $parent->path . '/' . $slugName;

        DB::beginTransaction();
        try {
            $folder = UserFolder::create([
                'user_id' => $user->id,
                'parent_id' => $request->parent_id,
                'name' => $request->name,
                'path' => $path,
                'description' => $request->description,
                'is_root' => false,
            ]);

            // Tạo physical folder
            Storage::disk('public')->makeDirectory($path);

            // Log activity
            UserFileActivity::create([
                'user_id' => $user->id,
                'folder_id' => $folder->id,
                'action' => 'create_folder',
                'description' => "Tạo thư mục: {$request->name}",
                'ip_address' => request()->ip(),
                'created_at' => now(),
            ]);

            DB::commit();
            return back()->with('success', 'Đã tạo thư mục thành công!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    // Upload file - LƯU VÀO ĐÚNG FOLDER PATH
    public function upload(Request $request)
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'file|max:102400', // 100MB
            'folder_id' => 'required|exists:user_folders,id',
        ]);

        $user = Auth::user();
        $quota = UserStorageQuota::where('user_id', $user->id)->first();

        if (!$quota) {
            return response()->json(['error' => 'Không tìm thấy thông tin quota'], 400);
        }

        // Lấy folder hiện tại
        $folder = UserFolder::where('user_id', $user->id)
            ->findOrFail($request->folder_id);

        DB::beginTransaction();
        try {
            $uploadedFiles = [];
            $totalSize = 0;

            foreach ($request->file('files') as $file) {
                $fileSize = $file->getSize();
                $totalSize += $fileSize;

                // Kiểm tra quota
                if (($quota->used_space + $totalSize) > $quota->quota_limit) {
                    throw new \Exception('Vượt quá dung lượng cho phép!');
                }

                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) 
                    . '_' . time() . '_' . Str::random(6) . '.' . $extension;

                // ✅ LƯU VÀO ĐÚNG FOLDER PATH
                $path = $file->storeAs($folder->path, $fileName, 'public');

                $userFile = UserFile::create([
                    'user_id' => $user->id,
                    'folder_id' => $request->folder_id,
                    'name' => $fileName,
                    'original_name' => $originalName,
                    'file_path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'size' => $fileSize,
                ]);

                // Log activity
                UserFileActivity::create([
                    'user_id' => $user->id,
                    'file_id' => $userFile->id,
                    'action' => 'upload',
                    'description' => "Upload file: {$originalName}",
                    'ip_address' => request()->ip(),
                    'created_at' => now(),
                ]);

                $uploadedFiles[] = $userFile;
            }

            // Cập nhật quota
            $quota->increment('used_space', $totalSize);
            $quota->update(['last_calculated_at' => now()]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Upload thành công ' . count($uploadedFiles) . ' file',
                    'files' => $uploadedFiles
                ]);
            }

            return back()->with('success', 'Upload thành công ' . count($uploadedFiles) . ' file!');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 400);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    // Download file
    public function download($id)
    {
        $user = Auth::user();
        $file = UserFile::where('user_id', $user->id)->findOrFail($id);

        if (!Storage::disk('public')->exists($file->file_path)) {
            return back()->with('error', 'File không tồn tại!');
        }

        // Log activity
        UserFileActivity::create([
            'user_id' => $user->id,
            'file_id' => $file->id,
            'action' => 'download',
            'description' => "Download file: {$file->original_name}",
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);

        return Storage::disk('public')->download($file->file_path, $file->original_name);
    }

    // Xóa file
    public function deleteFile($id)
    {
        $user = Auth::user();
        $file = UserFile::where('user_id', $user->id)->findOrFail($id);

        DB::beginTransaction();
        try {
            // Xóa file vật lý
            if (Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }

            // Giảm quota
            $quota = UserStorageQuota::where('user_id', $user->id)->first();
            if ($quota) {
                $quota->decrement('used_space', $file->size);
            }

            // Log activity
            UserFileActivity::create([
                'user_id' => $user->id,
                'file_id' => $file->id,
                'action' => 'delete',
                'description' => "Xóa file: {$file->original_name}",
                'ip_address' => request()->ip(),
                'created_at' => now(),
            ]);

            $file->delete();

            DB::commit();

            return back()->with('success', 'Đã xóa file thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    // Xóa folder - KHÔNG cho xóa root
    public function deleteFolder($id)
    {
        $user = Auth::user();
        $folder = UserFolder::where('user_id', $user->id)->findOrFail($id);

        // Ngăn xóa root folder
        if ($folder->is_root) {
            return back()->with('error', 'Không thể xóa thư mục gốc!');
        }

        DB::beginTransaction();
        try {
            // Xóa tất cả files trong folder (recursive)
            $this->deleteFolderRecursive($folder);

            // Xóa physical folder
            if (Storage::disk('public')->exists($folder->path)) {
                Storage::disk('public')->deleteDirectory($folder->path);
            }

            // Log activity
            UserFileActivity::create([
                'user_id' => $user->id,
                'folder_id' => $folder->id,
                'action' => 'delete_folder',
                'description' => "Xóa thư mục: {$folder->name}",
                'ip_address' => request()->ip(),
                'created_at' => now(),
            ]);

            $folder->delete();

            DB::commit();

            return back()->with('success', 'Đã xóa thư mục thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    // Đổi tên file
    public function renameFile(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $file = UserFile::where('user_id', $user->id)->findOrFail($id);

        $oldName = $file->original_name;
        $extension = pathinfo($file->original_name, PATHINFO_EXTENSION);
        $newOriginalName = $request->name . '.' . $extension;

        $file->update([
            'original_name' => $newOriginalName,
        ]);

        // Log activity
        UserFileActivity::create([
            'user_id' => $user->id,
            'file_id' => $file->id,
            'action' => 'rename',
            'description' => "Đổi tên: {$oldName} → {$newOriginalName}",
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);

        return back()->with('success', 'Đã đổi tên file thành công!');
    }

    // Di chuyển file
    public function moveFile(Request $request, $id)
    {
        $request->validate([
            'folder_id' => 'required|exists:user_folders,id',
        ]);

        $user = Auth::user();
        $file = UserFile::where('user_id', $user->id)->findOrFail($id);

        // Kiểm tra folder đích thuộc user
        $targetFolder = UserFolder::where('user_id', $user->id)
            ->findOrFail($request->folder_id);

        // Không di chuyển nếu đã ở folder đích
        if ($file->folder_id == $request->folder_id) {
            return back()->with('info', 'File đã ở thư mục này rồi!');
        }

        DB::beginTransaction();
        try {
            // Di chuyển file vật lý
            $oldPath = $file->file_path;
            $fileName = basename($oldPath);
            $newPath = $targetFolder->path . '/' . $fileName;

            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->move($oldPath, $newPath);
            }

            $file->update([
                'folder_id' => $request->folder_id,
                'file_path' => $newPath
            ]);

            // Log activity
            UserFileActivity::create([
                'user_id' => $user->id,
                'file_id' => $file->id,
                'action' => 'move',
                'description' => "Di chuyển file: {$file->original_name} → {$targetFolder->name}",
                'ip_address' => request()->ip(),
                'created_at' => now(),
            ]);

            DB::commit();

            return back()->with('success', 'Đã di chuyển file thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    // Lịch sử hoạt động
    public function activities()
    {
        $activities = UserFileActivity::with(['file', 'folder'])
            ->where('user_id', Auth::id())
            ->latest('created_at')
            ->paginate(50);

        return view('customer.file_manager.activities', compact('activities'));
    }

    // Helper: Xóa folder đệ quy
    private function deleteFolderRecursive($folder)
    {
        $quota = UserStorageQuota::where('user_id', $folder->user_id)->first();

        // Xóa files trong folder
        $files = UserFile::where('folder_id', $folder->id)->get();
        foreach ($files as $file) {
            if (Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }
            if ($quota) {
                $quota->decrement('used_space', $file->size);
            }
            $file->delete();
        }

        // Xóa subfolders
        $subfolders = UserFolder::where('parent_id', $folder->id)->get();
        foreach ($subfolders as $subfolder) {
            $this->deleteFolderRecursive($subfolder);
            $subfolder->delete();
        }
    }

    // Helper: Tạo root folder
    private function createRootFolder($user)
    {
        $rootFolder = UserFolder::create([
            'user_id' => $user->id,
            'parent_id' => null,
            'name' => 'user_' . $user->id,
            'path' => 'users/user_' . $user->id,
            'description' => 'Thư mục gốc',
            'is_root' => true,
        ]);
        
        Storage::disk('public')->makeDirectory($rootFolder->path);
        
        return $rootFolder;
    }
}