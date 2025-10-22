@extends('backend.layouts.master')

@section('title', 'Thêm Biểu mẫu')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Thêm Biểu mẫu</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.files.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
                @if($errors->any())

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.files.store') }}" enctype="multipart/form-data">

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
                        <input type="hidden" name="file_category" value="template">
                        
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
                            <label for="description">Mô tả / Phân loại</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      placeholder="Ví dụ: Biểu mẫu tài chính, Biểu mẫu pháp lý...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Hiển thị cho khách hàng
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Lưu Biểu mẫu
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

