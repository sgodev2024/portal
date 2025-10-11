<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
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

        $customers = $query->orderBy('created_at', 'desc')->paginate(10);

        if ($request->ajax()) {
            return view('backend.customers.table', compact('customers'))->render();
        }

        return view('backend.customers.index', compact('customers'));
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
            'username'         => 'required|string|max:50|unique:users',
            'gender'           => 'nullable|in:male,female,other',
            'birthday'         => 'nullable|date',
        ]);

        $user = User::create([
            'name'              => $request->name,
            'username'          => $request->username,
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
            Mail::send('emails.new_user', [
                'user' => $user,
                'default_password' => '123456',
            ], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Tài khoản của bạn đã được tạo');
            });
        } catch (\Exception $e) {
            Log::error('Mail error: ' . $e->getMessage());
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
            'username'         => 'required|string|max:50|unique:users,username,' . $id,
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
            'username' => $request->username,
            'must_update_profile' => $request->boolean('must_update_profile'),
            'is_active' => $request->boolean('is_active'), // ✅ xử lý checkbox chuẩn
            'tax_code' => $request->tax_code,
            'gender' => $request->gender,
            'birthday' => $request->birthday,
        ]);

        return redirect()->route('customers.index')->with('success', 'Cập nhật khách hàng thành công!');
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return redirect()->route('customers.index')->with('success', 'Đã xóa khách hàng!');
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
}
