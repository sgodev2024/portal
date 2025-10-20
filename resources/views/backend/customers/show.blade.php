@extends('backend.layouts.master')

@section('title', 'Chi tiết khách hàng')

@section('content')
    <div class="container py-4">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                <i class="fas fa-user-circle me-2 text-primary"></i>
                Chi tiết khách hàng
            </h4>
            <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
            </a>
        </div>

        <!-- Thông tin khách hàng -->
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body px-4 py-3">

                <!-- Thông tin cá nhân -->
                <h6 class="text-uppercase text-muted mb-3">
                    <i class="fas fa-id-card me-2 text-secondary"></i> Thông tin cá nhân
                </h6>
                <div class="row mb-3">
                    <div class="col-md-6 mb-2">
                        <p class="mb-1 text-muted">Họ và tên:</p>
                        <p class="fw-semibold">{{ $user->name }}</p>
                    </div>
                    <div class="col-md-6 mb-2">
                        <p class="mb-1 text-muted">Email công ty:</p>
                        <p class="fw-semibold">{{ $user->email }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6 mb-2">
                        <p class="mb-1 text-muted">Số điện thoại:</p>
                        <p class="fw-semibold">{{ $user->account_id ?? '—' }}</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6 mb-2">
                        <p class="mb-1 text-muted">Công ty</p>
                        <p class="fw-semibold">{{ $user->company ?? '—' }}</p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-2">
                        <p class="mb-1 text-muted">Địa chỉ:</p>
                        <p class="fw-semibold">{{ $user->address ?? '—' }}</p>
                    </div>
                </div>
                {{-- <div class="row mb-4">
                <div class="col-md-6 mb-2">
                    <p class="mb-1 text-muted">Nhóm</p>
                    <p class="fw-semibold">{{ $user-> ?? '—' }}</p>
                </div>
            </div> --}}
                <hr>

                <!-- Trạng thái -->
                <h6 class="text-uppercase text-muted mb-3">
                    <i class="fas fa-user-shield me-2 text-secondary"></i> Trạng thái tài khoản
                </h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <p class="mb-1 text-muted">Tài khoản:</p>
                        <span class="badge rounded-pill bg-{{ $user->is_active ? 'success' : 'secondary' }}">
                            <i class="fas {{ $user->is_active ? 'fa-check-circle' : 'fa-ban' }} me-1"></i>
                            {{ $user->is_active ? 'Hoạt động' : 'Ngừng hoạt động' }}
                        </span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <p class="mb-1 text-muted">Hồ sơ cá nhân:</p>
                        @if ($user->must_update_profile)
                            <span class="badge rounded-pill bg-warning text-dark">
                                <i class="fas fa-exclamation-circle me-1"></i> Chưa cập nhật hồ sơ
                            </span>
                        @else
                            <span class="badge rounded-pill bg-info">
                                <i class="fas fa-check me-1"></i> Đã cập nhật hồ sơ
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
