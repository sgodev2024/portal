@extends('backend.layouts.master')

@section('title', 'Chi tiết thông báo')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Chi tiết thông báo</h4>
        <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <!-- Thông tin thông báo -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-bell"></i> Thông tin thông báo</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-12">
                    Tiêu đề gửi:<h5 class="fw-bold text-primary">{{ $notification->title }}</h5>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user text-muted me-2"></i>
                        <div>
                            <small class="text-muted d-block">Người tạo</small>
                            <strong>{{ $notification->creator->name ?? '-' }}</strong>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    @php
                        $roleLabels = [
                            'staff' => 'Nhân viên',
                            'user' => 'Khách hàng',
                        ];
                        $roleColors = [
                            'staff' => 'warning',
                            'user' => 'info',
                        ];
                    @endphp
                    <div class="d-flex align-items-center">
                        <i class="fas fa-users text-muted me-2"></i>
                        <div>
                            <small class="text-muted d-block">Gửi cho</small>
                            <span class="badge bg-{{ $roleColors[$notification->target_role] ?? 'secondary' }}">
                                {{ $roleLabels[$notification->target_role] ?? strtoupper($notification->target_role) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <div class="mb-3">
                <h6 class="fw-bold mb-2"><i class="fas fa-align-left"></i> Nội dung:</h6>
                <div class="p-3 bg-light rounded">
                    {!! nl2br(e($notification->content ?? 'Không có nội dung')) !!}
                </div>
            </div>

            @if ($notification->attachment_path)
                <div class="mt-3">
                    <h6 class="fw-bold mb-2"><i class="fas fa-paperclip"></i> File đính kèm:</h6>
                    <a href="{{ asset('storage/' . $notification->attachment_path) }}"
                       target="_blank"
                       class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-download"></i> Tải xuống file đính kèm
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
