<nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
    <div class="container-fluid">
        <nav class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
            <div class="input-group">
            </div>
        </nav>

        <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
            <li class="nav-item topbar-icon dropdown hidden-caret d-flex d-lg-none">
                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button"
                    aria-expanded="false" aria-haspopup="true">
                    <i class="fa fa-search"></i>
                </a>
                <ul class="dropdown-menu dropdown-search animated fadeIn">
                    <form class="navbar-left navbar-form nav-search">
                        <div class="input-group">
                            <input type="text" placeholder="Search ..." class="form-control" />
                        </div>
                    </form>
                </ul>
            </li>
            
            {{-- Language Switcher --}}
            <li class="nav-item" style="display: flex; align-items: center; margin-right: 15px;">
                @include('components.language-switcher')
            </li>
            
            {{-- Google Translate Hidden Element --}}
            <li class="nav-item" style="display: none;">
                <div id="google_translate_element"></div>
            </li>

            {{-- Notifications Dropdown --}}
            <li class="nav-item topbar-icon dropdown hidden-caret">
                <a class="nav-link dropdown-toggle" href="#" id="notificationsDropdown" role="button"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-bell"></i>
                    <span class="notification-counter" style="display: none;">0</span>
                </a>
                <ul class="dropdown-menu notifications-menu animated fadeIn" aria-labelledby="notificationsDropdown">
                    <div class="dropdown-header d-flex justify-content-between align-items-center">
                        <span class="notification-title">Thông báo</span>
                        <a href="#" class="text-muted mark-all-read" style="font-size: 0.8rem;">Đánh dấu đã đọc</a>
                    </div>
                    <div class="notifications-scroll" style="max-height: 300px; overflow-y: auto;">
                        <div class="notifications-list">
                            <div class="text-center p-3 loading-notifications" style="display: none;">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="visually-hidden">Đang tải...</span>
                                </div>
                            </div>
                            <div class="no-notifications text-center p-3 text-muted" style="display: none;">
                                <i class="fas fa-bell-slash mb-2"></i><br>
                                Không có thông báo mới
                            </div>
                        </div>
                    </div>
                    <div class="dropdown-footer text-center p-2 border-top">
                        @php
                            $notificationIndexRoute = match(Auth::user()->role) {
                                1 => route('admin.notifications.index'),
                                2 => route('staff.notifications.index'),
                                3 => route('customer.notifications.index'),
                                default => '#'
                            };
                        @endphp
                        <a href="{{ $notificationIndexRoute }}" class="text-primary">
                            Xem tất cả thông báo
                        </a>
                    </div>
                </ul>
            </li>
            
            <li class="nav-item topbar-user dropdown hidden-caret">
                <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                    <div class="avatar-sm">
                        @if(Auth::user()->avatar)
                            <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar"
                                class="avatar-img rounded-circle" />
                        @else
                            <img src="{{ asset('backend/assets/img/jm_denis.jpg') }}" alt="Default Avatar"
                                class="avatar-img rounded-circle" />
                        @endif
                    </div>
                    <span class="profile-username">
                        <span class="op-7">Hi,</span>
                        <span class="fw-bold">{{ Auth::user()->name }}</span>
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-user animated fadeIn" style="width: 287px">
                    <div class="dropdown-user-scroll scrollbar-outer">
                        <li>
                            <div class="user-box">
                                <div class="avatar-lg">
                                    @if(Auth::user()->avatar)
                                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar"
                                            class="avatar-img rounded" />
                                    @else
                                        <img src="{{ asset('backend/assets/img/jm_denis.jpg') }}" alt="Default Avatar"
                                            class="avatar-img rounded" />
                                    @endif
                                </div>
                                <div class="u-text">
                                    <h4>{{ Auth::user()->name }}</h4>
                                    <p class="text-muted">{{ Auth::user()->email }}</p>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                            @if(Auth::user()->role == 3)
                                <a class="dropdown-item" href="{{ route('customer.profile.edit') }}">
                                    <i class="fas fa-user-cog"></i> Cập nhật thông tin
                                </a>
                            @endif
                            <a class="dropdown-item" href="{{ route('logout') }}">
                                <i class="fas fa-sign-out-alt"></i> Đăng xuất
                            </a>
                        </li>
                    </div>
                </ul>
            </li>
        </ul>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Notifications functionality - CHỈ 1 LẦN
