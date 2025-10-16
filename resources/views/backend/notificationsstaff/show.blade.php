@extends('backend.layouts.master')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Chi tiết thông báo</h3>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ $notification->title }}</h5>
                    <p class="card-text">{!! nl2br(e($notification->content)) !!}</p>
                    @if ($notification->attachment_path)
                        <a href="{{ Storage::url($notification->attachment_path) }}" target="_blank" class="btn btn-outline-secondary">Tệp đính kèm</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('backend.layouts.master')

@section('title', 'Chi tiết thông báo')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-envelope-open-text me-2"></i>Chi tiết thông báo</h4>
            <a href="{{ route('staff.notifications.index') }}" class="btn btn-light btn-sm">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>

        <div class="card-body">
            <div class="mb-3">
                <h5 class="fw-bold text-primary">{{ $notification->title }}</h5>
                <p class="text-muted mb-1">
                    Gửi bởi: <strong>{{ $notification->creator->name ?? 'Hệ thống' }}</strong> •
                    {{ $notification->created_at->format('d/m/Y H:i') }}
                </p>
                <hr>
            </div>

            <div class="mb-4">
                <p>{!! nl2br(e($notification->content)) !!}</p>
            </div>
        </div>
    </div>
</div>
@endsection
