{{-- @extends('backend.layouts.master')

@section('content')
<style>
    .total_ticket {
    display: inline-block;
    background-color: red;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    text-align: center;
    line-height: 20px;
    font-size: 14px;
    position: relative;
    top: -10px;
    left: 5px;
}
</style>


@endsection --}}
@php
    $user = Auth::user();
    $isAdmin = $user->role == 1;
    $isStaff = $user->role == 2;
    $isCustomer = $user->role == 3;
@endphp

@extends('backend.layouts.master')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-2">
                @if($isAdmin)
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard Quản trị
                @elseif($isStaff)
                    <i class="bi bi-briefcase me-2"></i>Dashboard Nhân viên
                @else
                    <i class="bi bi-house-door me-2"></i>Chào mừng, {{ $user->name }}!
                @endif
            </h2>
            <p class="text-muted">
                @if($isAdmin)
                    Tổng quan hệ thống và quản lý toàn bộ
                @elseif($isStaff)
                    Quản lý công việc và tickets được gán
                @else
                    Đây là tổng quan về tài khoản của bạn
                @endif
            </p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        @if($isAdmin)
            <!-- Admin Stats: Users -->
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 card-hover">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-people-fill display-4 text-primary"></i>
                        </div>
                        <h3 class="fw-bold mb-2">{{ $total_customers ?? 0 }}</h3>
                        <p class="text-muted mb-0">Khách hàng</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 card-hover">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-person-badge-fill display-4 text-success"></i>
                        </div>
                        <h3 class="fw-bold mb-2">{{ $total_staff ?? 0 }}</h3>
                        <p class="text-muted mb-0">Nhân viên</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Ticket Stats (All roles) -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 card-hover">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-ticket-perforated display-4 text-primary"></i>
                    </div>
                    <h3 class="fw-bold mb-2">{{ $ticket_stats['total'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">
                        @if($isCustomer) Tổng Tickets @else Tổng tickets @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 card-hover">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-clock-history display-4 text-info"></i>
                    </div>
                    <h3 class="fw-bold mb-2">{{ $ticket_stats['new'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">Mới tạo</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 card-hover">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-hourglass-split display-4 text-warning"></i>
                    </div>
                    <h3 class="fw-bold mb-2">{{ $ticket_stats['in_progress'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">Đang xử lý</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 card-hover">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-check-circle display-4 text-success"></i>
                    </div>
                    <h3 class="fw-bold mb-2">{{ $ticket_stats['responded'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">Đã phản hồi</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 card-hover">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-x-circle display-4 text-secondary"></i>
                    </div>
                    <h3 class="fw-bold mb-2">{{ $ticket_stats['closed'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">Đã đóng</p>
                </div>
            </div>
        </div>

        @if($isAdmin)
            <!-- Admin: Unassigned tickets -->
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100 card-hover">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-person-x display-4 text-danger"></i>
                        </div>
                        <h3 class="fw-bold mb-2">{{ $ticket_stats['unassigned'] ?? 0 }}</h3>
                        <p class="text-muted mb-0">Chưa gán</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="row g-4">
        <!-- Recent Tickets -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-ticket-perforated me-2"></i>
                            @if($isAdmin || $isStaff)
                                Tickets gần đây
                            @else
                                Tickets của tôi
                            @endif
                            @if(($ticket_stats['new'] ?? 0) + ($ticket_stats['in_progress'] ?? 0) > 0)
                                <span class="total_ticket">{{ ($ticket_stats['new'] ?? 0) + ($ticket_stats['in_progress'] ?? 0) }}</span>
                            @endif
                        </h5>
                        <a href="{{ $isCustomer ? route('customer.tickets.index') : route('admin.tickets.index') }}" 
                           class="btn btn-sm btn-outline-primary">
                            Xem tất cả <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @php
                        $tickets = $isAdmin || $isStaff ? ($recent_tickets ?? collect()) : ($my_tickets ?? collect());
                    @endphp

                    @if($tickets->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="8%">#ID</th>
                                        @if($isAdmin || $isStaff)
                                            <th width="20%">Khách hàng</th>
                                        @endif
                                        <th width="{{ $isCustomer ? '50%' : '30%' }}">Tiêu đề</th>
                                        @if($isAdmin)
                                            <th width="15%">Nhân viên</th>
                                        @endif
                                        <th width="15%">Trạng thái</th>
                                        <th width="{{ $isAdmin ? '12%' : '17%' }}">Ngày tạo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tickets as $ticket)
                                    <tr>
                                        <td class="fw-bold text-primary">#{{ $ticket->id }}</td>
                                        
                                        @if($isAdmin || $isStaff)
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle bg-primary text-white me-2" style="width: 30px; height: 30px; font-size: 0.75rem;">
                                                    {{ strtoupper(substr($ticket->user->name ?? 'U', 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-semibold small">{{ $ticket->user->name ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        @endif

                                        <td>
                                            <a href="{{ $isCustomer ? route('customer.tickets.show', $ticket->id) : route('admin.tickets.show', $ticket->id) }}" 
                                               class="text-decoration-none text-dark fw-semibold">
                                                {{ Str::limit($ticket->subject, 40) }}
                                            </a>
                                            <br>
                                            <small class="text-muted">
                                                <i class="bi bi-chat-dots"></i> {{ $ticket->messages->count() ?? 0 }} tin nhắn
                                            </small>
                                        </td>

                                        @if($isAdmin)
                                        <td>
                                            @if($ticket->assignedStaff)
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle bg-success text-white me-1" style="width: 26px; height: 26px; font-size: 0.7rem;">
                                                        {{ strtoupper(substr($ticket->assignedStaff->name, 0, 1)) }}
                                                    </div>
                                                    <span class="small">{{ Str::limit($ticket->assignedStaff->name, 15) }}</span>
                                                </div>
                                            @else
                                                <span class="badge bg-secondary small">Chưa gán</span>
                                            @endif
                                        </td>
                                        @endif

                                        <td>
                                            @switch($ticket->status)
                                                @case('new')
                                                    <span class="badge bg-info">
                                                        <i class="bi bi-clock"></i> Mới tạo
                                                    </span>
                                                    @break
                                                @case('in_progress')
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="bi bi-hourglass-split"></i> Đang xử lý
                                                    </span>
                                                    @break
                                                @case('responded')
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle"></i> Đã phản hồi
                                                    </span>
                                                    @break
                                                @case('closed')
                                                    <span class="badge bg-secondary">
                                                        <i class="bi bi-x-circle"></i> Đã đóng
                                                    </span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar3"></i> {{ $ticket->created_at->format('d/m/Y') }}
                                                <br>
                                                <i class="bi bi-clock"></i> {{ $ticket->created_at->format('H:i') }}
                                            </small>
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
                            @if($isCustomer)
                                <a href="{{ route('customer.tickets.create') }}" class="btn btn-primary mt-3">
                                    <i class="bi bi-plus-circle me-2"></i>Tạo ticket mới
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-lightning-charge me-2"></i>Thao tác nhanh
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        @if($isCustomer)
                            <a href="{{ route('customer.tickets.create') }}" class="btn btn-outline-primary btn-lg">
                                <i class="bi bi-plus-circle me-2"></i>Tạo ticket mới
                            </a>
                            <a href="{{ route('customer.tickets.index') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-list-ul me-2"></i>Xem tickets
                            </a>
                            
                        @else
                            <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-primary btn-lg">
                                <i class="bi bi-ticket-perforated me-2"></i>Quản lý Tickets
                            </a>
                            @if($isAdmin)
                                <a href="{{ route('customers.index') }}" class="btn btn-outline-success btn-lg">
                                    <i class="bi bi-people me-2"></i>Quản lý Khách hàng
                                </a>
                                <a href="{{ route('admin.staffs.index') }}" class="btn btn-outline-info btn-lg">
                                    <i class="bi bi-person-badge me-2"></i>Quản lý Nhân viên
                                </a>
                                 <a href="{{ route('chat.index') }}" class="btn btn-outline-warning btn-lg">
                                <i class="bi bi-chat-left-dots me-2"></i>Hỗ trợ Chat
                            </a>
                            @endif
                           
                        @endif
                    </div>
                </div>
            </div>

            <!-- Account/System Info -->
           
        </div>
    </div>
</div>

<style>
    .total_ticket {
        display: inline-block;
        background-color: #dc3545;
        color: white;
        border-radius: 50%;
        min-width: 24px;
        height: 24px;
        text-align: center;
        line-height: 24px;
        font-size: 12px;
        font-weight: 600;
        padding: 0 6px;
        position: relative;
        top: -2px;
        margin-left: 8px;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
    }

    .card-hover {
        transition: all 0.3s ease;
    }

    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .table tbody tr {
        transition: background-color 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .avatar-circle {
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        flex-shrink: 0;
    }
</style>
@endsection
