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
                        <label for="name">Họ tên <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $staff->name) }}"
                            class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email công ty -->
                    <div class="form-group mb-3">
                        <label for="email">Email công ty <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email', $staff->email) }}"
                            class="form-control @error('email') is-invalid @enderror" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Số điện thoại -->
                    <div class="form-group mb-3">
                        <label for="phone">Số điện thoại <span class="text-danger">*</span></label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $staff->phone) }}"
                            class="form-control @error('phone') is-invalid @enderror" required>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Phòng ban -->
                    <div class="form-group mb-3">
                        <label for="department">Phòng ban <span class="text-danger">*</span></label>
                        <input type="text" name="department" id="department"
                            value="{{ old('department', $staff->department) }}"
                            class="form-control @error('department') is-invalid @enderror" required>
                        @error('department')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Chức vụ -->
                    <div class="form-group mb-3">
                        <label for="position">Chức vụ <span class="text-danger">*</span></label>
                        <input type="text" name="position" id="position" value="{{ old('position', $staff->position) }}"
                            class="form-control @error('position') is-invalid @enderror" required>
                        @error('position')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Vai trò -->
                    <div class="form-group mb-3">
                        <label for="role">Vai trò <span class="text-danger">*</span></label>
                        <select name="role" id="role" class="form-control @error('role') is-invalid @enderror" required>
                            <option value="">-- Chọn vai trò --</option>
                            <option value="1" {{ old('role', $staff->role) == 1 ? 'selected' : '' }}>Admin</option>
                            <option value="2" {{ old('role', $staff->role) == 2 ? 'selected' : '' }}>Nhân viên</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>


                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Cập nhật
                        </button>
                        <a href="{{ route('admin.staffs.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
