<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\FileDownload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileCustomerController extends Controller
{
    // Danh sách báo cáo đã nhận
    public function reports(Request $request)
    {
        $user = Auth::user();
        
        // Lấy các file báo cáo có id của user trong recipients (recipients now stores user IDs)
        $files = File::reports()
            ->whereJsonContains('recipients', $user->id)
            ->with(['uploader', 'sender'])
            ->latest('sent_at')
            ->paginate(10);

        return view('customer.files.reports', compact('files'));
    }

    // Danh sách biểu mẫu
    public function templates(Request $request)
    {
        $query = File::templates()->active()->with('uploader');

        // Filter by category
        if ($request->category) {
            $query->where('description', 'like', "%{$request->category}%");
        }

        // Search
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        $files = $query->latest()->paginate(10);

        // Lấy danh sách categories
        $categories = File::templates()->active()
            ->whereNotNull('description')
            ->distinct()
            ->pluck('description')
            ->filter();

        // Lấy danh sách ID đã download
        $myDownloads = FileDownload::where('user_id', Auth::id())
            ->pluck('file_id')
            ->toArray();

        return view('customer.files.templates', compact('files', 'categories', 'myDownloads'));
    }

    // Download báo cáo
    public function downloadReport($id)
    {
        $user = Auth::user();
        $file = File::reports()->findOrFail($id);
        
        // Kiểm tra quyền truy cập
        if (!in_array($user->id, $file->recipients ?? [])) {
            abort(403, 'Bạn không có quyền truy cập file này!');
        }

        if (!Storage::disk('public')->exists($file->file_path)) {
            abort(404, 'File không tồn tại');
        }

        // Ghi lại lượt download
        FileDownload::create([
            'file_id' => $file->id,
            'user_id' => $user->id,
            'downloaded_at' => now(),
            'ip_address' => request()->ip(),
        ]);

        return Storage::disk('public')->download($file->file_path, $file->file_name);
    }

    // Download biểu mẫu
    public function downloadTemplate($id)
    {
        $file = File::templates()->active()->findOrFail($id);

        if (!Storage::disk('public')->exists($file->file_path)) {
            return back()->with('error', 'File không tồn tại!');
        }

        // Ghi lại lượt download
        FileDownload::create([
            'file_id' => $file->id,
            'user_id' => Auth::id(),
            'downloaded_at' => now(),
            'ip_address' => request()->ip(),
        ]);

        // Tăng download_count
        $file->incrementDownloadCount();

        return Storage::disk('public')->download($file->file_path, $file->file_name);
    }

    // Xem chi tiết báo cáo
    public function showReport($id)
    {
        $user = Auth::user();
        $file = File::reports()->with(['uploader', 'sender'])->findOrFail($id);
        
        // Kiểm tra quyền truy cập (recipients holds user IDs)
        if (!in_array($user->id, $file->recipients ?? [])) {
            abort(403, 'Bạn không có quyền truy cập file này!');
        }

        return view('customer.files.show_report', compact('file'));
    }

    // Lịch sử downloads
    public function myDownloads()
    {
        $downloads = FileDownload::with('file.uploader')
            ->where('user_id', Auth::id())
            ->latest('downloaded_at')
            ->paginate(20);

        return view('customer.files.my_downloads', compact('downloads'));
    }
}

