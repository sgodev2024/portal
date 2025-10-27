@extends('backend.layouts.master')

@section('title', 'Nhóm Khách hàng của tôi')

@section('content')
<div class="container-fluid py-4">

    <!-- Header -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h2 class="fw-semibold mb-1 text-primary">
                <i class="bi bi-collection me-2"></i>Nhóm Khách hàng
            </h2>
            <p class="text-muted mb-0">Nhận và quản lý các nhóm khách hàng được phân công hoặc đang trống</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <div class="d-inline-flex align-items-center bg-light rounded px-3 py-2 shadow-sm small">
                <span class="me-3 text-primary">
                    <i class="bi bi-star-fill text-warning me-1"></i> {{ $myGroups->count() }} nhóm của tôi
                </span>
                <span class="text-success">
                    <i class="bi bi-plus-circle me-1"></i> {{ $availableGroups->count() }} nhóm khả dụng
                </span>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Nhóm đã nhận -->
    <div class="mb-5">
        <div class="d-flex align-items-center mb-3">
            <i class="bi bi-person-badge-fill text-warning me-2 fs-5"></i>
            <h5 class="fw-semibold mb-0">Nhóm của tôi ({{ $myGroups->count() }})</h5>
        </div>

        @if ($myGroups->isEmpty())
            <div class="alert alert-info border-0 shadow-sm">
                <i class="bi bi-info-circle me-2"></i>Bạn chưa nhận nhóm nào. Hãy chọn nhóm bên dưới!
            </div>
        @else
            <div class="row g-4">
                @foreach ($myGroups as $group)
                    <div class="col-md-6 col-xl-4">
                        <div class="card shadow-sm border-0 rounded-3 h-100 hover-card">
                            <div class="card-header bg-warning bg-opacity-10 border-0 py-3">
                                <h6 class="mb-0 fw-semibold text-dark">
                                    <i class="bi bi-collection text-warning me-2"></i>{{ $group->name }}
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small mb-3">{{ $group->description ?? 'Không có mô tả.' }}</p>

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge bg-light text-dark border">
                                        <i class="bi bi-people me-1"></i>{{ $group->users->count() }} khách hàng
                                    </span>
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="bi bi-person-check me-1"></i>Bạn phụ trách
                                    </span>
                                </div>

                                <form action="{{ route('staff.groups.leave', $group->id) }}" 
                                      method="POST"
                                      onsubmit="return confirm('Bạn có chắc muốn rời nhóm này?\n\nTất cả tickets sẽ được bỏ gán!')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                        <i class="bi bi-box-arrow-right me-1"></i>Rời nhóm
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Nhóm khả dụng -->
    <div>
        <div class="d-flex align-items-center mb-3">
            <i class="bi bi-plus-circle-fill text-success me-2 fs-5"></i>
            <h5 class="fw-semibold mb-0">Nhóm khả dụng ({{ $availableGroups->count() }})</h5>
        </div>

        @if ($availableGroups->isEmpty())
            <div class="alert alert-secondary border-0 shadow-sm">
                <i class="bi bi-inbox me-2"></i>Không có nhóm nào đang trống
            </div>
        @else
            <div class="row g-4">
                @foreach ($availableGroups as $group)
                    <div class="col-md-6 col-xl-4">
                        <div class="card shadow-sm border-0 rounded-3 h-100 hover-card">
                            <div class="card-header bg-light border-0 py-3">
                                <h6 class="mb-0 fw-semibold text-dark">
                                    <i class="bi bi-collection text-success me-2"></i>{{ $group->name }}
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small mb-3">{{ $group->description ?? 'Không có mô tả.' }}</p>

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge bg-light text-dark border">
                                        <i class="bi bi-people me-1"></i>{{ $group->users->count() }} khách hàng
                                    </span>
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="bi bi-check-circle me-1"></i>Trống
                                    </span>
                                </div>

                                <form action="{{ route('staff.groups.claim', $group->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm w-100">
                                        <i class="bi bi-hand-thumbs-up me-1"></i>Nhận nhóm này
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<style>
    .hover-card {
        transition: all 0.25s ease;
    }
    .hover-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.08);
    }
    .badge {
        font-weight: 500;
        border-radius: 6px;
    }
    .btn-sm {
        font-size: 0.85rem;
        border-radius: 6px;
    }
    .alert {
        border-radius: 8px;
    }
    .bg-success-subtle {
        background-color: rgba(25,135,84,0.1) !important;
    }
    .bg-warning.bg-opacity-10 {
        background-color: rgba(255,193,7,0.1) !important;
    }
</style>
@endsection
