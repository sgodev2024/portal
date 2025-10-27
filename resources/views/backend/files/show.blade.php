@extends('backend.layouts.master')

@section('title', 'Chi tiết File')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-2">
                  <div class="card-tools">
                        <a href="{{ route('admin.files.index', $file->id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                <h3 class="mb-0">Chi tiết File</h3>
            </div>
            <p class="text-muted small mb-0 mt-1">Xem chi tiết thông tin và lịch sử của file</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row g-4">
        <!-- Main Info Column -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-bottom bg-transparent py-3">
                    <div class="d-flex align-items-center">
                        <div class="file-icon me-3">
                            <i class="fas fa-file-{{ $file->file_type === 'pdf' ? 'pdf' : 'alt' }}"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1">{{ $file->title }}</h5>
                            <p class="text-muted small mb-0">{{ $file->file_name }}</p>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-4">
                    <div class="info-grid">
                        <!-- File Type & Status -->
                        <div class="info-section">
                            <div class="d-flex align-items-center gap-3">
                                <div class="info-badge">
                                    <span class="badge bg-{{ $file->file_category === 'report' ? 'primary' : 'success' }}">
                                        {{ $file->file_category === 'report' ? 'Báo cáo' : 'Biểu mẫu' }}
                                    </span>
                                </div>
                                <div class="info-badge">
                                    <span class="badge bg-{{ $file->is_active ? 'success' : 'secondary' }}">
                                        {{ $file->is_active ? 'Hoạt động' : 'Không hoạt động' }}
                                    </span>
                                </div>
                                <div class="info-badge">
                                    <span class="badge bg-info">{{ $file->file_size_formatted }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        @if($file->description)
                        <div class="info-section">
                            <h6 class="section-title">Mô tả</h6>
                            <p class="mb-0">{{ $file->description }}</p>
                        </div>
                        @endif

                        <!-- Meta Information -->
                        <div class="info-section">
                            <h6 class="section-title">Thông tin chi tiết</h6>
                            <div class="meta-grid">
                                <div class="meta-item">
                                    <div class="meta-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="meta-content">
                                        <span class="meta-label">Người tạo</span>
                                        <span class="meta-value">{{ $file->uploader->name ?? 'N/A' }}</span>
                                    </div>
                                </div>

                                <div class="meta-item">
                                    <div class="meta-icon">
                                        <i class="fas fa-calendar"></i>
                                    </div>
                                    <div class="meta-content">
                                        <span class="meta-label">Ngày tạo</span>
                                        <span class="meta-value">{{ $file->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>

                                @if($file->file_category === 'report')
                                <div class="meta-item">
                                    <div class="meta-icon">
                                        <i class="fas fa-paper-plane"></i>
                                    </div>
                                    <div class="meta-content">
                                        <span class="meta-label">Người gửi</span>
                                        <span class="meta-value">{{ $file->sender->name ?? 'N/A' }}</span>
                                    </div>
                                </div>

                                <div class="meta-item">
                                    <div class="meta-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="meta-content">
                                        <span class="meta-label">Ngày gửi</span>
                                        <span class="meta-value">
                                            @if($file->sent_at)
                                                {{ $file->sent_at->format('d/m/Y H:i') }}
                                            @else
                                                <span class="text-muted">Chưa gửi</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                @endif

                                <div class="meta-item">
                                    <div class="meta-icon">
                                        <i class="fas fa-download"></i>
                                    </div>
                                    <div class="meta-content">
                                        <span class="meta-label">Lượt tải</span>
                                        <span class="meta-value">{{ $file->download_count }} lượt</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                        
                </div>
            </div>

            <!-- Download History -->
            @if($file->downloads->count() > 0)
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-transparent py-3">
                    <h6 class="card-title mb-0">Lịch sử tải xuống</h6>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3">Người tải</th>
                                <th class="py-3">IP Address</th>
                                <th class="py-3">Thời gian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($file->downloads as $download)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <span>{{ $download->user->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <code class="small">{{ $download->ip_address }}</code>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span>{{ $download->downloaded_at->format('d/m/Y') }}</span>
                                        <small class="text-muted">{{ $download->downloaded_at->format('H:i') }}</small>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <!-- Side Column -->
        <div class="col-lg-4">
            <!-- Actions Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent py-3">
                    <h6 class="card-title mb-0">Thao tác</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <a href="{{ route('admin.files.download', $file->id) }}" 
                           class="btn btn-primary">
                            <i class="fas fa-download me-2"></i> Tải xuống
                        </a>
                        <a href="{{ route('admin.files.edit', $file->id) }}" 
                           class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i> Chỉnh sửa
                        </a>
                        @if($file->file_category === 'report' && !empty($file->recipients))
                        <form method="POST" action="{{ route('admin.files.resend', $file->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-paper-plane me-2"></i> Gửi lại
                            </button>
                        </form>
                        @endif
                        <form method="POST" action="{{ route('admin.files.destroy', $file->id) }}" 
                              onsubmit="return confirm('Bạn có chắc muốn xóa file này?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-trash me-2"></i> Xóa
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Recipients Card -->
            @if($file->file_category === 'report' && !empty($file->recipients))
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-transparent py-3">
                    <h6 class="card-title mb-0">
                        Người nhận
                        <span class="badge bg-primary ms-2">{{ count($file->recipients) }}</span>
                    </h6>
                </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @php
                                $recipientUsers = !empty($file->recipients) ? \App\Models\User::whereIn('id', $file->recipients)->get() : collect();
                            @endphp
                            @foreach($recipientUsers as $recipient)
                            <div class="list-group-item border-0 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-3">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div class="flex-grow-1 text-break">
                                        {{ $recipient->email }}
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Common */
    .shadow-hover {
        transition: all 0.3s ease;
    }
    
    .shadow-hover:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
    }

    /* Header & Typography */
    .section-title {
        color: #344767;
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    /* File Icon */
    .file-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background-color: #e8f0fe;
        color: #4e73df;
        font-size: 1.5rem;
    }

    /* Info Sections */
    .info-grid {
        display: grid;
        gap: 2rem;
    }

    .info-section {
        padding: 0.5rem 0;
    }

    .info-badge .badge {
        font-size: 0.75rem;
        padding: 0.5em 1em;
        font-weight: 500;
    }

    /* Meta Grid */
    .meta-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1.5rem;
    }

    .meta-item {
        display: flex;
        align-items: start;
        gap: 1rem;
    }

    .meta-icon {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background-color: #f8f9fa;
        color: #4e73df;
    }

    .meta-content {
        display: flex;
        flex-direction: column;
    }

    .meta-label {
        font-size: 0.75rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }

    .meta-value {
        font-weight: 500;
        color: #344767;
    }

    /* Avatar */
    .avatar-sm {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background-color: #f8f9fa;
        color: #6c757d;
    }

    /* Table */
    .table th {
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
    }

    /* Buttons */
    .btn {
        padding: 0.6rem 1.2rem;
        font-weight: 500;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .meta-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
    }
</style>
@endpush
                </div>
            </div>
        </div>
    </div>
</div>
@endsection