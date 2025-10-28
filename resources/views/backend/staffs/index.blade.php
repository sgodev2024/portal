{{-- resources/views/backend/staffs/index.blade.php --}}
@extends('backend.layouts.master')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">
                <i class="fas fa-user-tie"></i> Quản lý nhân viên
            </h3>
        </div>

        <!-- Thông báo -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Card chứa bảng -->
        <div class="card shadow-sm border-0">
            <!-- Form Bulk Action -->
            <form id="bulkActionForm" method="POST" action="{{ route('admin.staffs.deleteSelected') }}">
                @csrf
                <input type="hidden" name="action" id="actionInput">

                <div class="card-body p-0">
                    <!-- Thanh công cụ -->
                    <div class="toolbar-section p-3 bg-light border-bottom">
                        <div class="row align-items-center g-2">
                            <div class="col-lg-6 col-md-12">
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    <div class="dropdown">
                                        <button class="btn btn-light dropdown-toggle border" type="button"
                                            id="bulkActionDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                                            disabled>
                                            <i class="fas fa-tasks"></i> Thao tác
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="bulkActionDropdown">
                                            <li>
                                                <a class="dropdown-item" href="#"
                                                    onclick="applyBulkAction('delete'); return false;">
                                                    <i class="fas fa-trash text-danger"></i> Xóa đã chọn
                                                </a>
                                            </li>
                                        </ul>
                                    </div>

                                    <button type="button" class="btn btn-light border" onclick="location.reload()">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-12">
                                <div class="d-flex align-items-center justify-content-lg-end flex-wrap gap-2">
                                    <div class="search-box">
                                        <input type="text" class="form-control form-control-sm" placeholder="Tìm kiếm..."
                                            id="searchInput">
                                    </div>

                                    <!-- Nút Import Excel -->
                                    <button type="button" class="btn btn-sm btn-light border"
                                        onclick="document.getElementById('excelFile').click()">
                                        <i class="fas fa-file-import"></i> Import
                                    </button>
                                    
                                    <!-- Nút Export Excel -->
                                    <a href="{{ route('admin.staffs.export') }}" class="btn btn-sm btn-success border">
                                        <i class="fas fa-file-excel"></i> Export
                                    </a>

                                    <a href="{{ route('admin.staffs.create') }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus"></i> Thêm mới
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12 text-end">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1 text-primary"></i>
                                    <a href="{{ route('admin.staffs.downloadTemplate') }}" class="text-decoration-none fw-semibold text-primary">
                                        Tải file Excel mẫu
                                    </a>
                                    để import dữ liệu
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Bảng nhân viên -->
                    <div id="staffTableWrapper">
                        @include('backend.staffs.table', ['staffs' => $staffs])
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Form Import Excel -->
    <form id="importForm" action="{{ route('admin.staffs.import') }}" method="POST" enctype="multipart/form-data"
        style="display:none;">
        @csrf
        <input type="file" name="file" id="excelFile" accept=".xlsx,.xls,.csv" required>
    </form>
@endsection

