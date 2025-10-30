@extends('backend.layouts.master')

@section('title', 'Quản lý Nhân viên - Nhóm khách hàng')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-1 text-primary">
                            <i class="bi bi-people-fill me-2"></i>Quản lý Nhân viên - Nhóm khách hàng
                        </h2>
                        <p class="text-muted mb-0">Phân công nhân viên phụ trách các nhóm khách hàng (Mỗi nhóm 1 nhân viên)</p>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-primary px-3 py-2 me-2">
                            <i class="bi bi-collection me-1"></i>{{ $groups->count() }} nhóm
                        </span>
                        <span class="badge bg-success px-3 py-2">
                            <i class="bi bi-person-check me-1"></i>{{ $groups->whereNotNull('staff')->count() }} đã có NV
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Info Banner -->
        <div class="alert alert-info border-0 mb-4" role="alert">
            <i class="bi bi-lightbulb me-2"></i>
            Mỗi nhóm chỉ có <strong>1 nhân viên duy nhất</strong> phụ trách. Nhân viên phụ trách sẽ <strong>có quyền quản lý tất cả tickets</strong> của khách hàng trong nhóm; gán ticket lẻ vẫn thực hiện riêng khi cần.
        </div>

        <!-- Groups Grid -->
        <div class="row g-4">
            @foreach ($groups as $group)
                @php $assignedStaff = $group->staff->first(); @endphp
                <div class="col-md-6 col-xl-4">
                    <div class="card border shadow-sm h-100">
                        <!-- Header -->
                        <div class="card-header bg-light border-bottom">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1 pe-2">
                                    <h6 class="fw-bold mb-1">{{ $group->name }}</h6>
                                    <small class="text-muted d-block text-truncate">{{ $group->description }}</small>
                                </div>
                                <span class="badge bg-secondary">
                                    <i class="bi bi-people me-1"></i>{{ $group->users->count() }}
                                </span>
                            </div>
                        </div>

                        <div class="card-body">
                            <h6 class="text-muted text-uppercase small mb-2">
                                <i class="bi bi-person-badge me-1"></i>Nhân viên phụ trách
                            </h6>

                            @if ($assignedStaff)
                                <div class="d-flex align-items-center justify-content-between border rounded p-2 bg-light">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-primary text-white me-2">
                                            {{ strtoupper(substr($assignedStaff->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $assignedStaff->name }}</div>
                                            <small class="text-muted">{{ $assignedStaff->email }}</small>
                                        </div>
                                    </div>
                                    <form action="{{ route('admin.group_staff.remove', [$group->id, $assignedStaff->id]) }}" 
                                          method="POST" class="ms-2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Bạn có chắc muốn gỡ {{ $assignedStaff->name }} khỏi nhóm này?')">
                                            <i class="fa-solid fa-user-xmark"></i>
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="alert alert-warning py-2 px-3 mb-3">
                                    <small><i class="bi bi-exclamation-triangle me-1"></i>Chưa có nhân viên phụ trách</small>
                                </div>

                                <!-- Add Staff Form -->
                                <form action="{{ route('admin.group_staff.assign') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="group_id" value="{{ $group->id }}">
                                    <div class="mb-2">
                                        <select class="form-select form-select-sm" name="staff_id" required>
                                            <option value="">-- Chọn nhân viên --</option>
                                            @foreach ($staffList as $staff)
                                                <option value="{{ $staff->id }}">{{ $staff->name }} ({{ $staff->email }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="auto_assign" id="auto_assign_{{ $group->id }}" checked>
                                        <label class="form-check-label small" for="auto_assign_{{ $group->id }}">Tự động gán các ticket chưa có người phụ trách</label>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-primary w-100">
                                        <i class="bi bi-plus-circle me-1"></i>Gán nhân viên
                                    </button>
                                </form>
                            @endif
                        </div>

                        <div class="card-footer bg-white border-top small text-muted">
                            <div class="d-flex justify-content-between">
                                <span>
                                    @if ($assignedStaff)
                                        <span class="badge bg-success">Đã gán</span>
                                    @else
                                        <span class="badge bg-secondary">Chưa gán</span>
                                    @endif
                                </span>
                                <span><i class="bi bi-people me-1"></i>{{ $group->users->count() }} KH</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            @if ($groups->isEmpty())
                <div class="col-12 text-center py-5 text-muted">
                    <i class="bi bi-inbox display-4 d-block mb-3"></i>
                    <h5>Chưa có nhóm khách hàng</h5>
                    <p>Vui lòng tạo nhóm khách hàng trước khi phân công nhân viên.</p>
                </div>
            @endif
        </div>
    </div>

    <style>
        .avatar-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
        }

        .alert {
            border-radius: 0.5rem;
        }
    </style>
@endsection
