@extends('backend.layouts.master')

@section('title', 'Chi tiết khách hàng')

@section('content')
    <div class="page-inner">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">
                <i class="fas fa-user-circle me-2 text-primary"></i>
                Chi tiết khách hàng
            </h4>
            <div>
                <a href="{{ route('customers.edit', $user->id) }}" class="btn btn-warning btn-sm me-2">
                    <i class="fas fa-edit me-1"></i> Chỉnh sửa
                </a>
                <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>
        </div>

        <!-- Thông tin khách hàng -->
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body px-4 py-4">

                <!-- Thông tin cá nhân -->
                <h6 class="text-uppercase text-muted mb-3 border-bottom pb-2">
                    <i class="fas fa-id-card me-2 text-secondary"></i> Thông tin cá nhân
                </h6>
                <div class="row mb-3">
                    <div class="col-md-6 mb-3">
                        <p class="mb-1 text-muted small">Họ và tên:</p>
                        <p class="fw-semibold mb-0">{{ $user->name }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <p class="mb-1 text-muted small">Email:</p>
                        <p class="fw-semibold mb-0">
                            <a href="mailto:{{ $user->email }}" class="text-decoration-none">{{ $user->email }}</a>
                        </p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6 mb-3">
                        <p class="mb-1 text-muted small">Số điện thoại:</p>
                        <p class="fw-semibold mb-0">
                            @if($user->account_id)
                                <a href="tel:{{ $user->account_id }}" class="text-decoration-none">{{ $user->account_id }}</a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <p class="mb-1 text-muted small">Công ty:</p>
                        <p class="fw-semibold mb-0">{{ $user->company ?? '—' }}</p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-12 mb-3">
                        <p class="mb-1 text-muted small">Địa chỉ:</p>
                        <p class="fw-semibold mb-0">{{ $user->address ?? '—' }}</p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-12 mb-3">
                        <p class="mb-1 text-muted small">Nhóm khách hàng:</p>
                        @if($user->groups && $user->groups->count() > 0)
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($user->groups as $group)
                                    <span class="badge bg-primary rounded-pill px-3 py-2">
                                        <i class="fas fa-users me-1"></i>
                                        {{ $group->name }}
                                        @if(!$group->is_active)
                                            <i class="fas fa-ban ms-1" title="Nhóm không hoạt động"></i>
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted mb-0">
                                <i class="fas fa-info-circle me-1"></i>
                                Chưa thuộc nhóm nào
                            </p>
                        @endif
                    </div>
                </div>

                <hr>

                <!-- Trạng thái -->
                <h6 class="text-uppercase text-muted mb-3 border-bottom pb-2">
                    <i class="fas fa-user-shield me-2 text-secondary"></i> Trạng thái tài khoản
                </h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <p class="mb-1 text-muted small">Trạng thái tài khoản:</p>
                        <span class="badge rounded-pill bg-{{ $user->is_active ? 'success' : 'secondary' }} px-3 py-2">
                            <i class="fas {{ $user->is_active ? 'fa-check-circle' : 'fa-ban' }} me-1"></i>
                            {{ $user->is_active ? 'Đang hoạt động' : 'Ngừng hoạt động' }}
                        </span>
                    </div>
                    <div class="col-md-6 mb-3">
                        <p class="mb-1 text-muted small">Trạng thái hồ sơ:</p>
                        @if ($user->must_update_profile)
                            <span class="badge rounded-pill bg-warning text-dark px-3 py-2">
                                <i class="fas fa-exclamation-circle me-1"></i> Chưa cập nhật hồ sơ
                            </span>
                        @else
                            <span class="badge rounded-pill bg-info text-white px-3 py-2">
                                <i class="fas fa-check me-1"></i> Đã cập nhật hồ sơ
                            </span>
                        @endif
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6 mb-3">
                        <p class="mb-1 text-muted small">Ngày tạo:</p>
                        <p class="fw-semibold mb-0">
                            <i class="fas fa-calendar-alt me-1 text-muted"></i>
                            {{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : '—' }}
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <p class="mb-1 text-muted small">Cập nhật lần cuối:</p>
                        <p class="fw-semibold mb-0">
                            <i class="fas fa-calendar-check me-1 text-muted"></i>
                            {{ $user->updated_at ? $user->updated_at->format('d/m/Y H:i') : '—' }}
                        </p>
                    </div>
                </div>

                <hr>
            </div>
        </div>
    </div>

    <style>
        .badge {
            font-weight: 500;
            font-size: 0.85rem;
        }

        .small {
            font-size: 0.875rem;
        }

        .gap-2 {
            gap: 0.5rem !important;
        }
    </style>
@endsection