@push('styles')
    <style>
        .card {
            border-radius: 8px;
            overflow: hidden;
        }

        .toolbar-section {
            background-color: #f8f9fa !important;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            font-weight: 600;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            color: #495057;
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            padding: 12px 10px;
        }

        .table tbody td {
            padding: 12px 10px;
            font-size: 0.875rem;
            vertical-align: middle;
            border-bottom: 1px solid #e9ecef;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        .table-hover tbody tr:hover {
            background-color: #f1f3f5;
        }

        .badge {
            font-weight: 500;
            padding: 0.35em 0.75em;
            font-size: 0.75rem;
        }

        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }

        .search-box input {
            width: 200px;
            border: 1px solid #dee2e6;
        }

        .dropdown-menu {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
        }

        .dropdown-item {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        .dropdown-item i {
            width: 20px;
            margin-right: 8px;
        }

        .form-check-input {
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .gap-2 {
            gap: 0.5rem !important;
        }

        @media (max-width: 768px) {
            .search-box input {
                width: 150px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const excelFile = document.getElementById('excelFile');
            const importForm = document.getElementById('importForm');
            const searchInput = document.getElementById('searchInput');

            // Upload Excel
            if (excelFile && importForm) {
                excelFile.addEventListener('change', function() {
                    if (this.files.length > 0) {
                        console.log('File selected:', this.files[0].name);
                        importForm.submit();
                    } else {
                        alert('Không có file nào được chọn!');
                    }
                });
            }

            // Hàm khởi tạo checkbox và bulk action button
            function initCheckboxes() {
                const checkAll = document.getElementById('checkAll');
                const checkItems = document.querySelectorAll('.checkItem');
                const bulkActionBtn = document.getElementById('bulkActionDropdown');

                console.log('Init checkboxes - checkAll:', checkAll);
                console.log('Init checkboxes - checkItems count:', checkItems.length);

                // Xử lý checkbox "Chọn tất cả"
                if (checkAll) {
                    const newCheckAll = checkAll.cloneNode(true);
                    checkAll.parentNode.replaceChild(newCheckAll, checkAll);

                    newCheckAll.addEventListener('change', function() {
                        console.log('CheckAll changed:', this.checked);
                        const currentCheckItems = document.querySelectorAll('.checkItem');
                        currentCheckItems.forEach(item => {
                            item.checked = this.checked;
                        });
                        updateBulkActionButton();
                    });
                }

                // Xử lý từng checkbox con
                checkItems.forEach((item, index) => {
                    const newItem = item.cloneNode(true);
                    item.parentNode.replaceChild(newItem, item);

                    newItem.addEventListener('change', function() {
                        console.log('CheckItem', index, 'changed:', this.checked);
                        updateCheckAllState();
                        updateBulkActionButton();
                    });
                });

                updateCheckAllState();
                updateBulkActionButton();
            }

            // Hàm cập nhật trạng thái checkbox "Chọn tất cả"
            function updateCheckAllState() {
                const checkAll = document.getElementById('checkAll');
                const checkItems = document.querySelectorAll('.checkItem');

                if (checkAll && checkItems.length > 0) {
                    const checkedCount = document.querySelectorAll('.checkItem:checked').length;
                    const allChecked = checkedCount === checkItems.length;
                    const someChecked = checkedCount > 0 && checkedCount < checkItems.length;

                    checkAll.checked = allChecked;
                    checkAll.indeterminate = someChecked;

                    console.log('UpdateCheckAllState - checked:', checkedCount, 'total:', checkItems.length);
                }
            }

            // Hàm cập nhật trạng thái nút bulk action
            function updateBulkActionButton() {
                const bulkActionBtn = document.getElementById('bulkActionDropdown');
                const checkedCount = document.querySelectorAll('.checkItem:checked').length;

                console.log('UpdateBulkActionButton - checked count:', checkedCount);

                if (bulkActionBtn) {
                    bulkActionBtn.disabled = checkedCount === 0;
                }
            }

            // Hàm load lại bảng với AJAX
            function loadTable(params = {}) {
                const query = new URLSearchParams({
                    search: params.search !== undefined ? params.search : searchInput.value,
                    page: params.page || 1
                });

                for (let [key, value] of [...query.entries()]) {
                    if (value === '' || value === null || value === undefined) {
                        query.delete(key);
                    }
                }

                const queryString = query.toString();

                fetch(`{{ route('admin.staffs.index') }}?${queryString}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.text();
                    })
                    .then(data => {
                        document.getElementById('staffTableWrapper').innerHTML = data;
                        console.log('Table loaded, reinitializing checkboxes...');
                        initCheckboxes();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra khi tải dữ liệu. Vui lòng thử lại!');
                    });
            }

            // Khởi tạo checkbox lần đầu
            initCheckboxes();

            // Debounce tìm kiếm
            let debounceTimeout;
            searchInput?.addEventListener('input', function() {
                clearTimeout(debounceTimeout);
                debounceTimeout = setTimeout(() => loadTable(), 300);
            });

            // Phân trang Ajax
            document.addEventListener('click', function(e) {
                const link = e.target.closest('.pagination a');
                if (link) {
                    e.preventDefault();
                    const url = new URL(link.href);
                    const page = url.searchParams.get('page');
                    loadTable({
                        page
                    });
                }
            });

            // Hàm áp dụng hành động bulk
            window.applyBulkAction = function(action) {
                const form = document.getElementById('bulkActionForm');
                const checkedBoxes = form.querySelectorAll('.checkItem:checked');
                const count = checkedBoxes.length;

                console.log('Bulk Action - Số checkbox đã chọn:', count);

                if (count === 0) {
                    alert('Vui lòng chọn ít nhất một nhân viên!');
                    return false;
                }

                if (confirm(`Bạn có chắc muốn xóa ${count} nhân viên đã chọn không?`)) {
                    const methodInput = form.querySelector('input[name="_method"]');
                    if (methodInput) methodInput.remove();
                    form.method = 'POST';
                    document.getElementById('actionInput').value = action;

                    form.submit();
                }

                return false;
            };
        });
    </script>
@endpush
