@extends('backend.layouts.master')

@section('title', 'Chi tiết khách hàng')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <i class="fas fa-user-circle me-2 text-primary"></i>
            Chi tiết khách hàng
        </h4>
        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body px-4 py-3">
            <div class="row mb-2">
                <div class="col-md-6">
                    <p class="mb-1 text-muted">Họ tên:</p>
                    <p class="fw-semibold">{{ $user->name }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1 text-muted">Tên đăng nhập:</p>
                    <p class="fw-semibold">{{ $user->username }}</p>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-6">
                    <p class="mb-1 text-muted">Email:</p>
                    <p class="fw-semibold">{{ $user->email }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1 text-muted">Số điện thoại:</p>
                    <p class="fw-semibold">{{ $user->phone ?? '—' }}</p>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-6">
                    <p class="mb-1 text-muted">Số CCCD:</p>
                    <p class="fw-semibold">{{ $user->identity_number ?? '—' }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1 text-muted">Mã số thuế:</p>
                    <p class="fw-semibold">{{ $user->tax_code ?? '—' }}</p>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-6">
                    <p class="mb-1 text-muted">Giới tính:</p>
                    <p class="fw-semibold">
                        @if($user->gender === 'male')
                            Nam
                        @elseif($user->gender === 'female')
                            Nữ
                        @else
                            Khác
                        @endif
                    </p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1 text-muted">Ngày sinh:</p>
                    <p class="fw-semibold">{{ $user->birthday ? date('d/m/Y', strtotime($user->birthday)) : '—' }}</p>
                </div>
            </div>

            <hr>

            <div class="row mt-3">
                <div class="col-md-6">
                    <p class="mb-1 text-muted">Trạng thái tài khoản:</p>
                    <span class="badge rounded-pill bg-{{ $user->is_active ? 'success' : 'secondary' }}">
                        {{ $user->is_active ? 'Hoạt động' : 'Ngừng hoạt động' }}
                    </span>
                </div>
                <div class="col-md-6">
                    <p class="mb-1 text-muted">Tình trạng hồ sơ:</p>
                    <span class="badge rounded-pill bg-{{ $user->must_update_profile ? 'info' : 'warning' }}">
                        {{ $user->must_update_profile ? 'Đã cập nhật' : 'Chưa cập nhật' }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
