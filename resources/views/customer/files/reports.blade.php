@extends('backend.layouts.master')

@section('title', 'Báo cáo đã nhận')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Báo cáo đã nhận</h3>
                </div>
                <div class="card-body">
                    @forelse($files as $file)
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5 class="card-title">{{ $file->title }}</h5>
                                    @if($file->description)
                                        <p class="card-text">{{ $file->description }}</p>
                                    @endif
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                <i class="fas fa-user"></i> Gửi bởi: {{ $file->uploader->name }}
                                            </small>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                <i class="fas fa-clock"></i> Ngày gửi: {{ $file->sent_at->format('d/m/Y H:i') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-right">
                                    <div class="mb-2">
                                        <i class="fas fa-file-{{ $file->file_type === 'pdf' ? 'pdf' : ($file->file_type === 'doc' || $file->file_type === 'docx' ? 'word' : 'alt') }}"></i>
                                        {{ $file->file_name }}
                                    </div>
                                    <div class="mb-2">
                                        <span class="badge badge-info">{{ $file->file_size_formatted }}</span>
                                    </div>
                                    <div>
                                        <a href="{{ route('customer.files.download_report', $file->id) }}" 
                                           class="btn btn-primary btn-sm">
                                            <i class="fas fa-download"></i> Tải xuống
                                        </a>
                                        <a href="{{ route('customer.files.show_report', $file->id) }}" 
                                           class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> Xem chi tiết
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5>Chưa có báo cáo nào</h5>
                        <p class="text-muted">Bạn sẽ nhận được báo cáo qua email khi admin gửi.</p>
                    </div>
                    @endforelse

                    @if($files->count() > 0)
                    <div class="d-flex justify-content-center">
                        {{ $files->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

