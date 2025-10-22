@extends('backend.layouts.master')

@section('title', 'Thêm File Báo cáo')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Thêm File Báo cáo</h2>
            <p class="text-muted mb-0">Tải lên và gửi báo cáo tài chính đến khách hàng</p>
        </div>
        <a href="{{ route('admin.files.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Quay lại
        </a>
    </div>

    <form method="POST" action="{{ route('admin.files.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="file_category" value="report">
        
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- File Information Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-file-alt text-primary mr-2"></i>Thông tin báo cáo</h5>
                    </div>
                    <div class="card-body">
                        @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h6 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Có lỗi xảy ra:</h6>
                            <ul class="mb-0 pl-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                        @endif

                        <div class="form-group">
                            <label for="title" class="font-weight-bold">Tiêu đề <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" 
                                   placeholder="VD: Báo cáo tài chính quý 1/2025" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="file" class="font-weight-bold">Tải lên file <span class="text-danger">*</span></label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('file') is-invalid @enderror" 
                                       id="file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.zip,.rar" required>
                                <label class="custom-file-label" for="file">Chọn file...</label>
                            </div>
                            <small class="form-text text-muted mt-2">
                                <i class="fas fa-info-circle"></i> Định dạng: PDF, DOC, DOCX, XLS, XLSX, ZIP, RAR (Tối đa 50MB)
                            </small>
                            @error('file')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description" class="font-weight-bold">Mô tả</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" 
                                      placeholder="Nhập mô tả chi tiết về báo cáo...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" 
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">
                                <strong>Kích hoạt ngay</strong>
                                <small class="text-muted d-block">File sẽ được gửi ngay sau khi lưu</small>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Recipients Selection -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-users text-success mr-2"></i>Chọn người nhận</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label class="font-weight-bold mb-3">
                                <i class="fas fa-layer-group mr-1"></i> Chọn nhóm khách hàng 
                                <span class="badge badge-secondary ml-1" id="groupCount">0 nhóm</span>
                            </label>
                            
                            <div class="border rounded bg-light p-3">
                                <div class="d-flex gap-2 mb-3">
                                    <button type="button" class="btn btn-sm btn-primary" id="selectAllGroups">
                                        <i class="fas fa-check-square"></i> Chọn tất cả
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="clearAllGroups">
                                        <i class="fas fa-square"></i> Bỏ chọn tất cả
                                    </button>
                                </div>
                                
                                <div class="groups-container" style="max-height: 300px; overflow-y: auto;">
                                    @if($groups->count() > 0)
                                        <div class="row">
                                            @foreach($groups as $group)
                                            <div class="col-md-6 mb-2">
                                                <div class="custom-control custom-checkbox">
                                                    <input class="custom-control-input group-checkbox" type="checkbox" 
                                                           name="recipient_group_ids[]" value="{{ $group->id }}" 
                                                           id="group_{{ $group->id }}">
                                                    <label class="custom-control-label" for="group_{{ $group->id }}">
                                                        <strong>{{ $group->name }}</strong>
                                                        @if($group->description)
                                                            <br><small class="text-muted">{{ $group->description }}</small>
                                                        @endif
                                                    </label>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-users fa-3x mb-2 opacity-50"></i>
                                            <p class="mb-0">Chưa có nhóm khách hàng nào</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Individual Users Selection -->
                        <div>
                            <label class="font-weight-bold mb-3">
                                <i class="fas fa-user-check mr-1"></i> Chọn khách hàng cụ thể 
                                <span class="badge badge-secondary ml-1" id="userCount">0 khách hàng</span>
                            </label>
                            
                            <div class="border rounded bg-light p-3">
                                <div class="mb-3">
                                    <div class="d-flex gap-2 mb-2">
                                        <button type="button" class="btn btn-sm btn-primary" id="selectAllUsers">
                                            <i class="fas fa-check-square"></i> Chọn tất cả
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="clearAllUsers">
                                            <i class="fas fa-square"></i> Bỏ chọn tất cả
                                        </button>
                                    </div>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white">
                                                <i class="fas fa-search"></i>
                                            </span>
                                        </div>
                                        <input type="text" class="form-control" id="searchUsers" 
                                               placeholder="Tìm kiếm theo tên hoặc email...">
                                    </div>
                                </div>
                                
                                <div id="usersList" class="users-container" style="max-height: 400px; overflow-y: auto;">
                                    @foreach($customers as $customer)
                                    <div class="custom-control custom-checkbox mb-2 user-item" 
                                         data-name="{{ strtolower($customer->name) }}" 
                                         data-email="{{ strtolower($customer->email) }}">
                                        <input class="custom-control-input user-checkbox" type="checkbox" 
                                               name="recipient_user_ids[]" value="{{ $customer->id }}" 
                                               id="user_{{ $customer->id }}">
                                        <label class="custom-control-label" for="user_{{ $customer->id }}">
                                            <strong>{{ $customer->name }}</strong>
                                            <small class="text-muted ml-1">({{ $customer->email }})</small>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @error('recipient_user_ids')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Summary Card -->
                <div class="card shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-header bg-gradient-primary text-white py-3">
                        <h5 class="mb-0"><i class="fas fa-paper-plane mr-2"></i>Tổng kết gửi</h5>
                    </div>
                    <div class="card-body">
                        <div id="recipientSummary">
                            <div class="text-center py-4" id="noSelection">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">Chưa chọn người nhận nào</p>
                                <small class="text-muted">Vui lòng chọn ít nhất 1 nhóm hoặc 1 khách hàng</small>
                            </div>
                            
                            <div id="hasSelection" style="display: none;">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted">Số nhóm đã chọn:</span>
                                        <span class="badge badge-primary badge-pill" id="selectedGroupCount">0</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Số khách hàng đã chọn:</span>
                                        <span class="badge badge-success badge-pill" id="selectedUserCount">0</span>
                                    </div>
                                </div>
                                
                                <hr>
                                
                                <div class="alert alert-success mb-0">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check-circle fa-2x mr-3"></i>
                                        <div>
                                            <strong>Đã sẵn sàng gửi!</strong>
                                            <p class="mb-0 small" id="totalRecipients"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <button type="submit" class="btn btn-primary btn-block btn-lg">
                            <i class="fas fa-save mr-2"></i> Lưu và Gửi
                        </button>
                        <a href="{{ route('admin.files.index') }}" class="btn btn-outline-secondary btn-block">
                            <i class="fas fa-times mr-2"></i> Hủy
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    .border-left-info {
        border-left: 4px solid #17a2b8;
    }
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .custom-file-label::after {
        content: "Chọn";
    }
    .sticky-top {
        position: sticky;
    }
    .gap-2 {
        gap: 0.5rem;
    }
    .opacity-50 {
        opacity: 0.5;
    }
    .groups-container::-webkit-scrollbar,
    .users-container::-webkit-scrollbar {
        width: 8px;
    }
    .groups-container::-webkit-scrollbar-track,
    .users-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .groups-container::-webkit-scrollbar-thumb,
    .users-container::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }
    .groups-container::-webkit-scrollbar-thumb:hover,
    .users-container::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>
