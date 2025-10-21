 @extends('backend.layouts.master')

@section('title', 'Chỉnh sửa nhóm khách hàng')

@section('content')
    <div class="page-inner">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-edit me-2"></i> Chỉnh sửa nhóm: {{ $group->name }}
                </h4>
                <a href="{{ route('admin.customer-groups.index') }}" class="btn btn-light btn-sm">
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

                <form action="{{ route('admin.customer-groups.update', $group->id) }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        {{-- Tên nhóm --}}
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">Tên nhóm <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $group->name) }}"
                                class="form-control @error('name') is-invalid @enderror"
                                placeholder="Nhập tên nhóm khách hàng" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Trạng thái --}}
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="form-check form-switch mt-3">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                    value="1" {{ $group->is_active ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_active">Kích hoạt nhóm</label>
                            </div>
                        </div>

                        {{-- Mô tả --}}
                        <div class="col-12">
                            <label for="description" class="form-label fw-semibold">Mô tả</label>
                            <textarea name="description" id="description" rows="4"
                                class="form-control @error('description') is-invalid @enderror"
                                placeholder="Nhập mô tả về nhóm khách hàng (không bắt buộc)">{{ old('description', $group->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Nút hành động --}}
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-2"></i> Cập nhật nhóm khách hàng
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
