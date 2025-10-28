@extends('backend.layouts.master')

@section('title', 'Chi tiết Ticket')

@section('content')
    <div class="container-fluid py-4">
        <div class="row g-4">
            {{-- Main Content --}}
            <div class="col-12 col-lg-8">

                {{-- Header Card - Improved Design --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3 mb-3">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="badge bg-light text-dark px-2 py-1">#{{ $ticket->id }}</span>
                                    <span id="ticket-status-badge"
                                        class="badge px-3 py-2 {{ $ticket->status_badge }}">
                                        <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>
                                        <span id="ticket-status-text">{{ $ticket->status_label }}</span>
                                    </span>
                                </div>
                                <h1 class="h3 mb-3 fw-bold">{{ $ticket->subject }}</h1>
                                <div class="d-flex flex-wrap gap-3 text-muted small">
                                    <span>
                                        <i class="bi bi-tag-fill me-1"></i>
                                        <span class="badge {{ $ticket->category_badge }}">{{ $ticket->category_label }}</span>
                                    </span>
                                    <span>
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        <span class="badge {{ $ticket->priority_badge }}">{{ $ticket->priority_label }}</span>
                                    </span>
                                    <span>
                                        <i class="bi bi-calendar3 me-1"></i>
                                        {{ $ticket->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        @if($ticket->assignedStaff)
                            <div class="alert alert-light border d-flex align-items-center gap-2 mb-0" role="alert">
                                <div class="avatar-circle bg-success text-white">
                                    {{ strtoupper(substr($ticket->assignedStaff->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="small text-muted">Nhân viên hỗ trợ</div>
                                    <strong>{{ $ticket->assignedStaff->name }}</strong>
                                    <div class="small text-muted">ID: {{ $ticket->assignedStaff->account_id ?? 'N/A' }}</div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning border-0 mb-0" role="alert">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Đang chờ được phân công nhân viên hỗ trợ</strong>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Messages Section - Improved --}}
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">
                            <i class="bi bi-chat-left-text me-2"></i>Cuộc trò chuyện
                        </h5>
                        <span class="badge bg-primary rounded-pill" id="message-count">
                            {{ $ticket->messages->where('is_system_message', '!=', true)->count() }}
                        </span>
                    </div>
                    
                    <div id="messages-container" class="messages-list">
                        @foreach($ticket->messages as $message)
                            @if(!isset($message->is_system_message) || !$message->is_system_message)
                                <div class="message-card mb-3" data-message-id="{{ $message->id }}">
                                    <div class="message-header">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-circle {{ $message->sender_id === auth()->id() ? 'bg-primary' : 'bg-danger' }} text-white">
                                                {{ strtoupper(substr($message->sender->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $message->sender->name }}</div>
                                                @if($message->sender_id !== auth()->id())
                                                    <div class="small text-muted">ID: {{ $message->sender->account_id ?? 'N/A' }}</div>
                                                @endif
                                                <div class="small text-muted">
                                                    <i class="bi bi-clock me-1"></i>{{ $message->created_at->format('d/m/Y H:i') }}
                                                </div>
                                            </div>
                                        </div>
                                        @if($message->sender_id === auth()->id())
                                            <span class="badge bg-primary">
                                                <i class="bi bi-person"></i> Bạn
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="bi bi-shield-check"></i> Hỗ trợ
                                            </span>
                                        @endif
                                    </div>
                                    <div class="message-body">
                                        <div class="ck-content">{!! $message->message !!}</div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <div id="typing-indicator" class="text-muted small mb-3" style="display: none;">
                        <i class="bi bi-three-dots"></i> Nhân viên đang nhập...
                    </div>
                </div>

                {{-- Reply Form - Improved --}}
                @if($ticket->status !== 'closed')
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5 class="mb-3">
                                <i class="bi bi-reply me-2"></i>Trả lời
                            </h5>

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
                                    <textarea id="reply_message" name="message" class="form-control @error('message') is-invalid @enderror"
                                        placeholder="Nhập phản hồi của bạn...">{{ old('message') }}</textarea>
                                    @error('message')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary px-4" id="sendBtn">
                                        <i class="bi bi-send me-2"></i>Gửi phản hồi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="alert alert-info border-0 d-flex align-items-center" role="alert">
                        <i class="bi bi-lock-fill fs-4 me-3"></i>
                        <div>
                            <strong>Ticket đã đóng</strong>
                            <div class="small">Nếu bạn có vấn đề mới, vui lòng tạo ticket mới.</div>
                        </div>
                    </div>
                @endif

            </div>

            {{-- Sidebar - Improved --}}
            <div class="col-12 col-lg-4">
                <div class="sidebar-sticky">
                    {{-- Info Card --}}
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body p-4">
                            <h6 class="text-uppercase text-muted small mb-3">Thông tin Ticket</h6>
                            
                            <div class="info-item">
                                <div class="info-label">
                                    <i class="bi bi-tag text-muted"></i>
                                    Danh mục
                                </div>
                                <div class="info-value">
                                    <span class="badge {{ $ticket->category_badge }}">{{ $ticket->category_label }}</span>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-label">
                                    <i class="bi bi-exclamation-triangle text-muted"></i>
                                    Mức độ ưu tiên
                                </div>
                                <div class="info-value">
                                    <span class="badge {{ $ticket->priority_badge }}">
                                        @if($ticket->priority === 'urgent')
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                        @elseif($ticket->priority === 'high')
                                            <i class="bi bi-arrow-up-circle-fill me-1"></i>
                                        @endif
                                        {{ $ticket->priority_label }}
                                    </span>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-label">
                                    <i class="bi bi-person-badge text-muted"></i>
                                    Nhân viên hỗ trợ
                                </div>
                                <div class="info-value">
                                    @if($ticket->assignedStaff)
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-circle avatar-sm bg-success text-white">
                                                {{ strtoupper(substr($ticket->assignedStaff->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <strong>{{ $ticket->assignedStaff->name }}</strong>
                                                <div class="small text-muted">ID: {{ $ticket->assignedStaff->account_id ?? 'N/A' }}</div>
                                                <div class="small text-muted">{{ $ticket->assignedStaff->email }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="badge bg-secondary">Chưa gán</span>
                                    @endif
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-label">
                                    <i class="bi bi-chat-dots text-muted"></i>
                                    Tin nhắn
                                </div>
                                <div class="info-value">
                                    <strong id="sidebar-message-count">{{ $ticket->messages->where('is_system_message', '!=', true)->count() }}</strong> tin nhắn
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-label">
                                    <i class="bi bi-flag text-muted"></i>
                                    Trạng thái
                                </div>
                                <div class="info-value">
                                    <span id="sidebar-status-badge" class="badge {{ $ticket->status_badge }}">
                                        {{ $ticket->status_label }}
                                    </span>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-label">
                                    <i class="bi bi-calendar-event text-muted"></i>
                                    Ngày tạo
                                </div>
                                <div class="info-value small">
                                    {{ $ticket->created_at->format('d/m/Y H:i:s') }}
                                </div>
                            </div>

                            <div class="info-item mb-0">
                                <div class="info-label">
                                    <i class="bi bi-hourglass-split text-muted"></i>
                                    Thời gian phản hồi dự kiến
                                </div>
                                <div class="info-value small">
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
                                </div>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('customer.tickets.index') }}" class="btn btn-outline-secondary w-100 mb-3">
                        <i class="bi bi-arrow-left me-2"></i>Quay lại danh sách
                    </a>

                    {{-- Quick Tips --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-3">
                            <h6 class="mb-2">
                                <i class="bi bi-lightbulb text-warning me-2"></i>Mẹo hữu ích
                            </h6>
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
    </div>

    {{-- CKEditor 5 --}}
    <script src="https://cdn.ckeditor.com/ckeditor5/43.3.1/ckeditor5.umd.js"></script>
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/43.3.1/ckeditor5.css" />

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const {
                ClassicEditor,
                Essentials,
                Bold,
                Italic,
                Underline,
                Strikethrough,
                Code,
                Font,
                Paragraph,
                Heading,
                Link,
                AutoLink,
                List,
                Alignment,
                BlockQuote,
                CodeBlock,
                Table,
                TableToolbar,
                HorizontalLine,
                Highlight,
                RemoveFormat,
                Indent,
                SourceEditing,
                PasteFromOffice,
                Undo
            } = CKEDITOR;

            let editorInstance;
            let lastMessageId = {{ $ticket->messages->last()->id ?? 0 }};
            let isPolling = true;
            let pollingInterval;

            // Initialize CKEditor
            ClassicEditor
                .create(document.querySelector('#reply_message'), {
                    plugins: [
                        Essentials, Bold, Italic, Underline, Strikethrough, Code,
                        Font, Paragraph, Heading, Link, AutoLink, List,
                        Alignment, BlockQuote, CodeBlock,
                        Table, TableToolbar, HorizontalLine,
                        Highlight, RemoveFormat, Indent,
                        SourceEditing, PasteFromOffice, Undo
                    ],
                    toolbar: {
                        items: [
                            'undo', 'redo', '|',
                            'heading', '|',
                            'bold', 'italic', 'underline', '|',
                            'link', 'bulletedList', 'numberedList', '|',
                            'alignment', 'outdent', 'indent', '|',
                            'blockQuote', 'insertTable', '|',
                            'removeFormat'
                        ],
                        shouldNotGroupWhenFull: true
                    },
                    heading: {
                        options: [
                            { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                            { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                            { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
                        ]
                    },
                    table: {
                        contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
                    }
                })
                .then(editor => {
                    editorInstance = editor;
                    editor.editing.view.change(writer => {
                        writer.setStyle('min-height', '200px', editor.editing.view.document.getRoot());
                    });
                })
                .catch(error => {
                    console.error('CKEditor error:', error);
                });

            // Form submit validation
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

            // Real-time polling
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
                    .catch(error => console.error('Lỗi khi lấy tin nhắn:', error));
            }

            function appendMessage(message) {
                const messagesContainer = document.getElementById('messages-container');
                
                // Bỏ qua system messages
                if (message.is_system_message) {
                    return;
                }
                
                const isOwn = message.sender_id == {{ Auth::id() }};
                const avatarClass = isOwn ? 'bg-primary' : 'bg-danger';
                const badgeHtml = isOwn 
                    ? '<span class="badge bg-primary"><i class="bi bi-person"></i> Bạn</span>'
                    : '<span class="badge bg-danger"><i class="bi bi-shield-check"></i> Hỗ trợ</span>';
                
                const messageHtml = `
                    <div class="message-card mb-3 animate__animated animate__fadeIn" data-message-id="${message.id}">
                        <div class="message-header">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-circle ${avatarClass} text-white">
                                    ${escapeHtml(message.sender.name.charAt(0).toUpperCase())}
                                </div>
                                <div>
                                    <div class="fw-semibold">${escapeHtml(message.sender.name)}</div>
                                    ${message.sender_id != {{ Auth::id() }} ? `<div class="small text-muted">ID: ${escapeHtml(message.sender.account_id || 'N/A')}</div>` : ''}
                                    <div class="small text-muted">
                                        <i class="bi bi-clock me-1"></i>${formatDate(message.created_at)}
                                    </div>
                                </div>
                            </div>
                            ${badgeHtml}
                        </div>
                        <div class="message-body">
                            <div class="ck-content">${message.message}</div>
                        </div>
                    </div>
                `;
                messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
            }

            function scrollToBottom() {
                const container = document.getElementById('messages-container');
                const lastMessage = container.lastElementChild;
                if (lastMessage) {
                    lastMessage.scrollIntoView({ behavior: 'smooth', block: 'end' });
                }
            }

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

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            function updateMessageCount() {
                const count = document.querySelectorAll('.message-card').length;
                document.getElementById('message-count').textContent = count;
                document.getElementById('sidebar-message-count').textContent = count;
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
                    const mainBadge = document.getElementById('ticket-status-badge');
                    mainBadge.className = `badge px-3 py-2 ${status.class}`;
                    document.getElementById('ticket-status-text').textContent = status.text;

                    const sidebarBadge = document.getElementById('sidebar-status-badge');
                    sidebarBadge.className = `badge ${status.class}`;
                    sidebarBadge.textContent = status.text;

                    if (newStatus === 'closed') {
                        location.reload();
                    }
                }
            }

            function playNotificationSound() {
                try {
                    const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBR10yPLaizsIGGS56+mnVRwHPJjb88p5KwUue8rx3ZJBChd0zPDTgjMJHmzB7Oq4YjcHSaXm87BdGgk+ltv0xnQjBSuDz/LajjgIGmy66Oq7ZSIGSans87hnHAU5jdfywnwvBSiDz/LYjzoJHWrA7uq4YjUHSKTl87FgHQlAndny');
                    audio.volume = 0.3;
                    audio.play().catch(e => console.log('Không thể phát âm thanh'));
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
        /* Avatar */
        .avatar-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .avatar-circle.avatar-sm {
            width: 32px;
            height: 32px;
            font-size: 0.875rem;
        }

        /* Message Cards */
        .messages-list {
            max-height: 600px;
            overflow-y: auto;
            padding-right: 8px;
        }

        .message-card {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.2s ease;
        }

        .message-card:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transform: translateY(-1px);
        }

        .message-header {
            padding: 16px;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .message-body {
            padding: 20px;
        }

        /* Info Items */
        .info-item {
            padding: 16px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .info-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .info-label {
            font-size: 0.875rem;
            color: #6c757d;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .info-value {
            font-size: 0.95rem;
        }

        /* Sidebar Sticky */
        .sidebar-sticky {
            position: sticky;
            top: 20px;
        }

        /* CKEditor */
        .ck-editor__editable {
            min-height: 200px !important;
            max-height: 400px;
            overflow-y: auto;
        }

        /* Animations */
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
            animation: fadeIn 0.4s ease-in-out;
        }

        /* Scrollbar */
        .messages-list::-webkit-scrollbar {
            width: 6px;
        }

        .messages-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .messages-list::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        .messages-list::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .sidebar-sticky {
                position: relative;
                top: 0;
            }
        }

        /* Card improvements */
        .card {
            transition: all 0.2s ease;
        }

        /* CK Content Styling */
        .ck-content {
            font-size: 1rem;
            line-height: 1.6;
        }

        .ck-content h2 { 
            font-size: 1.5em; 
            margin: 0.83em 0;
            font-weight: 600;
        }
        
        .ck-content h3 { 
            font-size: 1.17em; 
            margin: 1em 0;
            font-weight: 600;
        }

        .ck-content blockquote {
            border-left: 4px solid #dee2e6;
            padding-left: 1em;
            margin-left: 0;
            font-style: italic;
            color: #6c757d;
        }

        .ck-content code {
            background-color: #f8f9fa;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
            color: #d63384;
        }

        .ck-content pre {
            background-color: #282c34;
            color: #abb2bf;
            padding: 1em;
            border-radius: 6px;
            overflow-x: auto;
        }

        .ck-content pre code {
            background-color: transparent;
            padding: 0;
            color: inherit;
        }

        .ck-content table {
            border-collapse: collapse;
            width: 100%;
            margin: 1em 0;
        }

        .ck-content table td,
        .ck-content table th {
            border: 1px solid #dee2e6;
            padding: 8px 12px;
        }

        .ck-content table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .ck-content ul,
        .ck-content ol {
            padding-left: 2em;
        }

        .ck-content li {
            margin: 0.5em 0;
        }

        .ck-content a {
            color: #0d6efd;
            text-decoration: underline;
        }

        .ck-content a:hover {
            color: #0a58ca;
        }
    </style>
@endsection