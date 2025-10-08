@extends('backend.layouts.master')

@section('content')
    <div class="modal fade" id="transferModal" tabindex="-1" role="dialog" aria-labelledby="transferModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('transfer.domain') }}" method="POST" id="transferForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="transferModalLabel">Chuyển dữ liệu</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <div class="form-group">
                            <label for="domain">Tên Domain để đổi:</label>
                            <input type="text" name="domain" id="data-domain" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label for="toUser">Người nhận domain (Người nhận):</label>
                            <select name="username" id="username" class="form-control">
                                <option value="">--- Chọn người nhận ---</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->email }}">{{ $user->full_name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary close-modal" data-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Xác nhận chuyển</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="content">
        <!-- Bảng danh sách danh mục -->
        <div class="category-list">
            <div style="overflow-x: auto;">
                <table class="table table-striped table-hover" id="categoryTable">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th class="text-center">Domain</th>
                            <th>Trạng thái</th>
                            <th class="text-center">Ngày bắt đầu</th>
                            <th class="text-center">Ngày kết thúc</th>
                            <th class="text-center">Chuyển</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .dataTables_scrollBody thead tr {
            display: none;
        }

        #add-category-btn {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            */
            /* text-align: end; */
            padding: 10px;
            margin-right: 100px;
        }


        td a {
            padding: 8px 11px !important;
            border-radius: 5px;
            color: white;
            display: inline-block;
        }

        .edit {
            background: #ffc107;
            margin: 0px 15px;
        }

        .delete {
            background: #dc3545;
            padding: 8px 12px !important;
        }

        td,
        th {
            text-align: center !important;
        }

        .status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 12px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
            text-transform: capitalize;
            line-height: 1.5;
            white-space: nowrap;
            transition: all 0.3s ease-in-out;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .status .icon-check,
        .status .icon-warning {
            display: inline-block;
            width: 16px;
            height: 16px;
            margin-right: 8px;
            background-size: contain;
            background-repeat: no-repeat;
        }


        .status.active {
            background-color: #e6f4ea;
            color: #2b8a3e;
            border: 1px solid #cce7d0;
        }

        .status.active .icon-check {
            background-image: url('https://cdn-icons-png.flaticon.com/512/845/845646.png');
        }

        .status.paused {
            background-color: #fdecea;
            color: #d93025;
            border: 1px solid #f5c6cb;
        }

        .status.paused .icon-warning {
            background-image: url('https://cdn-icons-png.flaticon.com/512/1828/1828843.png');
        }

        .btn-transfer {
            background-color: #e6f4ff;
            border: 1px solid #91d5ff;
            color: #1890ff;
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }
    </style>
@endpush

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            var APP_URL = '{{ env('APP_URL') }}';
            $('#categoryTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: APP_URL + '/admin/domain',
                order: [], // Vô hiệu hóa sắp xếp mặc định
                columns: [{
                        data: null,
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.settings._iDisplayStart + meta.row + 1;
                        }
                    },
                    {
                        data: 'name',
                        name: 'name',
                        orderable: false
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false
                    },
                    {
                        data: 'created_date',
                        name: 'created_date',
                        orderable: false
                    },
                    {
                        data: 'expiration_date',
                        name: 'expiration_date',
                        orderable: false
                    },
                    {
                        data: 'another_column',
                        name: 'another_column',
                        orderable: false
                    }

                ],
                columnDefs: [{
                        width: '5%',
                        targets: 0
                    },
                    {
                        width: '15%',
                        targets: 1
                    },
                    {
                        width: '15%',
                        targets: 2
                    },
                    {
                        width: '19%',
                        targets: 3
                    },
                    {
                        width: '19%',
                        targets: 4
                    },
                    {
                        width: '10%',
                        targets: 5
                    },

                ],
                pagingType: "full_numbers",
                language: {
                    paginate: {
                        previous: '&laquo;',
                        next: '&raquo;'
                    },
                    lengthMenu: "Hiển thị _MENU_ mục mỗi trang",
                    zeroRecords: "Không tìm thấy dữ liệu",
                    info: "Hiển thị _START_ đến _END_ của _TOTAL_ mục",
                    infoEmpty: "Không có dữ liệu để hiển thị",
                    infoFiltered: "(lọc từ _MAX_ mục)"
                },
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $(document).on('click', '.btn-transfer', function() {
                var id = $(this).data('id');
                var domain = $(this).data('domain');
                $('#data-domain').val(domain); // hoặc load dữ liệu vào modal
                $('#transferModal').modal('show');
            });

            $(document).on('click', '.close-modal, .close', function() {
                const modal = $('#transferModal');
                if (modal.length) {
                    modal.modal('hide');
                }
            });

        });
    </script>

    <script>
        $(document).ready(function() {
            $('#transferForm').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công!',
                            text: 'Chuyển domain thành công!',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            $('#transferModal').modal('hide');
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        let errorMessage = 'Có lỗi xảy ra. Vui lòng thử lại!';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: errorMessage,
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });
        });
    </script>
@endpush
