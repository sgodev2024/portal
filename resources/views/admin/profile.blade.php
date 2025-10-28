@extends('backend.layouts.master')

@section('title', 'Cập nhật thông tin quản trị viên')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Cập nhật thông tin quản trị viên</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <!-- Tên hiển thị -->
                            <div class="form-group mb-3">
                                <label for="name">Tên hiển thị <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                                    class="form-control @error('name') is-invalid @enderror" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="form-group mb-3">
                                <label for="email">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                                    class="form-control @error('email') is-invalid @enderror" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Số điện thoại -->
                            <div class="form-group mb-3">
                                <label for="phone">Số điện thoại</label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    placeholder="Nhập số điện thoại">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Công ty -->
                            <div class="form-group mb-3">
                                <label for="company">Công ty</label>
                                <input type="text" name="company" id="company" value="{{ old('company', $user->company) }}"
                                    class="form-control @error('company') is-invalid @enderror"
                                    placeholder="Nhập tên công ty">
                                @error('company')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Địa chỉ -->
                            <div class="form-group mb-3">
                                <label for="address">Địa chỉ</label>
                                <textarea name="address" id="address" rows="3" class="form-control @error('address') is-invalid @enderror"
                                    placeholder="Nhập địa chỉ">{{ old('address', $user->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Ảnh đại diện -->
                            <div class="form-group mb-3">
                                <label for="avatar">Ảnh đại diện (tùy chọn)</label>
                                @if (isset($user->avatar) && $user->avatar)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar"
                                            class="img-thumbnail" style="max-width: 150px;">
                                    </div>
                                @endif
                                <input type="file" name="avatar" id="avatar"
                                    class="form-control @error('avatar') is-invalid @enderror" accept="image/*">
                                @error('avatar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Định dạng: JPG, PNG, GIF. Tối đa 2MB</small>
                            </div>

                            <hr class="my-4">

                            <h5 class="mb-3">
                                Thay đổi mật khẩu
                                @if($user->must_update_profile)
                                    <span class="text-danger">*</span>
                                @endif
                            </h5>

                            @if($user->must_update_profile)
                                <p class="text-danger small fw-bold">Bạn phải thay đổi mật khẩu để tiếp tục</p>
                            @else
                                <p class="text-muted small">Để trống nếu không muốn thay đổi mật khẩu</p>
                            @endif

                            @if(!$user->must_update_profile)
                                <!-- Mật khẩu hiện tại (chỉ hiện khi không bắt buộc đổi) -->
                                <div class="form-group mb-3">
                                    <label for="current_password">Mật khẩu hiện tại</label>
                                    <input type="password" name="current_password" id="current_password"
                                        class="form-control @error('current_password') is-invalid @enderror"
                                        placeholder="Nhập mật khẩu hiện tại để thay đổi">
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Chỉ cần nhập nếu bạn muốn đổi mật khẩu</small>
                                </div>
                            @endif

                            <!-- Mật khẩu mới -->
                            <div class="form-group mb-3">
                                <label for="password">
                                    Mật khẩu mới
                                    @if($user->must_update_profile)
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <input type="password" name="password" id="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Nhập mật khẩu mới"
                                    @if($user->must_update_profile) required @endif>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Xác nhận mật khẩu mới -->
                            <div class="form-group mb-3">
                                <label for="password_confirmation">
                                    Xác nhận mật khẩu mới
                                    @if($user->must_update_profile)
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="form-control" placeholder="Nhập lại mật khẩu mới"
                                    @if($user->must_update_profile) required @endif>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Cập nhật
                                </button>
                                @if(!$user->must_update_profile)
                                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Quay lại
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
