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
        #categoryTable, #originTable{
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

        <!-- Custom template | don't include it in your project! -->
        <!-- End Custom template -->
    </div>

    @include('backend.layouts.partials.scripts')
</body>

</html>
