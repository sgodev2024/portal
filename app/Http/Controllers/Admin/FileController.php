<?php

namespace App\Http\Controllers\Admin;

use App\Models\File;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\CustomerGroup;
use App\Mail\FileNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    // Hiển thị danh sách tất cả file
    public function index(Request $request)
    {
        $query = File::with(['uploader', 'sender']);

        // Lọc theo loại
        if ($request->has('category')) {
            $query->where('file_category', $request->category);
        }

        // Tìm kiếm
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%")
                  ->orWhere('file_name', 'like', "%{$request->search}%");
            });
        }

        $files = $query->latest()->paginate(15);

        return view('backend.files.index', compact('files'));
    }

    // Hiển thị danh sách báo cáo
    public function reports(Request $request)
    {
        $query = File::reports()->with(['uploader', 'sender']);

        // Tìm kiếm
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%")
                  ->orWhere('file_name', 'like', "%{$request->search}%");
            });
        }

        $files = $query->latest()->paginate(15);

        return view('backend.files.reports', compact('files'));
    }

    // Hiển thị danh sách biểu mẫu
    public function templates(Request $request)
    {
        $query = File::templates()->with(['uploader']);

        // Tìm kiếm
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%")
                  ->orWhere('file_name', 'like', "%{$request->search}%");
            });
        }

        $files = $query->latest()->paginate(15);

        return view('backend.files.templates', compact('files'));
    }

    // Form tạo file mới
    public function create(Request $request)
    {
        if ($request->routeIs('admin.files.create_template')) {
            return view('backend.files.create_template');
        }
        
        if ($request->routeIs('admin.files.create_report')) {
            $customers = User::where('role', 3)
                ->where('is_active', 1)
                ->orderBy('name')
                ->get();

            $groups = CustomerGroup::orderBy('name')->get();

            return view('backend.files.create_report', compact('customers', 'groups'));
        }
        
        $type = $request->get('type', 'report');
        
        if ($type === 'template') {
            return view('backend.files.create_template');
        }

        $customers = User::where('role', 3)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        $groups = CustomerGroup::orderBy('name')->get();

        return view('backend.files.create_report', compact('customers', 'groups'));
    }

    public function store(Request $request)
    {
        // Log attempt
        Log::info('File upload attempt', [
            'category' => $request->file_category,
            'has_files' => $request->hasFile('files'),
        ]);

        // Detailed debug: list incoming file names for troubleshooting
        try {
            if ($request->hasFile('files')) {
                $names = array_map(function($f){ return $f->getClientOriginalName(); }, $request->file('files'));
                Log::info('Incoming upload files', ['count' => count($names), 'names' => $names]);
            }
        } catch (\Exception $e) {
            Log::warning('Could not read incoming file names for debug: ' . $e->getMessage());
        }

        // Basic validation for multiple files
        try {
            $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'files' => 'required|array',
            'files.*' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,zip,rar|max:51200',
            'file_category' => 'required|in:report,template',
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề',
            'files.required' => 'Vui lòng chọn ít nhất một file',
            'files.*.required' => 'Vui lòng chọn file hợp lệ',
            'files.*.mimes' => 'File phải có định dạng: PDF, DOC, DOCX, XLS, XLSX, ZIP, RAR',
            'files.*.max' => 'File không được vượt quá 50MB',
        ]);
        } catch (ValidationException $e) {
            // If request expects JSON (AJAX from file manager), return JSON errors
            Log::warning('File upload validation failed', ['errors' => $e->validator->errors()->all()]);
            if ($request->expectsJson() || $request->ajax() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'errors' => $e->validator->errors()->all()
                ], 422);
            }
            throw $e;
        }
        // If report type, try auto-detecting recipients from filenames first
        if ($request->file_category === 'report' && $request->hasFile('files') && empty($request->recipient_user_ids) && empty($request->recipient_group_ids)) {
            $filesForDetect = $request->file('files');
            $errorFiles = [];

            foreach ($filesForDetect as $tmpFile) {
                if (! $tmpFile || ! $tmpFile->isValid()) {
                    $errorFiles[] = ['filename' => ($tmpFile ? $tmpFile->getClientOriginalName() : 'unknown'), 'error' => 'File không hợp lệ'];
                    continue;
                }

                $baseName = pathinfo($tmpFile->getClientOriginalName(), PATHINFO_FILENAME);
                $accountId = ltrim($baseName, '0');

                if (! ctype_digit((string) $accountId)) {
                    $errorFiles[] = ['filename' => $tmpFile->getClientOriginalName(), 'error' => 'Tên file không phải là số điện thoại hợp lệ'];
                    continue;
                }

                $user = User::where('account_id', 'like', '%' . $accountId)
                    ->where('role', 3)
                    ->where('is_active', 1)
                    ->first();

                if (! $user) {
                    $errorFiles[] = ['filename' => $tmpFile->getClientOriginalName(), 'error' => 'Không tìm thấy khách hàng có số điện thoại này'];
                    continue;
                }
            }

            if (! empty($errorFiles)) {
                $messages = array_map(function($e){ return "File {$e['filename']}: {$e['error']}"; }, $errorFiles);
                Log::warning('File detection errors', ['errors' => $messages]);
                if ($request->expectsJson() || $request->ajax() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
                    return response()->json([ 'success' => false, 'errors' => $messages ], 422);
                }
                return redirect()->back()->withInput()->withErrors(['files' => $messages]);
            }
        }

        // Process and store files inside a transaction
        DB::beginTransaction();
        $storedPaths = [];
        $fileRecords = [];

        try {
            $files = $request->file('files');

            foreach ($files as $file) {
                if (! $file || ! $file->isValid()) {
                    throw new \Exception('File upload không hợp lệ');
                }

                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $fileName = time() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $extension;
                $folder = $request->file_category === 'report' ? 'reports' : 'templates';

                $storagePath = storage_path('app/public/' . $folder);
                if (! file_exists($storagePath)) {
                    mkdir($storagePath, 0755, true);
                }

                $path = $file->storeAs($folder, $fileName, 'public');
                $storedPaths[] = $path;

                if (! Storage::disk('public')->exists($path)) {
                    throw new \Exception('Lưu file thất bại');
                }

                // detect recipient for this file (if possible)
                $baseName = pathinfo($originalName, PATHINFO_FILENAME);
                $accountId = ltrim($baseName, '0');
                $recipientId = null;

                if (ctype_digit((string) $accountId)) {
                    $user = User::where('account_id', 'like', '%' . $accountId)
                        ->where('role', 3)
                        ->where('is_active', 1)
                        ->first();

                    if ($user) {
                        $recipientId = $user->id;
                    }
                }

                $fileRecord = File::create([
                    'title' => $request->title,
                    'description' => $request->description,
                    'file_name' => $originalName,
                    'file_path' => $path,
                    'file_type' => $extension,
                    'file_size' => $file->getSize(),
                    'uploaded_by' => Auth::id(),
                    'file_category' => $request->file_category,
                    'is_active' => $request->has('is_active') ? 1 : 0,
                    'recipients' => $recipientId ? [$recipientId] : [],
                    'sent_at' => $recipientId ? now() : null,
                    'sent_by' => $recipientId ? Auth::id() : null,
                ]);

                $fileRecords[] = $fileRecord;

                // send immediately if recipient found and this is a report
                if ($recipientId && $request->file_category === 'report') {
                    $recipient = User::find($recipientId);
                    if ($recipient) {
                        $downloadUrl = route('customer.files.download_report', $fileRecord->id);
                        Mail::to($recipient->email)->queue(new FileNotification($fileRecord, $downloadUrl));
                    }
                }
            }

            DB::commit();

            $fileCount = count($fileRecords);
            $recipientCount = collect($fileRecords)->pluck('recipients')->flatten()->unique()->count();

            $message = $request->file_category === 'report'
                ? "Đã tạo và gửi {$fileCount} báo cáo thành công đến {$recipientCount} khách hàng!"
                : "Đã tạo {$fileCount} biểu mẫu thành công!";

            // If AJAX request, return structured per-file results so UI can show links/errors
            if ($request->expectsJson() || $request->ajax() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
                $results = array_map(function($rec) {
                    return [
                        'id' => $rec->id,
                        'title' => $rec->title,
                        'file_name' => $rec->file_name,
                        'recipients' => $rec->recipients,
                        'download_url' => route('customer.files.download_report', $rec->id),
                    ];
                }, $fileRecords);

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'files' => $results,
                ]);
            }

            if ($request->file_category === 'template') {
                return redirect()->route('admin.files.templates')->with('success', $message);
            }

            return redirect()->route('admin.files.reports')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            // cleanup stored files
            foreach ($storedPaths as $p) {
                if (Storage::disk('public')->exists($p)) {
                    Storage::disk('public')->delete($p);
                }
            }

            Log::error('File upload error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withInput()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    // Xem chi tiết file
    public function show($id)
    {
        $file = File::with(['uploader', 'sender', 'downloads.user'])
            ->findOrFail($id);

        return view('backend.files.show', compact('file'));
    }

    // Form chỉnh sửa file
    public function edit($id)
    {
        $file = File::findOrFail($id);
        
        if ($file->file_category === 'report') {
            $customers = User::where('role', 3)
                ->where('is_active', 1)
                ->orderBy('name')
                ->get();

            $groups = CustomerGroup::orderBy('name')->get();

            return view('backend.files.edit_report', compact('file', 'customers', 'groups'));
        } else {
            return view('backend.files.edit_template', compact('file'));
        }
    }

    // ✅ Cập nhật file - ĐÃ SỬA
    public function update(Request $request, $id)
    {
        $file = File::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,zip,rar|max:51200',
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề',
            'file.mimes' => 'File phải có định dạng: PDF, DOC, DOCX, XLS, XLSX, ZIP, RAR',
            'file.max' => 'File không được vượt quá 50MB',
        ]);

        DB::beginTransaction();
        try {
            // ✅ SỬA: Chuyển is_active về integer rõ ràng
            $updateData = [
                'title' => $request->title,
                'description' => $request->description,
                'is_active' => $request->has('is_active') ? 1 : 0,
            ];

            // Cập nhật file nếu có
            if ($request->hasFile('file')) {
                $newFile = $request->file('file');
                
                if (!$newFile->isValid()) {
                    throw new \Exception('File upload không hợp lệ');
                }

                $originalName = $newFile->getClientOriginalName();
                $extension = $newFile->getClientOriginalExtension();
                $fileName = time() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $extension;
                
                $folder = $file->file_category === 'report' ? 'reports' : 'templates';
                $path = $newFile->storeAs($folder, $fileName, 'public');

                if (!Storage::disk('public')->exists($path)) {
                    throw new \Exception('Lưu file thất bại');
                }

                // Xóa file cũ
                if (Storage::disk('public')->exists($file->file_path)) {
                    Storage::disk('public')->delete($file->file_path);
                }

                $updateData = array_merge($updateData, [
                    'file_name' => $originalName,
                    'file_path' => $path,
                    'file_type' => $extension,
                    'file_size' => $newFile->getSize(),
                ]);
            }

            $file->update($updateData);

            DB::commit();

            // ✅ SỬA: Redirect về đúng trang dựa vào loại file
            if ($file->file_category === 'template') {
                return redirect()->route('admin.files.templates')
                    ->with('success', 'Cập nhật biểu mẫu thành công!');
            } else {
                return redirect()->route('admin.files.reports')
                    ->with('success', 'Cập nhật báo cáo thành công!');
            }

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('File update error', [
                'file_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if (isset($path) && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    // Download file
    public function download($id)
    {
        $file = File::findOrFail($id);
        
        if (!Storage::disk('public')->exists($file->file_path)) {
            abort(404, 'File không tồn tại');
        }

        $file->increment('download_count');

        \App\Models\FileDownload::create([
            'file_id' => $file->id,
            'user_id' => Auth::id(),
            'downloaded_at' => now(),
            'ip_address' => request()->ip(),
        ]);

        $filePath = Storage::disk('public')->path($file->file_path);
        
        return response()->download($filePath, $file->file_name, [
            'Content-Type' => Storage::disk('public')->mimeType($file->file_path),
        ]);
    }

    // Gửi lại báo cáo
    public function resend($id)
    {
        $file = File::findOrFail($id);

        if ($file->file_category !== 'report' || empty($file->recipients)) {
            return back()->with('error', 'Không thể gửi lại file này!');
        }

        try {
            // recipients now stores user IDs, so query by id
            $recipients = User::where('role', 3)
                ->where('is_active', 1)
                ->whereIn('id', $file->recipients)
                ->get();

            if ($recipients->isNotEmpty()) {
                foreach ($recipients as $recipient) {
                    $downloadUrl = route('customer.files.download_report', $file->id);
                    Mail::to($recipient->email)->send(new FileNotification($file, $downloadUrl));
                }

                $file->update([
                    'sent_at' => now(),
                    'sent_by' => Auth::id(),
                ]);

                return back()->with('success', "Đã gửi lại báo cáo đến {$recipients->count()} khách hàng!");
            } else {
                return back()->with('error', 'Không tìm thấy người nhận hợp lệ!');
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    // Xóa file
    public function destroy($id)
    {
        $file = File::findOrFail($id);

        if (Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        $file->delete();

        return redirect()->route('admin.files.index')
            ->with('success', 'Đã xóa file thành công!');
    }
}