@php
    use App\Models\Ticket;
    use App\Models\Notification as NotificationModel;

    $userRole = auth()->user()->role;
    $dashboardRoute = match($userRole) {
        1 => 'admin.dashboard',
        2 => 'staff.dashboard',
        default => 'customer.dashboard',
    };

    $openTickets = in_array($userRole, [1, 2]) ? Ticket::where('status', 'open')->count() : 0;
@endphp

<style>
    .sidebar-wrapper {
        background-color: #005aa1 !important;
    }

    .nav-collapse {
        padding: 0 0 0 13px;
        margin-bottom: 0 !important;
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

    .badge {
        font-size: 11px;
        padding: 3px 7px;
        border-radius: 10px;
        margin-left: 5px;
    }
</style>

<div class="sidebar" data-background-color="dark" id="sidebar">
    {{-- Logo Header --}}
    <div class="sidebar-logo">
        <div class="logo-header" data-background-color="white">
            <a target="_blank" href="https://sgomedia.vn/" class="logo">
                <img style="width: 80%;"
                    src="{{ $company?->company_logo ? asset('storage/' . $company->company_logo) : asset('backend/SGO VIET NAM (1000 x 375 px).png') }}"
                    alt="navbar brand" class="navbar-brand img-fluid" />
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
    </div>

    {{-- Sidebar Content --}}
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <ul class="nav nav-secondary">

                {{-- Dashboard --}}
                <li class="nav-item {{ request()->routeIs($dashboardRoute) ? 'active' : '' }}">
                    <a href="{{ route($dashboardRoute) }}">
                        <i class="fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>


                {{-- ============ ADMIN MENUS ============ --}}
                @if ($userRole == 1)

                    {{-- Cấu hình --}}
                    @php
                        $isConfigActive = request()->routeIs('company.index')
                            || request()->routeIs('admin.staffs.*')
                            || request()->routeIs('admin.email_templates.*')
                            || request()->routeIs('admin.stmt.*');
                    @endphp
                    <li class="nav-item {{ $isConfigActive ? 'active' : '' }}">
                        <a data-bs-toggle="collapse" href="#config">
                            <i class="fas fa-cogs"></i>
                            <p>Cấu hình</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse {{ $isConfigActive ? 'show' : '' }}" id="config">
                            <ul class="nav nav-collapse">
                                <li class="{{ request()->routeIs('company.index') ? 'active' : '' }}">
                                    <a href="{{ route('company.index') }}">
                                        <span class="sub-item">Thông tin công ty</span>
                                    </a>
                                </li>
                                <li class="{{ request()->routeIs('admin.staffs.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.staffs.index') }}">
                                        <span class="sub-item">Quản lý nhân viên</span>
                                    </a>
                                </li>
                                <li class="{{ request()->routeIs('admin.email_templates.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.email_templates.index') }}">
                                        <span class="sub-item">Quản lý Email</span>
                                    </a>
                                </li>
                                <li class="{{ request()->routeIs('admin.stmt.index') ? 'active' : '' }}">
                                    <a href="{{ route('admin.stmt.index') }}">
                                        <span class="sub-item">Cấu hình STMT</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    {{-- Thông báo Admin --}}
                    <li class="nav-item {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.notifications.index') }}">
                            <i class="fas fa-bell"></i>
                            <p>Thông báo</p>
                        </a>
                    </li>

                    {{-- Quản lý khách hàng --}}
                    @php
                        $isCustomerManageActive = request()->routeIs('customers.*')
                            || request()->routeIs('admin.customer-groups.*')
                            || request()->routeIs('admin.group-staff.*');
                    @endphp
                    <li class="nav-item {{ $isCustomerManageActive ? 'active' : '' }}">
                        <a data-bs-toggle="collapse" href="#customerMenu">
                            <i class="fas fa-users"></i>
                            <p>Quản lý khách hàng</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse {{ $isCustomerManageActive ? 'show' : '' }}" id="customerMenu">
                            <ul class="nav nav-collapse">
                                <li class="{{ request()->routeIs('customers.*') ? 'active' : '' }}">
                                    <a href="{{ route('customers.index') }}">
                                        <span class="sub-item">Danh sách khách hàng</span>
                                    </a>
                                </li>
                                @if (Route::has('admin.customer-groups.index'))
                                    <li class="{{ request()->routeIs('admin.customer-groups.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.customer-groups.index') }}">
                                            <span class="sub-item">Nhóm khách hàng</span>
                                        </a>
                                    </li>
                                @endif
                                @if (Route::has('admin.group-staff.index'))
                                    <li class="{{ request()->routeIs('admin.group-staff.*') ? 'active' : '' }}">
                                        <a href="{{ route('admin.group-staff.index') }}">
                                            <span class="sub-item">Nhân viên - Nhóm</span>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </li>

                @endif

                {{-- ============ STAFF MENUS ============ --}}
                @if ($userRole == 2)

                    {{-- Thông báo Staff --}}
                    <li class="nav-item {{ request()->routeIs('staff.notifications.*') ? 'active' : '' }}">
                        <a href="{{ route('staff.notifications.index') }}">
                            <i class="fas fa-bell"></i>
                            <p>Thông báo</p>
                            <span class="badge bg-danger d-none" id="badge-notif-count-staff">0</span>
                        </a>
                    </li>

                @endif

                {{-- ============ CUSTOMER MENUS ============ --}}
                @if ($userRole == 3)

                    {{-- Thông báo Customer --}}
                    <li class="nav-item {{ request()->routeIs('customer.notifications.*') ? 'active' : '' }}">
                        <a href="{{ route('customer.notifications.index') }}">
                            <i class="fas fa-bell"></i>
                            <p>Thông báo</p>
                            <span class="badge bg-danger d-none" id="badge-notif-count-customer">0</span>
                        </a>
                    </li>

                @endif

                {{-- ============ TICKETS (Admin/Staff) ============ --}}
                @if (in_array($userRole, [1, 2]))
                    <li class="nav-item {{ request()->routeIs('admin.tickets.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.tickets.index') }}">
                            <i class="fas fa-ticket-alt"></i>
                            <p>Tickets</p>
                            @if ($openTickets > 0)
                                <span class="badge bg-danger">{{ $openTickets }}</span>
                            @endif
                        </a>
                    </li>
                @endif

                {{-- ============ TICKETS (Customer) ============ --}}
                @if ($userRole == 3)
                    <li class="nav-item {{ request()->routeIs('customer.tickets.*') ? 'active' : '' }}">
                        <a href="{{ route('customer.tickets.index') }}">
                            <i class="fas fa-ticket-alt"></i>
                            <p>Tickets</p>
                        </a>
                    </li>
                @endif

                {{-- ============ LIVE CHAT ============ --}}
                @if ($userRole == 1)
                    <li class="nav-item {{ request()->routeIs('chat.*') ? 'active' : '' }}">
                        <a href="{{ route('chat.index') }}">
                            <i class="fas fa-comments"></i>
                            <p>Chat hỗ trợ</p>
                        </a>
                    </li>
                @elseif ($userRole == 2)
                    <li class="nav-item {{ request()->routeIs('staff.chats.*') ? 'active' : '' }}">
                        <a href="{{ route('staff.chats.index') }}">
                            <i class="fas fa-headset"></i>
                            <p>Chat khách hàng</p>
                        </a>
                    </li>

                @endif

                {{-- ============ QUẢN LÝ FILE (Admin/Staff) ============ --}}
                @if (in_array($userRole, [1]))
                    <li class="nav-item {{ request()->routeIs('admin.files.*') ? 'active' : '' }}">
                        <a data-bs-toggle="collapse" href="#files">
                            <i class="fas fa-folder-open"></i>
                            <p>Quản lý File</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse {{ request()->routeIs('admin.files.*') ? 'show' : '' }}" id="files">
                            <ul class="nav nav-collapse">
                                <li class="{{ request()->routeIs('admin.files.index') ? 'active' : '' }}">
                                    <a href="{{ route('admin.files.index') }}">
                                        <span class="sub-item">Tất cả File</span>
                                    </a>
                                </li>
                                @if (Route::has('admin.files.reports'))
                                    <li class="{{ request()->routeIs('admin.files.reports') ? 'active' : '' }}">
                                        <a href="{{ route('admin.files.reports') }}">
                                            <span class="sub-item">File Báo cáo</span>
                                        </a>
                                    </li>
                                @endif
                                @if (Route::has('admin.files.templates'))
                                    <li class="{{ request()->routeIs('admin.files.templates') ? 'active' : '' }}">
                                        <a href="{{ route('admin.files.templates') }}">
                                            <span class="sub-item">Biểu mẫu</span>
                                        </a>
                                    </li>
                                @endif

                            </ul>
                        </div>
                    </li>
                @endif

                {{-- ============ BÁO CÁO & FILE (Customer) ============ --}}
                @if ($userRole == 3)
                    @php
                        // Vérifier quelles routes existent - basé sur le fichier routes
                        $hasReportsRoute = Route::has('customer.files.reports');
                        $hasTemplatesRoute = Route::has('customer.files.templates');
                        $hasFileManagerRoute = Route::has('customer.file_manager.index');
                        $hasDownloadsRoute = Route::has('customer.files.my_downloads');

                        // Déterminer si le menu doit être actif
                        $isCustomerFilesActive = request()->routeIs('customer.files.reports')
                            || request()->routeIs('customer.files.show_report')
                            || request()->routeIs('customer.files.templates')
                            || request()->routeIs('customer.files.my_downloads')
                            || request()->routeIs('customer.file_manager.*');

                        // Afficher le menu si au moins une route existe
                        $showCustomerFilesMenu = $hasReportsRoute || $hasTemplatesRoute || $hasFileManagerRoute || $hasDownloadsRoute;
                    @endphp

                    @if ($showCustomerFilesMenu)
                        <li class="nav-item {{ $isCustomerFilesActive ? 'active' : '' }}">
                            <a data-bs-toggle="collapse" href="#customerFiles">
                                <i class="fas fa-folder-open"></i>
                                <p>Báo cáo & File</p>
                                <span class="caret"></span>
                            </a>
                            <div class="collapse {{ $isCustomerFilesActive ? 'show' : '' }}" id="customerFiles">
                                <ul class="nav nav-collapse">
                                    @if ($hasReportsRoute)
                                        <li class="{{ request()->routeIs('customer.files.reports') || request()->routeIs('customer.files.show_report') ? 'active' : '' }}">
                                            <a href="{{ route('customer.files.reports') }}">
                                                <span class="sub-item">File báo cáo</span>
                                            </a>
                                        </li>
                                    @endif

                                    @if ($hasTemplatesRoute)
                                        <li class="{{ request()->routeIs('customer.files.templates') ? 'active' : '' }}">
                                            <a href="{{ route('customer.files.templates') }}">
                                                <span class="sub-item">Biểu mẫu</span>
                                            </a>
                                        </li>
                                    @endif

                                    @if ($hasFileManagerRoute)
                                        <li class="{{ request()->routeIs('customer.file_manager.*') ? 'active' : '' }}">
                                            <a href="{{ route('customer.file_manager.index') }}">
                                                <span class="sub-item">File Manager</span>
                                            </a>
                                        </li>
                                    @endif

                                    @if ($hasDownloadsRoute)
                                        <li class="{{ request()->routeIs('customer.files.my_downloads') ? 'active' : '' }}">
                                            <a href="{{ route('customer.files.my_downloads') }}">
                                                <span class="sub-item">Lịch sử tải</span>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </li>
                    @endif
                @endif

            </ul>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Gestion de l'état actif des menus au chargement
    const lastMenu = localStorage.getItem('activeMenu');
    if (lastMenu) {
        const $collapse = $('#' + lastMenu);
        if ($collapse.length) {
            $collapse.addClass('show');
            $collapse.closest('.nav-item').addClass('active');
            $("a[href='#" + lastMenu + "']").attr('aria-expanded', 'true');
        }
    }

    // Garder le menu ouvert si un sous-élément est actif
    $('.nav-collapse li.active').each(function() {
        const $collapse = $(this).closest('.collapse');
        if ($collapse.length) {
            const menuId = $collapse.attr('id');
            $collapse.addClass('show');
            $collapse.closest('.nav-item').addClass('active');
            $("a[href='#" + menuId + "']").attr('aria-expanded', 'true');
            localStorage.setItem('activeMenu', menuId);
        }
    });

    // Gestion du clic sur menu parent (avec collapse)
    $(".nav-item > a[data-bs-toggle='collapse']").on('click', function(e) {
        const targetId = $(this).attr('href').replace('#', '');
        const $collapse = $('#' + targetId);

        // Ne pas fermer si on clique sur un menu déjà ouvert avec un élément actif
        if ($collapse.hasClass('show') && $collapse.find('li.active').length === 0) {
            localStorage.removeItem('activeMenu');
        } else if (!$collapse.hasClass('show')) {
            localStorage.setItem('activeMenu', targetId);
        }
    });

    // Gestion du clic sur sous-menu
    $(".nav-collapse li a").on('click', function(e) {
        // Marquer le sous-menu comme actif
        $('.nav-collapse li').removeClass('active');
        $(this).parent('li').addClass('active');

        // Garder le menu parent ouvert
        const $collapse = $(this).closest('.collapse');
        if ($collapse.length) {
            const menuId = $collapse.attr('id');
            localStorage.setItem('activeMenu', menuId);
        }
    });

    // Gestion du clic sur menu simple (sans collapse)
    $(".nav-item > a:not([data-bs-toggle='collapse'])").on('click', function() {
        // Retirer l'état actif des autres menus
        $('.nav-item').removeClass('active');
        $(this).parent('.nav-item').addClass('active');

        // Fermer tous les menus déroulants
        $('.collapse.show').removeClass('show');
        $('.nav-item > a[data-bs-toggle="collapse"]').attr('aria-expanded', 'false');

        // Nettoyer le localStorage
        localStorage.removeItem('activeMenu');
    });

    // Mise à jour du badge des notifications non lues
    function updateUnreadBadge() {
        $.ajax({
            url: "{{ route('notifications.unread_count') }}",
            method: 'GET',
            dataType: 'json',
            success: function(res) {
                const count = res.count || 0;
                const role = {{ $userRole }};

                if (role === 2) {
                    const $badge = $('#badge-notif-count-staff');
                    if (count > 0) {
                        $badge.text(count).removeClass('d-none');
                    } else {
                        $badge.addClass('d-none');
                    }
                } else if (role === 3) {
                    const $badge = $('#badge-notif-count-customer');
                    if (count > 0) {
                        $badge.text(count).removeClass('d-none');
                    } else {
                        $badge.addClass('d-none');
                    }
                }
            },
            error: function() {
                console.error('Erreur lors de la récupération du nombre de notifications');
            }
        });
    }

    // Mise à jour initiale et périodique
    updateUnreadBadge();
    setInterval(updateUnreadBadge, 15000); // Toutes les 15 secondes
});
</script>
