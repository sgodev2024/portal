<!DOCTYPE html>
<html>
<head>
    <title>Quên mật khẩu</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/6.0.0-beta1/css/tempus-dominus.min.css">

    <link rel="stylesheet" type="text/css" href="{{ asset('auth/css/style.css') }}">
    <link rel="icon" href="https://sgomedia.vn/wp-content/uploads/2023/06/cropped-favicon-sgomedia-32x32.png"
        type="image/x-icon">
    <!-- js -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.marquee/1.5.0/jquery.marquee.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.pause/0.2/jquery.pause.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/6.0.0-beta1/js/tempus-dominus.min.js">
    </script>
    <script src="{{ asset('auth/js/api.js') }}" async defer></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
</head>

<style type="text/css">
    .error_txt {
        color: red;
    }

    .active {
        display: none;
    }

    .btn {
        margin-top: 20px;
    }

    .pointer {
        cursor: pointer;
    }

    .g-recaptcha div {
        margin: auto;
    }

    .logo_login img {
        margin-bottom: 20px;
    }

    .loginButton:disabled {
        cursor: no-drop;
    }

    .disabled_button {
        background: #6d9abb !important;
        cursor: no-drop;
    }

    /* Icon styles */
    .list_group {
        position: relative;
    }

    .list_group i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        font-size: 16px;
        pointer-events: none;
        z-index: 1;
    }

    .list_group input {
        padding-left: 45px !important;
    }

    @media (min-width: 768px) {
        .login_page .ct_left {
            min-height: 625px;
        }

        .login_page .ct_right {
            min-height: 625px;
        }

        .add_phone {
            display: block;
            text-align: right;
        }

        .add_phone:first {
            padding: 0px 26px !important;
        }
    }

    @media (min-width: 375px) and (max-width: 550px) {
        .rc-image-tile-33 {
            width: 200%;
            height: 200%;
        }

        .rc-image-tile-44 {
            width: 300%;
            height: 300%;
        }

        .add_phone {
            display: block;
            text-align: right;
        }

        .add_phone:nth-of-type(1) {
            padding: 0px 29px;
        }
    }

    .support-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .support-item {
        display: flex;
        align-items: center;
    }

    .diff_strong {
        font-weight: bold;
        color: #fff;
        flex-shrink: 0;
        margin-right: 20px;
    }

    .phone-wrapper {
        display: flex;
        flex-direction: column;
        text-align: right;
    }

    .phone-wrapper span {
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
    }

    .normal_strong {
        font-weight: normal;
        color: #fff;
    }

    p {
        margin: 0;
        font-size: 14px;
        color: #ddd;
    }

    /* CSS cho trang Quên mật khẩu */
    .login_form h4 {
        color: #6b4423;
        font-weight: 600;
        font-size: 22px;
        margin-bottom: 12px;
    }

    .login_form p.text-muted {
        color: #666;
        font-size: 14px;
        line-height: 1.6;
        margin-bottom: 24px;
    }

    .text-danger {
        display: block;
        margin-top: 8px;
        font-size: 13px;
        color: #dc3545;
    }

    .text-success {
        display: block;
        margin-top: 8px;
        font-size: 13px;
        color: #155724;
        background: #d4edda;
        padding: 10px 12px;
        border-radius: 6px;
        border-left: 3px solid #28a745;
    }

    /* Link quay lại đăng nhập */
    .back-to-login {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #e0e0e0;
        text-align: center;
    }

    .back-to-login a {
        color: #007bff;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .back-to-login a:hover {
        background: rgba(0, 123, 255, 0.08);
        color: #0056b3;
    }

    /* Responsive */
    @media (max-width: 576px) {
        .login_form h4 {
            font-size: 20px;
        }

        .login_form p.text-muted {
            font-size: 13px;
        }
    }
</style>

<body class="form_page">
    <div id="qb_content_navi_2021">
        <div class="login_display_02 login_page">
            <div class="ct_right">
                <div class="ct_right_ct">
                    <figure class="logo_login">
                        <a href="{{ url('/') }}" class="logo">
                            <img src="{{ $company?->company_logo ? asset('storage/' . $company->company_logo) : asset('backend/default-logo.png') }}"
                                alt="Company Logo" class="navbar-brand" />
                        </a>
                    </figure>

                    <div class="login_form" id="forgot_form" style="display: block">
                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf
                            <div class="form_group" style="display: block;">
                                <h4 class="text-center mb-3">
                                    Quên mật khẩu
                                </h4>
                                <p class="text-center text-muted mb-4">
                                    Nhập email đã đăng ký để nhận liên kết đặt lại mật khẩu.
                                </p>

                                <div class="list_group">
                                    <i class="fas fa-envelope"></i>
                                    <input type="email" name="email" autocomplete="off" required
                                        placeholder="Nhập email của bạn" id="email" value="{{ old('email') }}">
                                    @error('email')
                                        <small class="text-danger mb-2">
                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                        </small>
                                    @enderror
                                    @if(session('success'))
                                        <small class="text-success mb-2">
                                            <i class="fas fa-check-circle me-1"></i>{{ session('success') }}
                                        </small>
                                    @endif
                                </div>

                                <div class="btn">
                                    <button type="submit" class="loginButton w-100" id="submitBtn">
                                        Gửi liên kết đặt lại
                                    </button>
                                </div>

                                <div class="back-to-login">
                                    <a href="{{ route('login') }}">
                                        <i class="fas fa-arrow-left"></i> Quay lại đăng nhập
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
