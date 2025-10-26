<div class="table-responsive">
<table class="table table-hover align-middle mb-0">
    <thead class="table-light">
        <tr>
            <th width="5%">STT</th>
            <th>Thời gian</th>
            <th>Tiêu đề</th>
            <th>Nội dung</th>
            <th>Người gửi</th>
            <th>Trạng thái</th>
            <th width="8%">Chi tiết</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($notifications as $index => $item)
            @php
                $userNotify = $item->userNotifications->first();
                $isRead = $userNotify && $userNotify->is_read;
            @endphp
            <tr class="{{ $isRead ? '' : 'fw-bold' }}">
                <td class="text-center">{{ ($notifications->currentPage() - 1) * $notifications->perPage() + $loop->iteration }}</td>
                <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $item->title }}</td>
                <td>{{ Str::limit($item->content, 80) }}</td>
                <td>{{ $item->creator->name ?? 'Hệ thống' }}</td>
                <td>
                    @if ($isRead)
                        <span class="badge bg-success">Đã đọc</span>
                    @else
                        <span class="badge bg-warning text-dark">Chưa đọc</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('customer.notifications.show', $item->id) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye"></i>
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>
    </table>
</div>

@if($notifications->hasPages())
    <div class="pagination-wrapper bg-white border-top p-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div class="text-muted small mb-2 mb-md-0">
                Hiển thị {{ $notifications->firstItem() }} - {{ $notifications->lastItem() }}
                trong tổng số {{ $notifications->total() }} thông báo
            </div>
            <div>
                {{ $notifications->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endif


