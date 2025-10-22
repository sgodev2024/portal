@extends('backend.layouts.master')

@section('title', 'Chi tiết Báo cáo')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Chi tiết Báo cáo</h3>
                    <div class="card-tools">
                        <a href="{{ route('customer.documents.reports') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h4>{{ $file->title }}</h4>
                            @if($file->description)
                                <p class="text-muted">{{ $file->description }}</p>
                            @endif
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Tên file:</strong></td>
                                            <td>{{ $file->file_name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Kích thước:</strong></td>
                                            <td>{{ $file->file_size_formatted }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Định dạng:</strong></td>
                                            <td>{{ strtoupper($file->file_type) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Gửi bởi:</strong></td>
                                            <td>{{ $file->uploader->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Ngày gửi:</strong></td>
                                            <td>{{ $file->sent_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <div class="alert alert-info">
                                        <h5><i class="fas fa-info-circle"></i> Thông tin</h5>
                                        <p>File này được gửi riêng cho bạn. Vui lòng tải xuống để xem nội dung.</p>
                                    </div>
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
                                        <a href="{{ route('customer.files.download_report', $file->id) }}" 
                                           class="btn btn-success btn-lg">
                                            <i class="fas fa-download"></i> Tải xuống
                                        </a>
                                        
                                        <div class="text-center mt-3">
                                            <small class="text-muted">
                                                <i class="fas fa-clock"></i> 
                                                File có thể hết hạn sau 7 ngày
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

