@extends('backend.layouts.master')

@section('title', 'Chỉnh sửa Báo cáo')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Chỉnh sửa Báo cáo</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.files.show', $file->id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.files.update', $file->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">Tiêu đề <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title', $file->title) }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="file">File mới <small class="text-muted">(Tùy chọn - để trống nếu không thay đổi)</small></label>
                                    <input type="file" class="form-control @error('file') is-invalid @enderror" 
                                           id="file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.zip,.rar">
                                    <small class="form-text text-muted">
                                        File hiện tại: <strong>{{ $file->file_name }}</strong> ({{ $file->file_size_formatted }})
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
                                      id="description" name="description" rows="3">{{ old('description', $file->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                                       {{ old('is_active', $file->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Kích hoạt
                                </label>
                            </div>
                        </div>

                        @if(!empty($file->recipients))
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Thông tin người nhận</h6>
                            <p class="mb-2">Báo cáo này đã được gửi đến {{ count($file->recipients) }} người:</p>
                            <div class="list-group">
                                @foreach($file->recipients as $email)
                                <div class="list-group-item py-1">
                                    <i class="fas fa-envelope"></i> {{ $email }}
                                </div>
                                @endforeach
                            </div>
                            <small class="text-muted mt-2 d-block">
                                <i class="fas fa-exclamation-triangle"></i> 
                                Lưu ý: Chỉnh sửa thông tin cơ bản, không thể thay đổi danh sách người nhận.
                            </small>
                        </div>
                        @endif

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Cập nhật
                            </button>
                            <a href="{{ route('admin.files.show', $file->id) }}" class="btn btn-secondary">
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


