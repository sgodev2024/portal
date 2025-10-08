@extends('backend.layouts.master')

@section('content')
    <div class="content">
        <!-- Bảng danh sách danh mục -->
        <div class="category-list">
            <div class="card-tools d-flex justify-content-end ">

                <a href="{{ route('client.create') }}" class="btn btn-primary btn-sm">Thêm mới (+)</a>

            </div>
            <div style="overflow-x: auto;">
                <table class="table table-striped table-hover" id="categoryTable">
                    <thead>
                        <tr>
                            <th><input type="checkbox" name="" id="checkboxAll"></th>
                            <th>STT</th>
                            <th>Họ và tên</th>
                            <th>Email</th>
                            <th>Điện thoại</th>
                            <th>Trạng thái</th>
                            {{-- <th>Địa chỉ</th> --}}
                            <th>Action</th>
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

        .col-md-6:last-child {
            display: flex;
            justify-content: space-around;
        }

        th,
        td {
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
    </style>
@endpush

@push('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            var APP_URL = '{{ env('APP_URL') }}';
            $('#categoryTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: APP_URL + '/admin/client',
                columns: [{
                        data: 'checkbox',
                        name: 'checkbox',
                        orderable: false,
                        searchable: false
                    }, {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'full_name',
                        name: 'full_name',
                        orderable: false,

                    },
                    {
                        data: 'email',
                        name: 'email',
                        orderable: false,
                    },
                    {
                        data: 'phone_number',
                        name: 'phone_number',
                        orderable: false,
                    },

                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                    },

                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                    }
                ],
                columnDefs: [{
                        width: '5%',
                        targets: 0
                    },
                    {
                        width: '5%',
                        targets: 1
                    },
                    {
                        width: '20%',
                        targets: 2
                    },
                    {
                        width: '15%',
                        targets: 3
                    },
                    {
                        width: '15%',
                        targets: 4
                    },

                    {
                        width: '10%',
                        targets: 5
                    },
                    {
                        width: '15%',
                        targets: 6
                    },

                ],
                order: [],
                pagingType: "full_numbers", // Kiểu phân trang
                language: {
                    paginate: {
                        previous: '&laquo;', // Nút trước
                        next: '&raquo;' // Nút sau
                    },
                    lengthMenu: "Hiển thị _MENU_ mục mỗi trang",
                    zeroRecords: "Không tìm thấy dữ liệu",
                    info: "Hiển thị _START_ đến _END_ của _TOTAL_ mục",
                    infoEmpty: "Không có dữ liệu để hiển thị",
                    infoFiltered: "(lọc từ _MAX_ mục)"
                },
                // dom: '<"row"<"col-md-6"l><"col-md-6"f>>t<"row"<"col-md-6"i><"col-md-6"p>>',
                // lengthMenu: [10, 25, 50, 100],
            });

            $('#checkboxAll').on('change', function() {
                $('.checkbox-item').prop('checked', this.checked);
                toggleDeleteButton();
            });

            $(document).on('change', '.checkbox-item', function() {
                $('#checkboxAll').prop('checked', $('.checkbox-item:checked').length === $('.checkbox-item')
                    .length);
                toggleDeleteButton();
            });

            function toggleDeleteButton() {
                if ($('.checkbox-item:checked').length > 0) {
                    if ($('#btn-delete').length === 0) {
                        $('<button id="btn-delete" class="btn-danger btn" style="padding:4px 15px">Xóa</button>').insertAfter('.dt-length');

                        $('#btn-delete').on('click', function() {
                            Swal.fire({
                                title: 'Bạn có chắc muốn xóa?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#d33',
                                cancelButtonColor: '#3085d6',
                                confirmButtonText: 'Có, xóa ngay!',
                                cancelButtonText: 'Hủy'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    let checkedValues = $('.checkbox-item:checked').map(function() {
                                        return $(this).val();
                                    }).get();

                                    if (checkedValues.length === 0) {
                                        Swal.fire('Lỗi', 'Bạn chưa chọn mục nào để xóa.', 'error');
                                        return;
                                    }

                                    $.ajax({
                                        url: "{{ route('items.client.delete') }}",
                                        method: 'POST',
                                        data: {
                                            ids: checkedValues,
                                            _token: $('meta[name="csrf-token"]').attr(
                                                'content')
                                        },
                                        success: function(response) {
                                            if (response.success) {
                                                $('#checkboxAll').prop('checked', false);
                                                $('#btn-delete').remove();
                                                Swal.fire('Đã xóa!', 'Các mục đã được xóa.','success');
                                                $('#categoryTable').DataTable().ajax.reload(null, false);
                                            } else {
                                                Swal.fire('Lỗi', response.message ||
                                                    'Xóa thất bại', 'error');
                                            }
                                        },
                                        error: function(xhr) {
                                            Swal.fire('Lỗi', 'Có lỗi xảy ra khi xóa',
                                                'error');
                                        }
                                    });
                                }

                            });
                        });
                    }
                } else {
                    $('#btn-delete').remove();
                }
            }


        });


        function confirmDelete(event, id) {
            event.preventDefault();

            Swal.fire({
                title: 'Bạn có chắc chắn muốn xóa?',
                text: "Hành động này không thể hoàn tác!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Nếu người dùng xác nhận, submit form xóa
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
@endpush
