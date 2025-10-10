@extends('backend.layouts.master')

@section('title', 'Chat với khách hàng')

@push('styles')
    <style>
        .chat-detail-container {
            height: calc(100vh - 200px);
            display: flex;
            flex-direction: column;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Header */
        .chat-detail-header {
            padding: 16px 24px;
            border-bottom: 1px solid #e4e6eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .chat-user-info {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .chat-user-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
        }

        .chat-user-details h5 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }

        .chat-user-status {
            font-size: 13px;
            opacity: 0.9;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .status-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #31a24c;
        }

        .chat-header-actions {
            display: flex;
            gap: 12px;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.15);
        }

        .btn-back {
            padding: 8px 16px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
        }

        .btn-close-chat {
            padding: 8px 16px;
            border-radius: 8px;
            background: #dc3545;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-close-chat:hover {
            background: #c82333;
        }

        /* Messages Area */
        .chat-messages-container {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
            background: #f0f2f5;
        }

        .message-wrapper {
            margin-bottom: 16px;
            display: flex;
            align-items: flex-end;
            animation: slideIn 0.3s ease-out;
        }

        .message-wrapper.sent {
            flex-direction: row-reverse;
        }

        .message-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            font-weight: 600;
            margin: 0 8px;
            flex-shrink: 0;
        }

        .message-wrapper.sent .message-avatar {
            background: linear-gradient(135deg, #0084ff 0%, #00a8ff 100%);
        }

        .message-content {
            max-width: 60%;
        }

        .message-wrapper.sent .message-content {
            text-align: right;
        }

        .message-bubble {
            display: inline-block;
            padding: 12px 16px;
            border-radius: 18px;
            background: white;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            word-wrap: break-word;
            font-size: 15px;
            line-height: 1.5;
        }

        .message-wrapper.sent .message-bubble {
            background: linear-gradient(135deg, #0084ff 0%, #00a8ff 100%);
            color: white;
        }

        .message-bubble img {
            max-width: 300px;
            border-radius: 12px;
            display: block;
            margin: 4px 0;
        }

        .message-bubble a {
            color: #0084ff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .message-wrapper.sent .message-bubble a {
            color: white;
        }

        .message-meta {
            font-size: 12px;
            color: #65676b;
            margin-top: 4px;
            padding: 0 8px;
        }

        /* Input Area */
        .chat-input-container {
            padding: 16px 24px;
            border-top: 1px solid #e4e6eb;
            background: white;
        }

        .chat-input-form {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .file-preview-area {
            display: none;
            padding: 12px;
            background: #e7f3ff;
            border-radius: 8px;
        }

        .file-preview-area.show {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .file-info {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #0c5460;
        }

        .remove-file-btn {
            background: transparent;
            border: none;
            color: #0084ff;
            cursor: pointer;
            font-weight: bold;
            padding: 4px 8px;
        }

        .input-area {
            display: flex;
            align-items: flex-end;
            gap: 12px;
            background: #f0f2f5;
            border-radius: 24px;
            padding: 8px 16px;
        }

        .file-attach-btn {
            background: transparent;
            border: none;
            color: #0084ff;
            cursor: pointer;
            padding: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            border-radius: 50%;
        }

        .file-attach-btn:hover {
            background: rgba(0, 132, 255, 0.1);
        }

        .message-input {
            flex: 1;
            border: none;
            background: transparent;
            outline: none;
            padding: 8px;
            font-size: 15px;
            resize: none;
            max-height: 120px;
            font-family: inherit;
            line-height: 1.5;
        }

        .send-btn {
            background: linear-gradient(135deg, #0084ff 0%, #00a8ff 100%);
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .send-btn:hover:not(:disabled) {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(0, 132, 255, 0.4);
        }

        .send-btn:disabled {
            background: #bcc0c4;
            cursor: not-allowed;
            opacity: 0.5;
        }

        /* Chat Closed State */
        .chat-closed-notice {
            padding: 20px;
            text-align: center;
            background: #f8f9fa;
            border-top: 1px solid #e4e6eb;
            color: #65676b;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .chat-closed-notice i {
            font-size: 32px;
            color: #95a5a6;
        }

        /* Scrollbar */
        .chat-messages-container::-webkit-scrollbar {
            width: 8px;
        }

        .chat-messages-container::-webkit-scrollbar-track {
            background: transparent;
        }

        .chat-messages-container::-webkit-scrollbar-thumb {
            background: #bcc0c4;
            border-radius: 4px;
        }

        .chat-messages-container::-webkit-scrollbar-thumb:hover {
            background: #95999d;
        }

        /* Animations */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Empty state */
        .chat-empty {
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #65676b;
        }

        .empty-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #e4e6eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin-bottom: 16px;
        }
    </style>
@endpush

@section('content')
    <div class="page-inner">
        <div class="chat-detail-container">
            <!-- Header -->
            <div class="chat-detail-header">
                <div class="chat-user-info">
                    <div class="chat-user-avatar">
                        {{ strtoupper(substr($chat->user->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="chat-user-details">
                        <h5>{{ $chat->user->name ?? 'Khách hàng' }}</h5>
                    </div>
                </div>

                <div class="chat-header-actions">
                    <a href="{{ route('staff.chats.index') }}" class="btn-back">
                        <i class="fas fa-arrow-left"></i>
                        Quay lại
                    </a>
                </div>
            </div>

            <!-- Messages -->
            <div class="chat-messages-container" id="chatBox">
                @php $messages = $chat->content ?? []; @endphp

                @if (empty($messages))
                    <div class="chat-empty">
                        <div class="empty-icon">💬</div>
                        <h5>Chưa có tin nhắn</h5>
                        <p>Bắt đầu cuộc trò chuyện với khách hàng</p>
                    </div>
                @else
                    @foreach ($messages as $msg)
                        @php
                            $isMe = $msg['sender_id'] === auth()->id();
                            $sender = $msg['sender_name'] ?? ($isMe ? auth()->user()->name : 'Khách hàng');
                        @endphp

                        <div class="message-wrapper {{ $isMe ? 'sent' : 'received' }}"
                            data-created-at="{{ $msg['created_at'] ?? '' }}">
                            <div class="message-avatar">{{ strtoupper(substr($sender, 0, 1)) }}</div>
                            <div class="message-content">
                                <div class="message-bubble">
                                    @if (($msg['type'] ?? '') === 'text')
                                        {{ $msg['content'] ?? '' }}
                                    @elseif(($msg['type'] ?? '') === 'image')
                                        <img src="{{ asset('storage/' . ($msg['file_path'] ?? '')) }}"
                                            alt="{{ $msg['file_name'] ?? '' }}">
                                    @elseif(isset($msg['file_path']))
                                        <a href="{{ asset('storage/' . $msg['file_path']) }}" target="_blank">
                                            <i class="fas fa-file"></i> {{ $msg['file_name'] ?? 'Tệp đính kèm' }}
                                        </a>
                                    @endif
                                </div>
                                <div class="message-meta">
                                    {{ $sender }} •
                                    {{ isset($msg['created_at']) ? \Carbon\Carbon::parse($msg['created_at'])->format('H:i d/m/Y') : 'Vừa xong' }}
                                </div>
                            </div>
                        </div>
                    @endforeach

                @endif
            </div>

            <!-- Input Area -->
            @if ($chat->status !== 'closed')
                <div class="chat-input-container">
                    <form class="chat-input-form" id="chatForm" method="POST"
                        action="{{ route('staff.chats.send', $chat->id) }}" enctype="multipart/form-data">
                        @csrf

                        <div class="file-preview-area" id="filePreview">
                            <div class="file-info">
                                <i class="fas fa-paperclip"></i>
                                <span id="fileName"></span>
                            </div>
                            <button type="button" class="remove-file-btn" onclick="removeFile()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <div class="input-area">
                            <label class="file-attach-btn" title="Đính kèm tệp">
                                <i class="fas fa-paperclip" style="font-size: 20px;"></i>
                                <input type="file" name="file" id="fileInput" accept="image/*,.pdf,.doc,.docx"
                                    style="display: none;">
                            </label>

                            <textarea name="message" class="message-input" placeholder="Aa" rows="1" id="messageInput"></textarea>

                            <button type="submit" class="send-btn" id="sendBtn" disabled>
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="chat-closed-notice">
                    <i class="fas fa-lock"></i>
                    <strong>Chat đã kết thúc</strong>
                    <span>Cuộc trò chuyện này đã được đóng, không thể gửi tin nhắn mới.</span>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chatBox = document.getElementById('chatBox');
            const chatForm = document.getElementById('chatForm');
            const messageInput = document.getElementById('messageInput');
            const fileInput = document.getElementById('fileInput');
            const sendButton = document.getElementById('sendBtn');
            const filePreview = document.getElementById('filePreview');
            const fileName = document.getElementById('fileName');
            const lastMessage = chatBox.querySelector('.message-wrapper:last-child');
            let lastMessageAt = lastMessage?.dataset.createdAt || null;

            chatBox.scrollTop = chatBox.scrollHeight;

            messageInput?.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 120) + 'px';
                updateSendButtonState();
            });

            fileInput?.addEventListener('change', function() {
                if (this.files.length > 0) {
                    fileName.textContent = this.files[0].name;
                    filePreview.classList.add('show');
                } else {
                    filePreview.classList.remove('show');
                }
                updateSendButtonState();
            });

            window.removeFile = function() {
                fileInput.value = '';
                filePreview.classList.remove('show');
                updateSendButtonState();
            };

            function updateSendButtonState() {
                const hasText = messageInput?.value.trim() !== '';
                const hasFile = fileInput?.files?.length > 0;
                if (sendButton) {
                    sendButton.disabled = !(hasText || hasFile);
                }
            }

            // Enter to send (Shift+Enter for new line)
            messageInput?.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    if (!sendButton.disabled) {
                        chatForm.dispatchEvent(new Event('submit'));
                    }
                }
            });

            // Submit form
            if (chatForm) {
                chatForm.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    const hasText = messageInput.value.trim() !== '';
                    const hasFile = fileInput.files.length > 0;
                    if (!hasText && !hasFile) {
                        alert('Vui lòng nhập tin nhắn hoặc chọn tệp trước khi gửi!');
                        return;
                    }

                    const formData = new FormData(this);
                    sendButton.disabled = true;

                    try {
                        const res = await fetch(this.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData,
                            credentials: 'same-origin'
                        });

                        if (!res.ok) {
                            throw new Error(`HTTP ${res.status}`);
                        }

                        const data = await res.json();
                        if (data.success) {
                            appendMessages([data.data]);
                            this.reset();
                            messageInput.style.height = 'auto';
                            filePreview.classList.remove('show');
                            updateSendButtonState();
                        } else {
                            alert('Gửi tin nhắn thất bại!');
                        }
                    } catch (err) {
                        console.error('Lỗi gửi tin nhắn:', err);
                        alert('Đã xảy ra lỗi khi gửi tin nhắn.');
                    } finally {
                        sendButton.disabled = false;
                    }
                });
            }

            // Polling: lấy tin mới mỗi 3s
            @if ($chat->status !== 'closed')
                setInterval(() => {
                    const url = "{{ route('staff.chats.messages', $chat->id) }}";
                    const params = lastMessageAt ? `?last_message_at=${encodeURIComponent(lastMessageAt)}` :
                        '';

                    fetch(url + params, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(res => res.json())
                        .then(res => {
                            if (res.success && res.messages.length) {
                                appendMessages(res.messages);
                            }
                        })
                        .catch(err => console.error('Lỗi lấy tin nhắn mới:', err));
                }, 3000);
            @endif

            // Append messages
            function appendMessages(messages) {
                // Xóa trạng thái chat trống nếu có
                const emptyState = chatBox.querySelector('.chat-empty');
                if (emptyState) emptyState.remove();

                messages.forEach(msg => {
                    const isMe = msg.sender_id == {{ auth()->id() }};
                    const sender = msg.sender_name || (isMe ? '{{ auth()->user()->name }}' : 'Khách hàng');
                    const senderInitial = sender.charAt(0).toUpperCase();

                    // Xác định nội dung tin nhắn
                    let content = '';
                    if (msg.type === 'text') {
                        content = msg.content ?? '';
                    } else if (msg.type === 'image') {
                        content = `<img src="/storage/${msg.file_path}" alt="${msg.file_name ?? ''}">`;
                    } else if (msg.file_path) {
                        content =
                            `<a href="/storage/${msg.file_path}" target="_blank"><i class="fas fa-file"></i> ${msg.file_name ?? 'Tệp đính kèm'}</a>`;
                    }

                    // Tạo HTML tin nhắn
                    const html = `
            <div class="message-wrapper ${isMe ? 'sent' : 'received'}" data-created-at="${msg.created_at}">
                <div class="message-avatar">${senderInitial}</div>
                <div class="message-content">
                    <div class="message-bubble">${content}</div>
                    <div class="message-meta">
                        ${sender} • ${new Date(msg.created_at).toLocaleString('vi-VN', {
                            hour: '2-digit',
                            minute: '2-digit',
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric'
                        })}
                    </div>
                </div>
            </div>
        `;
                    chatBox.insertAdjacentHTML('beforeend', html);
                });

                if (messages.length) lastMessageAt = messages[messages.length - 1].created_at;
                chatBox.scrollTop = chatBox.scrollHeight;
            }



            // Initialize
            updateSendButtonState();
        });
    </script>
@endpush
