@extends('backend.layouts.master')

@section('title', $title)

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-cog"></i> {{ $page }}
                </h5>
            </div>

            {{-- Form cập nhật cấu hình STMT --}}
            <form action="{{ route('admin.stmt.update') }}" method="POST">
                @csrf
                <div class="card-header bg-light">
                    <h4 class="mb-0">Cấu hình hệ thống gửi & nhận email (STMT)</h4>
                </div>

                <div class="card-body">
                    {{-- Mail Username --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            Mail Username (Email gửi) <span class="text-danger">*</span>
                        </label>
                        <input type="email" name="mail_username"
                            class="form-control @error('mail_username') is-invalid @enderror"
                            placeholder="Ví dụ: no-reply@tenmiencuaban.com"
                            value="{{ old('mail_username', $stmt->mail_username ?? '') }}" required>
                        @error('mail_username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Địa chỉ email dùng để gửi thư tự động từ hệ thống.</small>
                    </div>

                    {{-- Mail Password --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            Mail Password <span class="text-danger">*</span>
                        </label>
                        <input type="password" name="mail_password"
                            class="form-control @error('mail_password') is-invalid @enderror"
                            placeholder="Nhập mật khẩu ứng dụng email"
                            value="{{ old('mail_password', $stmt->mail_password ?? '') }}" required>
                        @error('mail_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            Mật khẩu ứng dụng (App Password) được tạo trong tài khoản email gửi.
                        </small>
                    </div>

                    {{-- Mail From Name --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            Mail From Name (Tên hiển thị)
                        </label>
                        <input type="text" name="mail_from_name"
                            class="form-control @error('mail_from_name') is-invalid @enderror"
                            placeholder="Ví dụ: Hệ thống Quản trị Portal"
                            value="{{ old('mail_from_name', $stmt->mail_from_name ?? '') }}">
                        @error('mail_from_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            Tên hiển thị kèm địa chỉ email gửi, ví dụ: "Hệ thống Portal &lt;admin@domain.com&gt;".
                        </small>
                    </div>

                    {{-- Notification Emails --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            Email nhận thông báo
                        </label>
                        <textarea
                            name="notification_emails"
                            class="form-control @error('notification_emails') is-invalid @enderror"
                            rows="3"
                            placeholder="Ví dụ: admin@gmail.com, support@gmail.com">{{ old('notification_emails', isset($stmt->notification_emails) ? implode(', ', (array) $stmt->notification_emails) : '') }}</textarea>
                        @error('notification_emails')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            Nhập nhiều email, cách nhau bằng dấu phẩy (,). Những địa chỉ này sẽ nhận thông báo hệ thống.
                        </small>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Lưu cấu hình
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
