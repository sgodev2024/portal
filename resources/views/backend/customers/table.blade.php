<table class="table table-bordered">
    <thead>
        <tr>
            <th>STT</th>
            <th>Tên</th>
            <th>Email</th>
            <th>SĐT</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($customers as $c)
            <tr>
                <td>{{ ($customers->currentPage() - 1) * $customers->perPage() + $loop->iteration }}</td>
                <td>{{ $c->name }}</td>
                <td>{{ $c->email }}</td>
                <td>{{ $c->phone }}</td>
                <td>
                    @if ($c->is_active)
                        <span class="badge bg-success">Hoạt động</span>
                    @else
                        <span class="badge bg-secondary">Ngừng hoạt động</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('customers.edit', $c->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                    <form action="{{ route('customers.delete', $c->id) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('Xóa khách hàng này?')" class="btn btn-danger btn-sm">Xóa</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="d-flex justify-content-end">
    {{ $customers->links('pagination::bootstrap-5') }}
</div>
