@extends('backend.layouts.master')

@section('title', 'Tạo thông báo mới')

@section('content')
<div class="container-fluid">
    <h4 class="mb-3">Tạo thông báo mới</h4>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.notifications.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                    @error('title') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Nội dung</label>
                    <textarea name="content" rows="5" class="form-control">{{ old('content') }}</textarea>
                    @error('content') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Gửi đến vai trò <span class="text-danger">*</span></label>
                    <select name="target_role" class="form-select" required>
                        <option value="">-- Chọn đối tượng --</option>
                        <option value="user" {{ old('target_role') == 'user' ? 'selected' : '' }}>Khách hàng</option>
                        <option value="staff" {{ old('target_role') == 'staff' ? 'selected' : '' }}>Nhân viên</option>
                    </select>
                    @error('target_role') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">File đính kèm (tuỳ chọn)</label>
                    <input type="file" name="attachment" class="form-control">
                    <small class="text-muted">Dung lượng tối đa: 5MB</small>
                    @error('attachment') <small class="text-danger d-block">{{ $message }}</small> @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-paper-plane"></i> Gửi thông báo
                    </button>
                    <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
