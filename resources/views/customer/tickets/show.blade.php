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
                        <div class="flex-grow-1">
                            <h2 class="card-title mb-3 fs-1">{{ $ticket->subject }}</h2>
                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                <span class="text-muted fs-5">#{{ $ticket->id }}</span>
                                <span class="badge {{ $ticket->category_badge }} fs-6">
                                    <i class="bi bi-tag-fill me-1"></i>{{ $ticket->category_label }}
                                </span>
                                <span class="badge {{ $ticket->priority_badge }} fs-6">
                                    @if($ticket->priority === 'urgent')
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                    @elseif($ticket->priority === 'high')
                                        <i class="bi bi-arrow-up-circle-fill me-1"></i>
                                    @elseif($ticket->priority === 'low')
                                        <i class="bi bi-arrow-down-circle-fill me-1"></i>
                                    @endif
                                    {{ $ticket->priority_label }}
                                </span>
                            </div>
                        </div>
                        <span id="ticket-status-badge" class="badge fs-6 {{ $ticket->status_badge }} p-3">
                            <i class="bi bi-circle-fill me-2" style="font-size: 0.6rem;"></i>
                            <span id="ticket-status-text">{{ $ticket->status_label }}</span>
                        </span>
                    </div>

                    <hr class="my-4">

                    <div class="row g-4">
                        <div class="col-md-3">
                            <small class="text-muted d-block mb-2">
                                <i class="bi bi-calendar-event me-2"></i>Ngày tạo
                            </small>
                            <p class="fs-5 fw-semibold">{{ $ticket->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block mb-2">
                                <i class="bi bi-arrow-repeat me-2"></i>Cập nhật
                            </small>
                            <p class="fs-5 fw-semibold">{{ $ticket->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block mb-2">
                                <i class="bi bi-person-badge me-2"></i>Nhân viên hỗ trợ
                            </small>
                            @if($ticket->assignedStaff)
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-success text-white me-2" style="width: 30px; height: 30px; font-size: 0.8rem;">
                                        {{ strtoupper(substr($ticket->assignedStaff->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="fs-5 fw-semibold text-success mb-0">{{ $ticket->assignedStaff->name }}</p>
                                        <small class="text-muted">{{ $ticket->assignedStaff->email }}</small>
                                    </div>
                                </div>
                            @else
                                <span class="badge bg-secondary">Chưa gán</span>
                            @endif
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block mb-2">
                                <i class="bi bi-chat-dots me-2"></i>Tin nhắn
                            </small>
                            <p class="fs-5 fw-semibold">
                                <span id="message-count-header">{{ $ticket->messages->count() }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Messages --}}
            <div class="mb-4">
                <h5 class="mb-4 fs-4">
                    <i class="bi bi-chat-dots me-2"></i>Cuộc trò chuyện
                    <span class="badge bg-primary" id="message-count">{{ $ticket->messages->count() }}</span>
                </h5>
                
                <div id="messages-container">
                    @foreach($ticket->messages as $message)
                        <div class="card shadow-sm mb-3 border-0 message-item @if($message->sender_id === auth()->id()) bg-light @endif" 
                             data-message-id="{{ $message->id }}">
                            <div class="card-header bg-white border-bottom">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <div>
                                        <strong class="fs-5">{{ $message->sender->name }}</strong>
                                        @if($message->sender_id === auth()->id())
                                            <span class="badge bg-primary ms-2">
                                                <i class="bi bi-person"></i> Bạn
                                            </span>
                                        @else
                                            <span class="badge bg-danger ms-2">
                                                <i class="bi bi-shield-check"></i> Hỗ trợ
                                            </span>
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

                        <form action="{{ route('customer.tickets.reply', $ticket->id) }}" method="POST" id="replyForm">
                            @csrf
                            <div class="mb-3">
                                <label for="reply_message" class="form-label fw-bold fs-5">
                                    Tin nhắn của bạn <span class="text-danger">*</span>
                                </label>
                                <textarea 
                                    id="reply_message"
                                    name="message" 
                                    class="form-control @error('message') is-invalid @enderror"
                                    placeholder="Nhập phản hồi của bạn..."
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
                            <i class="bi bi-tag me-2"></i>Danh mục
                        </small>
                        <span class="badge {{ $ticket->category_badge }} fs-6 p-2">
                            {{ $ticket->category_label }}
                        </span>
                    </div>
                    
                    <div class="mb-4 pb-4 border-bottom">
                        <small class="text-muted d-block mb-2">
                            <i class="bi bi-exclamation-triangle me-2"></i>Mức độ ưu tiên
                        </small>
                        <span class="badge {{ $ticket->priority_badge }} fs-6 p-2">
                            @if($ticket->priority === 'urgent')
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            @elseif($ticket->priority === 'high')
                                <i class="bi bi-arrow-up-circle-fill me-1"></i>
                            @endif
                            {{ $ticket->priority_label }}
                        </span>
                    </div>

                    <div class="mb-4 pb-4 border-bottom">
                        <small class="text-muted d-block mb-2">
                            <i class="bi bi-chat-dots me-2"></i>Tổng số tin nhắn
                        </small>
                        <h6 class="fs-4 fw-bold">
                            <span id="sidebar-message-count">{{ $ticket->messages->count() }}</span> tin nhắn
                        </h6>
                    </div>
                    
                    <div class="mb-4 pb-4 border-bottom">
                        <small class="text-muted d-block mb-2">
                            <i class="bi bi-flag me-2"></i>Trạng thái
                        </small>
                        <span id="sidebar-status-badge" class="badge fs-6 {{ $ticket->status_badge }} p-2">
                            {{ $ticket->status_label }}
                        </span>
                    </div>
                    
                    <div>
                        <small class="text-muted d-block mb-2">
                            <i class="bi bi-hourglass-split me-2"></i>Thời gian phản hồi dự kiến
                        </small>
                        <p class="mb-0 fs-6">
                            @switch($ticket->priority)
                                @case('urgent')
                                    <span class="text-danger fw-bold">1-2 giờ</span>
                                    @break
                                @case('high')
                                    <span class="text-warning fw-bold">4-8 giờ</span>
                                    @break
                                @case('normal')
                                    <span class="text-info fw-bold">1 ngày làm việc</span>
                                    @break
                                @case('low')
                                    <span class="text-secondary fw-bold">2-3 ngày làm việc</span>
                                    @break
                            @endswitch
                        </p>
                    </div>
                </div>
            </div>

            <a href="{{ route('customer.tickets.index') }}" class="btn btn-outline-secondary btn-lg w-100 mb-3">
                <i class="bi bi-arrow-left me-2"></i> Quay lại danh sách
            </a>

            {{-- Quick Tips --}}
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="bi bi-lightbulb text-warning me-2"></i>Mẹo hữu ích
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="small mb-0 ps-3">
                        <li class="mb-2">Phản hồi nhanh giúp giải quyết vấn đề sớm hơn</li>
                        <li class="mb-2">Cung cấp thông tin bổ sung khi được yêu cầu</li>
                        <li class="mb-2">Kiểm tra email để nhận thông báo cập nhật</li>
                        <li>Đánh giá chất lượng hỗ trợ sau khi hoàn tất</li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- CKEditor 5 CDN --}}
<script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let editorInstance;
        let lastMessageId = {{ $ticket->messages->last()->id ?? 0 }};
        let isPolling = true;
        let pollingInterval;

        // Initialize CKEditor
        ClassicEditor
            .create(document.querySelector('#reply_message'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'underline', 'link', '|', 'bulletedList', 'numberedList', '|', 'blockQuote', 'undo', 'redo'],
            })
            .then(editor => {
                editorInstance = editor;
            })
            .catch(error => console.error('CKEditor error:', error));

        // Sync và validate form
        const form = document.querySelector('#replyForm');
        form.addEventListener('submit', function(e) {
            if (editorInstance) {
                const data = editorInstance.getData().trim();
                if (!data) {
                    e.preventDefault();
                    alert('Vui lòng nhập phản hồi.');
                    return;
                }
                document.querySelector('#reply_message').value = data;
            }
        });

        // ============ CHAT REAL-TIME POLLING ============
        
        function fetchNewMessages() {
            if (!isPolling) return;
            
            fetch('{{ route("customer.tickets.messages", $ticket->id) }}?last_id=' + lastMessageId)
                .then(response => response.json())
                .then(data => {
                    if (data.messages && data.messages.length > 0) {
                        data.messages.forEach(msg => {
                            appendMessage(msg);
                            lastMessageId = msg.id;
                        });
                        
                        updateMessageCount();
                        scrollToBottom();
                        playNotificationSound();
                    }
                    
                    if (data.ticket_status !== '{{ $ticket->status }}') {
                        updateTicketStatus(data.ticket_status);
                    }
                })
                .catch(error => console.error('Lỗi:', error));
        }

        function appendMessage(message) {
            const container = document.getElementById('messages-container');
            const isOwn = message.sender_id == {{ Auth::id() }};
            
            const html = `
                <div class="card shadow-sm mb-3 border-0 message-item animate__animated animate__fadeIn ${isOwn ? 'bg-light' : ''}" 
                     data-message-id="${message.id}">
                    <div class="card-header bg-white border-bottom">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <strong class="fs-5">${escapeHtml(message.sender.name)}</strong>
                                ${isOwn ? '<span class="badge bg-primary ms-2"><i class="bi bi-person"></i> Bạn</span>' : '<span class="badge bg-danger ms-2"><i class="bi bi-shield-check"></i> Hỗ trợ</span>'}
                            </div>
                            <small class="text-muted">
                                <i class="bi bi-clock me-1"></i>${formatDate(message.created_at)}
                            </small>
                        </div>
                    </div>
                    <div class="card-body p-3 p-lg-4">
                        <div class="ck-content fs-5 lh-lg">${message.message}</div>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', html);
        }

        function scrollToBottom() {
            const container = document.getElementById('messages-container');
            const lastMsg = container.lastElementChild;
            if (lastMsg) lastMsg.scrollIntoView({ behavior: 'smooth', block: 'end' });
        }

        function updateMessageCount() {
            const count = document.querySelectorAll('.message-item').length;
            document.getElementById('message-count').textContent = count;
            document.getElementById('sidebar-message-count').textContent = count;
            document.getElementById('message-count-header').textContent = count;
        }

        function updateTicketStatus(newStatus) {
            const statusMap = {
                'new': { text: 'Mới tạo', class: 'bg-info' },
                'in_progress': { text: 'Đang xử lý', class: 'bg-warning text-dark' },
                'completed': { text: 'Hoàn tất', class: 'bg-success' },
                'closed': { text: 'Đóng', class: 'bg-secondary' }
            };

            const status = statusMap[newStatus];
            if (status) {
                document.getElementById('ticket-status-badge').className = `badge fs-6 ${status.class} p-3`;
                document.getElementById('ticket-status-text').textContent = status.text;
                document.getElementById('sidebar-status-badge').className = `badge fs-6 ${status.class} p-2`;
                document.getElementById('sidebar-status-badge').textContent = status.text;

                if (newStatus === 'closed') location.reload();
            }
        }

        function formatDate(dateString) {
            return new Date(dateString).toLocaleString('vi-VN', {
                day: '2-digit', month: '2-digit', year: 'numeric',
                hour: '2-digit', minute: '2-digit'
            });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function playNotificationSound() {
            try {
                const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBR10yPLaizsIGGS56+mnVRwHPJjb88p5KwUue8rx3ZJBChd0zPDTgjMJHmzB7Oq4YjcHSaXm87BdGgk+ltv0xnQjBSuDz/LajjgIGmy66Oq7ZSIGSans87hnHAU5jdfywnwvBSiDz/LYjzoJHWrA7uq4YjUHSKTl87FgHQlAndny');
                audio.volume = 0.3;
                audio.play().catch(e => {});
            } catch(e) {}
        }

        // Start polling
        pollingInterval = setInterval(fetchNewMessages, 3000);

        window.addEventListener('beforeunload', () => {
            isPolling = false;
            clearInterval(pollingInterval);
        });

        window.addEventListener('load', scrollToBottom);
    });
</script>

<style>
    .ck-editor__editable {
        min-height: 300px !important;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate__animated.animate__fadeIn {
        animation: fadeIn 0.5s ease-in-out;
    }

    @media (min-width: 992px) {
        .sticky-lg-top {
            position: sticky;
            top: 20px;
            z-index: 1020;
        }
    }
</style>
@endsection