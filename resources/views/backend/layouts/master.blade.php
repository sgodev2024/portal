<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>{{ isset($title) ? $title : 'Dashboard' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <meta name="zalo-platform-site-verification" content="MiwQ0wRY7m1OxBe-XC8UOLx6hZooi7vZDJGr" />
    @include('backend.layouts.partials.styles')

    <style>
        table tr td:last-child {
            text-align: center;
        }

        #categoryTable,
        #originTable {
            width: 100% !important;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        @include('backend.layouts.partials.sidebar')
        <!-- End Sidebar -->

        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <!-- Logo Header -->
                    <div class="logo-header" data-background-color="white">
                        <a href="index.html" class="logo">
                            <img src="{{ asset('backend/SGO VIET NAM (1000 x 375 px).png') }}" alt="navbar brand"
                                class="navbar-brand" height="20" />
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

                <!-- Navbar Header -->
                @include('backend.layouts.partials.navbar')
                <!-- End Navbar -->
            </div>

            <div class="container">
                <div class="page-inner">
                    @yield('content')
                </div>
            </div>

            <footer class="footer">
                @include('backend.layouts.partials.footer')
            </footer>
        </div>
    </div>
    @if (Auth::check() && Auth::user()->role == 3)
        <div id="chat-widget">
            {{-- Floating Chat Icon --}}
            <div id="chat-icon"
                style="position: fixed; bottom: 20px; right: 20px; cursor: pointer; background-color: #0084ff; border-radius: 50%; padding: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.3);">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 512 512"
                    fill="white">
                    <path
                        d="M256 32C132.3 32 32 125.1 32 240c0 63.5 30.3 120.3 78.9 158.7V480l72.2-39.9c23.3 6.4 48.3 9.9 73.9 9.9 123.7 0 224-93.1 224-208S379.7 32 256 32zm27.8 274.3l-69.4-74.1-133.1 74.1 145.9-155.9 69.5 74.1 133.1-74.1-146 155.9z" />
                </svg>
                <span class="chat-badge" id="unreadBadge" style="display: none;">0</span>
            </div>


            {{-- Chat Box --}}
            <div id="chat-box">
                <div class="chat-box-header">
                    <div class="header-content">
                        <div class="avatar-support">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="white">
                                <path
                                    d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4M12,6.5A2,2 0 0,1 14,8.5A2,2 0 0,1 12,10.5A2,2 0 0,1 10,8.5A2,2 0 0,1 12,6.5M12,11.5C14.67,11.5 17.5,12.84 17.5,13.5V14.5H6.5V13.5C6.5,12.84 9.33,11.5 12,11.5Z" />
                            </svg>
                        </div>
                        <div class="header-info">
                            <h4>Hỗ trợ khách hàng</h4>
                            <span class="status">● Online</span>
                        </div>
                    </div>
                    <button id="close-chat" class="close-btn" type="button">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="white">
                            <path
                                d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z" />
                        </svg>
                    </button>
                </div>
                <iframe src="{{ route('customer.chatcustomer.index') }}" id="chat-iframe"></iframe>
            </div>
        </div>

        <style>
            #chat-widget {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            }

            /* Floating Chat Icon */
            #chat-icon {
                position: fixed;
                bottom: 24px;
                right: 24px;
                width: 64px;
                height: 64px;
                background: linear-gradient(135deg, #0084ff 0%, #00a8ff 100%);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                z-index: 9999;
                box-shadow: 0 8px 24px rgba(0, 132, 255, 0.35);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            #chat-icon:hover {
                transform: scale(1.1);
                box-shadow: 0 12px 32px rgba(0, 132, 255, 0.5);
            }

            #chat-icon:active {
                transform: scale(0.95);
            }

            /* Notification Badge */
            .chat-badge {
                position: absolute;
                top: -4px;
                right: -4px;
                min-width: 24px;
                height: 24px;
                padding: 0 6px;
                background: linear-gradient(135deg, #ff3b30 0%, #ff6b6b 100%);
                color: white;
                font-size: 11px;
                font-weight: 700;
                border-radius: 50%;
                border: 3px solid white;
                display: flex;
                align-items: center;
                justify-content: center;
                animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
            }

            @keyframes pulse {

                0%,
                100% {
                    transform: scale(1);
                    opacity: 1;
                }

                50% {
                    transform: scale(1.15);
                    opacity: 0.9;
                }
            }

            /* Chat Box Container */
            #chat-box {
                display: none;
                position: fixed;
                bottom: 104px;
                right: 24px;
                width: 400px;
                height: 600px;
                background: white;
                border-radius: 20px;
                overflow: hidden;
                z-index: 10000;
                box-shadow: 0 12px 48px rgba(0, 0, 0, 0.18);
                flex-direction: column;
            }

            /* Animation mở */
            #chat-box.opening {
                display: flex;
                animation: slideUp 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            }

            /* Animation đóng */
            #chat-box.closing {
                display: flex;
                animation: slideDown 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            }

            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateY(20px) scale(0.95);
                }

                to {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }

            @keyframes slideDown {
                from {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }

                to {
                    opacity: 0;
                    transform: translateY(20px) scale(0.95);
                }
            }

            /* Chat Box Header */
            .chat-box-header {
                background: linear-gradient(135deg, #0084ff 0%, #00a8ff 100%);
                padding: 18px 20px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                color: white;
                box-shadow: 0 4px 12px rgba(0, 132, 255, 0.25);
                flex-shrink: 0;
            }

            .header-content {
                display: flex;
                align-items: center;
                gap: 14px;
            }

            .avatar-support {
                width: 44px;
                height: 44px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.25);
                display: flex;
                align-items: center;
                justify-content: center;
                backdrop-filter: blur(12px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            }

            .header-info h4 {
                margin: 0;
                font-size: 17px;
                font-weight: 600;
                letter-spacing: -0.2px;
            }

            .header-info .status {
                font-size: 13px;
                opacity: 0.95;
                font-weight: 500;
            }

            .close-btn {
                background: rgba(255, 255, 255, 0.2);
                border: none;
                border-radius: 50%;
                width: 36px;
                height: 36px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
                backdrop-filter: blur(8px);
            }

            .close-btn:hover {
                background: rgba(255, 255, 255, 0.35);
                transform: rotate(90deg);
            }

            .close-btn:active {
                transform: rotate(90deg) scale(0.9);
            }

            /* Chat Iframe */
            #chat-iframe {
                width: 100%;
                flex: 1;
                border: none;
                background: #f8f9fa;
            }

            /* Mobile Responsive */
            @media (max-width: 768px) {
                #chat-box {
                    width: calc(100% - 16px);
                    height: calc(100vh - 16px);
                    bottom: 8px;
                    right: 8px;
                    border-radius: 16px;
                }

                #chat-icon {
                    bottom: 16px;
                    right: 16px;
                    width: 56px;
                    height: 56px;
                }

                #chat-icon svg {
                    width: 24px;
                    height: 24px;
                }

                .chat-badge {
                    width: 20px;
                    height: 20px;
                    min-width: 20px;
                    font-size: 10px;
                    border-width: 2px;
                }
            }

            @media (max-width: 480px) {
                #chat-box {
                    width: 100%;
                    height: 100vh;
                    bottom: 0;
                    right: 0;
                    border-radius: 0;
                }
            }
        </style>

        <script>
            (function() {
                'use strict';

                document.addEventListener('DOMContentLoaded', function() {
                    // Elements
                    const chatIcon = document.getElementById('chat-icon');
                    const chatBox = document.getElementById('chat-box');
                    const closeBtn = document.getElementById('close-chat');
                    const iframe = document.getElementById('chat-iframe');
                    const badge = document.getElementById('unreadBadge');

                    if (!chatIcon || !chatBox || !closeBtn || !iframe) {
                        console.error('Chat widget elements not found');
                        return;
                    }

                    let isOpen = false;

                    // Open chat box
                    function openChatBox() {
                        if (isOpen) return;
                        isOpen = true;

                        // Remove any existing classes
                        chatBox.classList.remove('closing');

                        // Show and animate
                        chatBox.style.display = 'flex';
                        chatBox.classList.add('opening');

                        // Hide badge
                        if (badge) {
                            badge.style.display = 'none';
                        }

                        // Reload iframe
                        try {
                            iframe.contentWindow.location.reload();
                        } catch (e) {
                            console.log('Iframe reload prevented by browser policy');
                        }

                        // Remove opening class after animation
                        setTimeout(function() {
                            chatBox.classList.remove('opening');
                        }, 300);
                    }

                    // Close chat box
                    function closeChatBox() {
                        if (!isOpen) return;
                        isOpen = false;

                        // Remove opening class if exists
                        chatBox.classList.remove('opening');

                        // Add closing animation
                        chatBox.classList.add('closing');

                        // Hide after animation completes
                        setTimeout(function() {
                            chatBox.style.display = 'none';
                            chatBox.classList.remove('closing');
                        }, 300);
                    }

                    // Toggle chat box
                    chatIcon.addEventListener('click', function(e) {
                        e.stopPropagation();
                        if (isOpen) {
                            closeChatBox();
                        } else {
                            openChatBox();
                        }
                    });

                    // Close button
                    closeBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        closeChatBox();
                    });

                    // Close on outside click
                    document.addEventListener('click', function(e) {
                        if (isOpen &&
                            !chatBox.contains(e.target) &&
                            !chatIcon.contains(e.target)) {
                            closeChatBox();
                        }
                    });

                    // Prevent closing when clicking inside
                    chatBox.addEventListener('click', function(e) {
                        e.stopPropagation();
                    });

                    // ESC key to close
                    document.addEventListener('keydown', function(e) {
                        if (e.key === 'Escape' && isOpen) {
                            closeChatBox();
                        }
                    });
                });
            })();
        </script>
    @endif

    @include('backend.layouts.partials.scripts')
</body>

</html>
