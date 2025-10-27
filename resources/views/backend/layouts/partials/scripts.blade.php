<!-- Fonts and icons -->
<script src="{{ asset('backend/assets/js/plugin/webfont/webfont.min.js') }}"></script>
<script>
    WebFont.load({
        google: {
            families: ["Public Sans:300,400,500,600,700"]
        },
        custom: {
            families: [
                "Font Awesome 5 Solid",
                "Font Awesome 5 Regular",
                "Font Awesome 5 Brands",
                "simple-line-icons",
            ],
            urls: ["{{ asset('backend/assets/css/fonts.min.css') }}"],
        },
        active: function() {
            sessionStorage.fonts = true;
        },
    });
</script>
<!--   Core JS Files   -->

<script src="{{ asset('backend/assets/js/core/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('backend/assets/js/core/popper.min.js') }}"></script>
<script src="{{ asset('backend/assets/js/core/bootstrap.min.js') }}"></script>


<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


<!-- jQuery Scrollbar -->
<script src="{{ asset('backend/assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>

<!-- Chart JS -->
<script src="{{ asset('backend/assets/js/plugin/chart.js/chart.min.js') }}"></script>

<!-- jQuery Sparkline -->
<script src="{{ asset('backend/assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js') }}"></script>

<!-- Chart Circle -->
<script src="{{ asset('backend/assets/js/plugin/chart-circle/circles.min.js') }}"></script>

<!-- Datatables -->
<script src="{{ asset('backend/assets/js/plugin/datatables/datatables.min.js') }}"></script>

<!-- Bootstrap Notify -->
<script src="{{ asset('backend/assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>

<!-- jQuery Vector Maps -->
<script src="{{ asset('backend/assets/js/plugin/jsvectormap/jsvectormap.min.js') }}"></script>
<script src="{{ asset('backend/assets/js/plugin/jsvectormap/world.js') }}"></script>

<!-- Sweet Alert -->
{{-- <script src="{{ asset('backend/assets/js/plugin/sweetalert/sweetalert.min.js') }}"></script> --}}

<!-- Kaiadmin JS -->
<script src="{{ asset('backend/assets/js/kaiadmin.min.js') }}"></script>

<!-- Kaiadmin DEMO methods, don't include it in your project! -->
<script src="{{ asset('backend/assets/js/setting-demo.js') }}"></script>
{{-- <script src="{{ asset('backend/assets/js/demo.js') }}"></script> --}}
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="//cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
<script src="{{ asset('helper.js') }}"></script>
@include('backend/includes/alert')

<script>
    // Xử lý đánh dấu tickets đã đọc khi click vào menu
    document.addEventListener('DOMContentLoaded', function() {
        const ticketsMenuLink = document.querySelector('a[data-has-unread]');
        if (ticketsMenuLink) {
            ticketsMenuLink.addEventListener('click', function(e) {
                const hasUnread = this.getAttribute('data-has-unread') === 'true';
                
                if (hasUnread) {
                    e.preventDefault(); // Tạm dừng điều hướng
                    
                    // Gọi AJAX để mark all as read
                    fetch('{{ route('admin.tickets.mark_all_read') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Ẩn badge sau khi mark as read
                        const badge = document.getElementById('ticketsBadge');
                        if (badge) {
                            badge.style.display = 'none';
                        }
                        
                        // Cập nhật attribute để không hiện lại
                        this.setAttribute('data-has-unread', 'false');
                        
                        // Sau đó điều hướng đến trang tickets
                        window.location.href = '{{ route('admin.tickets.index') }}';
                    })
                    .catch(error => {
                        console.error('Error marking tickets as read:', error);
                        // Nếu có lỗi, vẫn cho phép điều hướng
                        window.location.href = '{{ route('admin.tickets.index') }}';
                    });
                }
            });
        }
    });
</script>

@stack('scripts')
