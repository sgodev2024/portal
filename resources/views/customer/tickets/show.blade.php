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
                            {{-- <p class="text-muted fs-5 mb-0">#{{ $ticket->id }}</p> --}}
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
                        <div class="col-md-6">
                            <small class="text-muted d-block mb-2">
                                <i class="bi bi-calendar-event me-2"></i>Ngày tạo
                            </small>
                            <p class="fs-5 fw-semibold">{{ $ticket->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block mb-2">
                                <i class="bi bi-arrow-repeat me-2"></i>Cập nhật lần cuối
                            </small>
                            <p class="fs-5 fw-semibold">{{ $ticket->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Messages --}}
            <div class="mb-4">
                <h5 class="mb-4 fs-4">
                    <i class="bi bi-chat-dots me-2"></i>Cuộc trò chuyện
                </h5>
                @foreach($ticket->messages as $message)
                    <div class="card shadow-sm mb-3 border-0 @if($message->sender_id === auth()->id())  bg-opacity-10 @endif">
                        <div class="card-header bg-white border-bottom">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <strong class="fs-5">{{ $message->sender->name }}</strong>
                                    @if($message->sender_id === auth()->id())
                                        <span class="badge bg-primary ms-2">Bạn</span>
                                    @else
                                        <span class="badge bg-secondary ms-2">Hỗ trợ</span>
                                    @endif
                                </div>
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>{{ $message->created_at->format('d/m/Y H:i') }}
                                </small>
                            </div>
                        </div>
                        <div class="card-body p-3 p-lg-4">
                            <div class="ck-content fs-5 lh-lg">
                                {!! $message->message !!}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Reply Form --}}
            @if($ticket->status !== 'closed')
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-primary text-white py-4">
                        <h5 class="mb-0 fs-5">
                            <i class="bi bi-reply me-2"></i>Trả lời
                        </h5>
                    </div>
                    <div class="card-body p-4 p-lg-5">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('customer.tickets.reply', $ticket->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="reply_message" class="form-label fw-bold fs-5">Tin nhắn của bạn <span class="text-danger">*</span></label>
                                <textarea 
                                    id="reply_message"
                                    name="message" 
                                    class="form-control @error('message') is-invalid @enderror"
                                    rows="6"
                                    placeholder="Nhập phản hồi của bạn..."
                                    required
                                >{{ old('message') }}</textarea>
                                @error('message')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-send me-2"></i> Gửi Phản Hồi
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="alert alert-info alert-lg p-4" role="alert">
                    <div class="d-flex gap-3">
                        <i class="bi bi-info-circle fs-4"></i>
                        <div>
                            <h5 class="alert-heading">Ticket đã đóng</h5>
                            <p class="mb-0">Ticket này đã đóng. Nếu bạn có vấn đề mới, vui lòng tạo một ticket mới.</p>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        {{-- Sidebar --}}
        <div class="col-12 col-lg-4">
            <div class="card shadow-lg border-0 mb-4 sticky-lg-top" style="top: 20px;">
                <div class="card-header bg-secondary text-white py-4">
                    <h5 class="mb-0 fs-5">
                        <i class="bi bi-info-circle me-2"></i>Thông tin Ticket
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4 pb-4 border-bottom">
                        <small class="text-muted d-block mb-2">
                            <i class="bi bi-chat-dots me-2"></i>Tổng số tin nhắn
                        </small>
                        <h6 class="fs-4 fw-bold">{{ $ticket->messages->count() }} tin nhắn</h6>
                    </div>
                    <div class="mb-4 pb-4 border-bottom">
                        <small class="text-muted d-block mb-2">
                            <i class="bi bi-tag me-2"></i>Trạng thái
                        </small>
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
                        <small class="text-muted d-block mb-2">
                            <i class="bi bi-hourglass-split me-2"></i>Thời gian phản hồi
                        </small>
                        <p class="mb-0 fs-5">Trong 24 giờ</p>
                    </div>
                </div>
            </div>

            <a href="{{ route('customer.tickets.index') }}" class="btn btn-outline-secondary btn-lg w-100">
                <i class="bi bi-arrow-left me-2"></i> Quay lại danh sách
            </a>
        </div>

    </div>
</div>

{{-- CKEditor 5 CDN --}}
<script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        ClassicEditor
            .create(document.querySelector('#reply_message'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'underline', 'link', '|', 'bulletedList', 'numberedList', '|', 'blockQuote', 'undo', 'redo'],
                height: '300px'
            })
            .catch(error => console.error('CKEditor error:', error));
    });
</script>

<style>
    .ck-editor__editable {
        min-height: 300px !important;
    }
</style>
@endsection