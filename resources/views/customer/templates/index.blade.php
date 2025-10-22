@extends('backend.layouts.master')

@section('title', 'Biểu Mẫu Tải Về')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h3><i class="fas fa-file-download me-2"></i>Biểu Mẫu Tải Về</h3>
            <p class="text-muted">Tải xuống các biểu mẫu, hợp đồng và tài liệu cần thiết</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('customer.templates.my_downloads') }}" class="btn btn-outline-info">
                <i class="fas fa-history me-1"></i>Lịch sử tải về
            </a>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <select name="category" class="form-select" onchange="this.form.submit()">
                        <option value="">Tất cả danh mục</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                                {{ $cat }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8">
                    <div class="input-group">
                        <input type="text" 
                               name="search" 
                               class="form-control" 
                               placeholder="Tìm kiếm biểu mẫu..."
                               value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                        @if(request('search') || request('category'))
                            <a href="{{ route('customer.templates.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Templates Grid -->
    @if($templates->count() > 0)
        <div class="row">
            @foreach($templates as $template)
                @php
                    $downloaded = in_array($template->id, $myDownloads);
                @endphp
                <div class="col-md-4 col-lg-3 mb-4">
                    <div class="card h-100 shadow-sm hover-card">
                        @if($template->category)
                            <div class="card-header bg-light py-2">
                                <small class="text-muted">
                                    <i class="fas fa-tag me-1"></i>{{ $template->category }}
                                </small>
                            </div>
                        @endif
                        <div class="card-body">
                            <div class="text-center mb-3">
                                @php
                                    $iconMap = [
                                        'pdf' => 'fas fa-file-pdf text-danger',
                                        'doc' => 'fas fa-file-word text-primary',
                                        'docx' => 'fas fa-file-word text-primary',
                                        'xls' => 'fas fa-file-excel text-success',
                                        'xlsx' => 'fas fa-file-excel text-success',
                                    ];
                                    $icon = $iconMap[$template->file_type] ?? 'fas fa-file text-muted';
                                @endphp
                                <i class="{{ $icon }}" style="font-size: 3.5rem;"></i>
                            </div>
                            
                            <h6 class="card-title">{{ $template->title }}</h6>
                            
                            @if($template->description)
                                <p class="card-text text-muted small">
                                    {{ Str::limit($template->description, 80) }}
                                </p>
                            @endif

                            <hr>

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-hdd me-1"></i>{{ $template->file_size_formatted }}
                                </small>
                                <small class="text-muted">
                                    <i class="fas fa-download me-1"></i>{{ $template->download_count }}
                                </small>
                            </div>

                            @if($downloaded)
                                <div class="alert alert-success py-1 mb-2">
                                    <small><i class="fas fa-check-circle me-1"></i>Đã tải</small>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer bg-white">
                            <div class="d-grid">
                                <a href="{{ route('customer.templates.download', $template->id) }}" 
                                   class="btn btn-primary">
                                    <i class="fas fa-download me-1"></i>Tải xuống
                                </a>
                            </div>
                            <div class="text-center mt-2">
                                <small class="text-muted">
                                    <i class="far fa-calendar-alt me-1"></i>
                                    {{ $template->created_at->format('d/m/Y') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted">
                Hiển thị {{ $templates->firstItem() }} - {{ $templates->lastItem() }} / {{ $templates->total() }} biểu mẫu
            </div>
            <div>
                {{ $templates->appends(request()->query())->links() }}
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-inbox text-muted" style="font-size: 4rem;"></i>
            <h4 class="text-muted mt-3">Không tìm thấy biểu mẫu nào</h4>
            <p class="text-muted">Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm</p>
        </div>
    @endif
</div>

@push('styles')
<style>
.hover-card {
    transition: all 0.3s;
}

.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
}
</style>
@endpush
@endsection