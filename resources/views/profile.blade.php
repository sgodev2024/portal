@extends('backend.layouts.master')

@section('title', 'Cập nhật thông tin tài khoản')

@section('content')
    <div class="container py-4">
        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Cập nhật thông tin khách hàng</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('customer.profile.update') }}" method="POST">
                    @csrf

                    <!-- Họ và tên -->
                    <div class="form-group mb-3">
                        <label for="name">Họ và tên</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                            class="form-control @error('name') is-invalid @enderror">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="form-group mb-3">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                            class="form-control @error('email') is-invalid @enderror">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Số điện thoại -->
                    <div class="form-group mb-3">
                        <label for="phone">Số điện thoại</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                            class="form-control @error('phone') is-invalid @enderror">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Mã số thuế -->
                    <div class="form-group mb-3">
                        <label for="tax_code">Mã số thuế</label>
                        <input type="text" name="tax_code" id="tax_code" value="{{ old('tax_code', $user->tax_code) }}"
                            class="form-control @error('tax_code') is-invalid @enderror">
                        @error('tax_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Số CMND/CCCD -->
                    <div class="form-group mb-3">
                        <label for="identity_number">Số CMND/CCCD</label>
                        <input type="text" name="identity_number" id="identity_number"
                            value="{{ old('identity_number', $user->identity_number) }}"
                            class="form-control @error('identity_number') is-invalid @enderror">
                        @error('identity_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Giới tính -->
                    <div class="form-group mb-3">
                        <label for="gender">Giới tính</label>
                        <select name="gender" id="gender" class="form-control">
                            <option value="">Chọn giới tính</option>
                            <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Nam
                            </option>
                            <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Nữ
                            </option>
                            <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Khác
                            </option>
                        </select>
                    </div>

                    <!-- Ngày sinh -->
                    <div class="form-group mb-3">
                        <label for="birthday">Ngày sinh</label>
                        <input type="date" name="birthday" id="birthday"
                            value="{{ old('birthday', $user->birthday ? date('Y-m-d', strtotime($user->birthday)) : '') }}"
                            class="form-control">
                    </div>

                    <button type="submit" class="btn btn-success">Cập nhật</button>
                </form>
            </div>
        </div>
    </div>
@endsection
