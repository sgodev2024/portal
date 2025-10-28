<!DOCTYPE html>
<html>


<!-- Mirrored from id.tenten.vn/loginNavi by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 04 Dec 2024 01:24:10 GMT -->
<!-- Added by HTTrack -->
<meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->

<head>
    <title>Login</title>
    <!-- css -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    
    <!-- Google Translate -->
    <script type="text/javascript">
        function googleTranslateElementInit() {
            try {
                new google.translate.TranslateElement({
                    pageLanguage: 'vi',
                    includedLanguages: 'vi,de',
                    layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
                    autoDisplay: false,
                    multilanguagePage: true,
                    gaTrack: true,
                    gaId: 'UA-XXXXX-X'
                }, 'google_translate_element');
                console.log('Google Translate initialized');
            } catch(e) {
                console.error('Google Translate initialization failed:', e);
            }
        }
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    
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
    
    <!-- Initialize Google Translate Select for Login Page -->
    <script type="text/javascript">
        // Wait for Google Translate to be ready
        function waitForGoogleTranslate() {
            let attempts = 0;
            const maxAttempts = 30;
            
            const checkForGoogleTranslate = setInterval(function() {
                attempts++;
                const select = document.querySelector('select.goog-te-combo');
                
                if (select && select.options && select.options.length > 0) {
                    console.log('Google Translate loaded successfully');
                    window.googleTranslateSelect = select;
                    
                    // Set up language change handler
                    select.addEventListener('change', function() {
                        console.log('Language changed to:', this.value);
                        // No need to reload page - let Google Translate handle it
                    });
                    
                    clearInterval(checkForGoogleTranslate);
                } else if (attempts >= maxAttempts) {
                    console.log('Google Translate failed to load');
                    clearInterval(checkForGoogleTranslate);
                }
            }, 300);
        }
        
        // Start checking when page loads
        window.addEventListener('load', waitForGoogleTranslate);
        
        // Also check when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', waitForGoogleTranslate);
        } else {
            waitForGoogleTranslate();
        }
    </script>

</head>
<style type="text/css">
    /* Google Translate Styling */
    #google_translate_element {
        position: absolute !important;
        left: -9999px !important;
        visibility: hidden !important;
    }
    .goog-te-banner-frame { display: none !important; }
    .goog-te-balloon-frame { display: none !important; }
    .goog-tooltip { display: none !important; }
    .goog-te-gadget { font-size: 0 !important; }
    .goog-te-gadget-simple {
        background: #fff !important;
        border: 2px solid #0066cc !important;
        border-radius: 5px !important;
        padding: 10px 20px !important;
        font-size: 15px !important;
        font-weight: bold !important;
        cursor: pointer !important;
        display: inline-block !important;
    }
    .goog-te-gadget-simple:hover {
        background: #0066cc !important;
        color: #fff !important;
    }
    .goog-te-gadget-icon { display: none !important; }
    .goog-te-menu-value { 
        color: #0066cc !important; 
        font-size: 15px !important; 
        font-weight: bold !important;
    }
    .goog-te-menu-value span { font-size: 15px !important; }
    .skiptranslate { display: none !important; }
    body { top: 0 !important; }
    .goog-te-menu-frame { z-index: 99999 !important; }
    
    /* Ensure translated content is visible */
    .goog-te-combo {
        display: block !important;
    }
    
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

        .add_phone:first,
        {
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
            /* padding: 0px 29px; */
        }

        .add_phone:nth-of-type(1),
        {
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
        /* justify-content: space-between; */
        align-items: center;
        /* margin-bottom: 20px; */
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
        /* Căn phải */
    }

    .phone-wrapper span {
        /* display: flex; */
        justify-content: flex-end;
        /* Căn nội dung số điện thoại và chú thích bên phải */
        align-items: center;
        gap: 10px;
        /* Khoảng cách giữa số và chú thích */
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

    .forgot-password-link {
        margin-top: 15px;
        margin-bottom: 0;
        text-align: left;
    }

    .forgot-password-link {
        margin-top: 15px;
        margin-bottom: 0;
        text-align: center;
    }

    .forgot-password-link {
        margin-top: 15px;
        margin-bottom: 0;
        text-align: right;
    }

    .forgot-password-link a {
        display: inline-block;
        padding: 5px 0;
    }
</style>

<body class="form_page">
    <div id="qb_content_navi_2021">
        <!-- Language Switcher -->
        <div style="position: absolute; top: 20px; right: 20px; z-index: 999;">
            @include('components.language-switcher')
        </div>
        
        <!-- Google Translate Element -->
        <div id="google_translate_element" style="position: absolute; left: -9999px; visibility: hidden;"></div>
        
        <div class="login_display_02 login_page">
            <div class="ct_right">
                <div class="ct_right_ct">

                    <figure class="logo_login">
                        <a href="{{ url('/') }}" class="logo">
                            <img src="{{ $company?->company_logo ? asset('storage/' . $company->company_logo) : asset('backend/default-logo.png') }}"
                                alt="Company Logo" class="navbar-brand" />
                        </a>
                    </figure>

                    <div class="login_form" id="login_form" style="display: block">
                        <form method="post" accept-charset="utf-8" id="form-login" action="{{ route('login.post') }}">
                            @csrf

                            <div class="form_group" style="display: block;">
                                <div class="list_group">
                                    <input type="text" name="email" autocomplete="off" required=""
                                        placeholder="Email" id="email" value="{{ old('email') }}">
                                    <figure class="feild_icon"><img
                                            src="{{ asset('auth/images/login_user_icon.png') }}"></figure>
                                    @error('email')
                                        <small class="text-danger mb-2">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="list_group">
                                    <input type="password" name="password" autocomplete="off" required=""
                                        placeholder="Password" id="password" value="{{ old('password') }}">
                                    <figure class="feild_icon"><img
                                            src="{{ asset('auth/images/login_padlock_icon.png') }}"></figure>
                                    @error('password')
                                        <small class="text-danger mb-2">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <div class="form-check my-3">
                                        <input class="form-check-input" name="remember" type="checkbox" id="remember">
                                        <label class="form-check-label" for="remember">
                                            Lưu mật khẩu
                                        </label>
                                    </div>
                                </div>
                                <div class="btn">
                                    <button type="submit" name="button"
                                        class="loginButton loginButtonGg remove-msg before-login " id="submitBtn">Đăng nhập</button>
                                </div>
                                <div class="forgot-password-link">
                                    <a href="{{ route('password.request') }}" class="text-decoration-none"
                                        style="color:#007bff;">Quên mật khẩu?</a>
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
