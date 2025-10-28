@extends('backend.layouts.master')

@section('content')
    <div class="container py-4 notranslate">
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

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Filter Section --}}
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('customer.tickets.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Tìm kiếm</label>
                        <input type="text" name="search" class="form-control" placeholder="ID, Tiêu đề..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted">Danh mục</label>
                        <select name="category" class="form-select">
                            <option value="">Tất cả</option>
                            @foreach(\App\Models\Ticket::getCategories() as $key => $label)
                                <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted">Trạng thái</label>
                        <select name="status" class="form-select">
                            <option value="">Tất cả</option>
                            @foreach(\App\Models\Ticket::getStatuses() as $key => $label)
                                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted">Ưu tiên</label>
                        <select name="priority" class="form-select">
                            <option value="">Tất cả</option>
                            @foreach(\App\Models\Ticket::getPriorities() as $key => $label)
                                <option value="{{ $key }}" {{ request('priority') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-search"></i> Lọc
                        </button>
                        <a href="{{ route('customer.tickets.index') }}" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-arrows-rotate"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                @if ($tickets->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">STT</th>
                                    <th width="13%">Ngày tạo</th>
                                    <th width="20%">Tiêu đề</th>
                                    <th width="12%">Danh mục</th>
                                    <th width="10%">Ưu tiên</th>
                                    <th width="12%">Trạng thái</th>
                                    <th width="15%">Phụ trách</th>
                                    <th width="10%" class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tickets as $index => $ticket)
                                    <tr>
                                        <td class="text-center">{{ $tickets->firstItem() + $index }}</td>
                                      
                                          <td>
                                            <small>
                                                <i class="bi bi-calendar3"></i>
                                                {{ $ticket->created_at->format('d/m/Y H:i') }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ Str::limit($ticket->subject, 40) }}</div>
                                            <small class="text-muted">
                                                <i class="bi bi-chat-dots"></i> {{ $ticket->messages->count() }} tin nhắn
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge {{ $ticket->category_badge }}">
                                                {{ $ticket->category_label }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $ticket->priority_badge }}">
                                                @if($ticket->priority === 'urgent')
                                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                                @elseif($ticket->priority === 'high')
                                                    <i class="bi bi-arrow-up-circle-fill"></i>
                                                @endif
                                                {{ $ticket->priority_label }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $ticket->status_badge }}">
                                                @switch($ticket->status)
                                                    @case('new')
                                                        <i class="bi bi-clock"></i>
                                                        @break
                                                    @case('in_progress')
                                                        <i class="bi bi-hourglass-split"></i>
                                                        @break
                                                    @case('completed')
                                                        <i class="bi bi-check-circle"></i>
                                                        @break
                                                    @case('closed')
                                                        <i class="bi bi-x-circle"></i>
                                                        @break
                                                @endswitch
                                                {{ $ticket->status_label }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($ticket->assignedStaff)
                                                <div class="d-flex align-items-center">
                                                    
                                                    <small class="fw-semibold">{{ $ticket->assignedStaff->name }}</small>
                                                </div>
                                            @else
                                                <span class="text-muted small">Chưa gán</span>
                                            @endif
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

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted small">
                            Hiển thị {{ $tickets->firstItem() }} - {{ $tickets->lastItem() }} trong tổng số {{ $tickets->total() }} ticket
                        </div>
                        {{ $tickets->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-inbox display-1 text-muted"></i>
                        <h5 class="mt-3 text-muted">Chưa có ticket nào</h5>
                        <p class="text-muted">
                            @if(request()->hasAny(['search', 'category', 'status', 'priority']))
                                Không tìm thấy ticket phù hợp với bộ lọc.
                                <a href="{{ route('customer.tickets.index') }}" class="text-decoration-none">Xóa bộ lọc</a>
                            @else
                                Tạo ticket mới để nhận hỗ trợ từ đội ngũ của chúng tôi
                            @endif
                        </p>
                        @if(!request()->hasAny(['search', 'category', 'status', 'priority']))
                            <a href="{{ route('customer.tickets.create') }}" class="btn btn-primary mt-2">
                                <i class="bi bi-plus-circle"></i> Tạo Ticket đầu tiên
                            </a>
                        @endif
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
        }

        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
        }
    </style>
@endsection