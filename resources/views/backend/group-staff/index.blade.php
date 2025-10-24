@extends('backend.layouts.master')

@section('title', 'Quản lý Nhân viên - Nhóm khách hàng')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-1">
                            <i class="bi bi-people-fill me-2 text-primary"></i>Quản lý Nhân viên - Nhóm khách hàng
                        </h2>
                        <p class="text-muted mb-0">Phân công nhân viên phụ trách các nhóm khách hàng</p>
                    </div>
                    <div class="text-end">
                        <div class="stats-mini">
                            <span class="badge bg-primary rounded-pill px-3 py-2">
                                <i class="bi bi-collection me-1"></i>{{ $groups->count() }} nhóm
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                    <div>{{ session('success') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                    <div>{{ session('error') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if (session('info'))
            <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                    <div>{{ session('info') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <div class="d-flex align-items-start">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                    <div>
                        <strong>Đã có lỗi xảy ra:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Info Banner -->
        <div class="alert alert-light border-start border-primary border-4 mb-4 shadow-sm" role="alert">
            <div class="d-flex align-items-start">
                <i class="bi bi-lightbulb text-primary fs-4 me-3"></i>
                <div>
                    <strong class="d-block mb-1">Hướng dẫn sử dụng</strong>
                    <small class="text-muted">
                        Gán nhân viên cho từng nhóm khách hàng. Tickets mới từ khách hàng sẽ tự động được phân công cho <strong>nhân viên chính</strong> của nhóm. Bạn có thể gán lại tickets hoặc thêm nhiều nhân viên hỗ trợ.
                    </small>
                </div>
            </div>
        </div>

        <!-- Groups Grid -->
        <div class="row g-4">
            @foreach ($groups as $group)
                <div class="col-md-6 col-xl-4">
                    <div class="card group-card border-0 shadow-sm h-100">
                        <!-- Card Header -->
                        <div class="card-header bg-gradient-primary text-white border-0 p-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1 pe-2">
                                    <h6 class="mb-1 fw-bold d-flex align-items-center">
                                        <i class="bi bi-collection me-2"></i>
                                        <span class="text-truncate">{{ $group->name }}</span>
                                    </h6>
                                    <small class="opacity-90 d-block text-truncate" title="{{ $group->description }}">
                                        {{ $group->description }}
                                    </small>
                                </div>
                                <span class="badge bg-white text-primary px-2 py-1 rounded-pill flex-shrink-0">
                                    <i class="bi bi-people me-1"></i>{{ $group->users->count() }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="card-body p-3">
                            <!-- Staff Section -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-semibold small mb-0 text-uppercase text-muted">
                                        <i class="bi bi-person-badge me-1"></i>Nhân viên
                                    </h6>
                                    @if ($group->staff->where('pivot.is_primary', true)->isNotEmpty())
                                        <form action="{{ route('admin.group-staff.reassign-tickets', ['groupId' => $group->id]) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Bạn có chắc muốn gán tất cả tickets chưa xử lý của nhóm này cho nhân viên chính?')">
                                            @csrf
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-info px-2 py-1" 
                                                    title="Gán lại tickets chưa xử lý"
                                                    data-bs-toggle="tooltip">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                                
                                @if ($group->staff->count() > 0)
                                    <div class="staff-list">
                                        @foreach ($group->staff->sortByDesc('pivot.is_primary') as $staff)
                                            <div class="staff-item {{ $staff->pivot->is_primary ? 'staff-primary' : '' }}">
                                                <div class="d-flex align-items-center flex-grow-1 min-w-0">
                                                    <div class="avatar-circle {{ $staff->pivot->is_primary ? 'bg-warning text-dark' : 'bg-success text-white' }} me-2">
                                                        {{ strtoupper(substr($staff->name, 0, 1)) }}
                                                    </div>
                                                    <div class="flex-grow-1 min-w-0">
                                                        <div class="d-flex align-items-center">
                                                            <span class="fw-semibold small text-truncate d-block">
                                                                {{ $staff->name }}
                                                            </span>
                                                            @if ($staff->pivot->is_primary)
                                                                <i class="bi bi-star-fill text-warning ms-1 flex-shrink-0" style="font-size: 0.7rem;"></i>
                                                            @endif
                                                        </div>
                                                        <small class="text-muted text-truncate d-block" style="font-size: 0.7rem;">
                                                            {{ $staff->email }}
                                                        </small>
                                                    </div>
                                                </div>
                                                <form action="{{ route('admin.group-staff.remove', [$group->id, $staff->id]) }}" 
                                                      method="POST" 
                                                      class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Bạn có chắc muốn gỡ nhân viên này?\n\nNếu nhân viên đang có tickets được gán, hệ thống sẽ tự động chuyển sang nhân viên khác.')"
                                                            title="Gỡ nhân viên">
                                                        <i class="fa-solid fa-circle-xmark"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="alert alert-warning mb-0 py-2 px-3" role="alert">
                                        <small class="d-flex align-items-center">
                                            <i class="bi bi-exclamation-circle me-2"></i>
                                            <span>Chưa có nhân viên phụ trách</span>
                                        </small>
                                    </div>
                                @endif
                            </div>

                            <!-- Add Staff Form -->
                            <div class="add-staff-section border-top pt-3">
                                <form action="{{ route('admin.group-staff.assign') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="group_id" value="{{ $group->id }}">
                                    
                                    <div class="mb-2">
                                        <label class="form-label fw-semibold small mb-1">
                                            <i class="bi bi-person-plus me-1"></i>Thêm nhân viên mới
                                        </label>
                                        <select class="form-select form-select-sm" name="staff_id" required>
                                            <option value="">-- Chọn nhân viên --</option>
                                            @php
                                                $assignedStaffIds = $group->staff->pluck('id')->toArray();
                                                $availableStaff = $staffList->reject(function($staff) use ($assignedStaffIds) {
                                                    return in_array($staff->id, $assignedStaffIds);
                                                });
                                            @endphp
                                            @forelse ($availableStaff as $staff)
                                                <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                            @empty
                                                <option value="" disabled>Không còn nhân viên khả dụng</option>
                                            @endforelse
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   name="is_primary" 
                                                   value="1" 
                                                   id="primary_{{ $group->id }}"
                                                   {{ $group->staff->where('pivot.is_primary', true)->isEmpty() ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="primary_{{ $group->id }}">
                                                <i class="bi bi-star text-warning me-1"></i>Đặt làm nhân viên chính
                                                @if ($group->staff->where('pivot.is_primary', true)->isEmpty())
                                                    <span class="badge bg-danger ms-1" style="font-size: 0.65rem;">Bắt buộc</span>
                                                @endif
                                            </label>
                                        </div>
                                    </div>
                                    
                                    @if ($availableStaff->isNotEmpty())
                                        <button type="submit" class="btn btn-primary btn-sm w-100">
                                            <i class="bi bi-plus-circle me-1"></i>Gán nhân viên
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-secondary btn-sm w-100" disabled>
                                            <i class="bi bi-person-x me-1"></i>Không còn nhân viên
                                        </button>
                                    @endif
                                </form>
                            </div>
                        </div>
                        
                        <!-- Card Footer -->
                        <div class="card-footer bg-light border-0 py-2 px-3">
                            <div class="d-flex justify-content-between align-items-center text-muted small">
                                <span class="d-flex align-items-center">
                                    <i class="bi bi-person-badge me-1"></i>
                                    <strong>{{ $group->staff->count() }}</strong>
                                    <span class="ms-1">nhân viên</span>
                                </span>
                                <span class="d-flex align-items-center">
                                    <i class="bi bi-people me-1"></i>
                                    <strong>{{ $group->users->count() }}</strong>
                                    <span class="ms-1">khách hàng</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            
            @if ($groups->isEmpty())
                <div class="col-12">
                    <div class="text-center py-5">
                        <div class="empty-state">
                            <i class="bi bi-inbox display-1 text-muted opacity-25"></i>
                            <h5 class="mt-4 text-muted">Chưa có nhóm khách hàng nào</h5>
                            <p class="text-muted">Vui lòng tạo nhóm khách hàng trước khi phân công nhân viên</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        /* Card Hover Effects */
        .group-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }
        
        .group-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.15) !important;
        }

        /* Gradient Header */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        }

        /* Avatar Circle */
        .avatar-circle {
            width: 32px;
            height: 32px;
            min-width: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.8rem;
            flex-shrink: 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Staff List */
        .staff-list {
            max-height: 240px;
            overflow-y: auto;
            padding-right: 4px;
        }

        .staff-list::-webkit-scrollbar {
            width: 4px;
        }

        .staff-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .staff-list::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        .staff-list::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Staff Item */
        .staff-item {
            padding: 0.65rem 0.75rem;
            margin-bottom: 0.5rem;
            border-radius: 10px;
            background: #f8f9fa;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .staff-item:hover {
            background: #e9ecef;
            border-color: #dee2e6;
            transform: translateX(4px);
        }

        .staff-item.staff-primary {
            background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%);
            border: 1px solid #ffc107;
        }

        .staff-item.staff-primary:hover {
            background: linear-gradient(135deg, #ffe69c 0%, #ffd43b 100%);
        }

        .staff-item:last-child {
            margin-bottom: 0;
        }

        /* Remove Button */
        .btn-remove-staff {
            padding: 0.25rem 0.5rem !important;
            font-size: 0.75rem;
            line-height: 1;
            border-radius: 4px;
            transition: all 0.2s ease;
        }

        .btn-remove-staff:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3);
        }

        .btn-remove-staff i {
            font-size: 0.7rem;
        }

        /* Min width for text truncation */
        .min-w-0 {
            min-width: 0;
        }

        /* Add Staff Section */
        .add-staff-section {
            background: #f8f9fa;
            margin: -0.75rem;
            margin-top: 0;
            padding: 0.75rem !important;
            border-bottom-left-radius: calc(0.375rem - 1px);
            border-bottom-right-radius: calc(0.375rem - 1px);
        }

        /* Form Controls */
        .form-select-sm {
            border-radius: 8px;
            border-color: #dee2e6;
        }

        .form-select-sm:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.1);
        }

        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        /* Buttons */
        .btn-sm {
            font-size: 0.8rem;
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            font-weight: 500;
        }

        /* Alert Animations */
        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert {
            animation: slideInDown 0.3s ease;
            border: none;
            border-radius: 10px;
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }
            to {
                opacity: 0;
                transform: translateY(-20px);
            }
        }

        .alert.fade-out {
            animation: fadeOut 0.3s ease forwards;
        }

        /* Empty State */
        .empty-state {
            padding: 3rem 1rem;
        }

        .empty-state i {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        /* Badge */
        .badge {
            font-weight: 500;
        }

        /* Stats Mini */
        .stats-mini .badge {
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .group-card:hover {
                transform: translateY(-4px);
            }

            .staff-list {
                max-height: 200px;
            }

            .avatar-circle {
                width: 28px;
                height: 28px;
                min-width: 28px;
                font-size: 0.75rem;
            }
        }

        /* Smooth Transitions */
        * {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
    </style>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Enable Bootstrap tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Auto-dismiss alerts after 5 seconds
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert-dismissible');
                alerts.forEach(function(alert) {
                    alert.classList.add('fade-out');
                    setTimeout(() => {
                        const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                        bsAlert.close();
                    }, 300);
                });
            }, 5000);

            // Smooth scroll for long staff lists
            const staffLists = document.querySelectorAll('.staff-list');
            staffLists.forEach(list => {
                list.style.scrollBehavior = 'smooth';
            });
        });
    </script>
    @endpush
@endsection