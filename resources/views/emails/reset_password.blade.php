@component('mail::message')
# Xin chào {{ $user->name }},

Mật khẩu tài khoản của bạn vừa được **đặt lại** bởi quản trị viên.

**Thông tin đăng nhập mới:**
- **Email:** {{ $user->email }}
- **Mật khẩu mới:** {{ $newPassword }}

Vì lý do bảo mật, bạn nên **đăng nhập ngay và thay đổi mật khẩu** mới để đảm bảo an toàn.

@component('mail::button', ['url' => $login_link])
Đăng nhập ngay
@endcomponent

Nếu bạn không yêu cầu thao tác này, vui lòng liên hệ ngay với quản trị viên để được hỗ trợ.

Cảm ơn bạn đã sử dụng hệ thống của chúng tôi!
Trân trọng,
**Đội ngũ hỗ trợ khách hàng**
@endcomponent
