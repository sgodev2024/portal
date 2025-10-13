@extends('backend.layouts.master')

{{-- @section('title', $title) --}}
<link href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.default.min.css" rel="stylesheet">
@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ isset($user) ? route('client.update', $user->id) : route('client.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf

                @if (isset($user))
                    @method('PUT')
                @endif

                <!-- Thông tin tài khoản -->
                <h5 class="section-title">Thông tin tài khoản</h5>
                <div class="row">
                    <div class="col-md-6">

                        <div class="form-group row">
                            <label for="full_name" class="form-label">Họ và tên</label>
                            <input type="text" class="form-control @error('full_name') is-invalid @enderror"
                                id="full_name" name="full_name" placeholder="Nhập họ và tên"
                                value="{{ old('full_name', $user->full_name ?? '') }}" />
                            @error('full_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group row">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password" placeholder="Nhập password" value="" />
                        </div>

                        <div class="form-group row">
                            <label for="phone_number" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control @error('phone_number') is-invalid @enderror"
                                id="phone_number" name="phone_number" placeholder="Nhập số điện thoại"
                                value="{{ old('phone_number', $user->phone_number ?? '') }}" />
                            @error('phone_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="form-group row">
                            <label for="tax_code" class="form-label">Mã số thuế</label>
                            <input type="text" class="form-control @error('tax_code') is-invalid @enderror"
                                id="tax_code" name="tax_code" placeholder="Nhập mã số thuế"
                                value="{{ old('tax_code', $user->tax_code ?? '') }}" />
                            @error('tax_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        {{-- <div class="form-group row">
                        <label for="province" class="form-label">Tỉnh/Thành phố </label>

                        <select class="form-control" name="province" id="province">
                            <option value="">Chọn thành phố</option>
                            @foreach ($province as $item)
                            <option value="{{ $item->id }}"
                                {{ old('province', $user->province ?? '') == $item->id ? 'selected' : '' }}>
                                {{ $item->name }}
                            </option>

                            @endforeach
                        </select>
                        @error('province')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                    </div> --}}

                        {{-- <div class="form-group row">
                        <label for="district" class="form-label">Quận/Huyện</span></label>

                        <select class="form-control" id="district" name="district">
                            <option value="" {{ old('district')=='' ? 'selected' : '' }}>-- Vui lòng chọn --</option>
                            <!-- Thêm các quận/huyện khác nếu cần -->
                        </select>
                        @error('district')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                    </div>

                    <div class="form-group row">
                        <label for="ward" class="form-label">Xã phường</label>

                        <select class="form-control" name="ward" id="ward">
                            <option value="" {{ old('ward')=='' ? 'selected' : '' }}>-- Vui lòng chọn --</option>
                            <!-- Thêm các xã/phường khác nếu cần -->
                        </select>
                        @error('ward')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                    </div> --}}

                        {{-- <div class="form-group row">
                        <label for="address" class="form-label">Địa chỉ</label>
                        <input type="text" class="form-control @error('address') is-invalid @enderror" id="address"
                            name="address" placeholder="Địa chỉ chi tiết"
                            value="{{ old('address', $user->address ?? '') }}" />
                        @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div> --}}


                    </div>

                    <div class="col-md-6">

                        <div class="form-group row">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" placeholder="Nhập email" value="{{ old('email', $user->email ?? '') }}" />
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group row">
                            <label for="gender" class="form-label">Giới tính</label>
                            <select class="form-control @error('gender') is-invalid @enderror" id="gender"
                                name="gender">
                                <option value="">----- Chọn giới tính -----</option>
                                <option value="male"
                                    {{ old('gender', $user->gender ?? '') == 'male' ? 'selected' : '' }}>Nam</option>
                                <option value="female"
                                    {{ old('gender', $user->gender ?? '') == 'female' ? 'selected' : '' }}>Nữ</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group row">
                            <label for="birth_date" class="form-label">Ngày sinh</label>
                            <input type="date" class="form-control @error('birth_date') is-invalid @enderror"
                                id="birth_date" name="birth_date"
                                value="{{ old('birth_date', $user->birth_date ?? '') }}" />
                            @error('birth_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group row">
                            <label for="identity_number" class="form-label">CMND/CCCD/Hộ chiếu</label>
                            <input type="text" class="form-control @error('identity_number') is-invalid @enderror"
                                id="identity_number" name="identity_number" placeholder="Nhập số CMND/CCCD/Hộ chiếu"
                                value="{{ old('identity_number', $user->identity_number ?? '') }}" />
                            @error('identity_number')
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
        .section-title {
            font-size: 1.2rem;
            font-weight: bold;
            padding-top: 20px;
            margin-bottom: 15px;
            padding: 10px;
            color: #fff;
            text-align: center;
        }

        .section-title:nth-of-type(1) {
            background-color: #4CAF50;
        }
    </style>
@endpush

@push('scripts')
    <script>
        const BASE_URL = "{{ url('/') }}";
    </script>
    {{-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js"></script>
<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>

<script src="{{ asset('ckfinder_php_3.7.0/ckfinder/ckfinder.js') }}"></script> --}}
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
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
    <script>
        $(document).ready(function() {
            // Giá trị từ user đã được đổ ra từ backend
            var selectedProvince = $('#province').val();
            var selectedDistrict = "{{ $user->district ?? '' }}";
            var selectedWard = "{{ $user->ward ?? '' }}";

            // Hàm tải quận huyện
            function loadDistricts(provinceId, districtId = null, wardId = null) {
                if (provinceId) {
                    $.ajax({
                        url: '/get-districts',
                        type: 'GET',
                        data: {
                            province_id: provinceId
                        },
                        success: function(data) {
                            var districts = data.districts;
                            var districtSelect = $('#district');
                            districtSelect.empty();
                            districtSelect.append('<option value="">Chọn quận huyện</option>');

                            districts.forEach(function(district) {
                                districtSelect.append('<option value="' + district.id + '" ' +
                                    (district.id == districtId ? 'selected' : '') + '>' +
                                    district.name + '</option>');
                            });

                            // Nếu có districtId, tự động tải danh sách phường/xã
                            if (districtId) {
                                loadWards(districtId, wardId);
                            } else {
                                $('#ward').empty().append('<option value="">Chọn phường xã</option>');
                            }
                        }
                    });
                } else {
                    // Xoá danh sách quận/huyện và phường/xã khi không chọn tỉnh thành
                    $('#district').empty().append('<option value="">Chọn quận huyện</option>');
                    $('#ward').empty().append('<option value="">Chọn phường xã</option>');
                }
            }

            // Hàm tải xã/phường
            function loadWards(districtId, wardId = null) {
                if (districtId) {
                    $.ajax({
                        url: '/get-wards',
                        type: 'GET',
                        data: {
                            district_id: districtId
                        },
                        success: function(data) {
                            var wards = data.wards;
                            var wardSelect = $('#ward');
                            wardSelect.empty();
                            wardSelect.append('<option value="">Chọn phường xã</option>');

                            wards.forEach(function(ward) {
                                wardSelect.append('<option value="' + ward.id + '" ' +
                                    (ward.id == wardId ? 'selected' : '') + '>' +
                                    ward.name + '</option>');
                            });
                        }
                    });
                } else {
                    $('#ward').empty().append('<option value="">Chọn phường xã</option>');
                }
            }

            // Tải dữ liệu khi load trang nếu đã có dữ liệu từ user
            if (selectedProvince) {
                loadDistricts(selectedProvince, selectedDistrict, selectedWard);
            }

            // Khi chọn lại tỉnh thành
            $('#province').change(function() {
                var newProvince = $(this).val();
                loadDistricts(newProvince); // Reset quận huyện và xã/phường khi thay đổi tỉnh thành
            });

            // Khi chọn lại quận huyện
            $('#district').change(function() {
                var newDistrict = $(this).val();
                loadWards(newDistrict); // Reset xã/phường khi thay đổi quận huyện
            });
        });
    </script>
    {{-- Script auto generate username --}}
    <script>
        function removeVietnameseTones(str) {
            str = str.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
            str = str.replace(/đ/g, "d").replace(/Đ/g, "d");
            return str.toLowerCase().replace(/\s+/g, '');
        }
        let abortController = null;
        async function checkUsernameExists(username) {
            if (abortController) {
                abortController.abort();
            }
            abortController = new AbortController();

            try {
                let response = await fetch(
                    "{{ route('client.check.username') }}?username=" + username, {
                        signal: abortController.signal
                    }
                );
                let data = await response.json();
                return data.exists;
            } catch (e) {
                if (e.name === 'AbortError') {
                    return false; // request bị hủy
                }
                return false;
            }
        }

        // Hàm debounce để tránh spam request
        function debounce(func, delay) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), delay);
            };
        }

        async function generateUsername() {
            let fullNameEl = document.getElementById('full_name');
            let phoneEl = document.getElementById('phone_number');
            let usernameEl = document.getElementById('username');

            let fullName = fullNameEl.value.trim();
            let phone = phoneEl.value.trim();

            if (!fullName) {
                usernameEl.value = '';
                return;
            }

            let baseUsername = removeVietnameseTones(fullName);
            let username = baseUsername;

            // lưu lại snapshot input hiện tại
            let snapshotFullName = fullName;
            let snapshotPhone = phone;

            let exists = await checkUsernameExists(baseUsername);

            // Chỉ set nếu input chưa thay đổi kể từ lúc gọi request
            if (snapshotFullName === fullNameEl.value.trim() &&
                snapshotPhone === phoneEl.value.trim()) {
                if (exists && phone.length >= 3) {
                    username = baseUsername + phone.slice(-3);
                }
                usernameEl.value = username;
            }
        }
        const debouncedGenerateUsername = debounce(generateUsername, 400);
        document.getElementById('full_name').addEventListener('input', debouncedGenerateUsername);
        document.getElementById('phone_number').addEventListener('input', debouncedGenerateUsername);
    </script>
@endpush
