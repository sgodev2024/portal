@extends('backend.layouts.master')

@section('title', 'Sửa khách hàng')

@section('content')
    <div class="page-inner">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-user-edit me-2"></i> Cập nhật thông tin khách hàng
                </h4>
                <a href="{{ route('customers.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>

            <div class="card-body p-4">

                {{-- Thông báo lỗi --}}
                @if ($errors->any())
                    <div class="alert alert-danger rounded-3 shadow-sm">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li><i class="fas fa-exclamation-circle me-1"></i> {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('customers.update', $user->id) }}" method="POST" class="needs-validation"
                    novalidate>
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        {{-- Họ tên --}}
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">Họ và tên <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                                class="form-control @error('name') is-invalid @enderror"
                                placeholder="Nhập họ tên khách hàng" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold">Email <span
                                    class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                                class="form-control @error('email') is-invalid @enderror" placeholder="example@email.com"
                                required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- SĐT --}}
                        <div class="col-md-6">
                            <label for="account_id" class="form-label fw-semibold">Số điện thoại</label>
                            <input type="text" name="account_id" id="account_id"
                                value="{{ old('account_id', $user->account_id ?? '') }}" class="form-control"
                                placeholder="Nhập số điện thoại">
                        </div>

                        {{-- Công ty --}}
                        <div class="col-md-6">
                            <label for="company" class="form-label fw-semibold">Công ty</label>
                            <input type="text" name="company" id="company"
                                value="{{ old('company', $user->company ?? '') }}" class="form-control"
                                placeholder="Tên công ty (nếu có)">
                        </div>

                        {{-- Địa chỉ --}}
                        <div class="col-md-6">
                            <label for="address" class="form-label fw-semibold">Địa chỉ</label>
                            <input type="text" name="address" id="address"
                                value="{{ old('address', $user->address ?? '') }}" class="form-control"
                                placeholder="Nhập địa chỉ khách hàng">
                        </div>

                        {{-- Nhóm --}}
                        <div class="col-md-4">
                            <label for="group" class="form-label fw-semibold">Nhóm khách hàng</label>
                            <input type="text" name="group" id="group"
                                value="{{ old('group', $user->group ?? '') }}" class="form-control"
                                placeholder="VD: VIP, Mới, Thường...">
                        </div>

                        {{-- Trạng thái --}}
                        <div class="col-md-2 d-flex align-items-center">
                            <div class="form-check form-switch mt-3">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                    value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_active">
                                    {{ old('is_active', $user->is_active) ? 'Đang hoạt động' : 'Đang bị khóa' }}
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Nút hành động --}}
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-2"></i> Cập nhật khách hàng
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Style bổ sung nhẹ --}}
    <style>
        .bg-gradient-primary {
            background: linear-gradient(90deg, #0052cc, #007bff);
        }

        .form-label {
            font-size: 14px;
            color: #333;
        }

        input.form-control {
            border-radius: 0.5rem;
            padding: 0.6rem 0.8rem;
        }

        .btn-primary {
            border-radius: 0.5rem;
        }
    </style>
@endsection
