<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserFolder;
use App\Models\UserFile;
use App\Models\UserFileActivity;
use App\Models\UserStorageQuota;
use App\Models\User;
use App\Models\FileDownload;
use App\Models\File;
use App\Mail\FileNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminFileManagerController extends Controller
{
    /**
     * File Manager - Display all folders and files
     */
    public function index(Request $request)
    {
        // Lấy tất cả folders (thư mục gốc của users)
        $foldersQuery = UserFolder::with(['user'])
            ->where('is_root', true);

        if ($request->user_id) {
            $foldersQuery->where('user_id', $request->user_id);
        }

        $folders = $foldersQuery->latest()->get();

        // Lấy files
        $filesQuery = UserFile::with(['user', 'folder']);

        if ($request->user_id) {
            $filesQuery->where('user_id', $request->user_id);
        }

        if ($request->search) {
            $filesQuery->where(function($q) use ($request) {
                $q->where('original_name', 'like', "%{$request->search}%")
                  ->orWhere('name', 'like', "%{$request->search}%");
            });
        }

        $files = $filesQuery->latest()->paginate(10);

        // Lấy users
        $users = User::where('role', 3)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        // Thống kê
        $totalFiles = UserFile::count();
        $totalStorage = UserFile::sum('size');
        $totalUsers = User::where('role', 3)->count();
        $totalFolders = UserFolder::where('is_root', true)->count();
        $totalStorageFormatted = $this->formatBytes($totalStorage);

        return view('backend.file_manager.admin_file_manager', compact(
            'folders',
            'files', 
            'users', 
            'totalFiles', 
            'totalStorage',
            'totalStorageFormatted',
            'totalUsers',
            'totalFolders'
        ));
    }

    /**
     * Download History
     */
    public function downloadHistory(Request $request)
    {
        $query = FileDownload::with(['file', 'user']);

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->from_date) {
            $query->whereDate('downloaded_at', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $query->whereDate('downloaded_at', '<=', $request->to_date);
        }

        if ($request->search) {
            $query->whereHas('file', function($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('file_name', 'like', "%{$request->search}%");
            });
        }

        $downloads = $query->latest('downloaded_at')->paginate(50);

        $users = User::where('role', 3)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        $totalDownloads = FileDownload::count();
        $todayDownloads = FileDownload::whereDate('downloaded_at', today())->count();
        $thisMonthDownloads = FileDownload::whereMonth('downloaded_at', now()->month)
            ->whereYear('downloaded_at', now()->year)
            ->count();

        return view('backend.file_manager.admin_download_history', compact(
            'downloads', 
            'users',
            'totalDownloads',
            'todayDownloads',
            'thisMonthDownloads'
        ));
    }

    /**
     * Show file details
     */
    public function showFile($id)
    {
        $file = UserFile::with(['user', 'folder'])
            ->findOrFail($id);

        return view('backend.file_manager.show_file', compact('file'));
    }

    /**
     * Download file
     */
    public function download($id)
    {
        $file = UserFile::findOrFail($id);

        if (!Storage::disk('public')->exists($file->file_path)) {
            return back()->with('error', 'File không tồn tại!');
        }

        UserFileActivity::create([
            'user_id' => $file->user_id,
            'file_id' => $file->id,
            'action' => 'download',
            'description' => "Admin tải xuống file: {$file->original_name}",
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);

        return Storage::disk('public')->download($file->file_path, $file->original_name);
    }

    /**
     * Delete file
     */
    public function deleteFile($id)
    {
        $file = UserFile::findOrFail($id);

        DB::beginTransaction();
        try {
            // Xóa file vật lý
            if (Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }

            // Giảm quota
            $quota = UserStorageQuota::where('user_id', $file->user_id)->first();
            if ($quota) {
                $quota->decrement('used_space', $file->size);
            }

            // Log activity
            UserFileActivity::create([
                'user_id' => $file->user_id,
                'file_id' => $file->id,
                'action' => 'delete',
                'description' => "Admin xóa file: {$file->original_name}",
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

    /**
     * Upload files
     */
   public function upload(Request $request)
{
    // Validate files
    $request->validate([
        'files.*' => 'required|file|max:52428800',
    ]);

    if (!$request->hasFile('files')) {
        return response()->json(['success' => false, 'error' => 'Không có file nào'], 400);
    }

    DB::beginTransaction();
    try {
        $uploadedCount = 0;
        $errors = [];

        foreach ($request->file('files') as $file) {
            try {
                // DETECT USER_ID TỪ TÊN FILE (account_id)
                $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $accountId = ltrim($baseName, '0');
                $targetUser = null;

                if (ctype_digit((string) $accountId)) {
                    $targetUser = User::where('account_id', 'like', '%' . $accountId)
                        ->where('role', 3)
                        ->where('is_active', 1)
                        ->first();
                }

                if (!$targetUser) {
                    $errors[] = $file->getClientOriginalName() . ': Không tìm thấy user với account_id "' . $accountId . '"';
                    continue;
                }

                $userId = $targetUser->id;
                
                // KIỂM TRA USER CÓ ROOT FOLDER
                $rootFolder = UserFolder::where('user_id', $userId)
                    ->where('is_root', true)
                    ->first();

                if (!$rootFolder) {
                    $errors[] = $file->getClientOriginalName() . ': User không có root folder';
                    continue;
                }

                // Upload file
                $filename = uniqid() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs("user_files/{$userId}", $filename, 'public');

                $userFile = UserFile::create([
                    'user_id' => $userId,
                    'folder_id' => $rootFolder->id,
                    'name' => $filename,
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ]);

                $quota = UserStorageQuota::where('user_id', $userId)->first();
                if ($quota) {
                    $quota->increment('used_space', $file->getSize());
                }

                UserFileActivity::create([
                    'user_id' => $userId,
                    'file_id' => $userFile->id,
                    'folder_id' => $rootFolder->id,
                    'action' => 'upload',
                    'description' => "Admin upload file: {$file->getClientOriginalName()}",
                    'ip_address' => request()->ip(),
                    'created_at' => now(),
                ]);

                // Tạo record File để hiển thị trong phần báo cáo của khách
                $fileRecord = File::create([
                    'title' => $file->getClientOriginalName(),
                    'description' => 'Uploaded from Admin File Manager',
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientOriginalExtension(),
                    'file_size' => $file->getSize(),
                    'uploaded_by' => Auth::id(),
                    'file_category' => 'report',
                    'is_active' => 1,
                    'recipients' => [$targetUser->id],
                    'sent_at' => now(),
                    'sent_by' => Auth::id(),
                ]);

                // Gửi email thông báo cho khách
                try {
                    $downloadUrl = route('customer.files.download_report', $fileRecord->id);
                    Mail::to($targetUser->email)->queue(new FileNotification($fileRecord, $downloadUrl));
                } catch (\Exception $e) {
                    // Không làm gián đoạn upload nếu mail lỗi
                    Log::error('Failed to queue report email from AdminFileManager upload: ' . $e->getMessage());
                }

                $uploadedCount++;

            } catch (\Exception $e) {
                $errors[] = $file->getClientOriginalName() . ': ' . $e->getMessage();
                Log::error('Upload error for file ' . $file->getClientOriginalName() . ': ' . $e->getMessage());
            }
        }

        DB::commit();

        if ($uploadedCount > 0 && count($errors) === 0) {
            return response()->json([
                'success' => true,
                'message' => "Đã upload thành công {$uploadedCount} file!",
                'uploaded_count' => $uploadedCount
            ]);
        } elseif ($uploadedCount > 0 && count($errors) > 0) {
            return response()->json([
                'success' => true,
                'message' => "Đã upload {$uploadedCount} file. Có " . count($errors) . " file bị lỗi.",
                'uploaded_count' => $uploadedCount,
                'errors' => $errors
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => 'Không thể upload file',
                'errors' => $errors
            ], 400);
        }

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}
   

    /**
     * Danh sách folders của tất cả users
     */
    public function folders(Request $request)
    {
        $query = UserFolder::with(['user'])
            ->where('is_root', false);

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $folders = $query->latest()->paginate(20);

        $users = User::where('role', 3)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        $totalFolders = UserFolder::where('is_root', false)->count();

        return view('backend.file_manager.admin_folders', compact('folders', 'users', 'totalFolders'));
    }

    /**
     * Lịch sử hoạt động của user files
     */
    public function activities(Request $request)
    {
        $query = UserFileActivity::with(['file', 'folder', 'user']);

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->action) {
            $query->where('action', $request->action);
        }

        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $activities = $query->latest('created_at')->paginate(50);

        $users = User::where('role', 3)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        $actionTypes = [
            'upload' => 'Tải lên',
            'download' => 'Tải xuống',
            'delete' => 'Xóa file',
            'rename' => 'Đổi tên',
            'move' => 'Di chuyển',
            'create_folder' => 'Tạo thư mục',
            'delete_folder' => 'Xóa thư mục',
        ];

        $totalActivities = UserFileActivity::count();

        return view('backend.file_manager.admin_activities', compact(
            'activities', 
            'users',
            'actionTypes',
            'totalActivities'
        ));
    }

    /**
     * Quản lý storage quota
     */
    public function storageQuota(Request $request)
    {
        $query = UserStorageQuota::with('user');

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->sort === 'usage_desc') {
            $query->orderByRaw('(used_space / quota_limit) DESC');
        } elseif ($request->sort === 'usage_asc') {
            $query->orderByRaw('(used_space / quota_limit) ASC');
        } else {
            $query->latest();
        }

        $quotas = $query->paginate(20);

        $users = User::where('role', 3)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        $totalQuota = UserStorageQuota::sum('quota_limit');
        $totalUsed = UserStorageQuota::sum('used_space');
        $averageUsage = $totalQuota > 0 ? ($totalUsed / $totalQuota) * 100 : 0;
        $totalUsers = UserStorageQuota::count();

        return view('backend.file_manager.admin_storage_quota', compact(
            'quotas',
            'users',
            'totalQuota',
            'totalUsed',
            'averageUsage',
            'totalUsers'
        ));
    }

    /**
     * Cập nhật quota cho user
     */
    public function updateQuota(Request $request, $userId)
    {
        $request->validate([
            'quota_limit' => 'required|numeric|min:0',
        ]);

        $quota = UserStorageQuota::where('user_id', $userId)->firstOrFail();
        
        $quota->update([
            'quota_limit' => $request->quota_limit * 1073741824,
        ]);

        return back()->with('success', 'Đã cập nhật quota thành công!');
    }

    /**
     * Format bytes to human readable
     */
    private function formatBytes($bytes)
    {
        if ($bytes == null || $bytes == 0) {
            return '0 KB';
        }
        
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }
}