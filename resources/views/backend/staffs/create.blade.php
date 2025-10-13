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

                <!-- Họ và tên -->
                <div class="form-group mb-3">
                    <label for="name">Họ và tên</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                        class="form-control @error('name') is-invalid @enderror" placeholder="Nhập họ tên nhân viên">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email -->
                <div class="form-group mb-3">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                        class="form-control @error('email') is-invalid @enderror" placeholder="email@example.com">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Số điện thoại -->
                <div class="form-group mb-3">
                    <label for="phone">Số điện thoại</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                        class="form-control @error('phone') is-invalid @enderror" placeholder="0xxxxxxxxx">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
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
                    <input type="date" name="birthday" id="birthday"
                        value="{{ old('birthday') }}"
                        class="form-control" max="{{ date('Y-m-d') }}">
                </div>

                <!-- Trạng thái hoạt động -->
                <div class="form-check form-switch mb-3">
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                        {{ old('is_active', 1) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        {{ old('is_active', 1) ? 'Tài khoản đang hoạt động' : 'Tài khoản bị khóa' }}
                    </label>
                </div>

                <button type="submit" class="btn btn-primary">Lưu</button>
                <a href="{{ route('admin.staffs.index') }}" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    if(togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.type === 'password' ? 'text' : 'password';
            passwordInput.type = type;
            eyeIcon.classList.toggle('fa-eye');
            eyeIcon.classList.toggle('fa-eye-slash');
        });
    }

    // Chỉ cho phép nhập số cho phone
    const phoneInput = document.getElementById('phone');
    if(phoneInput){
        phoneInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g,'');
        });
    }
});
</script>
@endpush
