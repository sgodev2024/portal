@extends('backend.layouts.master')

@section('title', 'Quản lý Báo cáo')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Quản lý Báo cáo</h3>
        <div>
            <a href="{{ route('admin.files.create_report') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm Báo cáo
            </a>
        </div>
    </div>

    <!-- Card chứa bảng -->
    <div class="card shadow-sm border-0">
        <div class="toolbar-section border-bottom p-3 bg-light">
            <div class="row align-items-center">
                <div class="col-md-6 mb-2 mb-md-0">
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.files.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-list"></i> Tất cả File
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <form method="GET" class="search-box">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Tìm kiếm báo cáo..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-outline-secondary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="p-3">
            @if($files->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tiêu đề</th>
                                <th>File</th>
                                <th>Người nhận</th>
                                <th>Người gửi</th>
                                <th>Ngày gửi</th>
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
                                    <div class="d-flex flex-column">
                                        <div>
                                            <i class="fas fa-file-{{ $file->file_type === 'pdf' ? 'pdf' : 'alt' }} text-primary"></i>
                                            {{ $file->file_name }}
                                        </div>
                                        <small class="text-muted">{{ $file->file_size_formatted }}</small>
                                    </div>
                                </td>
                                <td>
                                    @if($file->recipients && count($file->recipients) > 0)
                                        @php
                                            $recipientEmails = \App\Models\User::whereIn('id', $file->recipients)->pluck('email')->toArray();
                                        @endphp
                                        <span class="badge bg-info">{{ count($file->recipients) }} người nhận</span>
                                        <br>
                                        <small class="text-muted">
                                            {{ implode(', ', array_slice($recipientEmails, 0, 2)) }}
                                            {{ count($file->recipients) > 2 ? '...' : '' }}
                                        </small>
                                    @else
                                        <span class="badge bg-secondary">Chưa gửi</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span>{{ $file->sender->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        @if($file->sent_at)
                                            <span>{{ $file->sent_at->format('d/m/Y') }}</span>
                                            <small class="text-muted">{{ $file->sent_at->format('H:i') }}</small>
                                        @else
                                            <span class="text-muted">Chưa gửi</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('admin.files.download', $file->id) }}" 
                                           class="btn btn-sm btn-outline-primary" title="Tải xuống">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <a href="{{ route('admin.files.show', $file->id) }}" 
                                           class="btn btn-sm btn-outline-info" title="Chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(!empty($file->recipients))
                                            <form method="POST" action="{{ route('admin.files.resend', $file->id) }}" 
                                                  class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success" 
                                                        title="Gửi lại">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            </form>
                                        @endif
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
                        trong tổng số <strong>{{ $files->total() }}</strong> báo cáo
                    </div>
                    <div>
                        {{ $files->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                    <h5>Chưa có báo cáo nào</h5>
                    <p class="text-muted">Hãy thêm file báo cáo để gửi cho khách hàng.</p>
                </div>
            @endif
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

    @media (max-width: 768px) {
        .search-box input {
            width: 100%;
        }
    }
</style>
@endpush
@endsection