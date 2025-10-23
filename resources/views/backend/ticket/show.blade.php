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
                                    <i class="bi bi-hash me-1"></i>#{{ $ticket->id }} • 
                                    <i class="bi bi-person me-1"></i>Từ: <strong>{{ $ticket->user->name }}</strong>
                                </p>
                            </div>
                            <span id="ticket-status-badge"
                                class="badge fs-6
                            @if ($ticket->status === 'open') bg-warning text-dark
                            @elseif($ticket->status === 'in_progress') bg-info
                            @else bg-success @endif p-3">
                                <i class="bi bi-circle-fill me-2" style="font-size: 0.6rem;"></i>
                                <span id="ticket-status-text">
                                    @switch($ticket->status)
                                        @case('open')
                                            Mới
                                        @break
                                        @case('in_progress')
                                            Đang xử lý
                                        @break
                                        @case('closed')
                                            Đã đóng
                                        @break
                                    @endswitch
                                </span>
                            </span>
                        </div>

                        <hr class="my-4">

                        <div class="row g-4">
                            <div class="col-md-3">
                                <small class="text-muted d-block mb-2"><i class="bi bi-envelope me-2"></i>Email khách hàng</small>
                                <p class="fs-5">
                                    <a href="mailto:{{ $ticket->user->email }}" class="text-decoration-none">
                                        {{ $ticket->user->email }}
                                    </a>
                                </p>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block mb-2"><i class="bi bi-calendar-event me-2"></i>Ngày tạo</small>
                                <p class="fs-5 fw-semibold">{{ $ticket->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block mb-2"><i class="bi bi-arrow-repeat me-2"></i>Cập nhật</small>
                                <p class="fs-5 fw-semibold">{{ $ticket->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block mb-2"><i class="bi bi-person-check me-2"></i>Phụ trách</small>
                                @if($ticket->assignedStaff)
                                    <p class="fs-5 fw-semibold text-success">{{ $ticket->assignedStaff->name }}</p>
                                @else
                                    <span class="badge bg-secondary">Chưa gán</span>
                                @endif
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
                        @foreach ($ticket->messages as $message)
                            <div class="card shadow-sm mb-3 border-0 message-item" data-message-id="{{ $message->id }}">
                                <div class="card-header bg-white border-bottom">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                        <div>
                                            <strong class="fs-5">{{ $message->sender->name }}</strong>
                                            @if (isset($message->sender->role) && ($message->sender->role === '2' || $message->sender->role === '1'))
                                                <span class="badge bg-danger ms-2">
                                                    <i class="bi bi-shield-check"></i> Staff
                                                </span>
                                            @else
                                                <span class="badge bg-secondary ms-2">
                                                    <i class="bi bi-person"></i> Khách hàng
                                                </span>
                                            @endif
                                        </div>
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>{{ $message->created_at->format('d/m/Y H:i') }}
                                        </small>
                                    </div>
                                </div>
                                <div class="card-body p-3 p-lg-4">
                                    <div class="ck-content fs-5 lh-lg">{!! $message->message !!}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Typing Indicator -->
                    <div id="typing-indicator" class="text-muted small mb-3" style="display: none;">
                        <i class="bi bi-three-dots"></i> Đang có người đang nhập...
                    </div>
                </div>

                {{-- Reply Form --}}
                @if ($ticket->status !== 'closed')
                    <div class="card shadow-lg border-0 mb-4">
                        <div class="card-header bg-primary text-white py-4">
                            <h5 class="mb-0 fs-5"><i class="bi bi-reply me-2"></i>Trả lời</h5>
                        </div>
                        <div class="card-body p-4 p-lg-5">
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="bi bi-check-circle me-2"></i>
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <form action="{{ route('admin.tickets.reply', $ticket->id) }}" method="POST" id="replyForm">
                                @csrf
                                <div class="mb-3">
                                    <label for="admin_reply_message" class="form-label fw-bold fs-5">
                                        Phản hồi <span class="text-danger">*</span>
                                    </label>
                                    <textarea id="admin_reply_message" name="message" class="form-control @error('message') is-invalid @enderror"
                                        placeholder="Nhập phản hồi...">{{ old('message') }}</textarea>
                                    @error('message')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary btn-lg" id="sendBtn">
                                    <i class="bi bi-send me-2"></i> Gửi Phản Hồi
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning p-4" role="alert">
                        <i class="bi bi-lock-fill me-2"></i>
                        <strong>Ticket đã đóng.</strong> Không thể gửi thêm phản hồi.
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

                        <!-- Phụ trách -->
                        <div class="mb-4 pb-4 border-bottom">
                            <small class="text-muted d-block mb-2"><i class="bi bi-person-badge me-2"></i>Phụ trách</small>
                            @if($ticket->assignedStaff)
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-success text-white me-2">
                                        {{ strtoupper(substr($ticket->assignedStaff->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $ticket->assignedStaff->name }}</h6>
                                        <small class="text-muted">{{ $ticket->assignedStaff->email }}</small>
                                    </div>
                                </div>
                            @else
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-person-x"></i> Chưa được gán
                                </span>
                            @endif
                        </div>

                        <div class="mb-4 pb-4 border-bottom">
                            <small class="text-muted d-block mb-2"><i class="bi bi-chat-dots me-2"></i>Tổng số tin nhắn</small>
                            <h6 class="fs-4 fw-bold">
                                <span id="sidebar-message-count">{{ $ticket->messages->count() }}</span> tin nhắn
                            </h6>
                        </div>

                        <div class="mb-4 pb-4 border-bottom">
                            <small class="text-muted d-block mb-2"><i class="bi bi-tag me-2"></i>Trạng thái</small>
                            <span id="sidebar-status-badge"
                                class="badge fs-6
                            @if ($ticket->status === 'open') bg-warning text-dark
                            @elseif($ticket->status === 'in_progress') bg-info
                            @else bg-success @endif p-2">
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
                @if ($ticket->status !== 'closed')
                    <!-- Gán nhân viên (Admin only) -->
                    @if(Auth::user()->role == 1)
                    <div class="card shadow-lg border-info mb-4">
                        <div class="card-header bg-info text-white py-3">
                            <h5 class="mb-0 fs-6"><i class="bi bi-person-plus me-2"></i>Gán nhân viên</h5>
                        </div>
                        <div class="card-body p-3">
                            <form action="{{ route('admin.tickets.assign', $ticket->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <select class="form-select" name="assigned_to" required>
                                        <option value="">-- Chọn nhân viên --</option>
                                        @if(isset($staffList))
                                            @foreach($staffList as $staff)
                                                <option value="{{ $staff->id }}" 
                                                    {{ $ticket->assigned_staff_id == $staff->id ? 'selected' : '' }}>
                                                    {{ $staff->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-info w-100">
                                    <i class="bi bi-check-circle me-2"></i>Gán ngay
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif

                    <!-- Đóng ticket -->
                    <div class="card shadow-lg border-danger mb-4">
                        <div class="card-header bg-danger text-white py-3">
                            <h5 class="mb-0 fs-5"><i class="bi bi-gear me-2"></i>Hành động</h5>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('admin.tickets.close', $ticket->id) }}" method="POST"
                                onsubmit="return confirm('Bạn có chắc chắn muốn đóng ticket này?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-danger btn-lg w-100">
                                    <i class="bi bi-x-circle me-2"></i> Đóng Ticket
                                </button>
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

                <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-secondary btn-lg w-100">
                    <i class="bi bi-arrow-left me-2"></i> Quay lại danh sách
                </a>
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
                .create(document.querySelector('#admin_reply_message'), {
                    toolbar: ['heading', '|', 'bold', 'italic', 'underline', 'link', '|', 'bulletedList',
                        'numberedList', '|', 'blockQuote', 'undo', 'redo'
                    ]
                })
                .then(editor => {
                    editorInstance = editor;
                    console.log('CKEditor initialized');
                })
                .catch(error => console.error('CKEditor error:', error));

            // Sync CKEditor data before submit and validate
            const form = document.querySelector('#replyForm');
            form.addEventListener('submit', function(e) {
                if (editorInstance) {
                    const data = editorInstance.getData().trim();
                    if (!data) {
                        e.preventDefault();
                        alert('Vui lòng nhập phản hồi.');
                        return;
                    }
                    document.querySelector('#admin_reply_message').value = data;
                }
            });

            // ============ CHAT REAL-TIME POLLING ============

            // Hàm lấy tin nhắn mới
            function fetchNewMessages() {
                if (!isPolling) return;
                
                fetch('{{ route("admin.tickets.messages", $ticket->id) }}?last_id=' + lastMessageId)
                    .then(response => response.json())
                    .then(data => {
                        if (data.messages && data.messages.length > 0) {
                            data.messages.forEach(msg => {
                                appendMessage(msg);
                                lastMessageId = msg.id;
                            });
                            
                            // Update message count
                            updateMessageCount();
                            
                            // Scroll to bottom
                            scrollToBottom();
                            
                            // Play notification sound
                            playNotificationSound();
                        }
                        
                        // Update ticket status if changed
                        if (data.ticket_status !== '{{ $ticket->status }}') {
                            updateTicketStatus(data.ticket_status);
                        }
                    })
                    .catch(error => {
                        console.error('Lỗi khi lấy tin nhắn:', error);
                    });
            }

            // Hàm thêm tin nhắn mới vào UI
            function appendMessage(message) {
                const messagesContainer = document.getElementById('messages-container');
                const isStaff = message.sender.role == 1 || message.sender.role == 2;
                
                const messageHtml = `
                    <div class="card shadow-sm mb-3 border-0 message-item animate__animated animate__fadeIn" data-message-id="${message.id}">
                        <div class="card-header bg-white border-bottom">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <strong class="fs-5">${escapeHtml(message.sender.name)}</strong>
                                    ${isStaff ? '<span class="badge bg-danger ms-2"><i class="bi bi-shield-check"></i> Staff</span>' : '<span class="badge bg-secondary ms-2"><i class="bi bi-person"></i> Khách hàng</span>'}
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
                
                messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
            }

            // Scroll to bottom
            function scrollToBottom() {
                const container = document.getElementById('messages-container');
                const lastMessage = container.lastElementChild;
                if (lastMessage) {
                    lastMessage.scrollIntoView({ behavior: 'smooth', block: 'end' });
                }
            }

            // Format date
            function formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleString('vi-VN', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }

            // Escape HTML to prevent XSS
            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            // Update message count
            function updateMessageCount() {
                const count = document.querySelectorAll('.message-item').length;
                document.getElementById('message-count').textContent = count;
                document.getElementById('sidebar-message-count').textContent = count;
            }

            // Update ticket status
            function updateTicketStatus(newStatus) {
                const statusMap = {
                    'open': { text: 'Mới', class: 'bg-warning text-dark' },
                    'in_progress': { text: 'Đang xử lý', class: 'bg-info' },
                    'closed': { text: 'Đã đóng', class: 'bg-success' }
                };

                const status = statusMap[newStatus];
                if (status) {
                    // Update main badge
                    const mainBadge = document.getElementById('ticket-status-badge');
                    mainBadge.className = `badge fs-6 ${status.class} p-3`;
                    document.getElementById('ticket-status-text').textContent = status.text;

                    // Update sidebar badge
                    const sidebarBadge = document.getElementById('sidebar-status-badge');
                    sidebarBadge.className = `badge fs-6 ${status.class} p-2`;
                    sidebarBadge.textContent = status.text;

                    // If closed, hide reply form
                    if (newStatus === 'closed') {
                        location.reload();
                    }
                }
            }

            // Play notification sound
            function playNotificationSound() {
                try {
                    const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBR10yPLaizsIGGS56+mnVRwHPJjb88p5KwUue8rx3ZJBChd0zPDTgjMJHmzB7Oq4YjcHSaXm87BdGgk+ltv0xnQjBSuDz/LajjgIGmy66Oq7ZSIGSans87hnHAU5jdfywnwvBSiDz/LYjzoJHWrA7uq4YjUHSKTl87FgHQlAndny');
                    audio.volume = 0.3;
                    audio.play().catch(e => console.log('Không thể phát âm thanh'));
                } catch(e) {
                    console.log('Âm thanh không khả dụng');
                }
            }

            // Start polling every 3 seconds
            pollingInterval = setInterval(fetchNewMessages, 3000);

            // Stop polling when leaving page
            window.addEventListener('beforeunload', () => {
                isPolling = false;
                clearInterval(pollingInterval);
            });

            // Initial scroll to bottom
            window.addEventListener('load', scrollToBottom);
        });
    </script>

    <style>
        .avatar-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1rem;
        }

        .ck-editor__editable {
            min-height: 300px !important;
        }

        /* Animation for new messages */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate__animated.animate__fadeIn {
            animation: fadeIn 0.5s ease-in-out;
        }

        /* Sticky sidebar on large screens */
        @media (min-width: 992px) {
            .sticky-lg-top {
                position: sticky;
                top: 20px;
                z-index: 1020;
            }
        }
    </style>
@endsection