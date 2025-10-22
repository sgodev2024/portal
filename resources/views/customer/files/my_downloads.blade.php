@extends('backend.layouts.master')

@section('title', 'Lịch sử tải')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="gradient-text mb-1">Lịch sử tải xuống</h4>
            <p class="text-muted small mb-0"><i class="fas fa-history me-1"></i> Danh sách các lần tải trước của bạn</p>
        </div>
    </div>

    <div class="card border-0 shadow-hover">
        <div class="list-group list-group-flush">
            @forelse($downloads as $download)
            <div class="list-group-item list-item-hover">
                <div class="d-flex align-items-center py-3">
                    <!-- File Icon -->
                    <div class="file-icon me-3 rounded-circle d-flex align-items-center justify-content-center">
                        <i class="fas fa-file-{{ 
                            $download->file->file_type === 'pdf' 
                                ? 'pdf text-danger' 
                                : (($download->file->file_type === 'doc' || $download->file->file_type === 'docx') 
                                    ? 'word text-primary' 
                                    : 'alt text-secondary') 
                        }} fa-lg"></i>
                    </div>

                    <!-- File Info -->
                    <div class="flex-grow-1 min-width-0">
                        <div class="d-flex align-items-center mb-1">
                            <h6 class="mb-0 text-truncate">{{ $download->file->title }}</h6>
                            <span class="badge bg-light text-dark ms-2">{{ $download->file->file_size_formatted }}</span>
                        </div>

                        <p class="text-muted small mb-1 text-truncate">
                            <i class="fas fa-file me-1"></i> {{ $download->file->file_name }}
                        </p>

                        <div class="d-flex align-items-center small text-muted">
                            <span class="me-3"><i class="fas fa-weight me-1"></i>{{ $download->file->file_size_formatted }}</span>
                            <span class="me-3"><i class="fas fa-clock me-1"></i>{{ $download->downloaded_at->format('d/m/Y H:i') }}</span>
                            <span class="me-3"><i class="fas fa-user me-1"></i>{{ $download->file->uploader->name }}</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="actions ms-3 text-end">
                        <div class="mb-2">
                            <span class="badge px-3 py-1 rounded-pill {{ $download->file->file_category === 'report' ? 'bg-warning text-dark' : 'bg-info text-white' }}">
                                <i class="fas fa-tag me-1"></i>
                                {{ $download->file->file_category === 'report' ? 'Báo cáo' : 'Biểu mẫu' }}
                            </span>
                        </div>
                        <div>
                            @if($download->file->file_category === 'report')
                                <a href="{{ route('customer.files.download_report', $download->file->id) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-download me-1"></i> Tải lại
                                </a>
                            @else
                                <a href="{{ route('customer.files.download_template', $download->file->id) }}" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-download me-1"></i> Tải lại
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="list-group-item py-5 text-center">
                <i class="fas fa-cloud-download-alt fa-4x text-muted mb-3"></i>
                <h5 class="fw-semibold text-dark">Chưa có lịch sử tải nào</h5>
                <p class="text-muted">Lịch sử tải file của bạn sẽ hiển thị ở đây sau khi bạn tải xuống biểu mẫu hoặc báo cáo.</p>
            </div>
            @endforelse
        </div>

        @if($downloads->count() > 0)
        <div class="card-footer bg-white border-0 pt-0">
            <div class="d-flex justify-content-center py-3">
                {{ $downloads->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    /* Shared list-style used on reports/templates */
    .shadow-hover { transition: all 0.3s ease; }
    .shadow-hover:hover { box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.08) !important; }

    .list-item-hover { transition: all 0.2s ease; border-left: 3px solid transparent; }
    .list-item-hover:hover { background-color: #f8f9fa; border-left-color: #4e73df; }

    .file-icon { width:56px; height:56px; display:flex; align-items:center; justify-content:center; border-radius:50%; background:#fff; flex-shrink:0; }
    .file-icon i { font-size:1.4rem; }

    .badge { padding:0.4em 0.8em; font-weight:600; }

    .gradient-text { background: linear-gradient(45deg,#4e73df,#36b9cc); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight:600; }

    .btn-outline-primary, .btn-outline-success { border-radius: 0.5rem; }

    @media (max-width: 768px) {
        .actions { margin-top: 0.75rem; }
    }
</style>
@endpush

@endsection
