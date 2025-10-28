@extends('backend.layouts.master')

@section('title', 'Chỉnh sửa khách hàng')

@section('content')
    <div class="page-inner">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-user-edit me-2"></i> Chỉnh sửa: {{ $user->name }}
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

                        {{-- SĐT (đổi từ account_id sang phone) --}}
                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-semibold">Số điện thoại <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                                class="form-control @error('phone') is-invalid @enderror" placeholder="Nhập số điện thoại"
                                required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- Công ty --}}
                        <div class="col-md-6">
                            <label for="company" class="form-label fw-semibold">Công ty</label>
                            <input type="text" name="company" id="company"
                                value="{{ old('company', $user->company) }}"
                                class="form-control @error('company') is-invalid @enderror" placeholder="Tên công ty">
                            @error('company')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Địa chỉ --}}
                        <div class="col-md-6">
                            <label for="address" class="form-label fw-semibold">Địa chỉ</label>
                            <input type="text" name="address" id="address"
                                value="{{ old('address', $user->address) }}" class="form-control"
                                placeholder="Nhập địa chỉ khách hàng">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Nhóm khách hàng</label>
                            @if (isset($groups) && $groups->count() > 0)
                                <select class="form-select" name="group_id" id="customer_group">
                                    <option value="">-- Chọn nhóm khách hàng --</option>
                                    @foreach ($groups as $group)
                                        <option value="{{ $group->id }}"
                                            {{ old('group_id', $user->groups->first()?->id) == $group->id ? 'selected' : '' }}
                                            {{ !$group->is_active ? 'disabled' : '' }}>
                                            {{ $group->name }}
                                            @if (!$group->is_active)
                                                (Không hoạt động)
                                            @endif
                                            @if ($group->description)
                                                - {{ $group->description }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <div class="border rounded p-3" style="background-color: #f8f9fa;">
                                    <p class="text-muted mb-0">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Chưa có nhóm khách hàng.
                                        <a href="{{ route('admin.customer-groups.create') }}" target="_blank"
                                            class="text-primary">Tạo nhóm mới</a>
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Nút hành động --}}
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('customers.index') }}" class="btn btn-secondary px-4">
                            <i class="fas fa-times me-2"></i> Hủy
                        </a>
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

        .form-check-input:disabled~.form-check-label {
            opacity: 0.5;
        }
    </style>
@endsection
