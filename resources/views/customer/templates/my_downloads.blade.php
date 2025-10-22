@extends('backend.layouts.master')

@section('title', 'Lịch Sử Tải Biểu Mẫu')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h3><i class="fas fa-history me-2"></i>Lịch Sử Tải Biểu Mẫu</h3>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('customer.templates.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left me-1"></i>Quay lại danh sách
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            @if($downloads->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="40%">Biểu mẫu</th>
                                <th width="15%">Danh mục</th>
                                <th width="15%">Kích thước</th>
                                <th width="15%">Ngày tải</th>
                                <th width="10%" class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($downloads as $index => $download)
                                @if($download->template)
                                    <tr>
                                        <td>{{ $downloads->firstItem() + $index }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @php
                                                    $iconMap = [
                                                        'pdf' => 'fas fa-file-pdf text-danger',
                                                        'doc' => 'fas fa-file-word text-primary',
                                                        'docx' => 'fas fa-file-word text-primary',
                                                        'xls' => 'fas fa-file-excel text-success',
                                                        'xlsx' => 'fas fa-file-excel text-success',
                                                    ];
                                                    $icon = $iconMap[$download->template->file_type] ?? 'fas fa-file';
                                                @endphp
                                                <i class="{{ $icon }} me-2 fs-4"></i>
                                                <div>
                                                    <strong>{{ $download->template->title }}</strong>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($download->template->category)
                                                <span class="badge bg-secondary">{{ $download->template->category }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $download->template->file_size_formatted }}</td>
                                        <td>{{ $download->downloaded_at->format('d/m/Y H:i') }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('customer.templates.download', $download->template->id) }}" 
                                               class="btn btn-success btn-sm" 
                                               title="Tải lại">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Hiển thị {{ $downloads->firstItem() }} - {{ $downloads->lastItem() }} / {{ $downloads->total() }}
                    </div>
                    <div>
                        {{ $downloads->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox text-muted" style="font-size: 4rem;"></i>
                    <h4 class="text-muted mt-3">Chưa có lịch sử tải xuống</h4>
                    <p class="text-muted">Bạn chưa tải biểu mẫu nào</p>
                    <a href="{{ route('customer.templates.index') }}" class="btn btn-primary mt-2">
                        <i class="fas fa-file-download me-1"></i>Xem biểu mẫu
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection