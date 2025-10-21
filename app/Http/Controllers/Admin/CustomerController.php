<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Mail\GenericMail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CustomerGroup;
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
        $query = User::where('role', 3)->with('groups');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('account_id', 'like', "%{$search}%");
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
        $user = User::where('role', 3)->with('groups')->findOrFail($id);
        return view('backend.customers.show', compact('user'));
    }

    public function create()
    {
        $groups = CustomerGroup::orderBy('name')->get();
        return view('backend.customers.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users',
            'account_id' => 'required|string|max:20|unique:users',
            'company'    => 'required|string|max:255',
            'address'    => 'nullable|string|max:500',
            'groups'     => 'nullable|array',
            'groups.*'   => 'exists:customer_groups,id',
        ], [
            'name.required'       => 'Họ tên không được để trống.',
            'email.required'      => 'Email không được để trống.',
            'email.unique'        => 'Email này đã được sử dụng.',
            'account_id.required' => 'Số điện thoại không được để trống.',
            'account_id.unique'   => 'Số điện thoại này đã được sử dụng.',
            'company.required'    => 'Tên công ty không được để trống.',
            'groups.*.exists'     => 'Nhóm khách hàng không hợp lệ.',
        ]);

        // Mật khẩu mặc định
        $password = '123456';

        // Tạo người dùng mới
        $user = User::create([
            'name'                => $validated['name'],
            'email'               => $validated['email'],
            'account_id'          => $validated['account_id'],
            'company'             => $validated['company'],
            'address'             => $validated['address'] ?? null,
            'password'            => Hash::make($password),
            'role'                => 3,
            'is_active' => $request->boolean('is_active'),
            'must_update_profile' => true,
        ]);

        // Gán nhóm cho khách hàng (nếu có)
        if (!empty($validated['groups'])) {
            $user->groups()->sync($validated['groups']);
        }

        // Gửi email thông báo tạo tài khoản
        $template = EmailTemplate::where('code', 'new_user')
            ->where('is_active', true)
            ->first();

        if ($template) {
            try {
                Mail::to($user->email)->queue(new GenericMail(
                    $template,
                    [
                        'user_name'    => $user->name,
                        'user_email'   => $user->email,
                        'new_password' => $password,
                        'login_link'   => route('login'),
                        'app_name'     => config('app.name'),
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
        $user = User::with('groups')->findOrFail($id);
        $groups = CustomerGroup::orderBy('name')->get();
        return view('backend.customers.edit', compact('user', 'groups'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $id,
            'account_id' => 'required|string|max:20|unique:users,account_id,' . $id,
            'company'    => 'nullable|string|max:255',
            'address'    => 'nullable|string|max:255',
            'groups'     => 'nullable|array',
            'groups.*'   => 'exists:customer_groups,id',
        ], [
            'name.required'       => 'Họ tên không được để trống.',
            'email.required'      => 'Email không được để trống.',
            'email.unique'        => 'Email này đã được sử dụng.',
            'account_id.required' => 'Số điện thoại không được để trống.',
            'account_id.unique'   => 'Số điện thoại này đã được sử dụng.',
            'groups.*.exists'     => 'Nhóm khách hàng không hợp lệ.',
        ]);

        $user->update([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'account_id' => $validated['account_id'],
            'company'    => $validated['company'],
            'address'    => $validated['address'],
            'is_active'  => $request->has('is_active'),
        ]);

        // Cập nhật nhóm (sync sẽ thay thế hoàn toàn)
        $user->groups()->sync($validated['groups'] ?? []);

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
    public function downTemplates()
    {
        $filePath = storage_path('app/public/templates/customer_import_template.xlsx');

        if (!file_exists($filePath)) {
            return back()->with('error', 'File mẫu không tồn tại.');
        }

        return response()->download($filePath, 'customer_import_template.xlsx');
    }
}
