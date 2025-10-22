@extends('backend.layouts.master')

@section('title', 'Biểu mẫu')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Biểu mẫu</h3>
                </div>
                <div class="card-body">
                    <!-- Filter -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            @if($categories->count() > 0)
                            <div class="btn-group" role="group">
                                <a href="{{ route('customer.files.templates') }}" 
                                   class="btn {{ request()->get('category') == '' ? 'btn-primary' : 'btn-outline-primary' }}">
                                    Tất cả
                                </a>
                                @foreach($categories as $category)
                                <a href="{{ route('customer.files.templates', ['category' => $category]) }}" 
                                   class="btn {{ request()->get('category') == $category ? 'btn-primary' : 'btn-outline-primary' }}">
                                    {{ $category }}
                                </a>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <form method="GET" class="d-flex">
                                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm biểu mẫu..." 
                                       value="{{ request('search') }}">
                                <button type="submit" class="btn btn-outline-secondary ml-2">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Templates Grid -->
                    <div class="row">
                        @forelse($files as $file)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="text-center mb-3">
                                        <i class="fas fa-file-{{ $file->file_type === 'pdf' ? 'pdf' : ($file->file_type === 'doc' || $file->file_type === 'docx' ? 'word' : 'alt') }} fa-3x text-primary"></i>
                                    </div>
                                    <h5 class="card-title">{{ $file->title }}</h5>
                                    @if($file->description)
                                        <p class="card-text text-muted">{{ $file->description }}</p>
                                    @endif
                                    <div class="mb-2">
                                        <small class="text-muted">
                                            <i class="fas fa-download"></i> {{ $file->download_count }} lượt tải
                                        </small>
                                    </div>
                                    <div class="mb-2">
                                        <small class="text-muted">
                                            <i class="fas fa-weight"></i> {{ $file->file_size_formatted }}
                                        </small>
                                    </div>
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <i class="fas fa-user"></i> {{ $file->uploader->name }}
                                        </small>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="{{ route('customer.files.download_template', $file->id) }}" 
                                           class="btn btn-primary btn-sm">
                                            <i class="fas fa-download"></i> Tải xuống
                                        </a>
                                        @if(in_array($file->id, $myDownloads))
                                            <span class="badge badge-success">Đã tải</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                <h5>Chưa có biểu mẫu nào</h5>
                                <p class="text-muted">Admin sẽ thêm biểu mẫu để bạn có thể tải về.</p>
                            </div>
                        </div>
                        @endforelse
                    </div>

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

