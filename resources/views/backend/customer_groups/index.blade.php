@extends('backend.layouts.master')

@section('title', 'Danh sách nhóm khách hàng')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Danh sách nhóm khách hàng</h4>
        <a href="{{ route('admin.customer-groups.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Thêm nhóm
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th width="5%">ID</th>
                <th>Tên nhóm</th>
                <th>Mô tả</th>
                <th>Trạng thái</th>
                <th width="15%">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse($groups as $group)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $group->name }}</td>
                    <td>{{ $group->description }}</td>
                    <td>
                        @if($group->is_active)
                            <span class="badge bg-success">Đang hoạt động</span>
                        @else
                            <span class="badge bg-secondary">Ngừng</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.customer-groups.edit', $group->id) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.customer-groups.destroy', $group->id) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Bạn chắc chắn muốn xóa nhóm này?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center">Chưa có nhóm khách hàng nào</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
