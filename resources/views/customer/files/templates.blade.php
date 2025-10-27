@extends('backend.layouts.master')

@section('title', 'Biểu mẫu')

@section('content')
<div class="container-fluid py-4 px-4 px-lg-5">
    <!-- Header -->
    <div class="header-section mb-4">
        <div class="row align-items-end">
            <div class="col-lg-4">
                <h4 class="gradient-text mb-2">Biểu mẫu</h4>
                <p class="text-muted mb-0">
                    <i class="fas fa-file-alt me-1"></i>
                    Danh sách biểu mẫu và tài liệu có thể tải về
                </p>
            </div>
            
            <div class="col-lg-8">
                <div class="d-flex gap-3 justify-content-lg-end align-items-center flex-wrap">
                    <!-- Category Filter -->
                    @if($categories->count() > 0)
                    <div class="btn-group filter-group" role="group">
                        <a href="{{ route('customer.files.templates') }}" 
                           class="btn btn-filter {{ request()->get('category') == '' ? 'active' : '' }}">
                            <i class="fas fa-th me-1"></i>Tất cả
                        </a>
                        @foreach($categories as $category)
                        <a href="{{ route('customer.files.templates', ['category' => $category]) }}" 
                           class="btn btn-filter {{ request()->get('category') == $category ? 'active' : '' }}">
                            {{ $category }}
                        </a>
                        @endforeach
                    </div>
                    @endif

                    <!-- Search Form -->
                    <form method="GET" class="flex-grow-1" style="max-width: 350px;">
                        <div class="input-group search-box">
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Tìm kiếm biểu mẫu..." 
                                   value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Content -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;" class="text-center">STT</th>
                            <th style="width: 30%;">Tên biểu mẫu</th>
                            <th style="width: 12%;" class="text-center">Ngày tạo</th>
                            <th style="width: 15%;" class="text-center">Thao tác</th>
                            <th style="width: 38%;">Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($files as $index => $file)
                        <tr>
                            <!-- STT -->
                            <td class="text-center">
                                {{ $files->firstItem() + $index }}
                            </td>
                            
                            <!-- Tên biểu mẫu -->
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-file-{{ $file->file_type === 'pdf' ? 'pdf text-danger' : ($file->file_type === 'doc' || $file->file_type === 'docx' ? 'word text-primary' : 'alt text-secondary') }}"></i>
                                    <div>
                                        <div class="fw-semibold">{{ $file->title }}</div>
                                        <small class="text-muted">
                                            {{ strtoupper($file->file_type) }} • {{ $file->file_size_formatted }}
                                            @if($file->category)
                                            • {{ $file->category }}
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Ngày tạo -->
                            <td class="text-center">
                                <div>{{ $file->created_at->format('d/m/Y') }}</div>
                                <small class="text-muted">{{ $file->created_at->format('H:i') }}</small>
                            </td>
                            
                            <!-- Thao tác -->
                            <td class="text-center">
                                <a href="{{ route('customer.files.download_template', $file->id) }}" 
                                   class="btn btn-primary btn-sm">
                                    <i class="fas fa-download"></i> Tải xuống
                                </a>
                                @if(in_array($file->id, $myDownloads))
                                <div class="mt-1">
                                    <small class="text-success">
                                        <i class="fas fa-check-circle"></i> Đã tải
                                    </small>
                                </div>
                                @endif
                            </td>
                            
                            <!-- Ghi chú -->
                            <td>
                                @if($file->description)
                                    <small>{{ $file->description }}</small>
                                @else
                                    <small class="text-muted fst-italic">Không có ghi chú</small>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                                    <h5 class="mb-2">Không tìm thấy biểu mẫu</h5>
                                    <p class="mb-4">
                                        @if(request('search'))
                                            Không có biểu mẫu nào phù hợp với từ khóa "{{ request('search') }}"
                                        @else
                                            Chưa có biểu mẫu nào trong danh mục này
                                        @endif
                                    </p>
                                    @if(request('search') || request('category'))
                                    <a href="{{ route('customer.files.templates') }}" class="btn btn-light">
                                        <i class="fas fa-redo me-2"></i> Xem tất cả
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($files->count() > 0)
        <div class="card-footer bg-white border-0 pt-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Hiển thị <strong>{{ $files->firstItem() ?? 0 }}</strong> - <strong>{{ $files->lastItem() ?? 0 }}</strong> 
                    trong tổng số <strong>{{ $files->total() }}</strong> biểu mẫu
                </div>
                <div class="pagination-wrapper">
                    {{ $files->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    /* Header Styles */
    .gradient-text {
        background: linear-gradient(45deg, #4e73df, #36b9cc);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: 600;
        font-size: 1.75rem;
    }

    /* Filter Group */
    .filter-group {
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-radius: 8px;
        overflow: hidden;
    }

    .btn-filter {
        background: white;
        color: #6c757d;
        border: 1px solid #e9ecef;
        padding: 0.625rem 1rem;
        font-weight: 500;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .btn-filter:hover {
        background: #f8f9fa;
        color: #4e73df;
    }

    .btn-filter.active {
        background: linear-gradient(135deg, #4e73df, #36b9cc);
        color: white;
        border-color: transparent;
    }

    /* Search Box */
    .search-box {
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-radius: 8px;
        overflow: hidden;
    }

    .search-box .form-control {
        border: 1px solid #e9ecef;
        border-right: none;
        padding: 0.625rem 1rem;
    }

    .search-box .form-control:focus {
        border-color: #4e73df;
        box-shadow: none;
    }

    .search-box .btn-primary {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        padding: 0.625rem 1.25rem;
    }

    /* Card */
    .card {
        border-radius: 12px;
        overflow: hidden;
    }

    /* Table Styles */
    .table {
        margin-bottom: 0;
    }

    .table-header {
        background: linear-gradient(135deg, #f8f9fa, #ffffff);
        border-bottom: 2px solid #e9ecef;
    }

    .table-header th {
        color: #344767;
        font-weight: 600;
        font-size: 0.9rem;
        padding: 1rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e9ecef;
    }

    .table-row {
        transition: all 0.2s ease;
        border-left: 3px solid transparent;
    }

    .table-row:hover {
        background-color: #f8f9fa;
        border-left-color: #4e73df;
    }

    .table-row td {
        padding: 1.25rem 1rem;
        vertical-align: middle;
    }

    /* STT Badge */
    .stt-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #4e73df, #36b9cc);
        color: white;
        border-radius: 50%;
        font-weight: 600;
        font-size: 0.95rem;
    }

    /* File Icon */
    .file-icon-sm {
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: linear-gradient(135deg, #4e73df20, #36b9cc20);
        color: #4e73df;
        flex-shrink: 0;
    }

    .file-icon-sm i {
        font-size: 1.4rem;
    }

    /* File Name */
    .file-name {
        font-weight: 600;
        color: #344767;
        font-size: 1rem;
        line-height: 1.4;
        margin-bottom: 0.25rem;
    }

    .file-meta {
        display: flex;
        gap: 0.25rem;
        flex-wrap: wrap;
    }

    /* Date Info */
    .date-info {
        font-weight: 500;
        color: #344767;
        font-size: 0.95rem;
    }

    /* Note Content */
    .note-content {
        display: flex;
        align-items: start;
        gap: 0.5rem;
        padding: 0.75rem;
        background-color: #f8f9fa;
        border-radius: 8px;
        border-left: 3px solid #4e73df;
        font-size: 0.9rem;
        line-height: 1.5;
        color: #344767;
    }

    /* Badges */
    .badge {
        padding: 0.35em 0.65em;
        font-weight: 500;
        font-size: 0.7rem;
        letter-spacing: 0.3px;
        border-radius: 5px;
    }

    .bg-primary-soft {
        background-color: #4e73df20;
        color: #4e73df;
    }

    .bg-success-soft {
        background-color: #1cc88a20;
        color: #1cc88a;
    }

    .bg-warning-soft {
        background-color: #f6c23e20;
        color: #d4941e;
    }

    /* Download Button */
    .btn-download {
        padding: 0.5rem 1rem;
        font-weight: 500;
        border-radius: 8px;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .btn-primary {
        background: linear-gradient(135deg, #4e73df, #36b9cc);
        border: none;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #3d5cb5, #2a8fa0);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(78, 115, 223, 0.4);
    }

    /* Empty State */
    .empty-state .empty-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: linear-gradient(135deg, #4e73df10, #36b9cc10);
        color: #4e73df;
        font-size: 2rem;
    }

    /* Pagination */
    .page-link {
        padding: 0.5rem 0.875rem;
        border-radius: 8px;
        margin: 0 0.25rem;
        color: #4e73df;
        font-weight: 500;
        border: 1px solid #e9ecef;
    }

    .page-link:hover {
        background-color: #4e73df10;
        border-color: #4e73df;
    }

    .page-item.active .page-link {
        background: linear-gradient(135deg, #4e73df, #36b9cc);
        border-color: transparent;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .table-responsive {
            font-size: 0.875rem;
        }

        .file-name {
            font-size: 0.9rem;
        }

        .stt-badge {
            width: 35px;
            height: 35px;
            font-size: 0.85rem;
        }

        .file-icon-sm {
            width: 40px;
            height: 40px;
        }

        .file-icon-sm i {
            font-size: 1.2rem;
        }
    }

    @media (max-width: 768px) {
        .filter-group {
            width: 100%;
            flex-direction: column;
        }

        .btn-filter {
            width: 100%;
            text-align: left;
        }

        .table {
            font-size: 0.8rem;
        }

        .table-header th {
            padding: 0.75rem 0.5rem;
            font-size: 0.75rem;
        }

        .table-row td {
            padding: 1rem 0.5rem;
        }

        .btn-download {
            padding: 0.4rem 0.75rem;
            font-size: 0.8rem;
        }

        .note-content {
            font-size: 0.8rem;
            padding: 0.5rem;
        }
    }
    
</style>
@endpush
@endsection