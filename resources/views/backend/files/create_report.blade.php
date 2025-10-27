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
                            <label for="files" class="font-weight-bold">Tải lên file <span class="text-danger">*</span></label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('files') is-invalid @enderror" 
                                       id="files" name="files[]" accept=".pdf,.doc,.docx,.xls,.xlsx,.zip,.rar" multiple required>
                                <label class="custom-file-label" for="files" id="filesLabel">Chọn file...</label>
                            </div>
                            <small class="form-text text-muted mt-2">
                                <i class="fas fa-info-circle"></i> Định dạng: PDF, DOC, DOCX, XLS, XLSX, ZIP, RAR (Mỗi file tối đa 50MB)
                            </small>
                            <div id="selectedFiles" class="mt-2"></div>
                            @error('files')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @error('files.*')
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

                <!-- Thông tin về tự động gửi báo cáo -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-info-circle text-info mr-2"></i>Thông tin người nhận</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-0">
                            <h6><i class="fas fa-lightbulb mr-2"></i>Hướng dẫn gửi báo cáo</h6>
                            <p class="mb-0">Để tự động gửi nhiều báo cáo cho khách hàng:</p>
                            <ul class="mb-3">
                                <li>Đặt tên mỗi file là số điện thoại của khách hàng (ví dụ: <code>0968377516.pdf</code>)</li>
                                <li>Có thể chọn nhiều file cùng lúc cho nhiều khách hàng khác nhau</li>
                                <li>Hệ thống sẽ tự động tìm và gửi cho từng khách hàng theo số điện thoại tương ứng</li>
                                <li>Mỗi file sẽ chỉ được gửi cho đúng khách hàng có số điện thoại trùng với tên file</li>
                                <li>Nếu có file không khớp với số điện thoại nào, hệ thống sẽ thông báo lỗi</li>
                            </ul>
                            <p class="mb-0">
                                <i class="fas fa-exclamation-triangle text-warning mr-1"></i>
                                <small>Lưu ý: Đảm bảo tên file chính xác để gửi đúng người nhận</small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Summary Card -->
                <div class="card shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-header bg-gradient-primary text-white py-3">
                        <h5 class="mb-0"><i class="fas fa-upload mr-2"></i>Tạo báo cáo</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <i class="fas fa-file-upload fa-3x text-primary mb-3"></i>
                            <p class="mb-0">Sau khi tải lên, báo cáo sẽ được tự động gửi cho khách hàng dựa trên tên file.</p>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <button type="submit" class="btn btn-primary btn-block btn-lg">
                            <i class="fas fa-upload mr-1"></i> Tải lên và gửi
                        </button>
                        <a href="{{ route('admin.files.index') }}" class="btn btn-outline-secondary btn-block">
                            <i class="fas fa-times mr-1"></i> Hủy
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
    // Update files input label and show selected files
    $('#files').on('change', function() {
        var files = $(this)[0].files;
        var fileCount = files.length;
        
        if (fileCount > 0) {
            $('#filesLabel').html(fileCount + ' file đã chọn');
            
            // Hiển thị danh sách file đã chọn
            var fileList = '<div class="list-group">';
            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                var icon = 'file-alt';
                
                // Xác định icon dựa vào loại file
                if (file.name.toLowerCase().endsWith('.pdf')) icon = 'file-pdf';
                else if (file.name.toLowerCase().match(/\.(doc|docx)$/)) icon = 'file-word';
                else if (file.name.toLowerCase().match(/\.(xls|xlsx)$/)) icon = 'file-excel';
                else if (file.name.toLowerCase().match(/\.(zip|rar)$/)) icon = 'file-archive';
                
                fileList += '<div class="list-group-item py-2 px-3">' +
                           '<i class="fas fa-' + icon + ' text-primary mr-2"></i>' +
                           '<span class="font-weight-bold">' + file.name + '</span>' +
                           '<small class="text-muted ml-2">(' + formatFileSize(file.size) + ')</small>' +
                           '</div>';
            }
            fileList += '</div>';
            
            $('#selectedFiles').html(fileList);
        } else {
            $('#filesLabel').html('Chọn file...');
            $('#selectedFiles').empty();
        }
    });
    
    // Hàm format kích thước file
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
});
</script>
@endpush