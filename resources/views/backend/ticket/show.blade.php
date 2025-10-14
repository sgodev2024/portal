@extends('backend.layouts.master')

@section('title', 'Chi tiết Ticket')

@section('content')
<div class="container-fluid py-4 py-lg-5">
    <div class="row g-4">
        {{-- Main Content --}}
        <div class="col-12 col-lg-8">
            
            {{-- Header --}}
            <div class="card shadow-lg border-0 mb-4">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3 mb-4">
                        <div>
                            <h2 class="card-title mb-2 fs-1">{{ $ticket->subject }}</h2>
                            <p class="text-muted fs-5 mb-0">
                                {{-- <i class="bi bi-hash me-1"></i>#{{ $ticket->id }} •  --}}
                                <i class="bi bi-person me-1"></i>Từ: <strong>{{ $ticket->user->name }}</strong>
                            </p>
                        </div>
                        <span class="badge fs-6
                            @if($ticket->status === 'open') bg-warning text-dark
                            @elseif($ticket->status === 'in_progress') bg-info
                            @else bg-success
                            @endif p-3">
                            <i class="bi bi-circle-fill me-2" style="font-size: 0.6rem;"></i>
                            @switch($ticket->status)
                                @case('open') Mới @break
                                @case('in_progress') Đang xử lý @break
                                @case('closed') Đã đóng @break
                            @endswitch
                        </span>
                    </div>

                    <hr class="my-4">

                    <div class="row g-4">
                        <div class="col-md-4">
                            <small class="text-muted d-block mb-2"><i class="bi bi-envelope me-2"></i>Email khách hàng</small>
                            <p class="fs-5">
                                <a href="mailto:{{ $ticket->user->email }}" class="text-decoration-none">
                                    {{ $ticket->user->email }}
                                </a>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block mb-2"><i class="bi bi-calendar-event me-2"></i>Ngày tạo</small>
                            <p class="fs-5 fw-semibold">{{ $ticket->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block mb-2"><i class="bi bi-arrow-repeat me-2"></i>Cập nhật</small>
                            <p class="fs-5 fw-semibold">{{ $ticket->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Messages --}}
            <div class="mb-4">
                <h5 class="mb-4 fs-4"><i class="bi bi-chat-dots me-2"></i>Cuộc trò chuyện</h5>
                @foreach($ticket->messages as $message)
                    <div class="card shadow-sm mb-3 border-0">
                        <div class="card-header bg-white border-bottom">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <strong class="fs-5">{{ $message->sender->name }}</strong>
                                    @if(isset($message->sender->role) && ($message->sender->role === '2' || $message->sender->role === '1'))
                                        <span class="badge bg-danger ms-2">Admin</span>
                                    @else
                                        <span class="badge bg-secondary ms-2">Khách hàng</span>
                                    @endif
                                </div>
                                <small class="text-muted"><i class="bi bi-clock me-1"></i>{{ $message->created_at->format('d/m/Y H:i') }}</small>
                            </div>
                        </div>
                        <div class="card-body p-3 p-lg-4">
                            <div class="ck-content fs-5 lh-lg">{!! $message->message !!}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Reply Form --}}
            @if($ticket->status !== 'closed')
                <div class="card shadow-lg border-0 mb-4">
                    <div class="card-header bg-primary text-white py-4">
                        <h5 class="mb-0 fs-5"><i class="bi bi-reply me-2"></i>Trả lời</h5>
                    </div>
                    <div class="card-body p-4 p-lg-5">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('admin.tickets.reply', $ticket->id) }}" method="POST" id="replyForm">
                            @csrf
                            <div class="mb-3">
                                <label for="admin_reply_message" class="form-label fw-bold fs-5">Phản hồi <span class="text-danger">*</span></label>
                                <textarea 
                                    id="admin_reply_message"
                                    name="message" 
                                    class="form-control @error('message') is-invalid @enderror"
                                    placeholder="Nhập phản hồi..."
                                >{{ old('message') }}</textarea>
                                @error('message')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-send me-2"></i> Gửi Phản Hồi</button>
                        </form>
                    </div>
                </div>
            @endif

        </div>

        {{-- Sidebar --}}
        <div class="col-12 col-lg-4">
            <div class="card shadow-lg border-0 mb-4 sticky-lg-top" style="top: 20px;">
                <div class="card-header bg-secondary text-white py-4">
                    <h5 class="mb-0 fs-5"><i class="bi bi-info-circle me-2"></i>Thông tin Ticket</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4 pb-4 border-bottom">
                        <small class="text-muted d-block mb-2"><i class="bi bi-person me-2"></i>Khách hàng</small>
                        <h6 class="fs-5 fw-bold">{{ $ticket->user->name }}</h6>
                        <small class="text-muted">{{ $ticket->user->email }}</small>
                    </div>
                    <div class="mb-4 pb-4 border-bottom">
                        <small class="text-muted d-block mb-2"><i class="bi bi-chat-dots me-2"></i>Tổng số tin nhắn</small>
                        <h6 class="fs-4 fw-bold">{{ $ticket->messages->count() }} tin nhắn</h6>
                    </div>
                    <div class="mb-4 pb-4 border-bottom">
                        <small class="text-muted d-block mb-2"><i class="bi bi-tag me-2"></i>Trạng thái</small>
                        <span class="badge fs-6
                            @if($ticket->status === 'open') bg-warning text-dark
                            @elseif($ticket->status === 'in_progress') bg-info
                            @else bg-success
                            @endif p-2">
                            @switch($ticket->status)
                                @case('open') Mới @break
                                @case('in_progress') Đang xử lý @break
                                @case('closed') Đã đóng @break
                            @endswitch
                        </span>
                    </div>
                    <div>
                        <small class="text-muted d-block mb-2"><i class="bi bi-hourglass-split me-2"></i>Tạo lúc</small>
                        <p class="mb-0 fs-6">{{ $ticket->created_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            @if($ticket->status !== 'closed')
                <div class="card shadow-lg border-danger mb-4">
                    <div class="card-header bg-danger text-white py-3">
                        <h5 class="mb-0 fs-5"><i class="bi bi-gear me-2"></i>Hành động</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('admin.tickets.close', $ticket->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn đóng ticket này?');">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-danger btn-lg w-100"><i class="bi bi-x-circle me-2"></i> Đóng Ticket</button>
                        </form>
                    </div>
                </div>
            @else
                <div class="alert alert-success alert-lg p-4 mb-4" role="alert">
                    <div class="d-flex gap-3 align-items-center">
                        <i class="bi bi-check-circle fs-4"></i>
                        <div>
                            <h5 class="alert-heading mb-0">Ticket đã đóng</h5>
                        </div>
                    </div>
                </div>
            @endif

            <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-secondary btn-lg w-100"><i class="bi bi-arrow-left me-2"></i> Quay lại danh sách</a>
        </div>
    </div>
</div>

{{-- CKEditor 5 CDN --}}
<script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let editorInstance;

    ClassicEditor
        .create(document.querySelector('#admin_reply_message'), {
            toolbar: ['heading','|','bold','italic','underline','link','|','bulletedList','numberedList','|','blockQuote','undo','redo']
        })
        .then(editor => {
            editorInstance = editor;
            console.log('CKEditor initialized');
        })
        .catch(error => console.error('CKEditor error:', error));

    // Sync dữ liệu CKEditor trước submit và validate
    const form = document.querySelector('#replyForm');
    form.addEventListener('submit', function(e) {
        if(editorInstance){
            const data = editorInstance.getData().trim();
            if(!data){
                e.preventDefault();
                alert('Vui lòng nhập phản hồi.');
                return;
            }
            document.querySelector('#admin_reply_message').value = data;
        }
    });
});
</script>

<style>
.ck-editor__editable {
    min-height: 300px !important;
}
</style>
@endsection
