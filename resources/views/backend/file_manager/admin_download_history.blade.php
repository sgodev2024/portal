@extends('backend.layouts.master')

@section('title', 'Lịch sử Tải - Admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <h4><i class="fas fa-download me-2"></i>Lịch sử Tải Xuống</h4>
            <p class="text-muted">
                <i class="fas fa-history me-1"></i>
                Theo dõi tất cả các lần tải file từ tất cả người dùng
            </p>
        </div>
        <div class="col-lg-4">
            <div class="row g-2">
                <div class="col-6">
                    <div class="card text-center">
                        <div class="card-body p-2">
                            <h6 class="text-muted mb-1">Tổng lần tải</h6>
                            <h3 class="mb-0">{{ $totalDownloads }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card text-center">
                        <div class="card-body p-2">
                            <h6 class="text-muted mb-1">Hôm nay</h6>
                            <h3 class="mb-0">{{ $todayDownloads }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter Card -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <h6 class="card-title mb-3">
                <i class="fas fa-filter me-2"></i>Bộ lọc
            </h6>
            <form method="GET" action="{{ route('admin.file_manager.download_history') }}" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">User</label>
                    <select name="user_id" class="form-select">
                        <option value="">-- Tất cả --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Từ ngày</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Đến ngày</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tìm kiếm</label>
                    <input type="text" name="search" class="form-control" placeholder="Tên file..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i>Lọc
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Downloads Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light border-bottom">
            <h6 class="mb-0">Danh sách tải ({{ $downloads->total() }})</h6>
        </div>

        @if($downloads->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">#</th>
                            <th width="30%">Tên File</th>
                            <th width="15%">User Tải</th>
                            <th width="15%">Người Upload</th>
                            <th width="12%">Kích thước</th>
                            <th width="18%">Ngày tải</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($downloads as $index => $download)
                            <tr>
                                <td>{{ $downloads->firstItem() + $index }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-light text-dark" style="min-width: 35px;">
                                            @php
                                                $ext = strtoupper(pathinfo($download->file->file_name, PATHINFO_EXTENSION));
                                            @endphp
                                            {{ substr($ext, 0, 3) }}
                                        </span>
                                        <div>
                                            <strong class="text-truncate d-block" style="max-width: 250px;" 
                                                   title="{{ $download->file->title }}">
                                                {{ $download->file->title }}
                                            </strong>
                                            <small class="text-muted">
                                                {{ $download->file->file_size_formatted }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-xs bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                             style="width: 32px; height: 32px; font-size: 0.75rem; font-weight: 600;">
                                            {{ substr($download->user->name, 0, 1) }}
                                        </div>
                                        <span>{{ $download->user->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <small>{{ $download->file->uploader->name ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-success">
                                        {{ $download->file->file_size_formatted }}
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <small class="d-block fw-semibold">
                                            {{ $download->downloaded_at->format('d/m/Y') }}
                                        </small>
                                        <small class="text-muted">
                                            {{ $download->downloaded_at->format('H:i') }}
                                        </small>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="text-muted small">
                        Hiển thị <strong>{{ $downloads->firstItem() }}</strong> - <strong>{{ $downloads->lastItem() }}</strong> 
                        trong <strong>{{ $downloads->total() }}</strong> lần tải
                    </div>
                    {{ $downloads->links() }}
                </div>
            </div>
        @else
            <div class="card-body text-center py-5">
                <i class="fas fa-cloud-download-alt fs-1 text-muted mb-3 d-block opacity-50"></i>
                <h5>Không có lần tải nào</h5>
                <p class="text-muted">Không tìm thấy lần tải file phù hợp</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush

@endsection