$(document).ready(function() {
    function updateNotificationCount() {
        $.get('/notifications/unread-count', function(response) {
            const count = response.count;
            const counter = $('.notification-counter');
            if (count > 0) {
                counter.text(count).show();
            } else {
                counter.hide();
            }
        }).fail(function() {
            console.error('Failed to load notification count');
        });
    }

    function loadNotifications() {
        const list = $('.notifications-list');
        const loading = $('.loading-notifications');
        const noNotifications = $('.no-notifications');
        
        list.find('.notification-item').remove();
        loading.show();
        noNotifications.hide();

        $.get('/notifications/recent', function(response) {
            loading.hide();
            if (response.notifications && response.notifications.length > 0) {
                response.notifications.forEach(function(notification) {
                    const notificationHtml = `
                        <div class="dropdown-item notification-item ${!notification.is_read ? 'unread' : ''}" style="position: relative;">
                            <a href="javascript:void(0);" 
                               data-notification-id="${notification.id}" 
                               data-notification-link="${notification.link}"
                               class="notification-link d-flex align-items-start"
                               style="text-decoration: none; color: inherit; flex: 1;">
                                <div class="notification-icon">
                                    <i class="fas fa-bell"></i>
                                </div>
                                <div class="notification-content ms-3">
                                    <p class="notification-text mb-1">${notification.title}</p>
                                    <small class="text-muted">
                                        ${notification.created_at}
                                        ${!notification.is_read ? '<span class="unread-marker"></span>' : ''}
                                    </small>
                                </div>
                            </a>
                            <button class="btn-delete-notification" data-notification-id="${notification.id}" title="Xóa thông báo">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                    list.append(notificationHtml);
                });
            } else {
                noNotifications.show();
            }
        }).fail(function() {
            loading.hide();
            noNotifications.show();
        });
    }

    // Initial load
    updateNotificationCount();
    
    // Load notifications when dropdown is opened
    $('#notificationsDropdown').on('show.bs.dropdown', function () {
        loadNotifications();
        // Ẩn dấu đỏ ngay khi mở dropdown
        $('.notification-counter').hide();
    });

    // Handle notification link click
    $(document).on('click', '.notification-link', function(e) {
        e.preventDefault();
        const $this = $(this);
        const notificationId = $this.data('notification-id');
        const notificationLink = $this.data('notification-link');
        
        // Đánh dấu thông báo là đã đọc
        if (notificationId) {
            $.post('/notifications/' + notificationId + '/mark-read', function(response) {
                if (response.success) {
                    // Cập nhật UI ngay lập tức
                    $this.closest('.notification-item').removeClass('unread');
                    $this.find('.unread-marker').remove();
                    
                    // Chuyển hướng nếu có link
                    if (notificationLink && notificationLink !== '#') {
                        window.location.href = notificationLink;
                    }
                }
            }).fail(function() {
                // Vẫn chuyển hướng nếu có lỗi
                if (notificationLink && notificationLink !== '#') {
                    window.location.href = notificationLink;
                }
            });
        } else if (notificationLink && notificationLink !== '#') {
            window.location.href = notificationLink;
        }
    });

    // Handle delete notification button
    $(document).on('click', '.btn-delete-notification', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const $btn = $(this);
        const notificationId = $btn.data('notification-id');
        const $notificationItem = $btn.closest('.notification-item');
        
        if (!notificationId) return;
        
        // Xóa trực tiếp không cần confirm
        $.ajax({
            url: '/notifications/' + notificationId + '/delete',
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Xóa khỏi UI với animation
                    $notificationItem.fadeOut(300, function() {
                        $(this).remove();
                        
                        // Kiểm tra còn thông báo nào không
                        if ($('.notification-item').length === 0) {
                            $('.no-notifications').show();
                        }
                    });
                    
                    // Cập nhật counter
                    updateNotificationCount();
                }
            },
            error: function() {
                console.error('Có lỗi xảy ra khi xóa thông báo');
            }
        });
    });

    // Mark all as read
    $('.mark-all-read').click(function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        $.post('/notifications/mark-all-read', function(response) {
            if (response.success) {
                updateNotificationCount();
                loadNotifications();
            }
        }).fail(function() {
            console.error('Failed to mark notifications as read');
        });
    });

    // Poll for new notifications every 30 seconds
    setInterval(updateNotificationCount, 30000);
});
</script>

<style>
    /* Notification Counter */
    .notification-counter {
        position: absolute;
        top: 0;
        right: 0;
        padding: 0.25rem 0.5rem;
        border-radius: 10px;
        background-color: #dc3545;
        color: white;
        font-size: 0.75rem;
        font-weight: bold;
        transform: translate(25%, -25%);
    }

    /* Notification Dropdown */
    .notifications-menu {
        width: 320px;
        padding: 0;
    }

    .dropdown-header {
        padding: 0.75rem 1rem;
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    .notification-title {
        font-weight: 600;
        color: #495057;
    }

    .notification-item {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #dee2e6;
        transition: background-color 0.2s;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .notification-item:hover {
        background-color: #f8f9fa;
        text-decoration: none;
    }

    .notification-item.unread {
        background-color: #f0f7ff;
    }

    .notification-link {
        flex: 1;
        padding-right: 10px;
    }

    .btn-delete-notification {
        background: none;
        border: none;
        color: #6c757d;
        padding: 0.25rem 0.5rem;
        cursor: pointer;
        transition: all 0.2s;
        opacity: 0;
        font-size: 1rem;
        line-height: 1;
    }

    .notification-item:hover .btn-delete-notification {
        opacity: 1;
    }

    .btn-delete-notification:hover {
        color: #dc3545;
        transform: scale(1.2);
    }

    .btn-delete-notification:active {
        transform: scale(0.95);
    }

    .notification-icon {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #e9ecef;
        border-radius: 50%;
        color: #6c757d;
    }

    .notification-text {
        color: #495057;
        font-size: 0.875rem;
        margin: 0;
        line-height: 1.4;
    }

    .unread-marker {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #0d6efd;
        margin-left: 5px;
    }

    /* Scrollbar styling */
    .notifications-scroll::-webkit-scrollbar {
        width: 6px;
    }

    .notifications-scroll::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .notifications-scroll::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }

    .notifications-scroll::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
</style>