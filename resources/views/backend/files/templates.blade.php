@extends('backend.layouts.master')

@section('title', 'Biểu mẫu')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">
            <i class="fas fa-file-invoice"></i> Biểu mẫu
        </h3>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Card chứa bảng -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <!-- Thanh công cụ -->
            <div class="toolbar-section p-3 bg-light border-bottom">
                <div class="row align-items-center g-2">
                    <div class="col-lg-6 col-md-12">
                        <div class="d-flex align-items-center flex-wrap gap-2">
                             <a href="{{ route('admin.files.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-list"></i> Tất cả File
                        </a>

                            <button type="button" class="btn btn-sm btn-light border" onclick="location.reload()">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-12">
                        <div class="d-flex align-items-center justify-content-lg-end flex-wrap gap-2">
                            <form method="GET" class="search-box">
                                <input type="text" name="search" class="form-control form-control-sm" 
                                       placeholder="Tìm kiếm biểu mẫu..." value="{{ request('search') }}">
                            </form>

                            <a href="{{ route('admin.files.create_template') }}" class="btn btn-sm btn-success">
                                <i class="fas fa-plus"></i> Thêm Biểu mẫu
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bảng dữ liệu -->
            @if($files->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Tiêu đề</th>
                                <th>File</th>
                                <th>Lượt tải</th>
                                <th>Người tạo</th>
                                <th>Ngày tạo</th>
                                <th>Trạng thái</th>
                                <th width="150">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($files as $file)
                            <tr>
                                <td>
                                    <strong>{{ $file->title }}</strong>
                                    @if($file->description)
                                        <br><small class="text-muted">{{ Str::limit($file->description, 50) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <i class="fas fa-file-{{ $file->file_type === 'pdf' ? 'pdf' : 'alt' }} text-muted"></i>
                                    {{ $file->file_name }}
                                    <br><small class="text-muted">{{ $file->file_size_formatted }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $file->download_count }} lượt</span>
                                </td>
                                <td>{{ $file->uploader->name ?? 'N/A' }}</td>
                                <td>{{ $file->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($file->is_active)
                                        <span class="badge bg-success">Hoạt động</span>
                                    @else
                                        <span class="badge bg-secondary">Không hoạt động</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('admin.files.show', $file->id) }}" 
                                            class="btn btn-sm btn-outline-info" title="Chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.files.edit', $file->id) }}" 
                                           class="btn btn-sm btn-outline-warning" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.files.download', $file->id) }}" 
                                           class="btn btn-sm btn-outline-primary" title="Tải xuống">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.files.destroy', $file->id) }}" 
                                              class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                         
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3 px-3 pb-3">
                    <div class="text-muted small">
                        Hiển thị <strong>{{ $files->firstItem() ?? 0 }}</strong> - <strong>{{ $files->lastItem() ?? 0 }}</strong> 
                        trong tổng số <strong>{{ $files->total() }}</strong> biểu mẫu
                    </div>
                    <div>
                        {{ $files->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-file-alt fa-3x text-muted mb-3 opacity-50"></i>
                    <h5 class="text-muted">Chưa có biểu mẫu nào</h5>
                    <p class="text-muted">Hãy thêm biểu mẫu để khách hàng có thể tải về.</p>
                </div>
            @endif
        </div>
    </div>
</div>
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
    }

    /* Pagination Styling */
    .pagination {
        margin-bottom: 0;
    }

    .pagination .page-link {
        color: #0d6efd;
        border: 1px solid #dee2e6;
        padding: 0.5rem 0.75rem;
        transition: all 0.2s ease;
    }

    .pagination .page-link:hover {
        background-color: #e9ecef;
        border-color: #dee2e6;
    }

    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
        font-weight: 600;
    }

    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #fff;
        border-color: #dee2e6;
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

    .gap-1 {
        gap: 0.25rem !important;
    }

    .gap-2 {
        gap: 0.5rem !important;
    }

    .me-1 {
        margin-right: 0.25rem !important;
    }

    .me-2 {
        margin-right: 0.5rem !important;
    }

    @media (max-width: 768px) {
        .search-box input {
            width: 100%;
        }
        
        .btn-group {
            flex-wrap: wrap;
        }
        
        .btn-group .btn {
            flex: 1 1 auto;
            min-width: 120px;
        }
    }
</style>
@endpush