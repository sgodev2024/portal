@extends('backend.layouts.master')

@section('title', 'Thêm khách hàng')

@section('content')
    <div class="page-inner">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-user-plus me-2"></i> Thêm khách hàng mới
                </h4>
                <a href="{{ route('customers.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>

            <div class="card-body p-4">

                {{-- Hiển thị lỗi --}}
                @if ($errors->any())
                    <div class="alert alert-danger rounded-3 shadow-sm">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li><i class="fas fa-exclamation-circle me-1"></i> {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('customers.store') }}" method="POST" class="needs-validation" novalidate>
                    @csrf

                    <div class="row g-3">
                        {{-- Họ tên --}}
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">Họ và tên <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}"
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
                            <input type="email" name="email" id="email" value="{{ old('email') }}"
                                class="form-control @error('email') is-invalid @enderror" placeholder="example@email.com"
                                required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- SĐT --}}
                        <div class="col-md-6">
                            <label for="account_id" class="form-label fw-semibold">Số điện thoại <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="account_id" id="account_id" value="{{ old('account_id') }}"
                                class="form-control @error('account_id') is-invalid @enderror"
                                placeholder="Nhập số điện thoại" required>
                            @error('account_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Công ty --}}
                        <div class="col-md-6">
                            <label for="company" class="form-label fw-semibold">Công ty <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="company" id="company" value="{{ old('company') }}"
                                class="form-control @error('company') is-invalid @enderror" placeholder="Tên công ty"
                                required>
                            @error('company')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Địa chỉ --}}
                        <div class="col-md-12">
                            <label for="address" class="form-label fw-semibold">Địa chỉ</label>
                            <input type="text" name="address" id="address" value="{{ old('address') }}"
                                class="form-control" placeholder="Nhập địa chỉ khách hàng">
                        </div>

                        {{-- Nhóm khách hàng --}}
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Nhóm khách hàng</label>
                            <div class="border rounded p-3" style="background-color: #f8f9fa;">
                                @if (isset($groups) && $groups->count() > 0)
                                    <div class="row">
                                        @foreach ($groups as $group)
                                            <div class="col-md-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="groups[]"
                                                        value="{{ $group->id }}" id="group_{{ $group->id }}"
                                                        {{ in_array($group->id, old('groups', [])) ? 'checked' : '' }}
                                                        {{ !$group->is_active ? 'disabled' : '' }}>
                                                    <label class="form-check-label" for="group_{{ $group->id }}">
                                                        <strong>{{ $group->name }}</strong>
                                                        @if (!$group->is_active)
                                                            <span class="badge bg-secondary ms-1">Không hoạt động</span>
                                                        @endif
                                                        @if ($group->description)
                                                            <small
                                                                class="text-muted d-block mt-1">{{ $group->description }}</small>
                                                        @endif
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted mb-0">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Chưa có nhóm khách hàng.
                                        <a href="{{ route('admin.customer-groups.create') }}" target="_blank"
                                            class="text-primary">Tạo nhóm mới</a>
                                    </p>
                                @endif
                            </div>
                        </div>
                        {{-- Trạng thái --}}
                        <div class="col-md-12">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                    value="1" checked>
                                <label class="form-check-label fw-semibold" for="is_active">Đang hoạt động</label>
                            </div>
                        </div>
                    </div>

                    {{-- Nút hành động --}}
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-2"></i> Lưu khách hàng
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

        .form-check-input:disabled~.form-check-label {
            opacity: 0.5;
        }
    </style>
@endsection
