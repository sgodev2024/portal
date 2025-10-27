<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stmt;
use Illuminate\Http\Request;

class StmtController extends Controller
{
    public function index()
    {
        $page = "Cấu hình STMT";
        $title = "Cấu hình hệ thống gửi và nhận mail";
        $stmt = Stmt::first();

        return view('backend.stmt.index', compact('stmt', 'page', 'title'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'mail_username' => 'required|email',
            'mail_password' => 'required|string',
            'mail_from_name' => 'nullable|string|max:255',
            'notification_emails' => 'nullable|string',
        ], [
            'mail_username.required' => 'Email gửi không được để trống.',
            'mail_username.email' => 'Email gửi phải là địa chỉ email hợp lệ.',
            'mail_password.required' => 'Mật khẩu email không được để trống.',
            'mail_password.string' => 'Mật khẩu email phải là chuỗi ký tự.',
            'mail_from_name.string' => 'Tên hiển thị phải là chuỗi ký tự.',
            'mail_from_name.max' => 'Tên hiển thị không được vượt quá 255 ký tự.',
            'notification_emails.string' => 'Danh sách email nhận thông báo phải là chuỗi ký tự.',
        ]);

        $emails = [];
        $invalidEmails = [];

        if (!empty($request->notification_emails)) {
            $emailList = array_map('trim', explode(',', $request->notification_emails));

            foreach ($emailList as $email) {
                if (empty($email)) {
                    continue;
                }

                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $emails[] = $email;
                } else {
                    $invalidEmails[] = $email;
                }
            }
        }
        if (!empty($invalidEmails)) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'notification_emails' => 'Các email sau không hợp lệ: ' . implode(', ', $invalidEmails)
                ]);
        }
        $data = [
            'mail_username' => $request->mail_username,
            'mail_password' => $request->mail_password,
            'mail_from_name' => $request->mail_from_name,
            'notification_emails' => $emails,
        ];

        $stmt = Stmt::updateOrCreate(['id' => 1], $data);

        toastr()->success('Cập nhật cấu hình STMT và danh sách email nhận thông báo thành công.');
        return redirect()->back();
    }
}
