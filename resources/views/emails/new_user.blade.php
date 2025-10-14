@component('mail::message')
# Xin chào {{ $user->name }},

Tài khoản của bạn đã được tạo thành công.

**Tên đăng nhập:** {{ $user->email }}
**Mật khẩu mặc định:** {{ $default_password }}

---

@component('mail::button', ['url' => $login_link])
👉 Đăng nhập ngay
@endcomponent

Trân trọng,
**Đội ngũ SGO Port**
@endcomponent
