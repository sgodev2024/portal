@extends('backend.layouts.master')

@section('title', 'Chỉnh sửa nhóm dự án')

@section('content')
    <div class="page-inner">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-edit me-2"></i> Chỉnh sửa: {{ $projectGroup->name }}
                </h4>
                <a href="{{ route('admin.project-groups.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>

            <div class="card-body p-4">

                {{-- Hiển thị lỗi --}}
                @if ($errors->any())
                    <div class="alert alert-danger rounded-3 shadow-sm">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li><i class="fas fa-exclamation-circle me-1"></i> {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.project-groups.update', $projectGroup->id) }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        {{-- Tên dự án --}}
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">
                                Tên dự án <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $projectGroup->name) }}"
                                class="form-control @error('name') is-invalid @enderror"
                                placeholder="Ví dụ: Vinhomes Central Park" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Mã dự án (không cho sửa) --}}
                        <div class="col-md-6">
                            <label for="code" class="form-label fw-semibold">
                                Mã dự án
                            </label>
                            <input type="text" class="form-control" 
                                value="{{ $projectGroup->code }}" 
                                disabled readonly>
                            <small class="text-muted">
                                <i class="fas fa-lock"></i> Mã dự án không thể thay đổi
                            </small>
                        </div>

                        {{-- Vị trí --}}
                        <div class="col-md-6">
                            <label for="location" class="form-label fw-semibold">Vị trí dự án</label>
                            <input type="text" name="location" id="location" value="{{ old('location', $projectGroup->location) }}"
                                class="form-control @error('location') is-invalid @enderror"
                                placeholder="Ví dụ: Quận 1, TP.HCM">
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tổng số căn hộ --}}
                        <div class="col-md-6">
                            <label for="total_units" class="form-label fw-semibold">Tổng số căn hộ</label>
                            <input type="number" name="total_units" id="total_units" value="{{ old('total_units', $projectGroup->total_units) }}"
                                class="form-control @error('total_units') is-invalid @enderror"
                                placeholder="Ví dụ: 500" min="0">
                            @error('total_units')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Trạng thái --}}
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">
                                Trạng thái <span class="text-danger">*</span>
                            </label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="status_active" 
                                           value="active" {{ old('status', $projectGroup->status) == 'active' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status_active">
                                        <i class="fas fa-check-circle text-success"></i> Hoạt động
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="status_inactive" 
                                           value="inactive" {{ old('status', $projectGroup->status) == 'inactive' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status_inactive">
                                        <i class="fas fa-times-circle text-secondary"></i> Ngừng hoạt động
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Mô tả --}}
                        <div class="col-12">
                            <label for="description" class="form-label fw-semibold">Mô tả</label>
                            <textarea name="description" id="description" rows="4"
                                class="form-control @error('description') is-invalid @enderror"
                                placeholder="Nhập mô tả về dự án (không bắt buộc)">{{ old('description', $projectGroup->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Nút hành động --}}
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-2"></i> Cập nhật nhóm dự án
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Style bổ sung --}}
    <style>
        .bg-gradient-primary {
            background: linear-gradient(90deg, #0052cc, #007bff);
        }

        .form-label {
            font-size: 14px;
            color: #333;
        }

        input.form-control,
        textarea.form-control {
            border-radius: 0.5rem;
            padding: 0.6rem 0.8rem;
        }

        .btn-primary {
            border-radius: 0.5rem;
        }
    </style>
@endsection
