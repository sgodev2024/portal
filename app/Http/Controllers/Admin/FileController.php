<?php

namespace App\Http\Controllers\Admin;

use App\Models\File;
use App\Models\User;
use Illuminate\Support\Str;
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
    Log::info('File upload attempt', [
        'category' => $request->file_category,
        'has_file' => $request->hasFile('file'),
        'recipient_users' => $request->recipient_user_ids,
        'recipient_groups' => $request->recipient_group_ids
    ]);

    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,zip,rar|max:51200',
        'file_category' => 'required|in:report,template',
    ], [
        'title.required' => 'Vui lòng nhập tiêu đề',
        'file.required' => 'Vui lòng chọn file',
        'file.mimes' => 'File phải có định dạng: PDF, DOC, DOCX, XLS, XLSX, ZIP, RAR',
        'file.max' => 'File không được vượt quá 50MB',
    ]);

    // Validation riêng cho báo cáo
    if ($request->file_category === 'report') {
        // Kiểm tra người nhận
        $hasUsers = !empty($request->recipient_user_ids) && is_array($request->recipient_user_ids);
        $hasGroups = !empty($request->recipient_group_ids) && is_array($request->recipient_group_ids);
        
        if (!$hasUsers && !$hasGroups) {
            return back()
                ->withErrors(['recipient_error' => 'Vui lòng chọn ít nhất 1 người nhận hoặc 1 nhóm khách hàng.'])
                ->withInput();
        }

        // Validate IDs nếu có
        if ($hasUsers) {
            $request->validate([
                'recipient_user_ids' => 'array',
                'recipient_user_ids.*' => 'exists:users,id',
            ]);
        }
        
        if ($hasGroups) {
            $request->validate([
                'recipient_group_ids' => 'array',
                'recipient_group_ids.*' => 'exists:customer_groups,id',
            ]);
        }
    }

    DB::beginTransaction();
    try {
        // Kiểm tra file upload
        if (!$request->hasFile('file')) {
            throw new \Exception('Không tìm thấy file upload');
        }

        $file = $request->file('file');
        
        // Kiểm tra file hợp lệ
        if (!$file->isValid()) {
            throw new \Exception('File upload không hợp lệ: ' . $file->getErrorMessage());
        }

        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $fileName = time() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $extension;
        
        $folder = $request->file_category === 'report' ? 'reports' : 'templates';
        
        // Tạo thư mục nếu chưa có
        $storagePath = storage_path('app/public/' . $folder);
        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0755, true);
        }
        
        $path = $file->storeAs($folder, $fileName, 'public');

        // Kiểm tra file đã lưu thành công
        if (!Storage::disk('public')->exists($path)) {
            throw new \Exception('Lưu file thất bại');
        }

        // Tạo record file
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
        ]);

        Log::info('File record created', ['id' => $fileRecord->id]);

        // Xử lý người nhận cho báo cáo
        $recipientCount = 0;
        if ($request->file_category === 'report') {
            $recipients = collect();

            // Lấy khách hàng từ nhóm
            if (!empty($request->recipient_group_ids)) {
                $groupUsers = User::where('role', 3)
                    ->where('is_active', 1)
                    ->whereHas('groups', function($query) use ($request) {
                        $query->whereIn('customer_groups.id', $request->recipient_group_ids);
                    })
                    ->get();
                $recipients = $recipients->merge($groupUsers);
                
                Log::info('Users from groups', ['count' => $groupUsers->count()]);
            }

            // Lấy khách hàng được chọn trực tiếp
            if (!empty($request->recipient_user_ids)) {
                $directUsers = User::where('role', 3)
                    ->where('is_active', 1)
                    ->whereIn('id', $request->recipient_user_ids)
                    ->get();
                $recipients = $recipients->merge($directUsers);
                
                Log::info('Direct users', ['count' => $directUsers->count()]);
            }

            // Loại bỏ trùng lặp
            $recipients = $recipients->unique('id');
            $recipientCount = $recipients->count();

            Log::info('Total unique recipients', ['count' => $recipientCount]);

            if ($recipients->isNotEmpty()) {
                // Cập nhật recipients
                $fileRecord->update([
                    'recipients' => $recipients->pluck('email')->toArray(),
                    'sent_at' => now(),
                    'sent_by' => Auth::id(),
                ]);

                // Gửi email thông báo
                foreach ($recipients as $recipient) {
                    try {
                        $downloadUrl = route('customer.documents.download_report', $fileRecord->id);
                        Mail::to($recipient->email)->queue(new FileNotification($fileRecord, $downloadUrl));
                    } catch (\Exception $e) {
                        Log::error('Failed to send email', [
                            'recipient' => $recipient->email,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
        }

        DB::commit();

        $message = $request->file_category === 'report' 
            ? "Đã tạo và gửi báo cáo '{$fileRecord->title}' thành công đến {$recipientCount} khách hàng!"
            : "Đã tạo biểu mẫu '{$fileRecord->title}' thành công!";

        return redirect()->route('admin.files.index')
            ->with('success', $message);

    } catch (\Exception $e) {
        DB::rollBack();

        Log::error('File upload error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        // Xóa file nếu đã upload
        if (isset($path) && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        return back()
            ->withInput()
            ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
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

    // Cập nhật file
    public function update(Request $request, $id)
    {
        $file = File::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,zip,rar|max:51200',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $updateData = [
                'title' => $request->title,
                'description' => $request->description,
                'is_active' => $request->has('is_active'),
            ];

            // Cập nhật file nếu có
            if ($request->hasFile('file')) {
                $newFile = $request->file('file');
                $originalName = $newFile->getClientOriginalName();
                $extension = $newFile->getClientOriginalExtension();
                $fileName = time() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $extension;
                
                $folder = $file->file_category === 'report' ? 'reports' : 'templates';
                $path = $newFile->storeAs($folder, $fileName, 'public');

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

            return redirect()->route('admin.files.show', $file->id)
                ->with('success', 'Cập nhật file thành công!');

        } catch (\Exception $e) {
            DB::rollBack();

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

        // Tăng số lượt download
        $file->increment('download_count');

        // Lưu log download
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
            // Lấy danh sách người nhận
            $recipients = User::where('role', 3)
                ->where('is_active', 1)
                ->whereIn('email', $file->recipients)
                ->get();

            if ($recipients->isNotEmpty()) {
                // Gửi email thông báo
                foreach ($recipients as $recipient) {
                    $downloadUrl = route('customer.documents.download_report', $file->id);
                    Mail::to($recipient->email)->send(new FileNotification($file, $downloadUrl));
                }

                // Cập nhật thời gian gửi
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

        // Xóa file vật lý
        if (Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        // Xóa record
        $file->delete();

        return redirect()->route('admin.files.index')
            ->with('success', 'Đã xóa file thành công!');
    }
}
