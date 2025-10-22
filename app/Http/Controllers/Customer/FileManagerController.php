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
    // Hiển thị file manager
    public function index(Request $request)
    {
        $user = Auth::user();
        $folderId = $request->folder;

        // Lấy thông tin storage quota
        $quota = UserStorageQuota::firstOrCreate(
            ['user_id' => $user->id],
            ['quota_limit' => 1073741824] // 1GB default
        );

        // Lấy folder hiện tại
        $currentFolder = null;
        if ($folderId) {
            $currentFolder = UserFolder::where('user_id', $user->id)
                ->findOrFail($folderId);
        }

        // Lấy danh sách folders
        $folders = UserFolder::where('user_id', $user->id)
            ->where('parent_id', $folderId)
            ->orderBy('name')
            ->get();

        // Lấy danh sách files
        $files = UserFile::where('user_id', $user->id)
            ->where('folder_id', $folderId)
            ->latest()
            ->get();

        // Breadcrumb
        $breadcrumb = [];
        if ($currentFolder) {
            $breadcrumb = $currentFolder->breadcrumb;
        }

        return view('customer.file_manager.index', compact(
            'folders', 
            'files', 
            'currentFolder', 
            'breadcrumb',
            'quota'
        ));
    }

    // Tạo folder mới
    public function createFolder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:user_folders,id',
            'description' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        // Kiểm tra trùng tên trong cùng parent
        $exists = UserFolder::where('user_id', $user->id)
            ->where('parent_id', $request->parent_id)
            ->where('name', $request->name)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Tên thư mục đã tồn tại!');
        }

        // Tạo path
        $path = '/user_' . $user->id;
        if ($request->parent_id) {
            $parent = UserFolder::find($request->parent_id);
            $path = $parent->path . '/' . Str::slug($request->name);
        } else {
            $path .= '/' . Str::slug($request->name);
        }

        $folder = UserFolder::create([
            'user_id' => $user->id,
            'parent_id' => $request->parent_id,
            'name' => $request->name,
            'path' => $path,
            'description' => $request->description,
        ]);

        // Log activity
        UserFileActivity::create([
            'user_id' => $user->id,
            'folder_id' => $folder->id,
            'action' => 'create_folder',
            'description' => "Tạo thư mục: {$request->name}",
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);

        return back()->with('success', 'Đã tạo thư mục thành công!');
    }

    // Upload file (drag & drop hoặc form)
    public function upload(Request $request)
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'file|max:102400', // 100MB per file
            'folder_id' => 'nullable|exists:user_folders,id',
        ]);

        $user = Auth::user();
        $quota = UserStorageQuota::where('user_id', $user->id)->first();

        if (!$quota) {
            return response()->json(['error' => 'Không tìm thấy thông tin quota'], 400);
        }

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

                // Lưu vào thư mục user
                $path = $file->storeAs('users/user_' . $user->id, $fileName, 'public');

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

    // Xóa folder
    public function deleteFolder($id)
    {
        $user = Auth::user();
        $folder = UserFolder::where('user_id', $user->id)->findOrFail($id);

        DB::beginTransaction();
        try {
            // Xóa tất cả files trong folder (recursive)
            $this->deleteFolderRecursive($folder);

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
        $extension = pathinfo($file->name, PATHINFO_EXTENSION);
        $newFileName = Str::slug($request->name) . '.' . $extension;

        $file->update([
            'original_name' => $request->name . '.' . $extension,
        ]);

        // Log activity
        UserFileActivity::create([
            'user_id' => $user->id,
            'file_id' => $file->id,
            'action' => 'rename',
            'description' => "Đổi tên: {$oldName} → {$request->name}",
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);

        return back()->with('success', 'Đã đổi tên file thành công!');
    }

    // Di chuyển file
    public function moveFile(Request $request, $id)
    {
        $request->validate([
            'folder_id' => 'nullable|exists:user_folders,id',
        ]);

        $user = Auth::user();
        $file = UserFile::where('user_id', $user->id)->findOrFail($id);

        // Kiểm tra folder đích thuộc user
        if ($request->folder_id) {
            $targetFolder = UserFolder::where('user_id', $user->id)
                ->findOrFail($request->folder_id);
        }

        $file->update(['folder_id' => $request->folder_id]);

        // Log activity
        UserFileActivity::create([
            'user_id' => $user->id,
            'file_id' => $file->id,
            'action' => 'move',
            'description' => "Di chuyển file: {$file->original_name}",
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);

        return back()->with('success', 'Đã di chuyển file thành công!');
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
        }
    }
}