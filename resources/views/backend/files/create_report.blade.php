@extends('backend.layouts.master')

@section('title', 'Thêm File Báo cáo')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Thêm File Báo cáo</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.files.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.files.store') }}" enctype="multipart/form-data">
                        @if($errors->any())
<div class="alert alert-danger">
    <h6><i class="fas fa-exclamation-triangle"></i> Có lỗi xảy ra:</h6>
    <ul class="mb-0">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
                        @csrf
                        <input type="hidden" name="file_category" value="report">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">Tiêu đề <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="file">File <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control @error('file') is-invalid @enderror" 
                                           id="file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.zip,.rar" required>
                                    <small class="form-text text-muted">
                                        Định dạng: PDF, DOC, DOCX, XLS, XLSX, ZIP, RAR (Tối đa 50MB)
                                    </small>
                                    @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Mô tả</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Hướng dẫn chọn người nhận -->
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Hướng dẫn chọn người nhận</h6>
                            <p class="mb-0">Bạn có thể chọn theo 3 cách:</p>
                            <ul class="mb-0">
                                <li><strong>Chỉ chọn nhóm:</strong> Gửi đến tất cả khách hàng trong nhóm</li>
                                <li><strong>Chỉ chọn khách hàng cụ thể:</strong> Gửi đến những khách hàng đã chọn</li>
                                <li><strong>Chọn cả hai:</strong> Gửi đến tất cả (hệ thống tự động loại trùng)</li>
                            </ul>
                        </div>

                        <div class="form-group">
                            <label>Chọn nhóm khách hàng <small class="text-muted">(Tùy chọn)</small></label>
                            <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                <div class="mb-2 d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllGroups">
                                        <i class="fas fa-check-square"></i> Chọn tất cả
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="clearAllGroups">
                                        <i class="fas fa-square"></i> Bỏ chọn tất cả
                                    </button>
                                    <span id="groupCount" class="badge badge-info align-self-center ml-2"></span>
                                </div>
                                @if($groups->count() > 0)
                                    @foreach($groups as $group)
                                    <div class="form-check">
                                        <input class="form-check-input group-checkbox" type="checkbox" 
                                               name="recipient_group_ids[]" value="{{ $group->id }}" 
                                               id="group_{{ $group->id }}">
                                        <label class="form-check-label" for="group_{{ $group->id }}">
                                            <strong>{{ $group->name }}</strong>
                                            @if($group->description)
                                                <br><small class="text-muted">{{ $group->description }}</small>
                                            @endif
                                        </label>
                                    </div>
                                    @endforeach
                                @else
                                    <div class="text-muted text-center py-3">
                                        <i class="fas fa-users"></i><br>
                                        Chưa có nhóm khách hàng nào
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Chọn khách hàng cụ thể <small class="text-muted">(Tùy chọn - có thể chọn thêm ngoài nhóm)</small></label>
                            <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                <div class="mb-2">
                                    <div class="d-flex gap-2 mb-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllUsers">
                                            <i class="fas fa-check-square"></i> Chọn tất cả
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="clearAllUsers">
                                            <i class="fas fa-square"></i> Bỏ chọn tất cả
                                        </button>
                                        <span id="userCount" class="badge badge-info align-self-center"></span>
                                    </div>
                                    <div>
                                        <input type="text" class="form-control form-control-sm" id="searchUsers" 
                                               placeholder="Tìm kiếm khách hàng theo tên hoặc email...">
                                    </div>
                                </div>
                                <div id="usersList">
                                    @foreach($customers as $customer)
                                    <div class="form-check user-item" data-name="{{ strtolower($customer->name) }}" data-email="{{ strtolower($customer->email) }}">
                                        <input class="form-check-input user-checkbox" type="checkbox" 
                                               name="recipient_user_ids[]" value="{{ $customer->id }}" 
                                               id="user_{{ $customer->id }}">
                                        <label class="form-check-label" for="user_{{ $customer->id }}">
                                            <strong>{{ $customer->name }}</strong> <small class="text-muted">({{ $customer->email }})</small>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @error('recipient_user_ids')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Kích hoạt ngay
                                </label>
                            </div>
                        </div>

                        <!-- Tổng kết người nhận -->
                        <div class="alert alert-success" id="recipientSummary" style="display: none;">
                            <h6><i class="fas fa-check-circle"></i> Tổng kết người nhận</h6>
                            <div id="selectedCount" class="mb-2"></div>
                            <div class="text-success">
                                <i class="fas fa-check"></i> <strong>Đã sẵn sàng gửi!</strong>
                            </div>
                        </div>

                        <!-- Cảnh báo nếu chưa chọn ai -->
                        <div class="alert alert-warning" id="noRecipientsWarning" style="display: none;">
                            <h6><i class="fas fa-exclamation-triangle"></i> Chưa chọn người nhận</h6>
                            <p class="mb-0">Vui lòng chọn ít nhất 1 nhóm khách hàng hoặc 1 khách hàng cụ thể.</p>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Lưu và Gửi
                            </button>
                            <a href="{{ route('admin.files.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@push('scripts')
