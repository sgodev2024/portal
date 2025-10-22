@extends('backend.layouts.master')

@section('title', 'File Báo cáo')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">File Báo cáo</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.files.create_report') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Thêm Báo cáo
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <a href="{{ route('admin.files.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left"></i> Tất cả File
                            </a>
                        </div>
                        <div class="col-md-6">
                            <form method="GET" class="d-flex">
                                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm báo cáo..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-outline-secondary ml-2">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    @if($files->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Tiêu đề</th>
                                        <th>File</th>
                                        <th>Người nhận</th>
                                        <th>Người gửi</th>
                                        <th>Ngày gửi</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($files as $file)
                                    <tr>
                                        <td>
                                            <strong>{{ $file->title }}</strong>
                                            @if($file->description)
                                                <br><small class="text-muted">{{ Str::limit($file->description, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <i class="fas fa-file-{{ $file->file_type === 'pdf' ? 'pdf' : 'alt' }}"></i>
                                            {{ $file->file_name }}
                                            <br><small class="text-muted">{{ $file->file_size_formatted }}</small>
                                        </td>
                                        <td>
                                            @if($file->recipients && count($file->recipients) > 0)
                                                <span class="badge badge-info">{{ count($file->recipients) }} người</span>
                                                <br><small class="text-muted">{{ implode(', ', array_slice($file->recipients, 0, 2)) }}{{ count($file->recipients) > 2 ? '...' : '' }}</small>
                                            @else
                                                <span class="text-muted">Chưa có</span>
                                            @endif
                                        </td>
                                        <td>{{ $file->sender->name ?? 'N/A' }}</td>
                                        <td>
                                            @if($file->sent_at)
                                                {{ $file->sent_at->format('d/m/Y H:i') }}
                                            @else
                                                <span class="text-muted">Chưa gửi</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.files.show', $file->id) }}" class="btn btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.files.download', $file->id) }}" class="btn btn-primary">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                @if(!empty($file->recipients))
                                                    <form method="POST" action="{{ route('admin.files.resend', $file->id) }}" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success" title="Gửi lại">
                                                            <i class="fas fa-paper-plane"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center">
                            {{ $files->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                            <h5>Chưa có báo cáo nào</h5>
                            <p class="text-muted">Hãy thêm file báo cáo để gửi cho khách hàng.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection