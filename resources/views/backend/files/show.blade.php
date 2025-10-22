@extends('backend.layouts.master')

@section('title', 'Chi tiết File')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Chi tiết File</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.files.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Thông tin File</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Tiêu đề:</strong></td>
                                            <td>{{ $file->title }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Loại:</strong></td>
                                            <td>
                                                <span class="badge badge-{{ $file->file_category === 'report' ? 'primary' : 'success' }}">
                                                    {{ $file->file_category === 'report' ? 'Báo cáo' : 'Biểu mẫu' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tên file:</strong></td>
                                            <td>
                                                <i class="fas fa-file-{{ $file->file_type === 'pdf' ? 'pdf' : 'alt' }}"></i>
                                                {{ $file->file_name }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Kích thước:</strong></td>
                                            <td>{{ $file->file_size_formatted }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Người tạo:</strong></td>
                                            <td>{{ $file->uploader->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Ngày tạo:</strong></td>
                                            <td>{{ $file->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        @if($file->file_category === 'report')
                                        <tr>
                                            <td><strong>Người gửi:</strong></td>
                                            <td>{{ $file->sender->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Ngày gửi:</strong></td>
                                            <td>
                                                @if($file->sent_at)
                                                    {{ $file->sent_at->format('d/m/Y H:i') }}
                                                @else
                                                    <span class="text-muted">Chưa gửi</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td><strong>Trạng thái:</strong></td>
                                            <td>
                                                @if($file->is_active)
                                                    <span class="badge badge-success">Hoạt động</span>
                                                @else
                                                    <span class="badge badge-secondary">Không hoạt động</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Lượt tải:</strong></td>
                                            <td>{{ $file->download_count }} lượt</td>
                                        </tr>
                                    </table>
                                    
                                    @if($file->description)
                                    <div class="mt-3">
                                        <strong>Mô tả:</strong>
                                        <p class="mt-2">{{ $file->description }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Thao tác</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('admin.files.download', $file->id) }}" class="btn btn-primary">
                                            <i class="fas fa-download"></i> Tải xuống
                                        </a>
                                        <a href="{{ route('admin.files.edit', $file->id) }}" class="btn btn-warning">
                                            <i class="fas fa-edit"></i> Chỉnh sửa
                                        </a>
                                        @if($file->file_category === 'report' && !empty($file->recipients))
                                        <form method="POST" action="{{ route('admin.files.resend', $file->id) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success w-100" title="Gửi lại">
                                                <i class="fas fa-paper-plane"></i> Gửi lại
                                            </button>
                                        </form>
                                        @endif
                                        <form method="POST" action="{{ route('admin.files.destroy', $file->id) }}" 
                                              onsubmit="return confirm('Bạn có chắc muốn xóa file này?')" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger w-100">
                                                <i class="fas fa-trash"></i> Xóa
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            @if($file->file_category === 'report' && !empty($file->recipients))
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5>Người nhận ({{ count($file->recipients) }})</h5>
                                </div>
                                <div class="card-body">
                                    <div class="list-group">
                                        @foreach($file->recipients as $email)
                                        <div class="list-group-item">
                                            <i class="fas fa-envelope"></i> {{ $email }}
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    @if($file->downloads->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Lịch sử tải xuống</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Người tải</th>
                                                    <th>IP Address</th>
                                                    <th>Thời gian</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($file->downloads as $download)
                                                <tr>
                                                    <td>{{ $download->user->name ?? 'N/A' }}</td>
                                                    <td>{{ $download->ip_address }}</td>
                                                    <td>{{ $download->downloaded_at->format('d/m/Y H:i') }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection