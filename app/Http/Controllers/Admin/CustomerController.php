<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Mail\NewUserMail;
use Illuminate\Http\Request;
use App\Imports\CustomerImport;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 3);

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->has('active') && $request->active !== '') {
            $query->where('is_active', $request->active);
        }

        if ($request->has('profile') && $request->profile !== '') {
            $query->where('must_update_profile', $request->profile);
        }

        $customers = $query->orderBy('created_at', 'desc')->paginate(8);

        if ($request->ajax()) {
            return view('backend.customers.table', compact('customers'))->render();
        }

        return view('backend.customers.index', compact('customers'));
    }


    public function show($id)
    {
        $user = User::where('role', 3)->findOrFail($id);
        return view('backend.customers.show', compact('user'));
    }

    /**
     * Form thêm khách hàng
     */
    public function create()
    {
        return view('backend.customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:users',
            'phone'            => 'nullable|unique:users',
            'identity_number'  => 'nullable|unique:users',
            'gender'           => 'nullable|in:male,female,other',
            'birthday'         => 'nullable|date',
            'tax_code'         => 'nullable|string|max:50',
        ]);

        $user = User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'phone'             => $request->phone,
            'identity_number'   => $request->identity_number,
            'password'          => Hash::make('123456'), // mật khẩu mặc định
            'role'              => 3,
            'is_active'         => true,
            'must_update_profile' => true,
            'gender'            => $request->gender,
            'birthday'          => $request->birthday,
            'tax_code'          => $request->tax_code,
        ]);

        // Gửi mail thông báo tạo tài khoản
        try {
            Mail::to($user->email)->queue(new NewUserMail($user, '123456'));
        } catch (\Exception $e) {
            Log::error('Mail queue error: ' . $e->getMessage());
        }

        return redirect()->route('customers.index')->with('success', 'Thêm khách hàng thành công!');
    }

    /**
     * Form sửa khách hàng
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('backend.customers.edit', compact('user'));
    }

    /**
     * Cập nhật thông tin khách hàng
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:users,email,' . $id,
            'phone'            => 'nullable|unique:users,phone,' . $id,
            'identity_number'  => 'nullable|unique:users,identity_number,' . $id,
            'must_update_profile' => 'nullable|boolean',
            'is_active'        => 'nullable|boolean',
            'tax_code'         => 'nullable|string|max:50',
            'gender'           => 'nullable|in:male,female,other',
            'birthday'         => 'nullable|date',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'identity_number' => $request->identity_number,
            'must_update_profile' => $request->boolean('must_update_profile'),
            'is_active' => $request->boolean('is_active'),
            'tax_code' => $request->tax_code,
            'gender' => $request->gender,
            'birthday' => $request->birthday,
        ]);

        return redirect()->route('customers.index')->with('success', 'Cập nhật khách hàng thành công!');
    }

    public function deleteSelected(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return back()->with('error', 'Vui lòng chọn ít nhất một khách hàng để xóa!');
        }

        User::whereIn('id', $ids)->delete();

        return back()->with('success', 'Đã xóa thành công ' . count($ids) . ' khách hàng!');
    }


    /**
     * Import file Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        $file = $request->file('file');

        try {
            Excel::import(new CustomerImport, $file);

            return redirect()->route('customers.index')->with('success', 'Import khách hàng thành công!');
        } catch (\Exception $e) {
            return redirect()->route('customers.index')->with('error', 'Import lỗi: ' . $e->getMessage());
        }
    }
    public function bulkAction(Request $request)
    {
        $ids = $request->input('ids', []);
        $action = $request->input('action');

        if (empty($ids)) {
            return back()->with('error', 'Vui lòng chọn ít nhất một khách hàng.');
        }

        switch ($action) {
            case 'delete':
                User::whereIn('id', $ids)->delete();
                return back()->with('success', 'Đã xóa các khách hàng được chọn.');

                // case 'activate':
                //     User::whereIn('id', $ids)->update(['is_active' => 1]);
                //     return back()->with('success', 'Đã kích hoạt các khách hàng được chọn.');

                // case 'deactivate':
                //     User::whereIn('id', $ids)->update(['is_active' => 0]);
                //     return back()->with('success', 'Đã ngừng hoạt động các khách hàng được chọn.');

            default:
                return back()->with('error', 'Hành động không hợp lệ.');
        }
    }
}
