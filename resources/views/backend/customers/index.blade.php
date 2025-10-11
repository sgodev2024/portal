@extends('backend.layouts.master')

@section('content')
    <div class="container">
        <!-- Header: Tìm kiếm + Thêm + Import -->
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
            <!-- Tiêu đề bên trái -->
            <h2 class="mb-2 mb-md-0">Quản lý khách hàng</h2>

            <!-- Nút và ô tìm kiếm bên phải -->
            <div class="d-flex align-items-center flex-wrap">
                <!-- Hàng nút -->
                <div class="d-flex align-items-center mb-2 me-2">
                    <!-- Nút thêm khách hàng -->
                    <a href="{{ route('customers.create') }}" class="btn btn-primary me-2">Thêm khách hàng</a>

                    <!-- Nút import Excel -->
                    <form id="importForm" action="{{ route('customers.import') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="file" name="file" id="excelFile" accept=".xlsx,.xls" style="display: none;"
                            required>
                        <button type="button" class="btn btn-success"
                            onclick="document.getElementById('excelFile').click()">
                            Import Excel
                        </button>
                    </form>
                </div>

                <!-- Hàng tìm kiếm -->
                <div class="d-flex align-items-center mb-2">
                    <input type="text" id="search" class="form-control" placeholder="Tìm kiếm khách hàng..."
                        style="max-width: 200px;">
                </div>
            </div>
        </div>


        <!-- Bảng khách hàng -->
        <div id="customerTable">
            @include('backend.customers.table', ['customers' => $customers])
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const excelFile = document.getElementById('excelFile');
            const importForm = document.getElementById('importForm');

            // Upload Excel
            if (excelFile && importForm) {
                excelFile.addEventListener('change', function() {
                    if (this.files.length > 0) {
                        importForm.submit();
                    }
                });
            }

            // Hàm load bảng bằng Ajax
            function loadTable(url) {
                fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById('customerTable').innerHTML = data;
                    });
            }

            // Tìm kiếm với debounce 300ms
            let debounceTimeout;
            const searchInput = document.getElementById('search');
            searchInput.addEventListener('keyup', function() {
                clearTimeout(debounceTimeout);
                const query = this.value;
                debounceTimeout = setTimeout(() => {
                    loadTable(`{{ route('customers.index') }}?search=${query}`);
                }, 300);
            });

            // Phân trang Ajax
            document.addEventListener('click', function(e) {
                if (e.target.closest('.pagination a')) {
                    e.preventDefault();
                    const url = e.target.closest('a').href;
                    loadTable(url + '&search=' + searchInput.value);
                }
            });
        });
    </script>
@endpush
