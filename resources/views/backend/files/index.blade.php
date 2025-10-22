@extends('backend.layouts.master')

@section('title', 'Quản lý File')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Quản lý File</h3>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.files.create_report') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm Báo cáo
            </a>
            <a href="{{ route('admin.files.create_template') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Thêm Biểu mẫu
            </a>
        </div>
    </div>

    <!-- Card -->
    <div class="card shadow-sm border-0">
        <div class="toolbar-section border-bottom p-3 bg-light">
            <div class="row align-items-center mb-3">
                <div class="col-md-6 mb-2 mb-md-0">
                    <div class="btn-group" role="group">
                        <a href="{{ route('admin.files.index') }}" 
                           class="btn {{ request()->routeIs('admin.files.index') ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="fas fa-list-ul me-1"></i> Tất cả
                        </a>
                        <a href="{{ route('admin.files.reports') }}" 
                           class="btn {{ request()->routeIs('admin.files.reports') ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="fas fa-chart-bar me-1"></i> Báo cáo
                        </a>
                        <a href="{{ route('admin.files.templates') }}" 
                           class="btn {{ request()->routeIs('admin.files.templates') ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="fas fa-file-alt me-1"></i> Biểu mẫu
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <form method="GET" class="search-box">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Tìm kiếm..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-outline-secondary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if($files->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tiêu đề</th>
                                <th>Loại</th>
                                <th>File</th>
                                <th>Kích thước</th>
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
                                    <div class="d-flex flex-column">
                                        <strong>{{ $file->title }}</strong>
                                        @if($file->description)
                                            <small class="text-muted">{{ Str::limit($file->description, 50) }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $file->file_category === 'report' ? 'primary' : 'success' }}">
                                        {{ $file->file_category === 'report' ? 'Báo cáo' : 'Biểu mẫu' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file-{{ $file->file_type === 'pdf' ? 'pdf' : 'alt' }} text-primary me-2"></i>
                                        <span>{{ $file->file_name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $file->file_size_formatted }}</small>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span>{{ $file->uploader->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span>{{ $file->created_at->format('d/m/Y') }}</span>
                                        <small class="text-muted">{{ $file->created_at->format('H:i') }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $file->is_active ? 'success' : 'secondary' }}">
                                        {{ $file->is_active ? 'Hoạt động' : 'Không hoạt động' }}
                                    </span>
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
                                        @if($file->file_category === 'report' && !empty($file->recipients))
                                            <form method="POST" action="{{ route('admin.files.resend', $file->id) }}" 
                                                  class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success" 
                                                        title="Gửi lại">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('admin.files.destroy', $file->id) }}" 
                                              class="d-inline" 
                                              onsubmit="return confirm('Bạn có chắc muốn xóa file này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                    title="Xóa">
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

                <div class="d-flex justify-content-center mt-3">
                    {{ $files->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <h5>Chưa có file nào</h5>
                    <p class="text-muted">Hãy thêm file báo cáo hoặc biểu mẫu để bắt đầu.</p>
                </div>
            @endif
                </div>
            </div>
        </div>
    </div>
</div>

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
@endsection