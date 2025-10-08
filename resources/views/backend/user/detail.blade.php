@extends('backend.layouts.master')

{{-- @section('title', $title) --}}
<link href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.default.min.css" rel="stylesheet">
@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ isset($user) ? route('user.update', $user->id) : route('user.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf

                @if (isset($user))
                    @method('PUT')
                @endif

                <!-- Thông tin tài khoản -->
                <h5 class="section-title">Thông tin tài khoản</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="username" class="form-label">Tên đăng nhập</label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror"
                                id="username" @if (isset($user)) readonly @endif name="username"
                                placeholder="Nhập tên đăng nhập" value="{{ old('username', $user->username ?? '') }}" />
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password" placeholder="Nhập mật khẩu" value="" />
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="full_name" class="form-label">Họ và tên</label>
                            <input type="text" class="form-control @error('full_name') is-invalid @enderror"
                                id="full_name" name="full_name" placeholder="Nhập họ và tên"
                                value="{{ old('full_name', $user->full_name ?? '') }}" />
                            @error('full_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="gender" class="form-label">Giới tính</label>
                            <select class="form-control form-select @error('gender') is-invalid @enderror" id="gender"
                                name="gender">
                                <option value="">----- Chọn giới tính -----</option>
                                <option value="male" {{ old('gender', $user->gender ?? '') == 'male' ? 'selected' : '' }}>
                                    Nam</option>
                                <option value="female"
                                    {{ old('gender', $user->gender ?? '') == 'female' ? 'selected' : '' }}>Nữ</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="birth_date" class="form-label">Ngày sinh</label>
                            <input type="date" class="form-control @error('birth_date') is-invalid @enderror"
                                id="birth_date" name="birth_date"
                                value="{{ old('birth_date', $user->birth_date ?? '') }}" />
                            @error('birth_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group row">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select class="form-control @error('status') is-invalid @enderror" id="status"
                                name="status">
                                <option value="active"
                                    {{ old('status', $user->status ?? '') == 'active' ? 'selected' : '' }}>Hoạt động
                                </option>
                                <option value="pending"
                                    {{ old('status', $user->status ?? '') == 'pending' ? 'selected' : '' }}>Tạm dừng
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                @if (isset($user)) readonly @endif name="email" placeholder="Nhập email"
                                value="{{ old('email', $user->email ?? '') }}" />
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="identity_number" class="form-label">CMND/CCCD/Hộ chiếu</label>
                            <input type="text" class="form-control @error('identity_number') is-invalid @enderror"
                                id="identity_number" name="identity_number" placeholder="Nhập số CMND/CCCD/Hộ chiếu"
                                value="{{ old('identity_number', $user->identity_number ?? '') }}" />
                            @error('identity_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tax_code" class="form-label">Mã số thuế</label>
                            <input type="text" class="form-control @error('tax_code') is-invalid @enderror"
                                id="tax_code" name="tax_code" placeholder="Nhập mã số thuế"
                                value="{{ old('tax_code', $user->tax_code ?? '') }}" />
                            @error('tax_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone_number" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control @error('phone_number') is-invalid @enderror"
                                id="phone_number" name="phone_number" placeholder="Nhập số điện thoại"
                                value="{{ old('phone_number', $user->phone_number ?? '') }}" />
                            @error('phone_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>



                        <div class="mb-3">
                            <label for="role_id" class="form-label">Vai trò</label>
                            <select class=" form-control form-select @error('role_id') is-invalid @enderror"
                                id="role_id" name="role_id">
                                <option value="">----- Chọn vai trò -----</option>
                                <option value="1"
                                    {{ old('role_id', $user->role_id ?? '') == '1' ? 'selected' : '' }}>Admin</option>
                                <option value="2"
                                    {{ old('role_id', $user->role_id ?? '') == '2' ? 'selected' : '' }}>Khách hàng</option>
                                    <option value="3"
                                    {{ old('role_id', $user->role_id ?? '') == '3' ? 'selected' : '' }}>Khách sạn</option>
                            </select>
                            @error('role_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-success">{{ isset($user) ? 'Cập nhật' : 'Lưu' }}</button>
                </div>
            </form>



        </div>
    </div>
@endsection

@push('styles')
    <style>
        .cke_notifications_area {
            display: none;
        }

        .error {
            color: red;
        }

        .selectize-control {
            border: 0px !important;
            padding: 0px !important;
        }

        .selectize-input {
            padding: 10px 12px !important;
            border: 2px solid #ebedf2 !important;
            border-radius: 5px !important;
            border-top: 1px solid #ebedf2 !important;
        }

        /* Phần danh mục */
        .section-title {
            font-size: 1.2rem;
            font-weight: bold;
            padding-top: 20px;
            margin-bottom: 15px;
            padding: 10px;
            color: #fff;
            text-align: center;
        }

        .section-title+.section-title {
            color: #FF9800
        }

        .section-title:nth-of-type(1) {
            background-color: #4CAF50;
        }

        /* Nền cam cho SEO */
        .section-title:nth-of-type(2) {
            margin-top: 20px;
            background-color: #695aec;
        }

        .mb-3 {
            margin-bottom: 15px;
        }

        .btn {
            font-size: 1rem;
            padding: 10px 20px;
            border-radius: 5px;
        }

        #preview-frame {
            width: 100%;
            height: 300px;
            border: 2px dashed #ddd;
            display: flex;
            border-radius: 10px;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            margin-top: 10px;
        }

        #preview-frame img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
        }

        label {
            font-weight: 600;
        }
    </style>
@endpush

@push('scripts')
    <script>
        const BASE_URL = "{{ url('/') }}";
    </script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js"></script>
    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>

    <script src="{{ asset('ckfinder_php_3.7.0/ckfinder/ckfinder.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const imageInput = document.getElementById('image');
            const previewFrame = document.getElementById('preview-frame');

            // Khi click vào khung preview, kích hoạt input file
            previewFrame.addEventListener('click', () => {
                imageInput.click();
            });

            // Khi chọn file, hiển thị ảnh
            imageInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewFrame.innerHTML =
                            `<img src="${e.target.result}" alt="Selected Image" style="max-width: 100%; height: auto;">`;
                    };
                    reader.readAsDataURL(file);
                } else {
                    previewFrame.innerHTML = '<p class="text-muted">Click here to select an image</p>';
                }
            });

            // Nếu có ảnh được chọn sẵn (ví dụ: từ trước khi tải lại trang), hiển thị ảnh
            const currentImageSrc =
            '{{ old('image', asset('storage/' . ($category->logo ?? ''))) }}'; // Thay đổi này theo cách bạn lấy ảnh cũ từ server
            if (currentImageSrc) {
                previewFrame.innerHTML =
                    `<img src="${currentImageSrc}" alt="Selected Image" style="max-width: 100%; height: auto;">`;
            }
        });
    </script>
    <script>
        var $jq = jQuery.noConflict();
        $jq(document).ready(function() {
            $jq('#keyword_seo').selectize({
                delimiter: ',',
                persist: false,
                create: function(input) {
                    return {
                        value: input,
                        text: input
                    };
                },
                plugins: ['remove_button'],
                onKeyDown: function(e) {
                    if (e.key === ' ') {
                        e.preventDefault();
                        var value = this.$control_input.val().trim();
                        if (value) {
                            this.addItem(value);
                            this.$control_input.val('');
                        }
                    }
                }
            });


        });
    </script>
@endpush
