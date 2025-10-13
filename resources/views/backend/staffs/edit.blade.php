@extends('backend.layouts.master')

@section('title', 'Sửa nhân viên')

@section('content')
    <div class="page-inner">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Cập nhật thông tin nhân viên</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.staffs.update', $staff->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Họ và tên -->
                    <div class="form-group mb-3">
                        <label for="name">Họ và tên</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $staff->name) }}"
                            class="form-control @error('name') is-invalid @enderror">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="form-group mb-3">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $staff->email) }}"
                            class="form-control @error('email') is-invalid @enderror">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Số điện thoại -->
                    <div class="form-group mb-3">
                        <label for="phone">Số điện thoại</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $staff->phone) }}"
                            class="form-control @error('phone') is-invalid @enderror">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Giới tính -->
                    <div class="form-group mb-3">
                        <label for="gender">Giới tính</label>
                        <select name="gender" id="gender" class="form-control">
                            <option value="">Chọn giới tính</option>
                            <option value="male" {{ old('gender', $staff->gender) == 'male' ? 'selected' : '' }}>Nam
                            </option>
                            <option value="female" {{ old('gender', $staff->gender) == 'female' ? 'selected' : '' }}>Nữ
                            </option>
                            <option value="other" {{ old('gender', $staff->gender) == 'other' ? 'selected' : '' }}>Khác
                            </option>
                        </select>
                    </div>

                    <!-- Ngày sinh -->
                    <div class="form-group mb-3">
                        <label for="birthday">Ngày sinh</label>
                        <input type="date" name="birthday" id="birthday"
                            value="{{ old('birthday', $staff->birthday ? date('Y-m-d', strtotime($staff->birthday)) : '') }}"
                            class="form-control">
                    </div>

                    <!-- Trạng thái hoạt động -->
                    <div class="form-check form-switch mb-3">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                            {{ old('is_active', $staff->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            {{ old('is_active', $staff->is_active) ? 'Tài khoản đang hoạt động' : 'Tài khoản bị khóa' }}
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                    <a href="{{ route('admin.staffs.index') }}" class="btn btn-secondary">Hủy</a>
                </form>
            </div>
        </div>
    </div>
@endsection
