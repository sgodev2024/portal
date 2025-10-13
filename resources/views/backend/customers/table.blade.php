{{-- resources/views/backend/customers/table.blade.php --}}
<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th width="40" class="text-center border-end">
                    <input type="checkbox" id="checkAll" class="form-check-input">
                </th>
                <th width="50" class="border-end">STT</th>
                <th class="border-end">TÊN KHÁCH HÀNG</th>
                <th class="border-end">EMAIL</th>
                <th width="130" class="border-end">SỐ ĐIỆN THOẠI</th>
                <th width="130" class="text-center border-end">TRẠNG THÁI</th>
                <th width="150" class="text-center border-end">CẬP NHẬT HỒ SƠ</th>
                <th width="150" class="text-center">HÀNH ĐỘNG</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($customers as $c)
                <tr>
                    <td class="text-center border-end">
                        <input type="checkbox" name="ids[]" value="{{ $c->id }}"
                               class="form-check-input checkItem">
                    </td>
                    <td class="text-center border-end">
                        {{ ($customers->currentPage() - 1) * $customers->perPage() + $loop->iteration }}
                    </td>
                    <td class="border-end">
                        <strong>{{ $c->name }}</strong>
                    </td>
                    <td class="border-end text-muted">{{ $c->email }}</td>
                    <td class="border-end">{{ $c->phone }}</td>
                    <td class="text-center border-end">
                        @if ($c->is_active)
                            <span class="badge rounded-pill bg-success">Kích hoạt</span>
                        @else
                            <span class="badge rounded-pill bg-secondary">Ngừng</span>
                        @endif
                    </td>
                    <td class="text-center border-end">
                        @if ($c->must_update_profile)
                            <span class="badge rounded-pill bg-warning text-dark">Chưa cập nhật</span>
                        @else
                            <span class="badge rounded-pill bg-success">Đã cập nhật</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-info"
                                onclick="window.location='{{ route('customers.show', $c->id) }}'"
                                title="Xem chi tiết">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-primary"
                                onclick="window.location='{{ route('customers.edit', $c->id) }}'"
                                title="Chỉnh sửa">
                            <i class="fas fa-edit"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                        <p class="text-muted mb-0">Không có khách hàng nào.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Phân trang -->
@if($customers->hasPages())
    <div class="pagination-wrapper bg-white border-top p-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div class="text-muted small mb-2 mb-md-0">
                Hiển thị {{ $customers->firstItem() }} - {{ $customers->lastItem() }}
                trong tổng số {{ $customers->total() }} khách hàng
            </div>
            <div>
                {{ $customers->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endif

<script>
// Reinitialize checkbox functionality sau khi AJAX load
(function() {
    const checkAll = document.getElementById('checkAll');
    const checkboxes = document.querySelectorAll('.checkItem');
    const bulkActionBtn = document.getElementById('bulkActionDropdown');

    function updateCount() {
        const count = document.querySelectorAll('.checkItem:checked').length;
        const allCheckboxes = document.querySelectorAll('.checkItem');

        // Enable/disable dropdown
        if (bulkActionBtn) {
            bulkActionBtn.disabled = count === 0;
        }

        // Cập nhật trạng thái checkAll
        if (checkAll && allCheckboxes.length > 0) {
            checkAll.checked = count === allCheckboxes.length && count > 0;
            checkAll.indeterminate = count > 0 && count < allCheckboxes.length;
        }
    }

    // Check/uncheck tất cả
    if (checkAll) {
        checkAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = checkAll.checked);
            updateCount();
        });
    }

    // Lắng nghe thay đổi từng checkbox
    checkboxes.forEach(cb => cb.addEventListener('change', updateCount));

    // Khởi tạo
    updateCount();

    // Hàm xóa từng khách hàng (global function)
    window.deleteCustomer = function(id) {
        if (confirm('Bạn có chắc muốn xóa khách hàng này không?')) {
            document.getElementById('deleteForm' + id).submit();
        }
    };
})();
</script>
