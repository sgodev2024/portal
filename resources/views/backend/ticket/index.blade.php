@extends('backend.layouts.master')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-md-12">
                <h2 class="mb-0">Quản lý Tickets</h2>
                <p class="text-muted">Danh sách tất cả yêu cầu hỗ trợ từ khách hàng</p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-info shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-clock-history display-4 text-info"></i>
                        <h3 class="mt-2">{{ $tickets->where('status', 'open')->count() }}</h3>
                        <p class="text-muted mb-0">Chờ xử lý</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-warning shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-hourglass-split display-4 text-warning"></i>
                        <h3 class="mt-2">{{ $tickets->where('status', 'in_progress')->count() }}</h3>
                        <p class="text-muted mb-0">Đang xử lý</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-success shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-check-circle display-4 text-success"></i>
                        <h3 class="mt-2">{{ $tickets->where('status', 'closed')->count() }}</h3>
                        <p class="text-muted mb-0">Đã đóng</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-primary shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-ticket-perforated display-4 text-primary"></i>
                        <h3 class="mt-2">{{ $tickets->count() }}</h3>
                        <p class="text-muted mb-0">Tổng tickets</p>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Filters -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <!-- Trạng thái -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Trạng thái</label>
                        <select class="form-select" name="status">
                            <option value="">Tất cả</option>
                            <option value="open" {{ $status == 'open' ? 'selected' : '' }}>Chờ xử lý</option>
                            <option value="in_progress" {{ $status == 'in_progress' ? 'selected' : '' }}>Đang xử lý</option>
                            <option value="closed" {{ $status == 'closed' ? 'selected' : '' }}>Đã đóng</option>
                        </select>
                    </div>

                    <!-- Tìm kiếm -->
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Tìm kiếm</label>
                        <input type="text" class="form-control" name="search" placeholder="Tìm theo tiêu đề, khách hàng"
                            value="{{ $search }}">
                    </div>

                    <!-- Nút tìm kiếm & reset -->
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-search me-1"></i> Tìm kiếm
                        </button>
                        <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-secondary flex-grow-1">
                            <i class="bi bi-x-circle me-1"></i> Reset
                        </a>
                    </div>
                </form>


            </div>
        </div>

        <!-- Tickets Table -->
        <div class="card shadow-sm">
            <div class="card-body">
                @if ($tickets->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>

                                    <th width="22%">Khách hàng</th>
                                    <th width="35%">Tiêu đề</th>
                                    <th width="12%">Trạng thái</th>
                                    <th width="13%">Ngày tạo</th>
                                    <th width="15%" class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tickets as $ticket)
                                    <tr>

                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle bg-primary text-white me-2">
                                                    {{ strtoupper(substr($ticket->user->name ?? 'U', 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $ticket->user->name ?? 'N/A' }}</div>
                                                    <small class="text-muted">{{ $ticket->user->email ?? '' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="fw-semibold">{{ $ticket->subject }}</td>
                                        <td>
                                            @switch($ticket->status)
                                                @case('open')
                                                    <span class="badge bg-info">
                                                        <i class="bi bi-clock"></i> Chờ xử lý
                                                    </span>
                                                @break

                                                @case('in_progress')
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="bi bi-hourglass-split"></i> Đang xử lý
                                                    </span>
                                                @break

                                                @case('closed')
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle"></i> Đã đóng
                                                    </span>
                                                @break
                                            @endswitch
                                        </td>
                                        <td>
                                            <small>
                                                <i class="bi bi-calendar3"></i>
                                                {{ $ticket->created_at->format('d/m/Y') }}<br>
                                                <i class="bi bi-clock"></i> {{ $ticket->created_at->format('H:i') }}
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            <!-- Xem chi tiết -->
                                            <a href="{{ route('admin.tickets.show', $ticket->id) }}"
                                                class="btn btn-sm btn-outline-primary me-1" title="Xem chi tiết">
                                                <i class="bi bi-eye"></i>
                                                <span class="d-none d-md-inline"> Xem</span>
                                            </a>

                                            <!-- Đóng ticket -->
                                            @if ($ticket->status != 'closed')
                                                <form action="{{ route('admin.tickets.close', $ticket->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Bạn có chắc muốn đóng ticket này?')">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-outline-success"
                                                        title="Đóng ticket">
                                                        <i class="bi bi-check-circle"></i>
                                                        <span class="d-none d-md-inline"> Đóng</span>
                                                    </button>
                                                </form>
                                            @endif
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
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .avatar-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .table tbody tr {
            transition: none;
            /* Bỏ hiệu ứng translate khi hover */
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* Nút nhỏ, responsive, icon + chữ */
        .btn-sm {
            padding: 0.35rem 0.65rem;
            font-size: 0.8rem;
        }
    </style>
@endsection
