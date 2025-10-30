<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        // Email wrapper template với màu xanh dương #0056b3
        $emailWrapper = function ($content) {
            return '
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background-color: #f4f7fa;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f7fa; padding: 40px 20px;">
        <tr>
            <td align="center">
                <!-- Main Container -->
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #0056b3 0%, #0069d9 100%); padding: 40px 30px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 700; letter-spacing: -0.5px;">
                                {app_name}
                            </h1>
                            <p style="margin: 10px 0 0 0; color: #cce5ff; font-size: 14px;">
                                Hệ thống quản lý chuyên nghiệp
                            </p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            ' . $content . '
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #e6f2ff; padding: 30px; text-align: center; border-top: 1px solid #cce5ff;">
                            <p style="margin: 0 0 10px 0; color: #64748b; font-size: 13px;">
                                Email này được gửi tự động, vui lòng không trả lời.
                            </p>
                            <p style="margin: 0; color: #94a3b8; font-size: 12px;">
                                © 2025 {app_name}. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
            ';
        };

        // 1. Reset mật khẩu
        EmailTemplate::updateOrCreate(
            ['code' => 'reset_password'],
            [
                'name' => 'Reset mật khẩu',
                'subject' => 'Yêu cầu đặt lại mật khẩu của bạn',
                'body_html' => $emailWrapper('
                    <div style="text-align: center; margin-bottom: 30px;">
                        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #0056b3, #0069d9); border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                            <span style="font-size: 40px;">🔐</span>
                        </div>
                    </div>

                    <h2 style="color: #0056b3; font-size: 24px; margin: 0 0 20px 0; text-align: center;">
                        Xin chào, {user_name}!
                    </h2>

                    <p style="color: #475569; font-size: 15px; line-height: 1.6; margin: 0 0 15px 0;">
                        Chúng tôi đã đặt lại mật khẩu cho tài khoản của bạn theo yêu cầu.
                    </p>

                    <div style="background-color: #e6f2ff; border-left: 4px solid #0056b3; padding: 20px; margin: 25px 0; border-radius: 6px;">
                        <p style="margin: 0 0 10px 0; color: #334155; font-size: 14px;">
                            <strong>Mật khẩu mới của bạn:</strong>
                        </p>
                        <p style="margin: 0; font-size: 20px; font-weight: 700; color: #0056b3; letter-spacing: 1px; font-family: monospace;">
                            {new_password}
                        </p>
                    </div>

                    <p style="color: #475569; font-size: 15px; line-height: 1.6; margin: 0 0 25px 0;">
                        Vui lòng đăng nhập và đổi mật khẩu ngay để bảo mật tài khoản của bạn.
                    </p>

                    <div style="text-align: center; margin: 30px 0;">
                        <a href="{login_link}" style="display: inline-block; background: linear-gradient(135deg, #0056b3, #0069d9); color: #ffffff; padding: 14px 40px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 12px rgba(0, 86, 179, 0.3);">
                            Đăng nhập ngay
                        </a>
                    </div>

                    <div style="background-color: #fff7ed; border-left: 4px solid #f59e0b; padding: 15px; margin: 25px 0; border-radius: 6px;">
                        <p style="margin: 0; color: #78350f; font-size: 13px;">
                            ⚠️ <strong>Lưu ý:</strong> Nếu bạn không yêu cầu thay đổi này, vui lòng liên hệ với chúng tôi ngay lập tức.
                        </p>
                    </div>

                    <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin: 30px 0 0 0;">
                        Trân trọng,<br>
                        <strong style="color: #0056b3;">Đội ngũ hỗ trợ {app_name}</strong>
                    </p>
                '),
                'is_active' => true,
                'created_by' => 1,
            ]
        );

        // 10. Ticket được nhân viên nhận (claim)
        EmailTemplate::updateOrCreate(
            ['code' => 'ticket_claimed'],
            [
                'name' => 'Ticket đã được nhân viên nhận',
                'subject' => 'Ticket #{ticket_id} đã có nhân viên xử lý',
                'body_html' => $emailWrapper('
                    <div style="text-align: center; margin-bottom: 30px;">
                        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #059669, #10b981); border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                            <span style="font-size: 40px;">🤝</span>
                        </div>
                    </div>

                    <h2 style="color: #059669; font-size: 24px; margin: 0 0 20px 0; text-align: center;">
                        Ticket của bạn đã được tiếp nhận
                    </h2>

                    <p style="color: #475569; font-size: 15px; line-height: 1.6; margin: 0 0 15px 0;">
                        Xin chào <strong>{user_name}</strong>, ticket <strong>#{ticket_id}</strong> đã được một nhân viên tiếp nhận xử lý.
                    </p>

                    <div style="background-color: #f0fdf4; border-radius: 8px; padding: 25px; margin: 25px 0;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="color: #64748b; font-size: 14px; padding: 8px 0;"><strong>Tiêu đề:</strong></td>
                                <td style="color: #0f172a; font-size: 14px; font-weight: 600; text-align: right; padding: 8px 0;">{ticket_subject}</td>
                            </tr>
                        </table>
                    </div>

                    <div style="text-align: center; margin: 30px 0;">
                        <a href="{ticket_link}" style="display: inline-block; background: linear-gradient(135deg, #059669, #10b981); color: #ffffff; padding: 14px 40px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);">
                            Xem ticket
                        </a>
                    </div>

                    <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin: 30px 0 0 0;">
                        Trân trọng,<br>
                        <strong style="color: #059669;">Đội ngũ hỗ trợ {app_name}</strong>
                    </p>
                '),
                'is_active' => true,
                'created_by' => 1,
            ]
        );

        // 11. Thông báo cho nhân viên khi được gán ticket
        EmailTemplate::updateOrCreate(
            ['code' => 'ticket_assigned_to_staff'],
            [
                'name' => 'Nhân viên được gán ticket',
                'subject' => 'Bạn được gán Ticket #{ticket_id}',
                'body_html' => $emailWrapper('
                    <div style="text-align: center; margin-bottom: 30px;">
                        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #2563eb, #60a5fa); border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                            <span style="font-size: 40px;">📌</span>
                        </div>
                    </div>

                    <h2 style="color: #2563eb; font-size: 24px; margin: 0 0 20px 0; text-align: center;">
                        Bạn vừa được gán một ticket mới
                    </h2>

                    <div style="background-color: #eff6ff; border-radius: 8px; padding: 25px; margin: 25px 0;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="color: #64748b; font-size: 14px; padding: 8px 0;"><strong>Ticket ID:</strong></td>
                                <td style="color: #0f172a; font-size: 16px; font-weight: 700; text-align: right; padding: 8px 0;">#{ticket_id}</td>
                            </tr>
                            <tr>
                                <td style="color: #64748b; font-size: 14px; padding: 8px 0;"><strong>Tiêu đề:</strong></td>
                                <td style="color: #0f172a; font-size: 14px; font-weight: 600; text-align: right; padding: 8px 0;">{ticket_subject}</td>
                            </tr>
                            <tr>
                                <td style="color: #64748b; font-size: 14px; padding: 8px 0;"><strong>Khách hàng:</strong></td>
                                <td style="color: #0f172a; font-size: 14px; font-weight: 600; text-align: right; padding: 8px 0;">{user_name}</td>
                            </tr>
                        </table>
                    </div>

                    <div style="text-align: center; margin: 30px 0;">
                        <a href="{ticket_link}" style="display: inline-block; background: linear-gradient(135deg, #2563eb, #60a5fa); color: #ffffff; padding: 14px 40px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);">
                            Mở ticket
                        </a>
                    </div>

                    <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin: 30px 0 0 0;">
                        Trân trọng,<br>
                        <strong style="color: #2563eb;">Hệ thống {app_name}</strong>
                    </p>
                '),
                'is_active' => true,
                'created_by' => 1,
            ]
        );

        // 12. Khách hàng phản hồi (email tới nhân viên)
        EmailTemplate::updateOrCreate(
            ['code' => 'ticket_customer_replied'],
            [
                'name' => 'Khách hàng vừa phản hồi ticket',
                'subject' => 'Ticket #{ticket_id} - Khách hàng đã phản hồi',
                'body_html' => $emailWrapper('
                    <div style="text-align: center; margin-bottom: 30px;">
                        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #f59e0b, #fbbf24); border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                            <span style="font-size: 40px;">🗨️</span>
                        </div>
                    </div>

                    <h2 style="color: #92400e; font-size: 22px; margin: 0 0 16px 0; text-align: center;">
                        Khách hàng đã phản hồi ticket
                    </h2>

                    <div style="background-color: #fffbeb; border-radius: 8px; padding: 25px; margin: 25px 0;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="color: #64748b; font-size: 14px; padding: 8px 0;"><strong>Ticket ID:</strong></td>
                                <td style="color: #0f172a; font-size: 16px; font-weight: 700; text-align: right; padding: 8px 0;">#{ticket_id}</td>
                            </tr>
                            <tr>
                                <td style="color: #64748b; font-size: 14px; padding: 8px 0;"><strong>Tiêu đề:</strong></td>
                                <td style="color: #0f172a; font-size: 14px; font-weight: 600; text-align: right; padding: 8px 0;">{ticket_subject}</td>
                            </tr>
                            <tr>
                                <td style="color: #64748b; font-size: 14px; padding: 8px 0;"><strong>Khách hàng:</strong></td>
                                <td style="color: #0f172a; font-size: 14px; font-weight: 600; text-align: right; padding: 8px 0;">{user_name}</td>
                            </tr>
                        </table>
                    </div>

                    <div style="text-align: center; margin: 30px 0;">
                        <a href="{ticket_link}" style="display: inline-block; background: linear-gradient(135deg, #f59e0b, #fbbf24); color: #ffffff; padding: 14px 40px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);">
                            Xem ticket
                        </a>
                    </div>

                    <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin: 30px 0 0 0;">
                        Trân trọng,<br>
                        <strong style="color: #92400e;">Hệ thống {app_name}</strong>
                    </p>
                '),
                'is_active' => true,
                'created_by' => 1,
            ]
        );

        // 2. Tạo tài khoản mới
        EmailTemplate::updateOrCreate(
            ['code' => 'new_user'],
            [
                'name' => 'Tạo tài khoản mới',
                'subject' => 'Chào mừng bạn đến với hệ thống',
                'body_html' => $emailWrapper('
                    <div style="text-align: center; margin-bottom: 30px;">
                        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #0056b3, #0069d9); border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                            <span style="font-size: 40px;">🎉</span>
                        </div>
                    </div>

                    <h2 style="color: #0056b3; font-size: 24px; margin: 0 0 20px 0; text-align: center;">
                        Chào mừng, {user_name}!
                    </h2>

                    <p style="color: #475569; font-size: 15px; line-height: 1.6; margin: 0 0 15px 0;">
                        Tài khoản của bạn đã được tạo thành công tại <strong style="color: #0056b3;">{app_name}</strong>.
                    </p>

                    <p style="color: #475569; font-size: 15px; line-height: 1.6; margin: 0 0 25px 0;">
                        Dưới đây là thông tin đăng nhập của bạn:
                    </p>

                    <div style="background-color: #e6f2ff; border-radius: 8px; padding: 25px; margin: 25px 0;">
                        <table width="100%" cellpadding="8" cellspacing="0" border="0">
                            <tr>
                                <td style="color: #64748b; font-size: 14px; padding: 8px 0;">
                                    <strong>📧 Email:</strong>
                                </td>
                                <td style="color: #0f172a; font-size: 14px; font-weight: 600; text-align: right; padding: 8px 0;">
                                    {user_email}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding: 0;">
                                    <div style="height: 1px; background-color: #cce5ff; margin: 8px 0;"></div>
                                </td>
                            </tr>
                            <tr>
                                <td style="color: #64748b; font-size: 14px; padding: 8px 0;">
                                    <strong>🔑 Mật khẩu:</strong>
                                </td>
                                <td style="color: #0056b3; font-size: 16px; font-weight: 700; text-align: right; font-family: monospace; padding: 8px 0; letter-spacing: 1px;">
                                    {new_password}
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div style="text-align: center; margin: 30px 0;">
                        <a href="{login_link}" style="display: inline-block; background: linear-gradient(135deg, #0056b3, #0069d9); color: #ffffff; padding: 14px 40px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 12px rgba(0, 86, 179, 0.3);">
                            Đăng nhập ngay
                        </a>
                    </div>

                    <div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin: 25px 0; border-radius: 6px;">
                        <p style="margin: 0; color: #78350f; font-size: 13px;">
                            💡 <strong>Khuyến nghị:</strong> Sau khi đăng nhập lần đầu, vui lòng thay đổi mật khẩu để bảo mật tài khoản.
                        </p>
                    </div>

                    <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin: 30px 0 0 0;">
                        Chúc bạn có trải nghiệm tuyệt vời!<br>
                        <strong style="color: #0056b3;">Đội ngũ hỗ trợ {app_name}</strong>
                    </p>
                '),
                'is_active' => true,
                'created_by' => 1,
            ]
        );

        // 3. Nhắc nhở khách hàng chưa cập nhật hồ sơ
        EmailTemplate::updateOrCreate(
            ['code' => 'reminder_mail'],
            [
                'name' => 'Nhắc nhở cập nhật hồ sơ',
                'subject' => 'Hoàn tất hồ sơ của bạn',
                'body_html' => $emailWrapper('
                    <div style="text-align: center; margin-bottom: 30px;">
                        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #0056b3, #0069d9); border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                            <span style="font-size: 40px;">📝</span>
                        </div>
                    </div>

                    <h2 style="color: #0056b3; font-size: 24px; margin: 0 0 20px 0; text-align: center;">
                        Xin chào, {user_name}!
                    </h2>

                    <p style="color: #475569; font-size: 15px; line-height: 1.6; margin: 0 0 15px 0;">
                        Chúng tôi nhận thấy bạn chưa hoàn tất việc cập nhật hồ sơ cá nhân của mình tại <strong style="color: #0056b3;">{app_name}</strong>.
                    </p>

                    <p style="color: #475569; font-size: 15px; line-height: 1.6; margin: 0 0 25px 0;">
                        Việc cập nhật đầy đủ thông tin sẽ giúp bạn:
                    </p>

                    <div style="background-color: #e6f2ff; border-radius: 8px; padding: 20px; margin: 25px 0;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="padding: 10px 0;">
                                    <span style="display: inline-block; width: 32px; height: 32px; background-color: #0056b3; color: #ffffff; border-radius: 50%; text-align: center; line-height: 32px; font-weight: 700; margin-right: 12px;">✓</span>
                                    <span style="color: #334155; font-size: 14px;">Truy cập đầy đủ các tính năng</span>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 10px 0;">
                                    <span style="display: inline-block; width: 32px; height: 32px; background-color: #0056b3; color: #ffffff; border-radius: 50%; text-align: center; line-height: 32px; font-weight: 700; margin-right: 12px;">✓</span>
                                    <span style="color: #334155; font-size: 14px;">Bảo mật tài khoản tốt hơn</span>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 10px 0;">
                                    <span style="display: inline-block; width: 32px; height: 32px; background-color: #0056b3; color: #ffffff; border-radius: 50%; text-align: center; line-height: 32px; font-weight: 700; margin-right: 12px;">✓</span>
                                    <span style="color: #334155; font-size: 14px;">Nhận hỗ trợ nhanh chóng hơn</span>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <p style="color: #475569; font-size: 15px; line-height: 1.6; margin: 0 0 25px 0;">
                        Vui lòng nhấn nút bên dưới để hoàn tất hồ sơ của bạn:
                    </p>

                    <div style="text-align: center; margin: 30px 0;">
                        <a href="{login_link}" style="display: inline-block; background: linear-gradient(135deg, #0056b3, #0069d9); color: #ffffff; padding: 14px 40px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 12px rgba(0, 86, 179, 0.3);">
                            Hoàn tất hồ sơ ngay
                        </a>
                    </div>

                    <div style="background-color: #f0fdf4; border-left: 4px solid #10b981; padding: 15px; margin: 25px 0; border-radius: 6px;">
                        <p style="margin: 0; color: #065f46; font-size: 13px;">
                            💪 Chỉ mất vài phút để hoàn tất! Cập nhật ngay hôm nay để không bỏ lỡ các tính năng tuyệt vời.
                        </p>
                    </div>

                    <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin: 30px 0 0 0;">
                        Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi!<br>
                        <strong style="color: #0056b3;">Đội ngũ hỗ trợ {app_name}</strong>
                    </p>
                '),
                'is_active' => true,
                'created_by' => 1,
            ]
        );
        // Thêm vào EmailTemplateSeeder.php

        // 4. Thông báo chung
        EmailTemplate::updateOrCreate(
            ['code' => 'notification'],
            [
                'name' => 'Thông báo chung từ hệ thống',
                'subject' => 'Thông báo mới',
                'body_html' => $emailWrapper('
            <div style="text-align: center; margin-bottom: 30px;">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #0056b3, #0069d9); border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                    <span style="font-size: 40px;">🔔</span>
                </div>
            </div>

            <h2 style="color: #0056b3; font-size: 24px; margin: 0 0 20px 0; text-align: center;">
                Xin chào, {user_name}!
            </h2>

            <p style="color: #475569; font-size: 15px; line-height: 1.6; margin: 0 0 15px 0;">
                Bạn có một thông báo mới từ <strong style="color: #0056b3;">{app_name}</strong>:
            </p>

            <div style="background-color: #e6f2ff; border-left: 4px solid #0056b3; padding: 25px; margin: 25px 0; border-radius: 8px;">
                <h3 style="color: #0056b3; font-size: 18px; margin: 0 0 15px 0;">
                    {notification_title}
                </h3>
                <div style="color: #334155; font-size: 15px; line-height: 1.8; white-space: pre-wrap;">
                    {notification_content}
                </div>
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{notification_link}" style="display: inline-block; background: linear-gradient(135deg, #0056b3, #0069d9); color: #ffffff; padding: 14px 40px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 12px rgba(0, 86, 179, 0.3);">
                    Xem chi tiết
                </a>
            </div>

            <div style="background-color: #f0fdf4; border-left: 4px solid #10b981; padding: 15px; margin: 25px 0; border-radius: 6px;">
                <p style="margin: 0; color: #065f46; font-size: 13px;">
                    💡 Đăng nhập vào hệ thống để xem đầy đủ nội dung và tương tác với thông báo.
                </p>
            </div>

            <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin: 30px 0 0 0;">
                Trân trọng,<br>
                <strong style="color: #0056b3;">Đội ngũ hỗ trợ {app_name}</strong>
            </p>
        '),
                'is_active' => true,
                'created_by' => 1,
            ]
        );
        // 5. Quên mật khẩu - Gửi link đặt lại
        EmailTemplate::updateOrCreate(
            ['code' => 'forgot_password'],
            [
                'name' => 'Quên mật khẩu',
                'subject' => 'Yêu cầu khôi phục mật khẩu của bạn',
                'body_html' => $emailWrapper('
            <div style="text-align: center; margin-bottom: 30px;">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #0056b3, #0069d9); border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                    <span style="font-size: 40px;">📩</span>
                </div>
            </div>

            <h2 style="color: #0056b3; font-size: 24px; margin: 0 0 20px 0; text-align: center;">
                Xin chào, {user_name}!
            </h2>

            <p style="color: #475569; font-size: 15px; line-height: 1.6; margin: 0 0 15px 0;">
                Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn tại <strong style="color: #0056b3;">{app_name}</strong>.
            </p>

            <p style="color: #475569; font-size: 15px; line-height: 1.6; margin: 0 0 25px 0;">
                Nếu bạn là người gửi yêu cầu này, vui lòng nhấn vào nút bên dưới để tạo mật khẩu mới:
            </p>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{reset_link}" style="display: inline-block; background: linear-gradient(135deg, #0056b3, #0069d9); color: #ffffff; padding: 14px 40px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 12px rgba(0, 86, 179, 0.3);">
                    Đặt lại mật khẩu
                </a>
            </div>

            <div style="background-color: #fff7ed; border-left: 4px solid #f59e0b; padding: 15px; margin: 25px 0; border-radius: 6px;">
                <p style="margin: 0; color: #78350f; font-size: 13px;">
                    ⚠️ <strong>Lưu ý:</strong> Liên kết này chỉ có hiệu lực trong 60 phút. Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.
                </p>
            </div>

            <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin: 30px 0 0 0;">
                Trân trọng,<br>
                <strong style="color: #0056b3;">Đội ngũ hỗ trợ {app_name}</strong>
            </p>
        '),
                'is_active' => true,
                'created_by' => 1,
            ]
        );

        // 6. Ticket được tạo
        EmailTemplate::updateOrCreate(
            ['code' => 'ticket_created'],
            [
                'name' => 'Ticket được tạo',
                'subject' => 'Ticket #{ticket_id} - {ticket_subject}',
                'body_html' => $emailWrapper('
                    <div style="text-align: center; margin-bottom: 30px;">
                        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #0056b3, #0069d9); border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                            <span style="font-size: 40px;">🎫</span>
                        </div>
                    </div>

                    <h2 style="color: #0056b3; font-size: 24px; margin: 0 0 20px 0; text-align: center;">
                        Ticket của bạn đã được tạo
                    </h2>

                    <p style="color: #475569; font-size: 15px; line-height: 1.6; margin: 0 0 15px 0;">
                        Xin chào <strong>{user_name}</strong>, chúng tôi đã nhận được ticket hỗ trợ của bạn tại <strong style="color: #0056b3;">{app_name}</strong>.
                    </p>

                    <div style="background-color: #e6f2ff; border-radius: 8px; padding: 25px; margin: 25px 0;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="color: #64748b; font-size: 14px; padding: 8px 0;"><strong>Ticket ID:</strong></td>
                                <td style="color: #0f172a; font-size: 16px; font-weight: 700; text-align: right; padding: 8px 0;">#{ticket_id}</td>
                            </tr>
                            <tr><td colspan="2" style="padding: 0;"><div style="height: 1px; background-color: #cce5ff; margin: 8px 0;"></div></td></tr>
                            <tr>
                                <td style="color: #64748b; font-size: 14px; padding: 8px 0;"><strong>Tiêu đề:</strong></td>
                                <td style="color: #0f172a; font-size: 14px; font-weight: 600; text-align: right; padding: 8px 0;">{ticket_subject}</td>
                            </tr>
                            <tr><td colspan="2" style="padding: 0;"><div style="height: 1px; background-color: #cce5ff; margin: 8px 0;"></div></td></tr>
                            <tr>
                                <td style="color: #64748b; font-size: 14px; padding: 8px 0;"><strong>Trạng thái:</strong></td>
                                <td style="text-align: right; padding: 8px 0;"><span style="background-color: #cfe2ff; color: #0056b3; padding: 5px 12px; border-radius: 12px; font-size: 13px; font-weight: 600;">Chưa xử lý</span></td>
                            </tr>
                        </table>
                    </div>

                    <div style="text-align: center; margin: 30px 0;">
                        <a href="{ticket_link}" style="display: inline-block; background: linear-gradient(135deg, #0056b3, #0069d9); color: #ffffff; padding: 14px 40px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 12px rgba(0, 86, 179, 0.3);">
                            Xem ticket
                        </a>
                    </div>

                    <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin: 30px 0 0 0;">
                        Trân trọng,<br>
                        <strong style="color: #0056b3;">Đội ngũ hỗ trợ {app_name}</strong>
                    </p>
                '),
                'is_active' => true,
                'created_by' => 1,
            ]
        );

        // 7. Ticket được gán
        EmailTemplate::updateOrCreate(
            ['code' => 'ticket_assigned'],
            [
                'name' => 'Ticket được gán',
                'subject' => 'Ticket #{ticket_id} đã được gán cho bạn',
                'body_html' => $emailWrapper('
                    <div style="text-align: center; margin-bottom: 30px;">
                        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #059669, #10b981); border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                            <span style="font-size: 40px;">✅</span>
                        </div>
                    </div>

                    <h2 style="color: #059669; font-size: 24px; margin: 0 0 20px 0; text-align: center;">
                        Ticket đã được phân công
                    </h2>

                    <p style="color: #475569; font-size: 15px; line-height: 1.6; margin: 0 0 15px 0;">
                        Xin chào <strong>{user_name}</strong>, ticket của bạn tại <strong style="color: #0056b3;">{app_name}</strong> đã được phân công cho nhân viên xử lý.
                    </p>

                    <div style="background-color: #f0fdf4; border-radius: 8px; padding: 25px; margin: 25px 0;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="color: #64748b; font-size: 14px; padding: 8px 0;"><strong>Ticket ID:</strong></td>
                                <td style="color: #0f172a; font-size: 16px; font-weight: 700; text-align: right; padding: 8px 0;">#{ticket_id}</td>
                            </tr>
                            <tr><td colspan="2" style="padding: 0;"><div style="height: 1px; background-color: #d1fae5; margin: 8px 0;"></div></td></tr>
                            <tr>
                                <td style="color: #64748b; font-size: 14px; padding: 8px 0;"><strong>Tiêu đề:</strong></td>
                                <td style="color: #0f172a; font-size: 14px; font-weight: 600; text-align: right; padding: 8px 0;">{ticket_subject}</td>
                            </tr>
                            <tr><td colspan="2" style="padding: 0;"><div style="height: 1px; background-color: #d1fae5; margin: 8px 0;"></div></td></tr>
                            <tr>
                                <td style="color: #64748b; font-size: 14px; padding: 8px 0;"><strong>Nhân viên:</strong></td>
                                <td style="color: #059669; font-size: 14px; font-weight: 700; text-align: right; padding: 8px 0;">{staff_name}</td>
                            </tr>
                        </table>
                    </div>

                    <div style="text-align: center; margin: 30px 0;">
                        <a href="{ticket_link}" style="display: inline-block; background: linear-gradient(135deg, #059669, #10b981); color: #ffffff; padding: 14px 40px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);">
                            Xem ticket
                        </a>
                    </div>

                    <div style="background-color: #f0fdf4; border-left: 4px solid #10b981; padding: 15px; margin: 25px 0; border-radius: 6px;">
                        <p style="margin: 0; color: #065f46; font-size: 13px;">
                            💡 Nhân viên sẽ liên hệ với bạn sớm nhất có thể để xử lý ticket của bạn.
                        </p>
                    </div>

                    <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin: 30px 0 0 0;">
                        Trân trọng,<br>
                        <strong style="color: #059669;">Đội ngũ hỗ trợ {app_name}</strong>
                    </p>
                '),
                'is_active' => true,
                'created_by' => 1,
            ]
        );

        // 8. Ticket được đóng
        EmailTemplate::updateOrCreate(
            ['code' => 'ticket_closed'],
            [
                'name' => 'Ticket được đóng',
                'subject' => 'Ticket #{ticket_id} đã được đóng',
                'body_html' => $emailWrapper('
                    <div style="text-align: center; margin-bottom: 30px;">
                        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #6b7280, #9ca3af); border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                            <span style="font-size: 40px;">🔒</span>
                        </div>
                    </div>

                    <h2 style="color: #6b7280; font-size: 24px; margin: 0 0 20px 0; text-align: center;">
                        Ticket đã được đóng
                    </h2>

                    <p style="color: #475569; font-size: 15px; line-height: 1.6; margin: 0 0 15px 0;">
                        Xin chào <strong>{user_name}</strong>, ticket của bạn tại <strong style="color: #0056b3;">{app_name}</strong> đã được đóng.
                    </p>

                    <div style="background-color: #f9fafb; border-radius: 8px; padding: 25px; margin: 25px 0;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="color: #64748b; font-size: 14px; padding: 8px 0;"><strong>Ticket ID:</strong></td>
                                <td style="color: #0f172a; font-size: 16px; font-weight: 700; text-align: right; padding: 8px 0;">#{ticket_id}</td>
                            </tr>
                            <tr><td colspan="2" style="padding: 0;"><div style="height: 1px; background-color: #e5e7eb; margin: 8px 0;"></div></td></tr>
                            <tr>
                                <td style="color: #64748b; font-size: 14px; padding: 8px 0;"><strong>Tiêu đề:</strong></td>
                                <td style="color: #0f172a; font-size: 14px; font-weight: 600; text-align: right; padding: 8px 0;">{ticket_subject}</td>
                            </tr>
                            <tr><td colspan="2" style="padding: 0;"><div style="height: 1px; background-color: #e5e7eb; margin: 8px 0;"></div></td></tr>
                            <tr>
                                <td style="color: #64748b; font-size: 14px; padding: 8px 0;"><strong>Trạng thái:</strong></td>
                                <td style="text-align: right; padding: 8px 0;"><span style="background-color: #f3f4f6; color: #6b7280; padding: 5px 12px; border-radius: 12px; font-size: 13px; font-weight: 600;">Đã đóng</span></td>
                            </tr>
                        </table>
                    </div>

                    <div style="text-align: center; margin: 30px 0;">
                        <a href="{ticket_link}" style="display: inline-block; background: linear-gradient(135deg, #6b7280, #9ca3af); color: #ffffff; padding: 14px 40px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3);">
                            Xem lịch sử ticket
                        </a>
                    </div>

                    <div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin: 25px 0; border-radius: 6px;">
                        <p style="margin: 0; color: #78350f; font-size: 13px;">
                            💡 Nếu bạn có bất kỳ thắc mắc hoặc câu hỏi nào, vui lòng tạo ticket mới.
                        </p>
                    </div>

                    <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin: 30px 0 0 0;">
                        Trân trọng,<br>
                        <strong style="color: #6b7280;">Đội ngũ hỗ trợ {app_name}</strong>
                    </p>
                '),
                'is_active' => true,
                'created_by' => 1,
            ]
        );

        // 9. Ticket có reply mới
        EmailTemplate::updateOrCreate(
            ['code' => 'ticket_replied'],
            [
                'name' => 'Ticket có phản hồi mới',
                'subject' => 'Ticket #{ticket_id} - Có phản hồi mới',
                'body_html' => $emailWrapper('
                    <div style="text-align: center; margin-bottom: 30px;">
                        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #0056b3, #0069d9); border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                            <span style="font-size: 40px;">💬</span>
                        </div>
                    </div>

                    <h2 style="color: #0056b3; font-size: 24px; margin: 0 0 20px 0; text-align: center;">
                        Có phản hồi mới
                    </h2>

                    <p style="color: #475569; font-size: 15px; line-height: 1.6; margin: 0 0 15px 0;">
                        Xin chào <strong>{user_name}</strong>, ticket của bạn đã nhận được phản hồi mới từ <strong style="color: #0056b3;">{app_name}</strong>.
                    </p>

                    <div style="background-color: #e6f2ff; border-radius: 8px; padding: 25px; margin: 25px 0;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="color: #64748b; font-size: 14px; padding: 8px 0;"><strong>Ticket ID:</strong></td>
                                <td style="color: #0f172a; font-size: 16px; font-weight: 700; text-align: right; padding: 8px 0;">#{ticket_id}</td>
                            </tr>
                            <tr><td colspan="2" style="padding: 0;"><div style="height: 1px; background-color: #cce5ff; margin: 8px 0;"></div></td></tr>
                            <tr>
                                <td style="color: #64748b; font-size: 14px; padding: 8px 0;"><strong>Tiêu đề:</strong></td>
                                <td style="color: #0f172a; font-size: 14px; font-weight: 600; text-align: right; padding: 8px 0;">{ticket_subject}</td>
                            </tr>
                            <tr><td colspan="2" style="padding: 0;"><div style="height: 1px; background-color: #cce5ff; margin: 8px 0;"></div></td></tr>
                            <tr>
                                <td style="color: #64748b; font-size: 14px; padding: 8px 0;"><strong>Người gửi:</strong></td>
                                <td style="color: #0f172a; font-size: 14px; font-weight: 600; text-align: right; padding: 8px 0;">{sender_name}</td>
                            </tr>
                        </table>
                    </div>

                    <div style="text-align: center; margin: 30px 0;">
                        <a href="{ticket_link}" style="display: inline-block; background: linear-gradient(135deg, #0056b3, #0069d9); color: #ffffff; padding: 14px 40px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 12px rgba(0, 86, 179, 0.3);">
                            Xem phản hồi
                        </a>
                    </div>

                    <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin: 30px 0 0 0;">
                        Trân trọng,<br>
                        <strong style="color: #0056b3;">Đội ngũ hỗ trợ {app_name}</strong>
                    </p>
                '),
                'is_active' => true,
                'created_by' => 1,
            ]
        );
    }
}
