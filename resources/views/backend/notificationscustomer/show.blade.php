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

