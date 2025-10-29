@extends('backend.layouts.master')

@section('content')
    <div class="container-fluid py-4 notranslate">
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
                        <h3 class="mt-2">{{ $tickets->where('status', 'new')->count() }}</h3>
                        <p class="text-muted mb-0">Chưa xử lý</p>
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
                        <h3 class="mt-2">{{ $tickets->total() }}</h3>
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
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Trạng thái</label>
                        <select class="form-select" name="status">
                            <option value="">Tất cả</option>
                            <option value="new" {{ $status == 'new' ? 'selected' : '' }}>Chưa xử lý</option>
                            <option value="in_progress" {{ $status == 'in_progress' ? 'selected' : '' }}>Đang xử lý</option>
                            <option value="responded" {{ $status == 'responded' ? 'selected' : '' }}>Đã phản hồi</option>
                            <option value="closed" {{ $status == 'closed' ? 'selected' : '' }}>Đã đóng</option>
                        </select>
                    </div>

                    <!-- Tìm kiếm -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tìm kiếm</label>
                        <input type="text" class="form-control" name="search" placeholder="Tìm theo tiêu đề, khách hàng"
                            value="{{ $search }}">
                    </div>

                    <!-- Nhân viên (Chỉ admin thấy) -->
                    @if (Auth::user()->role == 1)
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Nhân viên</label>
                            <select class="form-select" name="assigned_to">
                                <option value="">Tất cả</option>
                                <option value="unassigned" {{ request('assigned_to') == 'unassigned' ? 'selected' : '' }}>
                                    Chưa gán</option>
                                @foreach ($staffList as $staff)
                                    <option value="{{ $staff->id }}"
                                        {{ request('assigned_to') == $staff->id ? 'selected' : '' }}>
                                        {{ $staff->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <!-- Nút tìm kiếm & reset -->
                    <div class="col-md-3 d-flex gap-2">
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
                                    <th width="5%">STT</th>
                                    <th width="18%">Khách hàng</th>
                                    <th width="25%">Tiêu đề</th>
                                    @if (Auth::user()->role == 1)
                                        <th width="15%">Nhân viên</th>
                                    @endif
                                    <th width="12%">Trạng thái</th>
                                    <th width="12%">Ngày tạo</th>
                                    <th width="13%" class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tickets as $index => $ticket)
                                    <tr>
                                        <td class="text-center">{{ $tickets->firstItem() + $index }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle bg-primary text-white me-2">
                                                    {{ strtoupper(substr($ticket->user->name ?? 'U', 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $ticket->user->name ?? 'N/A' }}</div>
                                                    <small class="text-muted d-block">ID: {{ $ticket->user->account_id ?? 'N/A' }}</small>
                                                    <small class="text-muted">{{ $ticket->user->email ?? '' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ Str::limit($ticket->subject, 40) }}</div>
                                            <small class="text-muted">
                                                <i class="bi bi-chat-dots"></i> {{ $ticket->messages->count() }} tin nhắn
                                            </small>
                                        </td>

                                        <!-- Cột Nhân viên (chỉ admin thấy) -->
                                        @if (Auth::user()->role == 1)
                                            <td>
                                                @if ($ticket->assignedStaff)
                                                    <div class="d-flex align-items-center">
                                                        <span class="small">{{ $ticket->assignedStaff->name }}</span>
                                                    </div>
                                                @else
                                                    <form method="POST" action="{{ route('admin.tickets.assign', $ticket->id) }}" class="d-flex gap-2 align-items-center">
                                                        @csrf
                                                        <select name="assigned_to" class="form-select form-select-sm" required>
                                                            <option value="">-- Chọn nhân viên --</option>
                                                            @foreach($staffList as $staff)
                                                                <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <button type="submit" class="btn btn-sm btn-outline-primary">Gán</button>
                                                    </form>
                                                @endif
                                            </td>
                                        @endif

                                        <td>
                                            @switch($ticket->status)
                                                @case('new')
                                                    <span class="badge bg-info">
                                                        <i class="bi bi-clock"></i> Chưa xử lý
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
                                            <small>
                                                <i class="bi bi-calendar3"></i>
                                                {{ $ticket->created_at->format('d/m/Y') }}<br>
                                                <i class="bi bi-clock"></i> {{ $ticket->created_at->format('H:i') }}
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <!-- Xem chi tiết -->
                                                <a href="{{ route('admin.tickets.show', $ticket->id) }}"
                                                    class="btn btn-sm btn-outline-info" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>


                                                <!-- Claim ticket (nhân viên tự nhận) -->
                                                @if (Auth::user()->role == 2 && !$ticket->assignedStaff)
                                                    <form action="{{ route('admin.tickets.claim', $ticket->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-primary" title="Nhận xử lý ticket này">
                                                            <i class="fas fa-user-plus"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                <!-- Đóng ticket -->
                                                @if ($ticket->status != 'closed')
                                                    <form action="{{ route('admin.tickets.close', $ticket->id) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Bạn có chắc muốn đóng ticket này?')">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                            title="Đóng ticket">
                                                             <i class="fa-solid fa-x"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-4 px-3 pb-3">
                        <div class="text-muted small">
                            Hiển thị <strong>{{ $tickets->firstItem() ?? 0 }}</strong> - <strong>{{ $tickets->lastItem() ?? 0 }}</strong> 
                            trong tổng số <strong>{{ $tickets->total() }}</strong> tickets
                        </div>
                        <div>
                            {{ $tickets->links('pagination::bootstrap-5') }}
                        </div>
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
            font-size: 1rem;
        }

        .table tbody tr {
            transition: background-color 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .btn-sm {
            padding: 0.35rem 0.65rem;
            font-size: 0.8rem;
        }

        .btn-group .btn {
            border-radius: 0;
        }

        .btn-group .btn:first-child {
            border-top-left-radius: 0.25rem;
            border-bottom-left-radius: 0.25rem;
        }

        .btn-group .btn:last-child {
            border-top-right-radius: 0.25rem;
            border-bottom-right-radius: 0.25rem;
        }

        /* Pagination Styling */
        .pagination {
            margin-bottom: 0;
        }

        .pagination .page-link {
            color: #0d6efd;
            border: 1px solid #dee2e6;
            padding: 0.5rem 0.75rem;
            transition: all 0.2s ease;
        }

        .pagination .page-link:hover {
            background-color: #e9ecef;
            border-color: #dee2e6;
        }

        .pagination .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
            font-weight: 600;
        }

        .pagination .page-item.disabled .page-link {
            color: #6c757d;
            background-color: #fff;
            border-color: #dee2e6;
        }
    </style>

    <script>
        // Hiển thị modal gán ticket
        function showAssignModal(ticketId, ticketSubject) {
            document.getElementById('ticketSubject').value = '#' + ticketId + ' - ' + ticketSubject;
            document.getElementById('assignForm').action = '{{ route('admin.tickets.assign', ':id') }}'.replace(':id',
                ticketId);

            const modal = new bootstrap.Modal(document.getElementById('assignModal'));
            modal.show();
        }
    </script>
@endsection
