@extends('backend.layouts.master')

@section('title', 'Lịch sử tải')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Lịch sử tải</h3>
                </div>
                <div class="card-body">
                    @forelse($downloads as $download)
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5 class="card-title">{{ $download->file->title }}</h5>
                                    <p class="card-text">
                                        <i class="fas fa-file-{{ $download->file->file_type === 'pdf' ? 'pdf' : ($download->file->file_type === 'doc' || $download->file->file_type === 'docx' ? 'word' : 'alt') }}"></i>
                                        {{ $download->file->file_name }}
                                    </p>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                <i class="fas fa-weight"></i> {{ $download->file->file_size_formatted }}
                                            </small>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                <i class="fas fa-clock"></i> {{ $download->downloaded_at->format('d/m/Y H:i') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-right">
                                    <div class="mb-2">
                                        <span class="badge badge-{{ $download->file->file_category === 'report' ? 'warning' : 'info' }}">
                                            {{ $download->file->file_category === 'report' ? 'Báo cáo' : 'Biểu mẫu' }}
                                        </span>
                                    </div>
                                    <div>
                                        @if($download->file->file_category === 'report')
                                            <a href="{{ route('customer.files.download_report', $download->file->id) }}" 
                                               class="btn btn-primary btn-sm">
                                                <i class="fas fa-download"></i> Tải lại
                                            </a>
                                        @else
                                            <a href="{{ route('customer.files.download_template', $download->file->id) }}" 
                                               class="btn btn-success btn-sm">
                                                <i class="fas fa-download"></i> Tải lại
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <i class="fas fa-download fa-3x text-muted mb-3"></i>
                        <h5>Chưa có lịch sử tải nào</h5>
                        <p class="text-muted">Lịch sử tải file sẽ hiển thị ở đây.</p>
                    </div>
                    @endforelse

                    @if($downloads->count() > 0)
                    <div class="d-flex justify-content-center">
                        {{ $downloads->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

