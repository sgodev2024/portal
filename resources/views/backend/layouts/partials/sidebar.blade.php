@php
    use App\Models\Ticket;
    use App\Models\Notification as NotificationModel;
@endphp

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
                    if (auth()->user()->role == 1) {
                        $dashboardRoute = 'admin.dashboard';
                    } elseif (auth()->user()->role == 2) {
                        $dashboardRoute = 'staff.dashboard';
                    } else {
                        $dashboardRoute = 'customer.dashboard';
                    }
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
                                <li class="{{ request()->routeIs('admin.staffs.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.staffs.index') }}">
                                        <span class="sub-item"><span>Quản lý nhân viên</span></span>
                                    </a>
                                </li>

                                <li class="{{ request()->routeIs('admin.email_templates.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.email_templates.index') }}">
                                        <span class="sub-item"><span>Quản lý Email</span></span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.notifications.index') }}">
                            <i class="fas fa-bell"></i>
                            <p>Thông báo</p>
                        </a>
                    </li>
                @endif
                {{-- Thông báo cho nhân viên --}}
                @if (Auth::user()->role == 2)
                    <li class="nav-item {{ request()->routeIs('staff.notifications.*') ? 'active' : '' }}">
                        <a href="{{ route('staff.notifications.index') }}">
                            <i class="fas fa-bell"></i>
                            <p>Thông báo</p>
                            <span class="badge bg-danger d-none" id="badge-notif-count-staff">0</span>
                        </a>
                    </li>
                @endif
                @if (in_array(Auth::user()->role, [1, 2]))
                    <li
                        class="nav-item
        {{ Auth::user()->role == 1 && request()->routeIs('admin.tickets.*') ? 'active' : '' }}
        {{ Auth::user()->role == 2 && request()->routeIs('staff.tickets.*') ? 'active' : '' }}
    ">
                        <a href="{{ route('admin.tickets.index') }}">
                            <i class="fas fa-ticket-alt"></i>
                            <p>Tickets</p>
                            @php
                                $openTickets = Ticket::where('status', 'open')->count();
                            @endphp
                            @if ($openTickets > 0)
                                <span class="badge bg-danger">{{ $openTickets }}</span>
                            @endif
                        </a>
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
                    @endif

                </li>
                @if (Auth::user()->role == 3)
                    <li class="nav-item {{ request()->routeIs('customer.notifications.*') ? 'active' : '' }}">
                        <a href="{{ route('customer.notifications.index') }}">
                            <i class="fas fa-bell"></i>
                            <p>Thông báo</p>
                            <span class="badge bg-danger d-none" id="badge-notif-count-customer">0</span>
                        </a>
                    </li>
                @endif
                <li
                    class="nav-item

    {{ Auth::user()->role == 3 && request()->routeIs('customer.tickets.*') ? 'active' : '' }}
">
                    @if (Auth::user()->role == 3)
                        <a href="{{ route('customer.tickets.index') }}">
                            <i class="fas fa-ticket-alt"></i>
                            <p>Tickets</p>
                        </a>
                    @endif

                </li>

                @if (in_array(Auth::user()->role, [1, 2]))
                    <li class="nav-item {{ request()->routeIs('customer.*') ? 'active' : '' }}">
                        <a href="{{ route('customers.index') }}">
                            <i class="fas fa-users"></i>
                            <p>Quản lý khách hàng</p>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            const lastMenu = localStorage.getItem('activeMenu');
            if (lastMenu) {
                const $collapse = $('#' + lastMenu);
                $collapse.addClass('show');
                $collapse.closest('.nav-item').addClass('active');
                $("a[data-bs-target='#" + lastMenu + "']").attr('aria-expanded', 'true');
            }

            // Xử lý khi click menu cha
            $(".nav-item > a[data-bs-toggle='collapse']").on('click', function() {
                var targetId = $(this).attr('data-bs-target').replace('#', '');
                var $collapse = $('#' + targetId);
                var $parentItem = $(this).parent('.nav-item');

                if ($collapse.hasClass('show')) {
                    localStorage.removeItem('activeMenu');
                } else {
                    localStorage.setItem('activeMenu', targetId);
                }
            });

            // Khi click menu con
            $(".nav-collapse li a").on('click', function() {
                $('.nav-collapse li').removeClass('active');
                $(this).parent('li').addClass('active');
            });
            $(".nav-item > a:not([data-bs-toggle='collapse'])").on('click', function() {
                localStorage.removeItem('activeMenu');
                $('.collapse.show').removeClass('show');
                $('.nav-item > a[data-bs-toggle="collapse"]').attr('aria-expanded', 'false');
                $('.nav-item').removeClass('active');
                $(this).parent('.nav-item').addClass('active');
            });
            // Poll unread notifications count for staff/customer
            function updateUnreadBadge() {
                $.ajax({
                    url: "{{ route('notifications.unread_count') }}",
                    method: 'GET',
                    dataType: 'json',
                    success: function(res) {
                        var count = res.count || 0;
                        var role = {{ (int) Auth::user()->role }};
                        if (role === 2) {
                            var $badge = $('#badge-notif-count-staff');
                            if (count > 0) {
                                $badge.text(count).removeClass('d-none');
                            } else {
                                $badge.addClass('d-none');
                            }
                        } else if (role === 3) {
                            var $badgeC = $('#badge-notif-count-customer');
                            if (count > 0) {
                                $badgeC.text(count).removeClass('d-none');
                            } else {
                                $badgeC.addClass('d-none');
                            }
                        }
                    }
                });
            }
            updateUnreadBadge();
            setInterval(updateUnreadBadge, 15000);
        });
    </script>

</div>
