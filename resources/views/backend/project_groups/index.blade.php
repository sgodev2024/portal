@extends('backend.layouts.master')

@section('title', 'Danh sách nhóm dự án')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4><i class="fas fa-building"></i> Danh sách nhóm dự án</h4>
        <a href="{{ route('admin.project-groups.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Thêm nhóm dự án
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="5%">STT</th>
                        <th width="15%">Mã dự án</th>
                        <th>Tên dự án</th>
                        <th width="20%">Vị trí</th>
                        <th width="10%">Số căn hộ</th>
                        <th width="10%">Trạng thái</th>
                        <th width="15%">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projectGroups as $projectGroup)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong>{{ $projectGroup->code }}</strong></td>
                            <td>{{ $projectGroup->name }}</td>
                            <td>{{ $projectGroup->location ?? '-' }}</td>
                            <td class="text-center">{{ $projectGroup->total_units ?? '-' }}</td>
                            <td>
                                @if($projectGroup->status == 'active')
                                    <span class="badge bg-success">Hoạt động</span>
                                @else
                                    <span class="badge bg-secondary">Ngừng</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.project-groups.edit', $projectGroup->id) }}" 
                                   class="btn btn-sm btn-warning" title="Sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.project-groups.destroy', $projectGroup->id) }}" 
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Bạn chắc chắn muốn xóa nhóm dự án này?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger" title="Xóa">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p>Chưa có nhóm dự án nào</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
