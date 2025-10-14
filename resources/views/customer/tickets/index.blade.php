@extends('backend.layouts.master')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0">Danh sách Ticket hỗ trợ</h2>
            <p class="text-muted">Quản lý các yêu cầu hỗ trợ của bạn</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('customer.tickets.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tạo Ticket mới
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            @if($tickets->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="10%">ID</th>
                                <th width="35%">Tiêu đề</th>
                                <th width="20%">Trạng thái</th>
                                <th width="20%">Ngày tạo</th>
                                <th width="15%" class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tickets as $ticket)
                            <tr>
                                <td class="fw-bold text-primary">#{{ $ticket->id }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $ticket->subject }}</div>
                                    <small class="text-muted">
                                        {{-- {{ Str::limit($ticket->description, 60) }} --}}
                                    </small>
                                </td>
                                <td>
                                    @switch($ticket->status)
                                        @case('open')
                                            <span class="badge bg-info">
                                                <i class="bi bi-clock"></i> Chờ xử lý
                                            </span>
                                            @break
                                        @case('in_progress')
                                            <span class="badge bg-warning">
                                                <i class="bi bi-hourglass-split"></i> Đang xử lý
                                            </span>
                                            @break
                                        @case('closed')
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle"></i> Đã đóng
                                            </span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ $ticket->status }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    <small>
                                        <i class="bi bi-calendar3"></i>
                                        {{ $ticket->created_at->format('d/m/Y H:i') }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('customer.tickets.show', $ticket->id) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Xem
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <h5 class="mt-3 text-muted">Chưa có ticket nào</h5>
                    <p class="text-muted">Tạo ticket mới để nhận hỗ trợ từ đội ngũ của chúng tôi</p>
                    <a href="{{ route('customer.tickets.create') }}" class="btn btn-primary mt-2">
                        <i class="bi bi-plus-circle"></i> Tạo Ticket đầu tiên
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .table tbody tr {
        transition: all 0.2s;
    }
    .table tbody tr:hover {
        background-color: #f8f9fa;
        /* transform: translateX(2px); */
    }
</style>
@endsection