@endpush

@push('scripts')
<script>
$(function(){
    // Update file input label
    $('#file').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName);
    });

    // Select all groups
    $('#selectAllGroups').click(function() {
        $('.group-checkbox').prop('checked', true);
        updateSelectedCount();
    });

    // Clear all groups
    $('#clearAllGroups').click(function() {
        $('.group-checkbox').prop('checked', false);
        updateSelectedCount();
    });

    // Select all users
    $('#selectAllUsers').click(function() {
        $('.user-checkbox:visible').prop('checked', true);
        updateSelectedCount();
    });

    // Clear all users
    $('#clearAllUsers').click(function() {
        $('.user-checkbox').prop('checked', false);
        updateSelectedCount();
    });

    // Search users
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

    // Update selected count
    function updateSelectedCount() {
        var selectedUsers = $('.user-checkbox:checked').length;
        var selectedGroups = $('.group-checkbox:checked').length;
        var totalUsers = $('.user-checkbox').length;
        var totalGroups = $('.group-checkbox').length;
        
        // Update badges
        $('#groupCount').text(selectedGroups + '/' + totalGroups + ' nhóm');
        $('#userCount').text(selectedUsers + '/' + totalUsers + ' khách hàng');
        
        // Update summary
        $('#selectedGroupCount').text(selectedGroups);
        $('#selectedUserCount').text(selectedUsers);
        
        if (selectedUsers > 0 || selectedGroups > 0) {
            $('#noSelection').hide();
            $('#hasSelection').show();
            
            var totalText = [];
            if (selectedGroups > 0) totalText.push(selectedGroups + ' nhóm');
            if (selectedUsers > 0) totalText.push(selectedUsers + ' khách hàng');
            
            $('#totalRecipients').text('Tổng: ' + totalText.join(' + '));
        } else {
            $('#noSelection').show();
            $('#hasSelection').hide();
        }
    }

    // Update count on checkbox change
    $('.user-checkbox, .group-checkbox').change(function() {
        updateSelectedCount();
    });

    // Initialize
    updateSelectedCount();

    // Form validation
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