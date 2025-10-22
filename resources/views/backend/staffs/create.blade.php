@extends('backend.layouts.master')

@section('title', 'Thêm nhân viên')

@section('content')
    <div class="page-inner">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Thêm nhân viên mới</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.staffs.store') }}" method="POST">
                    @csrf

                    <!-- Họ tên -->
                    <div class="form-group mb-3">
                        <label for="name">Họ tên <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                            class="form-control @error('name') is-invalid @enderror" placeholder="Nhập họ tên nhân viên"
                            required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email công ty -->
                    <div class="form-group mb-3">
                        <label for="email">Email công ty <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror" placeholder="email@company.com"
                            required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Số điện thoại -->
                    <div class="form-group mb-3">
                        <label for="account_id">Số điện thoại <span class="text-danger">*</span></label>
                        <input type="number" name="account_id" id="account_id" value="{{ old('account_id') }}"
                            class="form-control @error('account_id') is-invalid @enderror" placeholder="0xxxxxxxxx" required>
                        @error('account_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Phòng ban -->
                    <div class="form-group mb-3">
                        <label for="department">Phòng ban <span class="text-danger">*</span></label>
                        <input type="text" name="department" id="department" value="{{ old('department') }}"
                            class="form-control @error('department') is-invalid @enderror" placeholder="Nhập phòng ban"
                            required>
                        @error('department')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Chức vụ -->
                    <div class="form-group mb-3">
                        <label for="position">Chức vụ <span class="text-danger">*</span></label>
                        <input type="text" name="position" id="position" value="{{ old('position') }}"
                            class="form-control @error('position') is-invalid @enderror" placeholder="Nhập chức vụ"
                            required>
                        @error('position')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>


                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Lưu
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
