@extends('backend.layouts.master')

@section('title', 'Biểu mẫu')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="gradient-text mb-1">Biểu mẫu</h4>
            <p class="text-muted small mb-0">
                <i class="fas fa-file-alt me-1"></i>
                Danh sách biểu mẫu có thể tải về
            </p>
        </div>

        <div class="d-flex gap-3 align-items-center">
            @if($categories->count() > 0)
            <div class="btn-group filter-group">
                <a href="{{ route('customer.files.templates') }}" class="btn {{ request()->get('category') == '' ? 'btn-primary' : 'btn-outline-primary' }}">Tất cả</a>
                @foreach($categories as $category)
                <a href="{{ route('customer.files.templates', ['category' => $category]) }}" class="btn {{ request()->get('category') == $category ? 'btn-primary' : 'btn-outline-primary' }}">{{ $category }}</a>
                @endforeach
            </div>
            @endif

            <form method="GET" class="d-flex">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm biểu mẫu..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
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
                        <div class="d-flex align-items-center mb-1">
                            <h6 class="mb-0 text-truncate file-title">{{ $file->title }}</h6>
                            <div class="ms-2 d-flex gap-2">
                                <span class="badge bg-primary-soft">{{ strtoupper($file->file_type) }}</span>
                                <span class="badge bg-success-soft">{{ $file->file_size_formatted }}</span>
                            </div>
                        </div>
                        
                        @if($file->description)
                        <p class="text-muted small mb-1 text-truncate">{{ $file->description }}</p>
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
                                {{ $file->created_at->format('d/m/Y') }}
                            </span>
                            <span class="small text-success d-flex align-items-center">
                                <i class="fas fa-download me-1 opacity-50"></i>
                                {{ $file->download_count }} lượt
                            </span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="actions ms-3">
                        <div class="d-grid gap-2">
                            <a href="{{ route('customer.files.download_template', $file->id) }}" 
                               class="btn btn-primary btn-sm">
                                <i class="fas fa-download me-1"></i> Tải xuống
                            </a>
                        </div>
                        @if(in_array($file->id, $myDownloads))
                        <div class="mt-2 text-center">
                            <span class="badge bg-success">Đã tải</span>
                        </div>
                        @endif
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
                    <h5 class="mb-2">Chưa có biểu mẫu nào</h5>
                    <p class="text-muted mb-4">Bạn sẽ nhận được thông báo khi có biểu mẫu mới</p>
                </div>
            </div>
            @endforelse
        </div>

        @if($files->count() > 0)
        <div class="card-footer bg-white border-0 pt-0">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Hiển thị {{ $files->firstItem() ?? 0 }}-{{ $files->lastItem() ?? 0 }} 
                    trong số {{ $files->total() }} biểu mẫu
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
    /* Reuse report-like styles for templates */
    .shadow-hover { transition: all 0.3s ease; }
    .shadow-hover:hover { box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.08) !important; }

    .list-item-hover { transition: all 0.2s ease; border-left: 3px solid transparent; }
    .list-item-hover:hover { background-color: #f8f9fa; border-left-color: #4e73df; }

    .file-icon { width:48px; height:48px; display:flex; align-items:center; justify-content:center; border-radius:10px; background:linear-gradient(45deg,#4e73df20,#36b9cc20); color:#4e73df; flex-shrink:0; }
    .file-icon i { font-size:1.4rem; }

    .file-title { color:#344767; font-weight:600; }

    .badge { padding:0.4em 0.8em; font-weight:600; }
    .bg-primary-soft { background-color: #4e73df20; color:#4e73df; }
    .bg-success-soft { background-color: #1cc88a20; color:#1cc88a; }

    .avatar-xs { width:24px; height:24px; }
    .avatar-initial { width:100%; height:100%; display:flex; align-items:center; justify-content:center; font-size:0.75rem; }

    .btn-primary { background: linear-gradient(45deg,#4e73df,#36b9cc); border:none; }

    .empty-state { padding:2rem 0; }
    .empty-icon-lg { width:80px; height:80px; margin:0 auto; display:flex; align-items:center; justify-content:center; border-radius:50%; background:linear-gradient(45deg,#4e73df10,#36b9cc10); color:#4e73df; font-size:2rem; }

    @media (max-width: 768px) {
        .actions { margin-top:1rem; }
        .d-grid { width:100%; }
    }
</style>
@endpush

@endsection
