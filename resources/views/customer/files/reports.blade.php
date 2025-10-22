@extends('backend.layouts.master')

@section('title', 'Báo cáo đã nhận')

@section('content')
<div class="container-fluid py-4">
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
                        Danh sách báo cáo được gửi đến bạn
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

    <!-- Main Content -->
    <div class="card border-0 shadow-hover">
        <div class="list-group list-group-flush">
            @forelse($files as $file)
            <div class="list-group-item list-item-hover">
                <div class="d-flex align-items-center py-3">
                    <!-- File Icon -->
                    <div class="file-icon me-3">
                        <i class="fas fa-file-{{ $file->file_type === 'pdf' ? 'pdf' : ($file->file_type === 'doc' || $file->file_type === 'docx' ? 'word' : 'alt') }}"></i>
                    </div>

                    <!-- File Info -->
                    <div class="flex-grow-1 min-width-0">
                        <div class="d-flex align-items-center mb-2">
                            <h6 class="mb-0 text-truncate file-title">{{ $file->title }}</h6>
                            <div class="ms-2 d-flex gap-2">
                                <span class="badge bg-primary-soft">{{ strtoupper($file->file_type) }}</span>
                                <span class="badge bg-success-soft">{{ $file->file_size_formatted }}</span>
                            </div>
                        </div>
                        
                        @if($file->description)
                        <p class="text-muted small mb-2 text-truncate">{{ $file->description }}</p>
                        @endif
                        
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center small">
                                <div class="avatar-xs me-2">
                                    <div class="avatar-initial rounded-circle bg-primary-soft text-primary">
                                        {{ substr($file->uploader->name, 0, 1) }}
                                    </div>
                                </div>
                                <span class="text-body">{{ $file->uploader->name }}</span>
                            </div>
                            <span class="small text-muted d-flex align-items-center">
                                <i class="fas fa-clock me-1 opacity-50"></i>
                                {{ $file->sent_at->format('d/m/Y H:i') }}
                            </span>
                            @if($file->downloads_count > 0)
                            <span class="small text-success d-flex align-items-center">
                                <i class="fas fa-download me-1 opacity-50"></i>
                                {{ $file->downloads_count }} lượt tải
                            </span>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="actions ms-3">
                        <div class="btn-group">
                            <a href="{{ route('customer.files.download_report', $file->id) }}" 
                               class="btn btn-sm btn-primary" title="Tải xuống">
                                <i class="fas fa-download me-1"></i>
                                <span class="d-none d-md-inline">Tải xuống</span>
                            </a>
                            <a href="{{ route('customer.files.show_report', $file->id) }}" 
                               class="btn btn-sm btn-light" title="Chi tiết">
                                <i class="fas fa-eye me-1"></i>
                                <span class="d-none d-md-inline">Chi tiết</span>
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
                    <p class="text-muted mb-4">Bạn sẽ nhận được thông báo khi có báo cáo mới</p>
                    <button class="btn btn-light" disabled>
                        <i class="fas fa-sync-alt me-1"></i> Làm mới
                    </button>
                </div>
            </div>
            @endforelse
        </div>

        @if($files->count() > 0)
        <div class="card-footer bg-white border-0 pt-0">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Hiển thị {{ $files->firstItem() ?? 0 }}-{{ $files->lastItem() ?? 0 }} 
                    trong số {{ $files->total() }} báo cáo
                </small>
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
    .header-section {
        position: relative;
    }

    .gradient-text {
        background: linear-gradient(45deg, #4e73df, #36b9cc);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: 600;
    }

    .header-icon {
        width: 32px;
        height: 32px;
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
    }

    .stats-value {
        color: #344767;
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0;
    }

    /* Card & List Styles */
    .shadow-hover {
        transition: all 0.3s ease;
    }

    .shadow-hover:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    }

    .list-item-hover {
        transition: all 0.2s ease;
        border-left: 3px solid transparent;
    }

    .list-item-hover:hover {
        background-color: #f8f9fa;
        border-left-color: #4e73df;
    }

    .min-width-0 {
        min-width: 0;
    }

    /* File Elements */
    .file-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: linear-gradient(45deg, #4e73df20, #36b9cc20);
        color: #4e73df;
        flex-shrink: 0;
    }

    .file-icon i {
        font-size: 1.4rem;
    }

    .file-title {
        color: #344767;
        font-weight: 600;
    }

    /* Badges */
    .badge {
        padding: 0.5em 0.8em;
        font-weight: 500;
        font-size: 0.75rem;
        letter-spacing: 0.3px;
    }

    .bg-primary-soft {
        background-color: #4e73df20;
        color: #4e73df;
    }

    .bg-success-soft {
        background-color: #1cc88a20;
        color: #1cc88a;
    }

    /* Avatar */
    .avatar-xs {
        width: 24px;
        height: 24px;
        position: relative;
    }

    .avatar-initial {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 500;
    }

    /* Buttons */
    .btn {
        padding: 0.5rem 1rem;
        font-weight: 500;
        border-radius: 0.5rem;
    }

    .btn-sm {
        padding: 0.4rem 0.8rem;
        font-size: 0.875rem;
    }

    .btn-primary {
        background: linear-gradient(45deg, #4e73df, #36b9cc);
        border: none;
    }

    .btn-light {
        background-color: #f8f9fa;
        border-color: #e9ecef;
        color: #6c757d;
    }

    .btn-light:hover {
        background-color: #e9ecef;
        color: #344767;
    }

    /* Empty State */
    .empty-state {
        padding: 2rem 0;
    }

    .empty-icon-lg {
        width: 80px;
        height: 80px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: linear-gradient(45deg, #4e73df10, #36b9cc10);
        color: #4e73df;
        font-size: 2rem;
    }

    /* Pagination */
    .pagination-wrapper .pagination {
        margin-bottom: 0;
    }

    .page-link {
        padding: 0.375rem 0.75rem;
        border-radius: 0.5rem;
        margin: 0 0.2rem;
        color: #4e73df;
    }

    .page-item.active .page-link {
        background: linear-gradient(45deg, #4e73df, #36b9cc);
        border-color: transparent;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .stats-card {
            margin-top: 1rem;
        }

        .d-flex.align-items-center.gap-3 {
            flex-wrap: wrap;
            gap: 0.5rem !important;
        }
        
        .actions {
            margin-top: 1rem;
            width: 100%;
        }

        .actions .btn-group {
            width: 100%;
        }

        .actions .btn {
            flex: 1;
        }
    }
</style>
@endpush
@endsection

