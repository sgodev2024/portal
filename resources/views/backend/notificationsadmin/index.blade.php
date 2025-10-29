@extends('backend.layouts.master')

@section('title', 'Danh sách thông báo')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Danh sách thông báo</h4>
            <a href="{{ route('admin.notifications.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tạo thông báo mới
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="50" class="text-center">ID</th>
                                <th width="250">Tiêu đề</th>
                                <th width="120">Gửi cho</th>
                                <th width="150">Người tạo</th>
                                <th width="150" class="text-center">Đã đọc</th>
                                <th width="130" class="text-center">Trạng thái</th>
                                <th width="150" class="text-center">Ngày gửi</th>
                                <th width="120" class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($notifications as $index => $n)
                                <tr>
                                    <td class="text-center">
                                        <strong>{{ $notifications->firstItem() + $index }}</strong>
                                    </td>
                                    <td>
                                        <strong>{{ $n->title }}</strong>
                                    </td>
                                    <td>
                                        @php
                                            $roleLabels = [
                                                'staff' => 'Nhân viên',
                                                'user' => 'Khách hàng',
                                                'all' => 'Tất cả',
                                            ];
                                            $roleColors = [
                                                'staff' => 'warning',
                                                'user' => 'info',
                                                'all' => 'success',
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $roleColors[$n->target_role] ?? 'secondary' }}">
                                            {{ $roleLabels[$n->target_role] ?? strtoupper($n->target_role) }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $n->creator->name ?? '-' }}
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $totalUsers = $n->userNotifications->count();
                                            $readUsers = $n->userNotifications->where('is_read', true)->count();
                                        @endphp

                                        @if ($totalUsers > 0)
                                            <span class="badge bg-success">{{ $readUsers }}</span>
                                            <span class="text-muted">/ {{ $totalUsers }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        @if ($n->is_sent)
                                            <span class="badge bg-success">Đã gửi</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Chưa gửi</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($n->sent_at)
                                            {{ \Carbon\Carbon::parse($n->sent_at)->format('d/m/Y H:i') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.notifications.show', $n->id) }}"
                                            class="btn btn-sm btn-info" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.notifications.destroy', $n->id) }}"
                                            class="d-inline">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger"
                                                onclick="return confirm('Bạn có chắc muốn xóa thông báo này?\n\nThao tác này không thể hoàn tác!')"
                                                title="Xóa">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block opacity-50"></i>
                                        <h6 class="text-muted">Chưa có thông báo nào</h6>
                                        <p class="small mb-0">Nhấn "Tạo thông báo mới" để bắt đầu</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4 px-3 pb-3 d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Hiển thị <strong>{{ $notifications->firstItem() ?? 0 }}</strong> - <strong>{{ $notifications->lastItem() ?? 0 }}</strong>
                        trong tổng số <strong>{{ $notifications->total() }}</strong> thông báo
                    </div>
                    <div>
                        {{ $notifications->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
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
@endpush
