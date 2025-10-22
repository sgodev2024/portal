<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Imports\StaffImport;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class StaffController extends Controller
{
    // Danh sách nhân viên
    public function index(Request $request)
    {
        $query = User::where('role', 2);

        // Tìm kiếm
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('account_id', 'LIKE', "%{$search}%")
                    ->orWhere('department', 'LIKE', "%{$search}%")
                    ->orWhere('position', 'LIKE', "%{$search}%");
            });
        }

        $query->orderBy('created_at', 'desc');
        $staffs = $query->paginate(8);
        if ($request->ajax()) {
            return view('backend.staffs.table', compact('staffs'))->render();
        }

        return view('backend.staffs.index', compact('staffs'));
    }

    // Form thêm nhân viên
    public function create()
    {
        return view('backend.staffs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'account_id' => 'required|string|max:20|unique:users,account_id',
            'email'      => 'required|email|unique:users,email',
            'department' => 'required|string|max:255',
            'position'   => 'required|string|max:255',
            'password'   => 'nullable|string|min:6',
        ], [
            'name.required'       => 'Họ tên không được để trống.',
            'account_id.required' => 'Mã nhân viên không được để trống.',
            'account_id.unique'   => 'Mã nhân viên này đã tồn tại.',
            'account_id.max'      => 'Mã nhân viên không được vượt quá 20 ký tự.',
            'email.required'      => 'Email công ty không được để trống.',
            'email.email'         => 'Email không hợp lệ.',
            'email.unique'        => 'Email này đã được sử dụng.',
            'department.required' => 'Phòng ban không được để trống.',
            'position.required'   => 'Chức vụ không được để trống.',
            'password.min'        => 'Mật khẩu phải có ít nhất 6 ký tự.',
        ]);

        $password = !empty($validated['password']) ? Hash::make($validated['password']) : Hash::make('123456');

        User::create([
            'name'       => $validated['name'],
            'account_id' => $validated['account_id'],
            'email'      => $validated['email'],
            'department' => $validated['department'],
            'position'   => $validated['position'],
            'password'   => $password,
            'role'       => 2,
            'is_active'  => false,
        ]);

        return redirect()->route('admin.staffs.index')->with('success', 'Thêm nhân viên thành công!');
    }

    // Form sửa nhân viên
    public function edit($id)
    {
        $staff = User::where('role', 2)->findOrFail($id);
        return view('backend.staffs.edit', compact('staff'));
    }

    // Cập nhật nhân viên
    public function update(Request $request, $id)
    {
        $staff = User::where('role', 2)->findOrFail($id);

        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'account_id' => 'required|string|max:20|unique:users,account_id,' . $staff->id,
            'email'      => 'required|email|unique:users,email,' . $staff->id,
            'department' => 'required|string|max:255',
            'position'   => 'required|string|max:255',
            'role'       => 'required|integer|in:1,2,3',
            'password'   => 'nullable|string|min:6',
            'is_active'  => 'nullable|boolean'
        ], [
            'name.required'       => 'Họ tên không được để trống.',
            'account_id.required' => 'Mã nhân viên không được để trống.',
            'account_id.unique'   => 'Mã nhân viên này đã tồn tại.',
            'account_id.max'      => 'Mã nhân viên không được vượt quá 20 ký tự.',
            'email.required'      => 'Email công ty không được để trống.',
            'email.email'         => 'Email không hợp lệ.',
            'email.unique'        => 'Email này đã được sử dụng.',
            'department.required' => 'Phòng ban không được để trống.',
            'position.required'   => 'Chức vụ không được để trống.',
            'role.required'       => 'Vai trò không được để trống.',
            'role.in'             => 'Vai trò không hợp lệ.',
            'password.min'        => 'Mật khẩu phải có ít nhất 6 ký tự.',
        ]);

        $updateData = [
            'name'       => $validated['name'],
            'account_id' => $validated['account_id'],
            'email'      => $validated['email'],
            'department' => $validated['department'],
            'position'   => $validated['position'],
            'role'       => $validated['role'],
            'is_active'  => $request->has('is_active') ? 1 : 0,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $staff->update($updateData);

        return redirect()->route('admin.staffs.index')->with('success', 'Cập nhật nhân viên thành công!');
    }

    // Xóa nhiều nhân viên
    public function deleteSelected(Request $request)
    {
        $ids = $request->input('ids');
        if (!$ids) {
            return back()->with('error', 'Vui lòng chọn nhân viên để xóa!');
        }

        User::whereIn('id', $ids)->where('role', 2)->delete();

        return back()->with('success', 'Đã xóa các nhân viên đã chọn!');
    }

    // Import Excel
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ], [
            'file.required' => 'Vui lòng chọn file Excel để import.',
            'file.mimes'    => 'Định dạng file không hợp lệ. Chỉ chấp nhận: xlsx, xls, csv.',
        ]);

        try {
            Excel::import(new StaffImport, $request->file('file'));
            return redirect()->route('admin.staffs.index')->with('success', 'Import nhân viên thành công!');
        } catch (\Exception $e) {
            return redirect()->route('admin.staffs.index')->with('error', 'Import thất bại: ' . $e->getMessage());
        }
    }
    public function downloadTemplate()
    {
        $filePath = storage_path('app/public/templates/staff_import_template.xlsx');

        if (!file_exists($filePath)) {
            return back()->with('error', 'File mẫu không tồn tại.');
        }

        return response()->download($filePath, 'staff_import_template.xlsx');
    }
}
