@extends('backend.layouts.master')

@section('title', 'Quản lý Nhân viên - Nhóm khách hàng')

@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-md-12">
                <h2 class="mb-0">Quản lý Nhân viên - Nhóm khách hàng</h2>
                <p class="text-muted">Gán nhân viên phụ trách các nhóm khách hàng</p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> Đã có lỗi xảy ra:
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            @foreach ($groups as $group)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-people me-2"></i>{{ $group->name }}
                            </h5>
                            <small>{{ $group->description }}</small>
                        </div>
                        <div class="card-body">
                            <!-- Nhân viên hiện tại -->
                            <div class="mb-3">
                                <h6 class="fw-semibold">Nhân viên phụ trách:</h6>
                                @if ($group->staff->count() > 0)
                                    @foreach ($group->staff as $staff)
                                        <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle bg-success text-white me-2" style="width: 30px; height: 30px; font-size: 0.8rem;">
                                                    {{ strtoupper(substr($staff->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $staff->name }}</div>
                                                    <small class="text-muted">{{ $staff->email }}</small>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                @if ($staff->pivot->is_primary)
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="bi bi-star-fill"></i> Chính
                                                    </span>
                                                @endif
                                                <form action="{{ route('admin.group-staff.remove', [$group->id, $staff->id]) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            onclick="return confirm('Bạn có chắc muốn gỡ nhân viên này?')">
                                                        <i class="bi bi-x"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-muted">Chưa có nhân viên phụ trách</p>
                                @endif
                            </div>

                            <!-- Form gán nhân viên mới -->
                            <form action="{{ route('admin.group-staff.assign') }}" method="POST">
                                @csrf
                                <input type="hidden" name="group_id" value="{{ $group->id }}">
                                
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Thêm nhân viên</label>
                                    <select class="form-select" name="staff_id" required>
                                        <option value="">-- Chọn nhân viên --</option>
                                        @foreach ($staffList as $staff)
                                            @if (!$group->staff->contains('id', $staff->id))
                                                <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_primary" value="1" id="primary_{{ $group->id }}">
                                        <label class="form-check-label" for="primary_{{ $group->id }}">
                                            Nhân viên chính phụ trách
                                        </label>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-person-plus me-2"></i>Gán nhân viên
                                </button>
                            </form>

                            <!-- Thông tin khách hàng trong nhóm -->
                            <div class="mt-3 pt-3 border-top">
                                <h6 class="fw-semibold">Khách hàng trong nhóm:</h6>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach ($group->users as $user)
                                        <span class="badge bg-info">{{ $user->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <style>
        .avatar-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
    </style>
@endsection
