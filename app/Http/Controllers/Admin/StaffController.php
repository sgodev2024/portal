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
                    ->orWhere('phone', 'LIKE', "%{$search}%");
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
            'name'     => 'required|string|max:255',
            'email'    => 'nullable|email|unique:users,email',
            'phone'    => 'nullable|string|max:20|unique:users,phone',
            'password' => 'nullable|string|min:6',
            'gender'   => 'nullable|in:male,female',
            'birthday' => 'nullable|date',
        ], [
            'name.required'     => 'Tên nhân viên không được để trống.',
            'name.string'       => 'Tên nhân viên phải là chuỗi ký tự.',
            'email.email'       => 'Email không hợp lệ.',
            'email.unique'      => 'Email này đã được sử dụng.',
            'phone.unique'      => 'Số điện thoại này đã tồn tại.',
            'phone.max'         => 'Số điện thoại không được vượt quá 20 ký tự.',
            'password.min'      => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'birthday.date'     => 'Ngày sinh không hợp lệ.',
            'gender.in'         => 'Giới tính không hợp lệ.',
        ]);

        $password = isset($validated['password']) ? Hash::make($validated['password']) : Hash::make('123456');
        $gender = $validated['gender'] ?? null;

        User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'] ?? null,
            'phone'     => $validated['phone'] ?? null,
            'password'  => $password,
            'role'      => 2,
            'is_active' => true,
            'gender'    => $gender,
            'birthday'  => $validated['birthday'] ?? null,
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
            'name'                => 'required|string|max:255',
            'email'               => 'nullable|email|unique:users,email,' . $staff->id,
            'phone'               => 'nullable|string|max:20|unique:users,phone,' . $staff->id,
            'password'            => 'nullable|string|min:6',
            'gender'              => 'nullable|string',
            'birthday'            => 'nullable|date',
            'is_active'           => 'nullable|boolean'
        ], [
            'name.required' => 'Tên nhân viên không được để trống.',
            'email.email'   => 'Email không hợp lệ.',
            'email.unique'  => 'Email này đã được sử dụng.',
            'phone.unique'  => 'Số điện thoại này đã tồn tại.',
            'password.min'  => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'is_active.required' => 'Vui lòng chọn trạng thái hoạt động.',
        ]);

        // Chuẩn bị dữ liệu update
        $updateData = [
            'name'                => $validated['name'],
            'email'               => $validated['email'] ?? $staff->email,
            'phone'               => $validated['phone'] ?? $staff->phone,
            'gender'              => $validated['gender'] ?? $staff->gender,
            'birthday'            => $validated['birthday'] ?? $staff->birthday,
            'is_active'           => $request->has('is_active') ? 1 : 0,
        ];

        // Chỉ update password nếu có nhập
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
}
