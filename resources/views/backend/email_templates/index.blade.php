@extends('backend.layouts.master')
@section('title','Email Templates')
@section('content')

<div class="container-fluid">
    <div class="d-flex justify-content-between mb-3">
        <h4>Danh sách Email Templates</h4>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>STT</th>
                <th>Mã template</th>
                <th>Tên template</th>
                <th>Subject</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($templates as $template)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $template->code }}</td>
                <td>{{ $template->name }}</td>
                <td>{{ $template->subject }}</td>
                <td>{{ $template->is_active ? 'Kích hoạt':'Tắt' }}</td>
                <td>
                    <a href="{{ route('admin.email_templates.edit',$template->id) }}" class="btn btn-sm btn-warning" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection