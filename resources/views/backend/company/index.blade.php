@extends('backend.layouts.master')

<link href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.default.min.css" rel="stylesheet">

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h4 class="mb-0">
                            <i class="fas fa-building me-2"></i>
                            {{ isset($company) ? 'Cập nhật thông tin công ty' : 'Thêm mới công ty' }}
                        </h4>
                    </div>

                    <div class="card-body p-4">
                        <form action="{{ route('company.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{ $company->id ?? '' }}">

                            <!-- Company Information -->
                            <div class="section-wrapper mb-4">
                                <h5 class="section-title">
                                    <i class="fas fa-info-circle me-2"></i>Thông tin công ty
                                </h5>
                                <div class="row g-3">
                                    @php
                                        $companyFields = [
                                            ['id' => 'company_name', 'label' => 'Tên công ty', 'type' => 'text', 'icon' => 'building', 'col' => 12],
                                            ['id' => 'company_address', 'label' => 'Địa chỉ', 'type' => 'text', 'icon' => 'map-marker-alt', 'col' => 12],
                                            ['id' => 'company_phone', 'label' => 'Số điện thoại', 'type' => 'text', 'icon' => 'phone', 'col' => 6],
                                            ['id' => 'company_email', 'label' => 'Email', 'type' => 'email', 'icon' => 'envelope', 'col' => 6],
                                            ['id' => 'company_website', 'label' => 'Website', 'type' => 'url', 'icon' => 'globe', 'col' => 6],
                                            ['id' => 'tax_id', 'label' => 'Mã số thuế', 'type' => 'text', 'icon' => 'file-invoice', 'col' => 6],
                                        ];
                                    @endphp

                                    @foreach ($companyFields as $field)
                                        <div class="col-md-{{ $field['col'] }}">
                                            <div class="form-group">
                                                <label for="{{ $field['id'] }}" class="form-label">
                                                    <i class="fas fa-{{ $field['icon'] }} me-1"></i>{{ $field['label'] }}
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="{{ $field['type'] }}"
                                                    class="form-control @error($field['id']) is-invalid @enderror"
                                                    id="{{ $field['id'] }}"
                                                    name="{{ $field['id'] }}"
                                                    placeholder="Nhập {{ strtolower($field['label']) }}"
                                                    value="{{ old($field['id'], $company->{$field['id']} ?? '') }}"
                                                    @if (in_array($field['id'], ['company_phone', 'tax_id']))
                                                        oninput="formatNumberInput(this)"
                                                        onpaste="handlePaste(event, this)"
                                                    @endif />
                                                @error($field['id'])
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    @endforeach

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="vat_rate" class="form-label">
                                                <i class="fas fa-percent me-1"></i>Tỷ lệ VAT (%)
                                            </label>
                                            <input type="number"
                                                class="form-control @error('vat_rate') is-invalid @enderror"
                                                id="vat_rate"
                                                name="vat_rate"
                                                placeholder="Ví dụ: 10"
                                                step="0.01"
                                                value="{{ old('vat_rate', $company->vat_rate ?? '10') }}" />
                                            @error('vat_rate')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="footer" class="form-label">
                                                <i class="fas fa-file-alt me-1"></i>Trân trang
                                            </label>
                                            <input type="text"
                                                class="form-control @error('footer') is-invalid @enderror"
                                                id="footer"
                                                name="footer"
                                                placeholder="Nhập nội dung footer"
                                                value="{{ old('footer', $company->footer ?? '') }}" />
                                            @error('footer')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Logo Section -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">
                                                <i class="fas fa-image me-1"></i>Logo công ty
                                            </label>
                                            <div class="logo-preview-wrapper">
                                                <div id="preview-frame" class="logo-preview">
                                                    @if(isset($company) && $company->company_logo)
                                                        <img src="{{ asset('storage/' . $company->company_logo) }}" alt="Company Logo">
                                                    @else
                                                        <div class="placeholder-content">
                                                            <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                                            <p class="text-muted mb-0 small">Click để chọn logo</p>
                                                        </div>
                                                    @endif
                                                </div>
                                                <input type="file" class="d-none" id="company_logo" name="company_logo" accept="image/*">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Representative Information -->
                            <div class="section-wrapper mb-4">
                                <h5 class="section-title">
                                    <i class="fas fa-user-tie me-2"></i>Thông tin người đại diện
                                </h5>
                                <div class="row g-3">
                                    @php
                                        $representativeFields = [
                                            ['id' => 'representative_name', 'label' => 'Họ và tên', 'type' => 'text', 'icon' => 'user', 'col' => 6],
                                            ['id' => 'representative_position', 'label' => 'Chức vụ', 'type' => 'text', 'icon' => 'id-badge', 'col' => 6],
                                            ['id' => 'representative_phone', 'label' => 'Số điện thoại', 'type' => 'text', 'icon' => 'phone', 'col' => 6],
                                            ['id' => 'representative_email', 'label' => 'Email', 'type' => 'email', 'icon' => 'envelope', 'col' => 6],
                                        ];
                                    @endphp

                                    @foreach ($representativeFields as $field)
                                        <div class="col-md-{{ $field['col'] }}">
                                            <div class="form-group">
                                                <label for="{{ $field['id'] }}" class="form-label">
                                                    <i class="fas fa-{{ $field['icon'] }} me-1"></i>{{ $field['label'] }}
                                                </label>
                                                <input type="{{ $field['type'] }}"
                                                    class="form-control @error($field['id']) is-invalid @enderror"
                                                    id="{{ $field['id'] }}"
                                                    name="{{ $field['id'] }}"
                                                    placeholder="Nhập {{ strtolower($field['label']) }}"
                                                    value="{{ old($field['id'], $company->{$field['id']} ?? '') }}"
                                                    @if ($field['id'] === 'representative_phone')
                                                        oninput="formatNumberInput(this)"
                                                        onpaste="handlePaste(event, this)"
                                                    @endif />
                                                @error($field['id'])
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-1"></i>{{ isset($company) ? 'Cập nhật' : 'Lưu lại' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .card {
            border: none;
            border-radius: 8px;
            overflow: hidden;
        }

        .card-header {
            padding: 1.5rem;
        }

        .section-wrapper {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e9ecef;
        }

        .form-label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .form-control {
            border-radius: 6px;
            border: 1px solid #ced4da;
            padding: 0.625rem 0.875rem;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
        }

        /* Logo Upload Styles */
        .logo-preview-wrapper {
            width: 100%;
        }

        .logo-preview {
            width: 100%;
            height: 150px;
            border: 2px dashed #ced4da;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            background: white;
            overflow: hidden;
            position: relative;
        }

        .logo-preview:hover {
            border-color: #6c757d;
            background: #f8f9fa;
        }

        .logo-preview img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .placeholder-content {
            text-align: center;
            padding: 1rem;
        }

        /* Button Styles */
        .btn {
            border-radius: 6px;
            padding: 0.625rem 1.5rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-success:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(40, 167, 69, 0.3);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .logo-preview {
                height: 120px;
            }

            .section-wrapper {
                padding: 1rem;
            }

            .card-body {
                padding: 1.5rem !important;
            }
        }

        /* Invalid feedback */
        .invalid-feedback {
            display: block;
            margin-top: 0.25rem;
            font-size: 0.875rem;
        }

        /* Icons spacing */
        .fas {
            width: 20px;
            text-align: center;
        }
    </style>
@endpush

@push('scripts')
    <script>
        const BASE_URL = "{{ url('/') }}";
    </script>

    <script>
        function formatNumberInput(input) {
            input.value = input.value.replace(/\D/g, "");
        }

        function handlePaste(event, input) {
            event.preventDefault();
            let pastedData = (event.clipboardData || window.clipboardData).getData("text");
            input.value = pastedData.replace(/\D/g, "");
        }
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const imageInput = document.getElementById('company_logo');
            const previewFrame = document.getElementById('preview-frame');

            if (previewFrame && imageInput) {
                previewFrame.addEventListener('click', () => {
                    imageInput.click();
                });

                imageInput.addEventListener('change', function(event) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewFrame.innerHTML = `<img src="${e.target.result}" alt="Company Logo">`;
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        });
    </script>
@endpush
