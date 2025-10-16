<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Mail\GenericMail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\EmailTemplate;
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
        $password = '123456';

        $user = User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'phone'             => $request->phone,
            'identity_number'   => $request->identity_number,
            'password'          => Hash::make($password),
            'role'              => 3,
            'is_active'         => true,
            'must_update_profile' => true,
            'gender'            => $request->gender,
            'birthday'          => $request->birthday,
            'tax_code'          => $request->tax_code,
        ]);

        $template = EmailTemplate::where('code', 'new_user')
            ->where('is_active', true)
            ->first();

        if ($template) {
            try {
                Mail::to($user->email)->queue(new GenericMail(
                    $template,
                    [
                        'user_name'   => $user->name,
                        'user_email'  => $user->email,
                        'new_password' => $password,
                        'login_link'  => route('login'),
                        'app_name'    => config('app.name'),
                    ]
                ));
            } catch (\Exception $e) {
                Log::error('Mail gửi thất bại: ' . $e->getMessage());
            }
        }

        return redirect()->route('customers.index')->with('success', 'Thêm khách hàng thành công và gửi email thông báo!');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('backend.customers.edit', compact('user'));
    }

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

    public function resetPassword($id)
    {
        $user = User::where('role', 3)->findOrFail($id);
        $newPassword = '123456';

        $user->update([
            'password' => Hash::make($newPassword),
            'must_update_profile' => true,
        ]);

        try {
            $template = EmailTemplate::where('code', 'reset_password')
                ->where('is_active', true)
                ->first();

            if (!$template) {
                return back()->with('error', 'Không tìm thấy mẫu email "reset_password".');
            }

            Mail::to($user->email)->queue(new GenericMail(
                $template,
                [
                    'user_name'    => $user->name,
                    'user_email'   => $user->email,
                    'new_password' => $newPassword,
                    'login_link'   => route('login'),
                    'app_name'     => config('app.name'),
                ]
            ));

            return back()->with('success', "Đã reset mật khẩu cho {$user->name} và gửi email thông báo!");
        } catch (\Exception $e) {
            Log::error("Mail gửi thất bại: " . $e->getMessage());
            return back()->with('warning', "Đã reset mật khẩu nhưng chưa gửi được email cho {$user->email}.");
        }
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

            case 'send_reminder_mail':
                $customers = User::whereIn('id', $ids)
                    ->where('is_active', 1)
                    ->where('must_update_profile', 1)
                    ->get();

                if ($customers->isEmpty()) {
                    return back()->with('error', 'Không có khách hàng nào đủ điều kiện (đang hoạt động và chưa cập nhật hồ sơ).');
                }

                $template = EmailTemplate::where('code', 'reminder_mail')
                    ->where('is_active', true)
                    ->first();

                if (!$template) {
                    return back()->with('error', 'Không tìm thấy mẫu email "reminder_mail".');
                }

                $sentCount = 0;
                $failedCount = 0;

                foreach ($customers as $customer) {
                    try {
                        Mail::to($customer->email)->queue(new GenericMail(
                            $template,
                            [
                                'user_name'  => $customer->name,
                                'login_link' => route('login'),
                                'app_name'   => config('app.name'),
                            ]
                        ));
                        $sentCount++;
                    } catch (\Exception $e) {
                        Log::error('Failed to send reminder mail to ' . $customer->email . ': ' . $e->getMessage());
                        $failedCount++;
                    }
                }

                $message = "Đã gửi email nhắc nhở cho {$sentCount} khách hàng.";
                if ($failedCount > 0) {
                    $message .= " Có {$failedCount} email gửi thất bại.";
                }

                return back()->with('success', $message);

            case 'reset_password':
                $customers = User::whereIn('id', $ids)->where('role', 3)->get();

                $template = EmailTemplate::where('code', 'reset_password')
                    ->where('is_active', true)
                    ->first();

                if (!$template) {
                    return back()->with('error', 'Không tìm thấy mẫu email "reset_password".');
                }

                $reset = 0;

                foreach ($customers as $customer) {
                    $newPassword = '123456';
                    $customer->update([
                        'password' => Hash::make($newPassword),
                        'must_update_profile' => true,
                    ]);

                    try {
                        Mail::to($customer->email)->queue(new GenericMail(
                            $template,
                            [
                                'user_name'    => $customer->name,
                                'user_email'   => $customer->email,
                                'new_password' => $newPassword,
                                'login_link'   => route('login'),
                                'app_name'     => config('app.name'),
                            ]
                        ));
                        $reset++;
                    } catch (\Exception $e) {
                        Log::error('Reset password mail error for ' . $customer->email . ': ' . $e->getMessage());
                    }
                }

                return back()->with('success', "Đã reset mật khẩu mặc định (123456) cho {$reset} khách hàng và gửi mail thông báo.");

            default:
                return back()->with('error', 'Hành động không hợp lệ.');
        }
    }
}
