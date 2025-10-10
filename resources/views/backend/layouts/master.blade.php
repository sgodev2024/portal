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
            {{-- Icon chat nổi --}}
            <div id="chat-icon"
                style="position: fixed; bottom: 25px; right: 25px; background-color: #0084ff;
                   border-radius: 50%; width: 60px; height: 60px; text-align: center;
                   line-height: 60px; cursor: pointer; color: white; z-index: 9999;
                   box-shadow: 0 3px 10px rgba(0,0,0,0.2); font-size: 28px;">
                💬
            </div>

            {{-- Hộp chat (iframe hiển thị nội dung chat) --}}
            <div id="chat-box"
                style="display:none; position: fixed; bottom: 100px; right: 25px; width: 350px;
                   height: 450px; background: white; border: 1px solid #ccc; border-radius: 10px;
                   overflow: hidden; z-index: 10000; box-shadow: 0 3px 12px rgba(0,0,0,0.3);">
                <iframe src="{{ route('customer.chatcustomer.index') }}" style="width:100%;height:100%;border:none;"
                    id="chat-iframe">
                </iframe>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const chatIcon = document.getElementById('chat-icon');
                const chatBox = document.getElementById('chat-box');
                const iframe = document.getElementById('chat-iframe');

                chatIcon.addEventListener('click', function() {
                    if (chatBox.style.display === 'none') {
                        chatBox.style.display = 'block';
                        iframe.contentWindow.location.reload();
                    } else {
                        chatBox.style.display = 'none';
                    }
                });
            });
        </script>
    @endif


    @include('backend.layouts.partials.scripts')
</body>

</html>
