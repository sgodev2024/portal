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
                {{-- <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}">
                        <i class="fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li> --}}

                <li class="nav-section">
                    <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
                    <h4 class="text-section">Components</h4>
                </li>

                @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 3)
                    <!-- Cấu hình -->
                    @if (Auth::user()->role_id == 1)
                        <li
                            class="nav-item {{ request()->routeIs('company.index', 'user.index', 'smtp.email', 'smtp.template') ? 'active' : '' }}">
                            <a data-bs-toggle="collapse" href="#config">
                                <i class="fas fa-pen-square"></i>
                                <p>Cấu hình</p>
                                <span class="caret"></span>
                            </a>
                            <div class="collapse {{ request()->routeIs('company.index', 'user.index', 'smtp.email', 'smtp.template') ? 'show' : '' }}"
                                id="config">
                                <ul class="nav nav-collapse">
                                    <li class="{{ request()->routeIs('company.index') ? 'active' : '' }}">
                                        <a href="{{ route('company.index') }}"><span class="sub-item"><span>Công
                                                    ty</span></span></a>
                                    </li>
                                    <li class="{{ request()->routeIs('user.index') ? 'active' : '' }}">
                                        <a href="{{ route('user.index') }}"><span class="sub-item"><span>Tài khoản
                                                    nhân sự</span></span></a>
                                    </li>
                                    <li class="{{ request()->routeIs('smtp.email', 'smtp.template') ? 'active' : '' }}">
                                        <a href="{{ route('smtp.email') }}"><span
                                                class="sub-item"><span>SMTP</span></span></a>
                                    </li>
                                    <li class="{{ request()->routeIs('config_bank.*') ? 'active' : '' }}">
                                        <a href="{{ route('config_bank.index') }}">
                                            <span class="sub-item"><span>Thông tin thanh toán</span></span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <!-- Quản lý khách -->
                        <li class="nav-item {{ request()->routeIs('client.index') ? 'active' : '' }}">
                            <a href="{{ route('client.index') }}">
                                <i class="fas fa-user"></i>
                                <p>Quản lý khách</p>
                            </a>
                        </li>

                        <!-- Quản lý dịch vụ -->
                        <li
                            class="nav-item {{ request()->routeIs('domain.price', 'hosting.index', 'email.index', 'cloud.index') ? 'active' : '' }}">
                            <a data-bs-toggle="collapse" href="#service">
                                <i class="fas fa-server"></i>
                                <p>Bảng giá dịch vụ</p>
                                <span class="caret"></span>
                            </a>
                            <div class="collapse {{ request()->routeIs('domain.price', 'hosting.index', 'email.index', 'cloud.index') ? 'show' : '' }}"
                                id="service">
                                <ul class="nav nav-collapse">
                                    <li class="{{ request()->routeIs('domain.price') ? 'active' : '' }}">
                                        <a href="{{ route('domain.price') }}"><span class="sub-item"><span>Tên
                                                    miền</span></span></a>
                                    </li>
                                    <li class="{{ request()->routeIs('hosting.index') ? 'active' : '' }}">
                                        <a href="{{ route('hosting.index') }}"><span
                                                class="sub-item"><span>Hosting</span></span></a>
                                    </li>
                                    <li class="{{ request()->routeIs('email.index') ? 'active' : '' }}">
                                        <a href="{{ route('email.index') }}"><span class="sub-item"><span>Email
                                                    Server</span></span></a>
                                    </li>
                                    <li class="{{ request()->routeIs('cloud.index') ? 'active' : '' }}">
                                        <a href="{{ route('cloud.index') }}"><span
                                                class="sub-item"><span>Cloud</span></span></a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endif

                    <!-- Quản lý dịch vụ được đăng ky -->
                    <li
                        class="nav-item {{ request()->routeIs('domain.index', 'service.hosting.list.hosting', 'service.cloud.list.cloud', 'service.hotel.list.hotel', 'service.email.list.email', 'service.crm.list.crm') ? 'active' : '' }}">
                        <a data-bs-toggle="collapse" href="#service_active">
                            <i class="fas fa-check-circle"></i>
                            <p>Dịch vụ được đăng ký</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse {{ request()->routeIs('domain.index', 'service.hosting.list.hosting', 'service.cloud.list.cloud', 'service.hotel.list.hotel', 'service.email.list.email', 'service.crm.list.crm') ? ' show' : '' }}"
                            id="service_active">
                            <ul class="nav nav-collapse">
                                @if (Auth::user()->role_id == 1)
                                    <li class="{{ request()->routeIs('domain.index') ? 'active' : '' }}">
                                        <a href="{{ route('domain.index') }}">
                                            <span class="sub-item"><span>Tên miền</span></span>
                                        </a>
                                    </li>
                                    <li
                                        class="{{ request()->routeIs('service.hosting.list.hosting') ? 'active' : '' }}">
                                        <a href="{{ route('service.hosting.list.hosting') }}">
                                            <span class="sub-item"><span>Hosting</span></span>
                                        </a>
                                    </li>
                                    <li class="{{ request()->routeIs('service.email.list.email') ? 'active' : '' }}">
                                        <a href="{{ route('service.email.list.email') }}">
                                            <span class="sub-item"><span>Email Server</span></span>
                                        </a>
                                    </li>
                                    <li class="{{ request()->routeIs('service.cloud.list.cloud') ? 'active' : '' }}">
                                        <a href="{{ route('service.cloud.list.cloud') }}">
                                            <span class="sub-item"><span>Cloud</span></span>
                                        </a>
                                    </li>
                                    <li class="{{ request()->routeIs('service.crm.list.crm') ? 'active' : '' }}">
                                        <a href="{{ route('service.crm.list.crm') }}">
                                            <span class="sub-item"><span>CRM</span></span>
                                        </a>
                                    </li>
                                @endif
                                <li class="{{ request()->routeIs('service.hotel.list.hotel') ? 'active' : '' }}">
                                    <a href="{{ route('service.hotel.list.hotel') }}">
                                        <span class="sub-item"><span>Khách sạn</span></span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    {{-- Qr Code tách riêng ra ngoài --}}
                    @if (Auth::user()->role_id == 1)
                        <li class="nav-item {{ request()->routeIs('qrcode.index') ? 'active' : '' }}">
                            <a href="{{ route('qrcode.index') }}">
                                <i class="fas fa-qrcode"></i>
                                <p>Qr Code</p>
                            </a>
                        </li>
                    @endif
                    @if (Auth::user()->role_id == 1)
                        <li class="nav-item {{ request()->routeIs('order.index') ? 'active' : '' }}">
                            <a data-bs-toggle="collapse" href="#order">
                                <i class="fa fa-box"></i>
                                <p>Quản lý đơn hàng</p>
                                <span class="caret"></span>
                            </a>
                            <div class="collapse {{ request()->routeIs('order.index') ? 'show' : '' }}"
                                id="order">
                                <ul class="nav nav-collapse">
                                    <li class="{{ request()->get('status') == 'nopayment' ? 'active' : '' }}">
                                        <a href="{{ route('order.index', ['status' => 'nopayment']) }}">
                                            <span class="sub-item"><span>Đơn chưa thanh toán</span></span>
                                        </a>
                                    </li>
                                    <li class="{{ request()->get('status') == 'payment' ? 'active' : '' }}">
                                        <a href="{{ route('order.index', ['status' => 'payment']) }}">
                                            <span class="sub-item"><span>Đơn đã thanh toán</span></span>
                                        </a>
                                    </li>
                                    <li class="{{ request()->get('status') == 'pending' ? 'active' : '' }}">
                                        <a href="{{ route('order.index', ['status' => 'pending']) }}">
                                            <span class="sub-item"><span>Đơn chờ cấp tài khoản</span></span>
                                        </a>
                                    </li>
                                    <li class="{{ request()->get('status') == 'active' ? 'active' : '' }}">
                                        <a href="{{ route('order.index', ['status' => 'active']) }}">
                                            <span class="sub-item"><span>Đơn đã kích hoạt</span></span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li
                            class="nav-item {{ request()->routeIs('zalo.dashboard', 'zalo.user', 'zalo.oa', 'zalo.messages', 'zalo.transaction') ? 'active' : '' }}">
                            <a href="{{ route('zalo.dashboard') }}">
                                <i class="fas fa-server"></i>
                                <p>Zalo</p>
                                <span class="caret"></span>
                            </a>
                            <div class="collapse {{ request()->routeIs('zalo.dashboard', 'zalo.user', 'zalo.oa', 'zalo.messages', 'zalo.transaction') ? 'show' : '' }}"
                                id="zalo">
                                <ul class="nav nav-collapse">

                                    <li class="{{ request()->routeIs('zalo.user') ? 'active' : '' }}">
                                        <a href="{{ route('zalo.user') }}"><span class="sub-item"><span>Khách
                                                    hàng</span></span></a>
                                    </li>
                                    <li class="{{ request()->routeIs('zalo.oa') ? 'active' : '' }}">
                                        <a href="{{ route('zalo.oa') }}"><span class="sub-item">
                                                <span>Oa</span></span></a>
                                    </li>

                                    <li class="{{ request()->routeIs('zalo.messages') ? 'active' : '' }}">
                                        <a href="{{ route('zalo.messages') }}"><span class="sub-item">
                                                <span>Tin nhắn</span></span></a>
                                    </li>

                                    <li class="{{ request()->routeIs('zalo.transaction') ? 'active' : '' }}">
                                        <a href="{{ route('zalo.transaction') }}"><span class="sub-item">
                                                <span>Lịch sử nạp tiền</span></span></a>
                                    </li>

                                </ul>
                            </div>
                        </li>

                        <li class="nav-item {{ request()->routeIs('token.list') ? 'active' : '' }}">
                            <a href="{{ route('token.list') }}">
                                <i class="fa-solid fa-puzzle-piece"></i>
                                <p>Api Fasthotel</p>
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a href="{{ route('business.index') }}">
                                <i class="fas fa-file-contract"></i>
                                <p>Đăng ký dinh doanh</p>
                            </a>
                        </li>

                        <li class="nav-item ">
                            <a href="">
                                <i class="fa fa-ticket-alt"></i>
                                <p>Ticket</p>
                            </a>
                        </li>
                    @endif
                @else
                    <!-- Đăng ký dịch vụ -->
                    <li
                        class="nav-item {{ request()->routeIs('customer.domain.index', 'customer.hosting.index', 'customer.email.index', 'customer.cloud.index') ? 'active' : '' }}">
                        <a data-bs-toggle="collapse" href="#service_dk">
                            <i class="fas fa-plus-square"></i>
                            <p>Bảng giá dịch vụ</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse {{ request()->routeIs('customer.domain.index', 'customer.hosting.index', 'customer.email.index', 'customer.cloud.index') ? 'show' : '' }}"
                            id="service_dk">
                            <ul class="nav nav-collapse">
                                <li class="{{ request()->routeIs('customer.domain.index') ? 'active' : '' }}">
                                    <a href="{{ route('customer.domain.index') }}"><span class="sub-item">
                                            <span>Tên miền</span></span></a>
                                </li>
                                <li class="{{ request()->routeIs('customer.hosting.index') ? 'active' : '' }}">
                                    <a href="{{ route('customer.hosting.index') }}"><span
                                            class="sub-item"><span>Hosting</span></span></a>
                                </li>
                                <li class="{{ request()->routeIs('customer.email.index') ? 'active' : '' }}">
                                    <a href="{{ route('customer.email.index', ['email_type' => 1]) }}"><span
                                            class="sub-item"><span>Email Server</span></span></a>
                                </li>
                                <li class="{{ request()->routeIs('customer.cloud.index') ? 'active' : '' }}">
                                    <a href="{{ route('customer.cloud.index') }}"><span
                                            class="sub-item"><span>Cloud</span></span></a>
                                </li>

                                {{-- <li class="{{ request()->routeIs('qrcode.create') ? 'active' : '' }}">
                                    <a href="{{ route('qrcode.create') }}"><span class="sub-item"><span>Qr
                                                Code</span></span></a>
                                </li> --}}
                            </ul>
                        </div>
                    </li>

                    <!-- Dịch vụ được đăng ký -->
                    <li class="nav-item {{ request()->routeIs('customer.service.list.service') ? 'active' : '' }}">
                        <a data-bs-toggle="collapse" href="#service_active">
                            <i class="fas fa-check-circle"></i>
                            <p>Dịch vụ được đăng ký</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse {{ request()->routeIs('customer.service.list.service') ? 'show' : '' }}"
                            id="service_active">
                            <ul class="nav nav-collapse">
                                <li class="{{ request()->get('type') == 'domain' ? 'active' : '' }}">
                                    <a href="{{ route('customer.service.list.service', ['type' => 'domain']) }}">
                                        <span class="sub-item"><span>Domain</span></span>
                                    </a>
                                </li>
                                <li class="{{ request()->get('type') == 'hosting' ? 'active' : '' }}">
                                    <a href="{{ route('customer.service.list.service', ['type' => 'hosting']) }}">
                                        <span class="sub-item"><span>Hosting</span></span>
                                    </a>
                                </li>
                                <li class="{{ request()->get('type') == 'email' ? 'active' : '' }}">
                                    <a href="{{ route('customer.service.list.service', ['type' => 'email']) }}">
                                        <span class="sub-item"><span>Email Server</span></span>
                                    </a>
                                </li>
                                <li class="{{ request()->get('type') == 'cloud' ? 'active' : '' }}">
                                    <a href="{{ route('customer.service.list.service', ['type' => 'cloud']) }}">
                                        <span class="sub-item"><span>Cloud</span></span>
                                    </a>
                                </li>

                                <li class="{{ request()->get('type') == 'hotel' ? 'active' : '' }}">
                                    <a href="{{ route('customer.service.list.hotel') }}">
                                        <span class="sub-item"><span>Khách sạn</span></span>
                                    </a>
                                </li>

                                {{-- <li class="{{ request()->routeIs('qrcode.index') ? 'active' : '' }}">
                                    <a href="{{ route('qrcode.index') }}"><span class="sub-item"><span>Qr
                                                Code</span></span></a>
                                </li> --}}
                            </ul>
                        </div>
                    </li>

                    <!-- Quản lý đơn hàng -->
                    <li class="nav-item {{ request()->routeIs('customer.order.index') ? 'active' : '' }}">
                        <a data-bs-toggle="collapse" href="#order">
                            <i class="fas fa-pen-square"></i>
                            <p>Quản lý đơn hàng</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse {{ request()->routeIs('customer.order.index') ? 'show' : '' }}"
                            id="order">
                            <ul class="nav nav-collapse">
                                <li class="{{ request()->get('status') == 'nopayment' ? 'active' : '' }}">
                                    <a href="{{ route('customer.order.index', ['status' => 'nopayment']) }}">
                                        <span class="sub-item"><span>Đơn chưa thanh toán</span></span>
                                    </a>
                                </li>
                                <li class="{{ request()->get('status') == 'payment' ? 'active' : '' }}">
                                    <a href="{{ route('customer.order.index', ['status' => 'payment']) }}">
                                        <span class="sub-item"><span>Đơn đã thanh toán</span></span>
                                    </a>
                                </li>
                                <li class="{{ request()->get('status') == 'active' ? 'active' : '' }}">
                                    <a href="{{ route('customer.order.index', ['status' => 'active']) }}">
                                        <span class="sub-item"><span>Đơn đã kích hoạt</span></span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item ">
                        <a href="{{ route('business.registration') }}">
                            <i class="fas fa-file-contract"></i>
                            <p>Đăng ký dinh doanh</p>
                        </a>
                    </li>

                    <li class="nav-item {{ request()->routeIs('customer.fasthotelApi.api') ? 'active' : '' }}">
                        <a href="{{ route('customer.fasthotelApi.api') }}">
                            <i class="fa-solid fa-puzzle-piece"></i>
                            <p>Api Fasthotel</p>
                        </a>
                    </li>
                @endif

                <!-- Lịch sử giao dịch -->
                @if (Auth::user()->role_id != 3)
                    <li
                        class="nav-item {{ request()->routeIs('history.index', 'transaction.index') ? 'active' : '' }}">
                        <a data-bs-toggle="collapse" href="#history">
                            <i class="fa fa-history"></i>
                            <p>Lịch sử</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse {{ request()->routeIs('history.index', 'transaction.index') ? 'show' : '' }}"
                            id="history">
                            <ul class="nav nav-collapse">
                                <li class="{{ request()->routeIs('history.index') ? 'active' : '' }}">
                                    <a href="{{ route('history.index') }}"><span class="sub-item">
                                            <span>Thanh toán</span></span></a>
                                </li>
                                <li class="{{ request()->routeIs('transaction.index') ? 'active' : '' }}">
                                    <a href="{{ route('transaction.index') }}"><span class="sub-item"><span>Nạp
                                                tiền</span></span></a>
                                </li>



                            </ul>
                        </div>
                    </li>

                    <li class="nav-item {{ request()->routeIs('check.domai') ? 'active' : '' }}">
                        <a href="{{ route('check.domain') }}">
                            <i class="fa-solid fa-search"></i>
                            <p>Kiểm tra tên miền</p>
                        </a>
                    </li>
                @endif

                @if (Auth::user()->role_id == 1)
                    <li
                        class="nav-item {{ request()->routeIs('partners.index', 'partners.create') ? 'active' : '' }}">
                        <a data-bs-toggle="collapse" href="#partners">
                            <i class="fa-solid fa-headset"></i>
                            <p>Chăm sóc khách hàng</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse {{ request()->routeIs('partners.index', 'partners.create') ? 'show' : '' }}"
                            id="partners">
                            <ul class="nav nav-collapse">
                                <li class="{{ request()->routeIs('partners.index') ? 'active' : '' }}">
                                    <a href="{{ route('partners.index') }}"><span class="sub-item">
                                            <span>Danh sách khách hàng</span></span></a>
                                </li>

                            </ul>
                        </div>
                    </li>
                @endif


            </ul>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $(".nav-item > a").click(function(e) {
                // Nếu menu đang mở, thì không làm gì
                if ($(this).next(".collapse").hasClass("show")) {
                    return;
                }

                // Đóng tất cả các menu khác trước khi mở menu mới
                $(".collapse").not($(this).next(".collapse")).removeClass("show");

                // Bỏ class 'active' trên tất cả menu chính
                $(".nav-item").removeClass("active");

                // Thêm class 'active' cho menu vừa được click
                $(this).parent().addClass("active");
            });
        });
    </script>

</div>
