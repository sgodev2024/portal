@extends('backend.layouts.master')

@section('content')
    <div class="container">
        <h2>Thêm Khách Hàng</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('customers.store') }}" method="POST">
            @csrf

            <!-- Họ và tên -->
            <div class="form-group mb-3">
                <label for="name">Họ và tên</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
            </div>

            <!-- Email -->
            <div class="form-group mb-3">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}"
                    required>
            </div>

            <!-- Số điện thoại -->
            <div class="form-group mb-3">
                <label for="phone">Số điện thoại</label>
                <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}">
            </div>

            <!-- Mã số thuế -->
            <div class="form-group mb-3">
                <label for="tax_code">Mã số thuế</label>
                <input type="text" name="tax_code" id="tax_code" class="form-control" value="{{ old('tax_code') }}">
            </div>

            <!-- Số CMND/CCCD -->
            <div class="form-group mb-3">
                <label for="identity_number">Số CMND/CCCD</label>
                <input type="text" name="identity_number" id="identity_number" class="form-control"
                    value="{{ old('identity_number') }}">
            </div>

            <!-- Giới tính -->
            <div class="form-group mb-3">
                <label for="gender">Giới tính</label>
                <select name="gender" id="gender" class="form-control">
                    <option value="">Chọn giới tính</option>
                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Nam</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Nữ</option>
                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Khác</option>
                </select>
            </div>

            <!-- Ngày sinh -->
            <div class="form-group mb-3">
                <label for="birthday">Ngày sinh</label>
                <input type="date" name="birthday" id="birthday" class="form-control" value="{{ old('birthday') }}">
            </div>

            <!-- Trạng thái hoạt động -->
            <div class="form-group mb-3">
                <label for="is_active">Trạng thái hoạt động</label>
                <select name="is_active" id="is_active" class="form-control">
                    <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>Hoạt động</option>
                    <option value="0" {{ old('is_active', 1) == 0 ? 'selected' : '' }}>Khóa</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Thêm Khách Hàng</button>
        </form>
    </div>
@endsection
