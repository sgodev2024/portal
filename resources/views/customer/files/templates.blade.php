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

        <!-- Stats Bar -->
        <div class="stats-bar mt-3">
            <div class="row g-3">
                <div class="col-auto">
                    <div class="stat-item">
                        <i class="fas fa-file-alt text-primary"></i>
                        <span class="stat-value">{{ $files->total() }}</span>
                        <span class="stat-label">Biểu mẫu</span>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="stat-item">
                        <i class="fas fa-folder text-warning"></i>
                        <span class="stat-value">{{ $categories->count() }}</span>
                        <span class="stat-label">Danh mục</span>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="stat-item">
                        <i class="fas fa-download text-success"></i>
                        <span class="stat-value">{{ count($myDownloads) }}</span>
                        <span class="stat-label">Đã tải</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content - Wide Layout -->
    <div class="card border-0 shadow-hover">
        <div class="list-group list-group-flush">
            @forelse($files as $file)
            <div class="list-group-item list-item-hover px-4 py-4">
                <div class="d-flex align-items-center gap-4">
                    <!-- File Icon -->
                    <div class="file-icon flex-shrink-0">
                        <i class="fas fa-file-{{ $file->file_type === 'pdf' ? 'pdf' : ($file->file_type === 'doc' || $file->file_type === 'docx' ? 'word' : 'alt') }}"></i>
                    </div>

                    <!-- File Title & Category -->
                    <div class="file-main-info">
                        <h5 class="file-title mb-2">{{ $file->title }}</h5>
                        <div class="d-flex gap-2 flex-wrap">
                            @if($file->category)
                            <span class="badge bg-warning-soft">
                                <i class="fas fa-folder me-1"></i>{{ $file->category }}
                            </span>
                            @endif
                            <span class="badge bg-primary-soft">
                                <i class="fas fa-file-alt me-1"></i>{{ strtoupper($file->file_type) }}
                            </span>
                            <span class="badge bg-success-soft">
                                <i class="fas fa-database me-1"></i>{{ $file->file_size_formatted }}
                            </span>
                        </div>
                    </div>

                    <!-- Description - Extended -->
                    @if($file->description)
                    <div class="file-description flex-grow-1">
                        <div class="description-content">
                            <i class="fas fa-info-circle text-muted me-2"></i>
                            <span class="text-body">{{ $file->description }}</span>
                        </div>
                    </div>
                    @else
                    <div class="flex-grow-1"></div>
                    @endif

                    <!-- Uploader Info -->
                    <div class="uploader-info flex-shrink-0">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-circle bg-primary-soft text-primary">
                                {{ substr($file->uploader->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="fw-semibold text-body small">{{ $file->uploader->name }}</div>
                                <small class="text-muted">{{ $file->created_at->format('d/m/Y') }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Download Stats -->
                    <div class="download-stats flex-shrink-0">
                        <div class="stats-badge">
                            <div class="stats-icon">
                                <i class="fas fa-download"></i>
                            </div>
                            <div class="stats-info">
                                <div class="stats-number">{{ $file->download_count }}</div>
                                <div class="stats-text">lượt tải</div>
                            </div>
                        </div>
                        @if(in_array($file->id, $myDownloads))
                        <div class="mt-2 text-center">
                            <span class="badge bg-success-soft text-success">
                                <i class="fas fa-check-circle me-1"></i>Đã tải
                            </span>
                        </div>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="actions-group flex-shrink-0">
                        <a href="{{ route('customer.files.download_template', $file->id) }}" 
                           class="btn btn-primary btn-download">
                            <i class="fas fa-download me-2"></i>
                            Tải xuống
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="list-group-item py-5">
                <div class="text-center">
                    <div class="empty-state mb-4">
                        <div class="empty-icon-lg">
                            <i class="fas fa-inbox"></i>
                        </div>
                    </div>
                    <h5 class="mb-2">Không tìm thấy biểu mẫu</h5>
                    <p class="text-muted mb-4">
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
            </div>
            @endforelse
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
    /* Container - Wider */
    .container-fluid {
        max-width: 100% !important;
    }

    /* Header Styles */
    .gradient-text {
        background: linear-gradient(45deg, #4e73df, #36b9cc);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: 600;
        font-size: 1.75rem;
    }

    /* Stats Bar */
    .stats-bar {
        padding: 1rem;
        background: linear-gradient(135deg, #f8f9fa, #ffffff);
        border-radius: 12px;
        border: 1px solid #e9ecef;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem 1rem;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .stat-item i {
        font-size: 1.5rem;
    }

    .stat-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: #344767;
    }

    .stat-label {
        font-size: 0.85rem;
        color: #6c757d;
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

    /* Card & List - Horizontal Layout */
    .shadow-hover {
        transition: all 0.3s ease;
        border-radius: 12px;
    }

    .list-item-hover {
        transition: all 0.2s ease;
        border-left: 4px solid transparent;
    }

    .list-item-hover:hover {
        background-color: #f8f9fa;
        border-left-color: #4e73df;
    }

    /* File Elements - Horizontal */
    .file-icon {
        width: 56px;
        height: 56px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: linear-gradient(135deg, #4e73df20, #36b9cc20);
        color: #4e73df;
    }

    .file-icon i {
        font-size: 1.75rem;
    }

    /* File Main Info */
    .file-main-info {
        min-width: 280px;
    }

    .file-title {
        color: #344767;
        font-weight: 600;
        font-size: 1.1rem;
        line-height: 1.4;
        margin-bottom: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 380px;
    }

    /* Description - Takes up remaining space */
    .file-description {
        max-width: 450px;
        min-width: 200px;
    }

    .description-content {
        padding: 0.75rem 1rem;
        background-color: #f8f9fa;
        border-radius: 8px;
        border-left: 3px solid #4e73df;
        font-size: 0.9rem;
        line-height: 1.5;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Uploader Info */
    .uploader-info {
        min-width: 180px;
    }

    .avatar-circle {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-weight: 600;
        font-size: 1.1rem;
    }

    /* Download Stats */
    .download-stats {
        min-width: 120px;
    }

    .stats-badge {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        background: linear-gradient(135deg, #1cc88a10, #1cc88a20);
        border-radius: 8px;
        border: 1px solid #1cc88a30;
    }

    .stats-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: white;
        border-radius: 50%;
        color: #1cc88a;
        font-size: 1.1rem;
    }

    .stats-number {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1cc88a;
        line-height: 1;
    }

    .stats-text {
        font-size: 0.75rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Actions */
    .actions-group {
        min-width: 140px;
    }

    .btn-download {
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        border-radius: 8px;
        white-space: nowrap;
        transition: all 0.2s ease;
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

    /* Badges */
    .badge {
        padding: 0.4em 0.75em;
        font-weight: 500;
        font-size: 0.75rem;
        letter-spacing: 0.3px;
        border-radius: 6px;
        white-space: nowrap;
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

    /* Empty State */
    .empty-icon-lg {
        width: 100px;
        height: 100px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: linear-gradient(135deg, #4e73df10, #36b9cc10);
        color: #4e73df;
        font-size: 2.5rem;
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
    @media (max-width: 1400px) {
        .file-description {
            max-width: 300px;
        }
        
        .file-title {
            max-width: 280px;
        }
    }

    @media (max-width: 1200px) {
        .list-item-hover .d-flex {
            flex-wrap: wrap;
        }

        .file-main-info,
        .uploader-info,
        .download-stats,
        .actions-group {
            margin-bottom: 1rem;
        }

        .file-description {
            width: 100%;
            max-width: 100%;
            order: 5;
        }

        .actions-group {
            width: 100%;
            order: 6;
        }

        .btn-download {
            width: 100%;
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

        .file-icon {
            width: 48px;
            height: 48px;
        }

        .file-icon i {
            font-size: 1.5rem;
        }

        .file-title {
            font-size: 1rem;
        }

        .stats-bar {
            overflow-x: auto;
        }

        .stats-bar .row {
            flex-wrap: nowrap;
        }
    }
</style>
@endpush
@endsection