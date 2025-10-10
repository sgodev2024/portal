<style>
    .nav-collapse {
        margin-bottom: 0px !important;
    }

    .sidebar-wrapper {
        background-color: #005aa1 no-repeat !important;
    }

    .nav-collapse {
        padding: 0px 0px 0px 13px;
    }

    .sub-item {
        font-size: 14px !important;
    }

    .sub-item span {
        margin-left: 14px;
    }

    #sidebar ul li a,
    #sidebar ul li p,
    #sidebar ul h4,
    #sidebar ul li i,
    #sidebar ul li span {
        color: #ffffff !important;
    }
</style>
<div class="sidebar" data-background-color="dark" id="sidebar">
    <div class="sidebar-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="white">
            <a target="_blank" href="https://sgomedia.vn/" class="logo">
                <img style="width: 80%;" src="{{ asset('backend/SGO VIET NAM (1000 x 375 px).png') }}" alt="navbar brand"
                    class="navbar-brand img-fluid" />
            </a>
            <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                    <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                    <i class="gg-menu-left"></i>
                </button>
            </div>
            <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
            </button>
        </div>
        <!-- End Logo Header -->
    </div>

    <div class="sidebar-wrapper scrollbar scrollbar-inner" style="background: #005aa1 no-repeat">
        <div class="sidebar-content">
            <ul class="nav nav-secondary">
                <!-- Dashboard -->
                @php
                    $dashboardRoute = auth()->user()->isAdmin() ? 'admin.dashboard' : 'staff.dashboard';
                @endphp

                <li class="nav-item {{ request()->routeIs($dashboardRoute) ? 'active' : '' }}">
                    <a href="{{ route($dashboardRoute) }}">
                        <i class="fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-section">
                    <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
                    <h4 class="text-section">Components</h4>
                </li>
                @if (Auth::user()->role == 1)
                    <!-- Cấu hình -->
                    <li
                        class="nav-item {{ request()->routeIs('company.index', 'user.index', 'smtp.email', 'smtp.template', 'config_bank.*') ? 'active' : '' }}">
                        <a data-bs-toggle="collapse" href="#config"
                            aria-expanded="{{ request()->routeIs('company.index', 'user.index', 'smtp.email', 'smtp.template', 'config_bank.*') ? 'true' : 'false' }}">
                            <i class="fas fa-cogs"></i>
                            <p>Cấu hình</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse {{ request()->routeIs('company.index', 'user.index', 'smtp.email', 'smtp.template', 'config_bank.*') ? 'show' : '' }}"
                            id="config">
                            <ul class="nav nav-collapse">
                                <li class="{{ request()->routeIs('company.index') ? 'active' : '' }}">
                                    <a href="{{ route('company.index') }}">
                                        <span class="sub-item"><span>Thông tin công ty</span></span>
                                    </a>
                                </li>
                                {{-- <li class="{{ request()->routeIs('user.index') ? 'active' : '' }}">
                                    <a href="{{ route('user.index') }}">
                                        <span class="sub-item"><span>Tài khoản nhân sự</span></span>
                                    </a>
                                </li>
                                <li class="{{ request()->routeIs('smtp.email', 'smtp.template') ? 'active' : '' }}">
                                    <a href="{{ route('smtp.email') }}">
                                        <span class="sub-item"><span>SMTP</span></span>
                                    </a>
                                </li>
                                <li class="{{ request()->routeIs('config_bank.*') ? 'active' : '' }}">
                                    <a href="{{ route('config_bank.index') }}">
                                        <span class="sub-item"><span>Thông tin thanh toán</span></span>
                                    </a>
                                </li> --}}
                            </ul>
                        </div>
                    </li>
                @endif
                <li
                    class="nav-item
    {{ Auth::user()->role == 1 && request()->routeIs('chat.*') ? 'active' : '' }}
    {{ Auth::user()->role == 2 && request()->routeIs('staff.chats.*') ? 'active' : '' }}
">
                    @if (Auth::user()->role == 1)
                        <a href="{{ route('chat.index') }}">
                            <i class="fas fa-comments"></i>
                            <p>Chat hỗ trợ</p>
                        </a>
                    @elseif (Auth::user()->role == 2)
                        <a href="{{ route('staff.chats.index') }}">
                            <i class="fas fa-headset"></i>
                            <p>Chat khách hàng</p>
                        </a>
                    @elseif (Auth::user()->role == 3)
                        {{-- <a href="{{ route('customer.chats.index') }}">
                            <i class="fas fa-comment-dots"></i>
                            <p>Liên hệ hỗ trợ</p>
                        </a> --}}
                    @endif
                </li>
            </ul>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $(".nav-item > a").click(function(e) {
                if ($(this).next(".collapse").hasClass("show")) {
                    return;
                }
                $(".collapse").not($(this).next(".collapse")).removeClass("show");
                $(".nav-item").removeClass("active");
                $(this).parent().addClass("active");
            });
        });
    </script>

</div>
