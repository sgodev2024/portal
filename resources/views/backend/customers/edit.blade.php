@extends('backend.layouts.master')

@section('title', 'Chỉnh sửa khách hàng')

@section('content')
    <div class="page-inner">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-user-edit me-2"></i> Chỉnh sửa: {{ $user->name }}
                </h4>
                <a href="{{ route('customers.index') }}" class="btn btn-light btn-sm">
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

                <form action="{{ route('customers.update', $user->id) }}" method="POST" class="needs-validation"
                    novalidate>
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        {{-- Họ tên --}}
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">Họ và tên <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                                class="form-control @error('name') is-invalid @enderror"
                                placeholder="Nhập họ tên khách hàng" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold">Email <span
                                    class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                                class="form-control @error('email') is-invalid @enderror" placeholder="example@email.com"
                                required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- SĐT (đổi từ account_id sang phone) --}}
                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-semibold">Số điện thoại <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                                class="form-control @error('phone') is-invalid @enderror" placeholder="Nhập số điện thoại"
                                required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- Công ty --}}
                        <div class="col-md-6">
                            <label for="company" class="form-label fw-semibold">Công ty</label>
                            <input type="text" name="company" id="company"
                                value="{{ old('company', $user->company) }}"
                                class="form-control @error('company') is-invalid @enderror" placeholder="Tên công ty">
                            @error('company')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Địa chỉ --}}
                        <div class="col-md-6">
                            <label for="address" class="form-label fw-semibold">Địa chỉ</label>
                            <input type="text" name="address" id="address"
                                value="{{ old('address', $user->address) }}" class="form-control"
                                placeholder="Nhập địa chỉ khách hàng">
                        </div>

                        {{-- Nhóm khách hàng --}}
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Nhóm khách hàng</label>
                            @if (isset($groups) && $groups->count() > 0)
                                <div class="border rounded p-3" style="background-color: #f8f9fa;">
                                    <div class="row">
                                        @php
                                            $halfCount = ceil($groups->count() / 2);
                                            $leftGroups = $groups->slice(0, $halfCount);
                                            $rightGroups = $groups->slice($halfCount);
                                            $selectedGroupIds = old('group_ids', $user->groups->pluck('id')->toArray());
                                        @endphp

                                        {{-- Cột trái --}}
                                        <div class="col-md-6">
                                            @foreach ($leftGroups as $group)
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="group_ids[]"
                                                        value="{{ $group->id }}" id="group_{{ $group->id }}"
                                                        {{ in_array($group->id, $selectedGroupIds) ? 'checked' : '' }}
                                                        {{ !$group->is_active ? 'disabled' : '' }}>
                                                    <label class="form-check-label" for="group_{{ $group->id }}">
                                                        <span class="{{ !$group->is_active ? 'text-muted' : '' }}">
                                                            {{ $group->name }}
                                                            @if (!$group->is_active)
                                                                <small class="text-danger">(Không hoạt động)</small>
                                                            @endif
                                                        </span>
                                                        @if ($group->description)
                                                            <br>
                                                            <small class="text-muted">{{ $group->description }}</small>
                                                        @endif
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>

                                        {{-- Cột phải --}}
                                        <div class="col-md-6">
                                            @foreach ($rightGroups as $group)
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="group_ids[]"
                                                        value="{{ $group->id }}" id="group_{{ $group->id }}"
                                                        {{ in_array($group->id, $selectedGroupIds) ? 'checked' : '' }}
                                                        {{ !$group->is_active ? 'disabled' : '' }}>
                                                    <label class="form-check-label" for="group_{{ $group->id }}">
                                                        <span class="{{ !$group->is_active ? 'text-muted' : '' }}">
                                                            {{ $group->name }}
                                                            @if (!$group->is_active)
                                                                <small class="text-danger">(Không hoạt động)</small>
                                                            @endif
                                                        </span>
                                                        @if ($group->description)
                                                            <br>
                                                            <small class="text-muted">{{ $group->description }}</small>
                                                        @endif
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <small class="text-muted mt-1 d-block">
                                    <i class="fas fa-info-circle"></i> Chọn một hoặc nhiều nhóm khách hàng
                                </small>
                            @else
                                <div class="border rounded p-3" style="background-color: #f8f9fa;">
                                    <p class="text-muted mb-0">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Chưa có nhóm khách hàng.
                                        <a href="{{ route('admin.customer-groups.create') }}" target="_blank"
                                            class="text-primary">
                                            Tạo nhóm mới
                                        </a>
                                    </p>
                                </div>
                            @endif
                        </div>

                        {{-- Nhóm dự án --}}
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Nhóm dự án <span class="text-danger">*</span></label>
                            @if (isset($projectGroups) && $projectGroups->count() > 0)
                                <div class="border rounded p-3" style="background-color: #f8f9fa;">
                                    <div class="row">
                                        @php
                                            $halfCount = ceil($projectGroups->count() / 2);
                                            $leftProjects = $projectGroups->slice(0, $halfCount);
                                            $rightProjects = $projectGroups->slice($halfCount);
                                            $selectedProjectIds = old('project_group_ids', $user->projectGroups->pluck('id')->toArray());
                                        @endphp

                                        {{-- Cột trái --}}
                                        <div class="col-md-6">
                                            @foreach ($leftProjects as $projectGroup)
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="project_group_ids[]"
                                                        value="{{ $projectGroup->id }}" id="project_{{ $projectGroup->id }}"
                                                        {{ in_array($projectGroup->id, $selectedProjectIds) ? 'checked' : '' }}
                                                        {{ $projectGroup->status != 'active' ? 'disabled' : '' }}>
                                                    <label class="form-check-label" for="project_{{ $projectGroup->id }}">
                                                        <span class="{{ $projectGroup->status != 'active' ? 'text-muted' : '' }}">
                                                            <strong>{{ $projectGroup->name }}</strong> ({{ $projectGroup->code }})
                                                            @if ($projectGroup->status != 'active')
                                                                <small class="text-danger">(Không hoạt động)</small>
                                                            @endif
                                                        </span>
                                                        @if ($projectGroup->location)
                                                            <br>
                                                            <small class="text-muted"><i class="fas fa-map-marker-alt"></i> {{ $projectGroup->location }}</small>
                                                        @endif
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>

                                        {{-- Cột phải --}}
                                        <div class="col-md-6">
                                            @foreach ($rightProjects as $projectGroup)
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="project_group_ids[]"
                                                        value="{{ $projectGroup->id }}" id="project_{{ $projectGroup->id }}"
                                                        {{ in_array($projectGroup->id, $selectedProjectIds) ? 'checked' : '' }}
                                                        {{ $projectGroup->status != 'active' ? 'disabled' : '' }}>
                                                    <label class="form-check-label" for="project_{{ $projectGroup->id }}">
                                                        <span class="{{ $projectGroup->status != 'active' ? 'text-muted' : '' }}">
                                                            <strong>{{ $projectGroup->name }}</strong> ({{ $projectGroup->code }})
                                                            @if ($projectGroup->status != 'active')
                                                                <small class="text-danger">(Không hoạt động)</small>
                                                            @endif
                                                        </span>
                                                        @if ($projectGroup->location)
                                                            <br>
                                                            <small class="text-muted"><i class="fas fa-map-marker-alt"></i> {{ $projectGroup->location }}</small>
                                                        @endif
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <small class="text-muted mt-1 d-block">
                                    <i class="fas fa-info-circle"></i> Chọn một hoặc nhiều nhóm dự án (bắt buộc)
                                </small>
                            @else
                                <div class="border rounded p-3" style="background-color: #f8f9fa;">
                                    <p class="text-muted mb-0">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Chưa có nhóm dự án.
                                        <a href="{{ route('admin.project-groups.create') }}" target="_blank"
                                            class="text-primary">
                                            Tạo nhóm dự án mới
                                        </a>
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Nút hành động --}}
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('customers.index') }}" class="btn btn-secondary px-4">
                            <i class="fas fa-times me-2"></i> Hủy
                        </a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-2"></i> Cập nhật khách hàng
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Style bổ sung nhẹ --}}
    <style>
        .bg-gradient-primary {
            background: linear-gradient(90deg, #0052cc, #007bff);
        }

        .form-label {
            font-size: 14px;
            color: #333;
        }

        input.form-control {
            border-radius: 0.5rem;
            padding: 0.6rem 0.8rem;
        }

        .btn-primary {
            border-radius: 0.5rem;
        }

        .form-check-input:disabled~.form-check-label {
            opacity: 0.5;
        }
    </style>
@endsection
