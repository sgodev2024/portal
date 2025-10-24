@extends('backend.layouts.master')

@section('title', 'Báo cáo đã nhận')

@section('content')
<div class="container-fluid py-4 px-4 px-lg-5">
    <!-- Header -->
    <div class="header-section mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h4 class="gradient-text mb-2">Báo cáo đã nhận</h4>
                <div class="d-flex align-items-center">
                    <div class="header-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <p class="text-muted mb-0 ms-2">
                        Danh sách báo cáo được gửi đến bạn từ admin và đội ngũ hỗ trợ
                    </p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="stats-card">
                    <div class="row g-0">
                        <div class="col-6 border-end">
                            <div class="p-3">
                                <h6 class="stats-label">Tổng báo cáo</h6>
                                <h3 class="stats-value">{{ $files->total() }}</h3>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3">
                                <h6 class="stats-label">Tháng này</h6>
                                <h3 class="stats-value">{{ $files->where('sent_at', '>=', now()->startOfMonth())->count() }}</h3>
                            </div>
                        </div>
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

                    <!-- File Title & Badges -->
                    <div class="file-main-info">
                        <h5 class="file-title mb-2">{{ $file->title }}</h5>
                        <div class="d-flex gap-2">
                            <span class="badge bg-primary-soft">
                                <i class="fas fa-file-alt me-1"></i>{{ strtoupper($file->file_type) }}
                            </span>
                            <span class="badge bg-success-soft">
                                <i class="fas fa-database me-1"></i>{{ $file->file_size_formatted }}
                            </span>
                            @if($file->downloads_count > 0)
                            <span class="badge bg-info-soft">
                                <i class="fas fa-download me-1"></i>{{ $file->downloads_count }} lượt
                            </span>
                            @endif
                        </div>
                    </div>

                    <!-- Description - Extended -->
                    @if($file->description)
                    <div class="file-description flex-grow-1">
                        <div class="description-content">
                            <i class="fas fa-quote-left text-muted me-2"></i>
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
                                <div class="fw-semibold text-body">{{ $file->uploader->name }}</div>
                                <small class="text-muted">{{ $file->uploader->email }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Date Info -->
                    <div class="date-info flex-shrink-0">
                        <div class="text-center">
                            <div class="date-badge">
                                <div class="date-day">{{ $file->sent_at->format('d') }}</div>
                                <div class="date-month">{{ $file->sent_at->format('M Y') }}</div>
                            </div>
                            <small class="text-muted d-block mt-1">{{ $file->sent_at->format('H:i') }}</small>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="actions-group flex-shrink-0">
                        <div class="d-flex gap-2">
                            <a href="{{ route('customer.files.download_report', $file->id) }}" 
                               class="btn btn-primary btn-action" 
                               title="Tải xuống">
                                <i class="fas fa-download"></i>
                            </a>
                            <a href="{{ route('customer.files.show_report', $file->id) }}" 
                               class="btn btn-light btn-action" 
                               title="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
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
                    <h5 class="mb-2">Chưa có báo cáo nào</h5>
                    <p class="text-muted mb-4">
                        Bạn sẽ nhận được thông báo qua email khi có báo cáo mới được gửi đến.<br>
                        Các báo cáo thường được gửi vào cuối tháng hoặc theo yêu cầu đặc biệt.
                    </p>
                    <button class="btn btn-light" disabled>
                        <i class="fas fa-sync-alt me-2"></i> Làm mới
                    </button>
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
                    trong tổng số <strong>{{ $files->total() }}</strong> báo cáo
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

    .header-icon {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: linear-gradient(45deg, #4e73df20, #36b9cc20);
        color: #4e73df;
    }

    /* Stats Card */
    .stats-card {
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .stats-label {
        color: #6c757d;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }

    .stats-value {
        color: #344767;
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0;
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
        min-width: 250px;
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
        max-width: 350px;
    }

    /* Description - Takes up remaining space */
    .file-description {
        max-width: 500px;
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
        min-width: 200px;
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

    /* Date Info */
    .date-info {
        min-width: 80px;
    }

    .date-badge {
        background: linear-gradient(135deg, #4e73df10, #36b9cc10);
        border-radius: 8px;
        padding: 0.5rem;
        text-align: center;
        border: 1px solid #e9ecef;
    }

    .date-day {
        font-size: 1.5rem;
        font-weight: 700;
        color: #4e73df;
        line-height: 1;
    }

    .date-month {
        font-size: 0.7rem;
        text-transform: uppercase;
        color: #6c757d;
        font-weight: 600;
        margin-top: 0.25rem;
    }

    /* Actions */
    .actions-group {
        min-width: 100px;
    }

    .btn-action {
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        padding: 0;
        font-size: 1.1rem;
        transition: all 0.2s ease;
    }

    .btn-primary {
        background: linear-gradient(135deg, #4e73df, #36b9cc);
        border: none;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #3d5cb5, #2a8fa0);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(78, 115, 223, 0.3);
    }

    .btn-light {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        color: #6c757d;
    }

    .btn-light:hover {
        background-color: #e9ecef;
        color: #344767;
        transform: translateY(-2px);
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

    .bg-info-soft {
        background-color: #36b9cc20;
        color: #36b9cc;
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

    /* Responsive - Stack on smaller screens */
    @media (max-width: 1400px) {
        .file-description {
            max-width: 300px;
        }
        
        .file-title {
            max-width: 250px;
        }
    }

    @media (max-width: 1200px) {
        .list-item-hover .d-flex {
            flex-wrap: wrap;
        }

        .file-main-info,
        .uploader-info,
        .date-info,
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

        .actions-group .d-flex {
            width: 100%;
        }

        .btn-action {
            flex: 1;
            width: auto;
        }
    }

    @media (max-width: 768px) {
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
    }
</style>
@endpush
@endsection