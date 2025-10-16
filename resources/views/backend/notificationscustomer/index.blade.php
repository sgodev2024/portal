@extends('backend.layouts.master')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0"><i class="fas fa-bell"></i> Thông báo</h3>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div id="notificationTableWrapper">
                        @include('backend.notificationscustomer.partials.table', ['notifications' => $notifications])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function () {
        function loadTable(params = {}) {
            const query = new URLSearchParams({
                page: params.page || 1
            });
            const queryString = query.toString();
            fetch(`{{ route('customer.notifications.index') }}?${queryString}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.text())
            .then(html => {
                $('#notificationTableWrapper').html(html);
            });
        }

        // Intercept pagination clicks
        $(document).on('click', '#notificationTableWrapper .pagination a', function(e){
            e.preventDefault();
            const url = new URL($(this).attr('href'));
            const page = url.searchParams.get('page') || 1;
            loadTable({ page });
        });

        // Auto refresh every 30s
        setInterval(function(){
            const currentPageLink = $('#notificationTableWrapper .pagination .active span').text();
            const page = parseInt(currentPageLink || '1', 10) || 1;
            loadTable({ page });
        }, 30000);
    });
</script>
@endpush

@endsection


