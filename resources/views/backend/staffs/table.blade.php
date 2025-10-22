{{-- resources/views/backend/staffs/table.blade.php --}}
<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th class="text-center" style="width: 50px;">
                    <input type="checkbox" class="form-check-input" id="checkAll">
                </th>
                <th class="text-center" style="width: 60px;">STT</th>
                <th style="min-width: 180px;">HỌ TÊN</th>
                <th style="min-width: 150px;">EMAIL CÔNG TY</th>
                <th style="min-width: 130px;">SỐ ĐIỆN THOẠI</th>
                <th style="min-width: 140px;">PHÒNG BAN</th>
                <th style="min-width: 140px;">CHỨC VỤ</th>
                <th style="min-width: 130px;">TRẠNG THÁI</th>
                <th style="min-width: 120px;">NGÀY TẠO</th>
                <th style="width: 110px;" class="text-center">HÀNH ĐỘNG</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($staffs as $staff)
                <tr>
                    <td class="text-center">
                        <input type="checkbox" class="form-check-input checkItem" name="ids[]" value="{{ $staff->id }}">
                    </td>
                    <td class="text-center text-muted">
                        {{ ($staffs->currentPage() - 1) * $staffs->perPage() + $loop->iteration }}
                    </td>
                    <td><strong>{{ $staff->name }}</strong></td>
                    <td>{{ $staff->email ?? '-' }}</td>
                    <td>{{ $staff->account_id ?? '-' }}</td>
                    <td>{{ $staff->department ?? '-' }}</td>
                    <td>{{ $staff->position ?? '-' }}</td>
                    <td>
                        @if($staff->is_active)
                            <span class="badge bg-success">Đang hoạt động</span>
                        @else
                            <span class="badge bg-danger">Chưa hoạt động</span>
                        @endif
                    </td>
                    <td>{{ $staff->created_at->format('d/m/Y') }}</td>
                    <td class="text-center">
                        <a href="{{ route('admin.staffs.edit', $staff->id) }}" class="btn btn-sm btn-warning" title="Chỉnh sửa">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                        Chưa có nhân viên nào
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Phân trang -->
@if($staffs->hasPages())
    <div class="pagination-wrapper bg-white border-top p-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div class="text-muted small mb-2 mb-md-0">
                Hiển thị {{ $staffs->firstItem() ?? 0 }} - {{ $staffs->lastItem() ?? 0 }}
                trong tổng số {{ $staffs->total() }} nhân viên
            </div>
            <div>
                {{ $staffs->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endif
