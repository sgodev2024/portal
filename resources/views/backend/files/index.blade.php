@extends('backend.layouts.master')

@section('title', 'Quản lý File')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quản lý File</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.files.create_report') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Thêm Báo cáo
                        </a>
                        <a href="{{ route('admin.files.create_template') }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> Thêm Biểu mẫu
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.files.index') }}" 
                                   class="btn {{ request()->routeIs('admin.files.index') ? 'btn-primary' : 'btn-outline-primary' }}">
                                    Tất cả
                                </a>
                                <a href="{{ route('admin.files.reports') }}" 
                                   class="btn {{ request()->routeIs('admin.files.reports') ? 'btn-primary' : 'btn-outline-primary' }}">
                                    Báo cáo
                                </a>
                                <a href="{{ route('admin.files.templates') }}" 
                                   class="btn {{ request()->routeIs('admin.files.templates') ? 'btn-primary' : 'btn-outline-primary' }}">
                                    Biểu mẫu
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <form method="GET" class="d-flex">
                                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm..." value="{{ request('search') }}">
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
                                        <th>Loại</th>
                                        <th>File</th>
                                        <th>Kích thước</th>
                                        <th>Người tạo</th>
                                        <th>Ngày tạo</th>
                                        <th>Trạng thái</th>
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
                                            <span class="badge badge-{{ $file->file_category === 'report' ? 'primary' : 'success' }}">
                                                {{ $file->file_category === 'report' ? 'Báo cáo' : 'Biểu mẫu' }}
                                            </span>
                                        </td>
                                        <td>
                                            <i class="fas fa-file-{{ $file->file_type === 'pdf' ? 'pdf' : 'alt' }}"></i>
                                            {{ $file->file_name }}
                                        </td>
                                        <td>{{ $file->file_size_formatted }}</td>
                                        <td>{{ $file->uploader->name ?? 'N/A' }}</td>
                                        <td>{{ $file->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if($file->is_active)
                                                <span class="badge badge-success">Hoạt động</span>
                                            @else
                                                <span class="badge badge-secondary">Không hoạt động</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.files.show', $file->id) }}" class="btn btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.files.edit', $file->id) }}" class="btn btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('admin.files.download', $file->id) }}" class="btn btn-primary">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                @if($file->file_category === 'report' && !empty($file->recipients))
                                                    <form method="POST" action="{{ route('admin.files.resend', $file->id) }}" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success" title="Gửi lại">
                                                            <i class="fas fa-paper-plane"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                <form method="POST" action="{{ route('admin.files.destroy', $file->id) }}" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
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
                            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                            <h5>Chưa có file nào</h5>
                            <p class="text-muted">Hãy thêm file báo cáo hoặc biểu mẫu để bắt đầu.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection