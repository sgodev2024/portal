<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        // Email wrapper template với màu xanh dương #0056b3
        $emailWrapper = function($content) {
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
                'from_name' => 'Hỗ trợ khách hàng',
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
                'from_name' => 'Hỗ trợ khách hàng',
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
                'from_name' => 'Hỗ trợ khách hàng',
                'is_active' => true,
                'created_by' => 1,
            ]
        );
    }
}
