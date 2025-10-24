@extends('backend.layouts.master')

@section('title', 'Lịch sử tải')

@section('content')
<div class="container-fluid py-4 px-4 px-lg-5">
    <!-- Header -->
    <div class="header-section mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h4 class="gradient-text mb-2">Lịch sử tải xuống</h4>
                <p class="text-muted mb-0">
                    <i class="fas fa-history me-1"></i>
                    Theo dõi tất cả các file bạn đã tải về từ hệ thống
                </p>
            </div>
            <div class="col-lg-4">
                <div class="stats-card">
                    <div class="row g-0">
                        <div class="col-6 border-end">
                            <div class="p-3">
                                <h6 class="stats-label">Tổng lượt tải</h6>
                                <h3 class="stats-value">{{ $downloads->total() }}</h3>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3">
                                <h6 class="stats-label">Tháng này</h6>
                                <h3 class="stats-value">{{ $downloads->where('downloaded_at', '>=', now()->startOfMonth())->count() }}</h3>
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
            @forelse($downloads as $download)
            <div class="list-group-item list-item-hover px-4 py-4">
                <div class="d-flex align-items-center gap-4">
                    <!-- File Icon -->
                    <div class="file-icon-circle flex-shrink-0 {{ 
                        $download->file->file_type === 'pdf' 
                            ? 'file-icon-pdf' 
                            : (($download->file->file_type === 'doc' || $download->file->file_type === 'docx') 
                                ? 'file-icon-word' 
                                : 'file-icon-other') 
                    }}">
                        <i class="fas fa-file-{{ 
                            $download->file->file_type === 'pdf' 
                                ? 'pdf' 
                                : (($download->file->file_type === 'doc' || $download->file->file_type === 'docx') 
                                    ? 'word' 
                                    : 'alt') 
                        }}"></i>
                    </div>

                    <!-- File Title & Info -->
                    <div class="file-main-info">
                        <h5 class="file-title mb-2">{{ $download->file->title }}</h5>
                        <div class="d-flex gap-2 flex-wrap">
                            <span class="badge badge-category {{ $download->file->file_category === 'report' ? 'badge-report' : 'badge-template' }}">
                                <i class="fas fa-tag me-1"></i>
                                {{ $download->file->file_category === 'report' ? 'Báo cáo' : 'Biểu mẫu' }}
                            </span>
                            <span class="badge bg-primary-soft">
                                <i class="fas fa-file-alt me-1"></i>{{ strtoupper($download->file->file_type) }}
                            </span>
                            <span class="badge bg-success-soft">
                                <i class="fas fa-database me-1"></i>{{ $download->file->file_size_formatted }}
                            </span>
                        </div>
                    </div>

                    <!-- File Name -->
                    <div class="file-name-info flex-grow-1">
                        <div class="file-name-box">
                            <i class="fas fa-file-code text-muted me-2"></i>
                            <code class="file-name-text">{{ $download->file->file_name }}</code>
                        </div>
                    </div>

                    <!-- Uploader Info -->
                    <div class="uploader-info flex-shrink-0">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-circle bg-primary-soft text-primary">
                                {{ substr($download->file->uploader->name, 0, 1) }}
                            </div>
                            <div>
                                <small class="text-muted d-block">Người tải lên</small>
                                <div class="fw-semibold text-body">{{ $download->file->uploader->name }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Download Time -->
                    <div class="download-time flex-shrink-0">
                        <div class="time-badge">
                            <div class="time-icon">
                                <i class="fas fa-download"></i>
                            </div>
                            <div class="time-info">
                                <small class="time-label">Đã tải lúc</small>
                                <div class="time-value">{{ $download->downloaded_at->format('d/m/Y') }}</div>
                                <small class="time-hour">{{ $download->downloaded_at->format('H:i') }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="actions-group flex-shrink-0">
                        @if($download->file->file_category === 'report')
                            <a href="{{ route('customer.files.download_report', $download->file->id) }}" 
                               class="btn btn-primary btn-download">
                                <i class="fas fa-redo me-2"></i>
                                Tải lại
                            </a>
                        @else
                            <a href="{{ route('customer.files.download_template', $download->file->id) }}" 
                               class="btn btn-success btn-download">
                                <i class="fas fa-redo me-2"></i>
                                Tải lại
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="list-group-item py-5">
                <div class="text-center">
                    <div class="empty-state mb-4">
                        <div class="empty-icon-lg">
                            <i class="fas fa-cloud-download-alt"></i>
                        </div>
                    </div>
                    <h5 class="mb-2">Chưa có lịch sử tải nào</h5>
                    <p class="text-muted mb-4">
                        Lịch sử tải file của bạn sẽ hiển thị ở đây sau khi bạn tải xuống biểu mẫu hoặc báo cáo.<br>
                        Mọi hoạt động tải xuống sẽ được ghi nhận để bạn dễ dàng theo dõi.
                    </p>
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="{{ route('customer.files.templates') }}" class="btn btn-primary">
                            <i class="fas fa-file-alt me-2"></i>Xem biểu mẫu
                        </a>
                        <a href="{{ route('customer.files.reports') }}" class="btn btn-light">
                            <i class="fas fa-chart-line me-2"></i>Xem báo cáo
                        </a>
                    </div>
                </div>
            </div>
            @endforelse
        </div>

        @if($downloads->count() > 0)
        <div class="card-footer bg-white border-0 pt-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Hiển thị <strong>{{ $downloads->firstItem() ?? 0 }}</strong> - <strong>{{ $downloads->lastItem() ?? 0 }}</strong> 
                    trong tổng số <strong>{{ $downloads->total() }}</strong> lần tải
                </div>
                <div class="pagination-wrapper">
                    {{ $downloads->links() }}
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

    /* File Icon Circle */
    .file-icon-circle {
        width: 56px;
        height: 56px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 1.75rem;
        transition: all 0.2s ease;
    }

    .file-icon-pdf {
        background: linear-gradient(135deg, #dc354520, #dc354530);
        color: #dc3545;
    }

    .file-icon-word {
        background: linear-gradient(135deg, #0d6efd20, #0d6efd30);
        color: #0d6efd;
    }

    .file-icon-other {
        background: linear-gradient(135deg, #6c757d20, #6c757d30);
        color: #6c757d;
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
        max-width: 350px;
    }

    /* File Name Info */
    .file-name-info {
        max-width: 400px;
        min-width: 200px;
    }

    .file-name-box {
        padding: 0.75rem 1rem;
        background-color: #f8f9fa;
        border-radius: 8px;
        border: 1px dashed #dee2e6;
        display: flex;
        align-items: center;
    }

    .file-name-text {
        font-size: 0.85rem;
        color: #495057;
        background: transparent;
        padding: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
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

    /* Download Time */
    .download-time {
        min-width: 160px;
    }

    .time-badge {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        background: linear-gradient(135deg, #36b9cc10, #36b9cc20);
        border-radius: 8px;
        border: 1px solid #36b9cc30;
    }

    .time-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: white;
        border-radius: 50%;
        color: #36b9cc;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .time-label {
        font-size: 0.7rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: block;
    }

    .time-value {
        font-size: 0.95rem;
        font-weight: 600;
        color: #344767;
        line-height: 1.2;
    }

    .time-hour {
        font-size: 0.75rem;
        color: #6c757d;
    }

    /* Actions */
    .actions-group {
        min-width: 130px;
    }

    .btn-download {
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        border-radius: 8px;
        white-space: nowrap;
        transition: all 0.2s ease;
        border: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, #4e73df, #36b9cc);
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #3d5cb5, #2a8fa0);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(78, 115, 223, 0.4);
    }

    .btn-success {
        background: linear-gradient(135deg, #1cc88a, #13a06f);
    }

    .btn-success:hover {
        background: linear-gradient(135deg, #17a673, #0f8558);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(28, 200, 138, 0.4);
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

    .badge-category {
        font-weight: 600;
    }

    .badge-report {
        background: linear-gradient(135deg, #f6c23e, #dda20a);
        color: white;
    }

    .badge-template {
        background: linear-gradient(135deg, #36b9cc, #258391);
        color: white;
    }

    .bg-primary-soft {
        background-color: #4e73df20;
        color: #4e73df;
    }

    .bg-success-soft {
        background-color: #1cc88a20;
        color: #1cc88a;
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
        .file-name-info {
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
        .download-time,
        .actions-group {
            margin-bottom: 1rem;
        }

        .file-name-info {
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
        .stats-card {
            margin-top: 1rem;
        }

        .file-icon-circle {
            width: 48px;
            height: 48px;
        }

        .file-icon-circle i {
            font-size: 1.5rem;
        }

        .file-title {
            font-size: 1rem;
        }
    }

    /* Hover Effects */
    @media (min-width: 769px) {
        .list-item-hover:hover .file-icon-circle {
            transform: scale(1.1) rotate(5deg);
        }
    }
</style>
@endpush
@endsection