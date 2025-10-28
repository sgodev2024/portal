<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th width="20" class="text-center border-end">
                    <input type="checkbox" id="checkAll" class="form-check-input">
                </th>
                <th width="40" class="border-end text-center">STT</th>
                <th width="100" class="border-end text-center notranslate">ID</th>
                <th class="border-end">HỌ TÊN</th>
                <th class="border-end notranslate">EMAIL</th>
                <th class="border-end notranslate">SỐ ĐIỆN THOẠI</th>
                <th class="border-end">CÔNG TY</th>
                <th class="border-end">NHÓM</th>
                <th class="text-center border-end">TRẠNG THÁI</th>
                <th class="text-center border-end">HỒ SƠ</th>
                <th width="180" class="text-center">HÀNH ĐỘNG</th>
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

                    <td class="text-center border-end notranslate">
                        {{ $c->account_id ?? '-' }}
                    </td>

                    <td class="border-end"><strong>{{ $c->name }}</strong></td>
                    <td class="border-end text-muted notranslate">{{ $c->email ?? '-' }}</td>
                    <td class="border-end notranslate">{{ $c->phone ?? '-' }}</td>
                    <td class="border-end">{{ $c->company ?? '-' }}</td>

                    <td class="border-end">
                        @if ($c->groups && $c->groups->count())
                            @foreach ($c->groups as $group)
                                <span class="badge bg-primary">{{ $group->name }}</span>
                            @endforeach
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>

                    <td class="text-center border-end">
                        @if ($c->is_active)
                            <span class="badge rounded-pill bg-success">Kích hoạt</span>
                        @else
                            <span class="badge rounded-pill bg-secondary">Chưa kích hoạt</span>
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
                        <button type="button" class="btn btn-sm btn-info me-1"
                            onclick="window.location='{{ route('customers.show', $c->id) }}'" title="Xem chi tiết">
                            <i class="fas fa-eye"></i>
                        </button>

                        <button type="button" class="btn btn-sm btn-primary me-1"
                            onclick="window.location='{{ route('customers.edit', $c->id) }}'" title="Chỉnh sửa">
                            <i class="fas fa-edit"></i>
                        </button>

                        <button type="button" class="btn btn-sm btn-warning"
                            onclick="if(confirm('Bạn có chắc muốn reset mật khẩu cho {{ $c->name }}?')) window.location='{{ route('customers.resetPassword', $c->id) }}'"
                            title="Reset mật khẩu">
                            <i class="fas fa-key"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                        <p class="text-muted mb-0">Không có khách hàng nào.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($customers->hasPages())
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
    (function() {
        const checkAll = document.getElementById('checkAll');
        const checkboxes = document.querySelectorAll('.checkItem');
        const bulkActionBtn = document.getElementById('bulkActionDropdown');

        function updateCount() {
            const count = document.querySelectorAll('.checkItem:checked').length;
            const allCheckboxes = document.querySelectorAll('.checkItem');

            if (bulkActionBtn) bulkActionBtn.disabled = count === 0;
            if (checkAll && allCheckboxes.length > 0) {
                checkAll.checked = count === allCheckboxes.length && count > 0;
                checkAll.indeterminate = count > 0 && count < allCheckboxes.length;
            }
        }

        if (checkAll) {
            checkAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = checkAll.checked);
                updateCount();
            });
        }

        checkboxes.forEach(cb => cb.addEventListener('change', updateCount));

        updateCount();

        window.deleteCustomer = function(id) {
            if (confirm('Bạn có chắc muốn xóa khách hàng này không?')) {
                document.getElementById('deleteForm' + id).submit();
            }
        };
    })();
</script>