<script>
    $(function(){
        // Xử lý nút "Chọn tất cả nhóm"
        $('#selectAllGroups').click(function() {
            $('.group-checkbox').prop('checked', true);
            updateSelectedCount();
        });

        // Xử lý nút "Bỏ chọn tất cả nhóm"
        $('#clearAllGroups').click(function() {
            $('.group-checkbox').prop('checked', false);
            updateSelectedCount();
        });

        // Xử lý nút "Chọn tất cả khách hàng"
        $('#selectAllUsers').click(function() {
            $('.user-checkbox:visible').prop('checked', true);
            updateSelectedCount();
        });

        // Xử lý nút "Bỏ chọn tất cả khách hàng"
        $('#clearAllUsers').click(function() {
            $('.user-checkbox').prop('checked', false);
            updateSelectedCount();
        });

        // Tìm kiếm khách hàng
        $('#searchUsers').on('keyup', function() {
            var searchTerm = $(this).val().toLowerCase();
            
            $('.user-item').each(function() {
                var name = $(this).data('name');
                var email = $(this).data('email');
                
                if (name.includes(searchTerm) || email.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Hiển thị số lượng đã chọn
        function updateSelectedCount() {
            var selectedUsers = $('.user-checkbox:checked').length;
            var selectedGroups = $('.group-checkbox:checked').length;
            var totalUsers = $('.user-checkbox').length;
            var totalGroups = $('.group-checkbox').length;
            
            // Cập nhật badge cho nhóm
            if (selectedGroups > 0) {
                $('#groupCount').text(selectedGroups + '/' + totalGroups + ' nhóm').show();
            } else {
                $('#groupCount').text(totalGroups + ' nhóm').show();
            }
            
            // Cập nhật badge cho khách hàng
            if (selectedUsers > 0) {
                $('#userCount').text(selectedUsers + '/' + totalUsers + ' khách hàng').show();
            } else {
                $('#userCount').text(totalUsers + ' khách hàng').show();
            }
            
            // Tổng kết
            var totalText = '';
            if (selectedUsers > 0) totalText += selectedUsers + ' khách hàng';
            if (selectedGroups > 0) {
                if (totalText) totalText += ', ';
                totalText += selectedGroups + ' nhóm';
            }
            
            if (totalText) {
                $('#selectedCount').text('Tổng người nhận: ' + totalText);
                $('#recipientSummary').show();
            } else {
                $('#selectedCount').text('Chưa chọn người nhận nào');
                $('#recipientSummary').show();
            }
        }

        // Cập nhật số lượng khi thay đổi checkbox
        $('.user-checkbox, .group-checkbox').change(function() {
            updateSelectedCount();
        });

        // Khởi tạo
        updateSelectedCount();

        // Validation trước khi submit
        $('form').on('submit', function(e) {
            var selectedUsers = $('.user-checkbox:checked').length;
            var selectedGroups = $('.group-checkbox:checked').length;
            
            if (selectedUsers === 0 && selectedGroups === 0) {
                e.preventDefault();
                alert('Vui lòng chọn ít nhất 1 nhóm khách hàng hoặc 1 khách hàng cụ thể!');
                return false;
            }
        });
    });
</script>
@endpush
