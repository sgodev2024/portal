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
