@extends('backend.layouts.master')

@section('title', 'Chat với khách hàng')

@push('styles')
    <style>
        .chat-container {
            height: calc(100vh - 200px);
            display: flex;
            flex-direction: column;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        /* Header */
        .chat-header {
            padding: 12px 16px;
            border-bottom: 1px solid #e4e6eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: white;
        }

        .chat-user {
            display: flex;
            align-items: center;
        }

        .chat-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            margin-right: 12px;
        }

        .chat-user-info h6 {
            margin: 0;
            font-weight: 600;
            font-size: 15px;
        }

        .chat-user-status {
            font-size: 12px;
            color: #65676b;
        }

        .btn-back {
            padding: 8px 16px;
            border-radius: 6px;
            border: 1px solid #e4e6eb;
            background: white;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.2s;
            text-decoration: none;
            color: #050505;
        }

        .btn-back:hover {
            background: #f2f3f5;
        }

        /* Khung tin nhắn */
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 16px;
            background: #f0f2f5;
            scroll-behavior: auto;
        }

        .message {
            margin-bottom: 12px;
            display: flex;
            align-items: flex-end;
            animation: slideIn 0.3s ease-out;
        }

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

        .message-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
            font-weight: 600;
            margin-right: 8px;
            flex-shrink: 0;
        }

        .message-content {
            max-width: 60%;
        }

        .message-bubble {
            background: white;
            padding: 8px 12px;
            border-radius: 18px;
            display: inline-block;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            word-wrap: break-word;
        }

        .message-bubble img {
            max-width: 200px;
            border-radius: 8px;
            display: block;
            margin: 4px 0;
        }

        .message-bubble a {
            color: #0084ff;
            text-decoration: none;
        }

        .message.sent {
            flex-direction: row-reverse;
        }

        .message.sent .message-avatar {
            margin-left: 8px;
            margin-right: 0;
            background: linear-gradient(135deg, #0084ff 0%, #00a8ff 100%);
        }

        .message.sent .message-content {
            text-align: right;
        }

        .message.sent .message-bubble {
            background: #0084ff;
            color: white;
        }

        .message.sent .message-bubble a {
            color: white;
        }

        .message-time {
            font-size: 11px;
            color: #65676b;
            margin-top: 4px;
            padding: 0 12px;
        }

        /* Khung gửi tin nhắn */
        .chat-input {
            padding: 12px 16px;
            border-top: 1px solid #e4e6eb;
            background: white;
        }

        .chat-input-form {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .input-wrapper {
            display: flex;
            align-items: flex-end;
            background: #f0f2f5;
            border-radius: 20px;
            padding: 8px 12px;
            gap: 8px;
        }

        .chat-input-field {
            flex: 1;
            border: none;
            background: transparent;
            outline: none;
            padding: 4px 8px;
            font-size: 15px;
            resize: none;
            max-height: 100px;
            font-family: inherit;
            line-height: 1.4;
        }

        .file-input-wrapper {
            position: relative;
        }

        .btn-attach {
            background: transparent;
            border: none;
            color: #0084ff;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .btn-attach:hover {
            opacity: 0.7;
        }

        .file-input-wrapper input[type="file"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
            top: 0;
            left: 0;
        }

        .file-preview {
            font-size: 12px;
            color: #65676b;
            padding: 8px 12px;
            background: #e7f3ff;
            border-radius: 8px;
            display: none;
            align-items: center;
            gap: 8px;
        }

        .file-preview.show {
            display: flex;
        }

        .file-preview .remove-file {
            cursor: pointer;
            color: #0084ff;
            font-weight: bold;
            margin-left: auto;
            padding: 0 4px;
        }

        .file-preview .remove-file:hover {
            color: #0073e6;
        }

        .btn-send {
            background: #0084ff;
            color: white;
            border: none;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .btn-send:hover:not(:disabled) {
            background: #0073e6;
            transform: scale(1.05);
        }

        .btn-send:disabled {
            background: #bcc0c4;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .chat-closed {
            padding: 32px;
            text-align: center;
            color: #65676b;
        }

        /* Scrollbar */
        .chat-messages::-webkit-scrollbar {
            width: 8px;
        }

        .chat-messages::-webkit-scrollbar-track {
            background: transparent;
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background: #bcc0c4;
            border-radius: 4px;
        }

        .chat-messages::-webkit-scrollbar-thumb:hover {
            background: #95999d;
        }
    </style>
@endpush

@section('content')
    <div class="chat-container">
        <!-- Header -->
        <div class="chat-header">
            <div class="chat-user">
                <div class="chat-avatar">
                    {{ strtoupper(substr($chat->user->name ?? 'U', 0, 1)) }}
                </div>
                <div class="chat-user-info">
                    <h6>{{ $chat->user->name ?? 'Khách hàng' }}</h6>
                    <div class="chat-user-status">Chat #{{ $chat->id }}</div>
                </div>
            </div>
            <a href="{{ route('staff.chats.index') }}" class="btn-back">← Quay lại</a>
        </div>

        <!-- Khung tin nhắn -->
        <div class="chat-messages" id="chatBox">
            @php $messages = $chat->content ?? []; @endphp
            @foreach ($messages as $msg)
                @php
                    $isMe = $msg['sender_id'] === auth()->id();
                    $sender = 'System';
                    if (isset($msg['sender_id'])) {
                        if ($chat->user && $msg['sender_id'] == $chat->user->id) {
                            $sender = $chat->user->name;
                        } elseif ($chat->staff && $msg['sender_id'] == $chat->staff->id) {
                            $sender = $chat->staff->name;
                        } elseif ($msg['sender_id'] == auth()->id()) {
                            $sender = auth()->user()->name;
                        }
                    }
                @endphp

                <div class="message {{ $isMe ? 'sent' : 'received' }}" data-created-at="{{ $msg['created_at'] }}">
                    <div class="message-avatar">
                        {{ strtoupper(substr($sender, 0, 1)) }}
                    </div>
                    <div class="message-content">
                        <div class="message-bubble">
                            @if(($msg['type'] ?? '') === 'image' && !empty($msg['file_path']))
                                <img src="{{ asset('storage/' . ($msg['file_path'] ?? '')) }}" alt="{{ $msg['file_name'] ?? '' }}">
                            @elseif(isset($msg['file_path']))
                                <a href="{{ asset('storage/' . $msg['file_path']) }}" target="_blank">📎 {{ $msg['file_name'] ?? 'Tệp đính kèm' }}</a>
                            @endif
                            @if(!empty($msg['content']))
                                <div style="margin-top:6px; white-space:pre-line;">{{ $msg['content'] }}</div>
                            @endif
                        </div>
                        <div class="message-time">
                            {{ $sender }} •
                            {{ isset($msg['created_at']) ? \Carbon\Carbon::parse($msg['created_at'])->format('d/m/Y H:i') : '' }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Khung gửi tin nhắn -->
        @if ($chat->status != 'closed')
            <div class="chat-input">
                <form id="chatForm" action="{{ route('staff.chats.send', $chat->id) }}" method="POST"
                    enctype="multipart/form-data" class="chat-input-form" data-current-user-id="{{ auth()->id() }}"
                    data-current-user-name="{{ auth()->user()->name }}"
                    data-chat-user-name="{{ $chat->user->name ?? 'Khách hàng' }}">
                    @csrf

                    <div id="filePreview" class="file-preview">
                        <span id="fileName"></span>
                        <span class="remove-file" onclick="removeFile()">✕</span>
                    </div>

                    <div class="input-wrapper">
                        <div class="file-input-wrapper">
                            <button type="button" class="btn-attach" title="Đính kèm tệp">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M21.44,11.05L12.25,20.24C11.32,21.17 10.08,21.7 8.79,21.7C7.5,21.7 6.26,21.17 5.33,20.24C4.4,19.31 3.87,18.07 3.87,16.78C3.87,15.5 4.4,14.26 5.33,13.33L14.53,4.13C15.19,3.47 16.11,3.1 17.06,3.1C18,3.1 18.93,3.47 19.59,4.13C20.25,4.79 20.62,5.72 20.62,6.66C20.62,7.61 20.25,8.53 19.59,9.19L10.4,18.39C10.07,18.72 9.63,18.91 9.17,18.91C8.71,18.91 8.27,18.72 7.94,18.39C7.61,18.06 7.42,17.62 7.42,17.16C7.42,16.7 7.61,16.26 7.94,15.93L15.83,8.04L14.77,6.97L6.88,14.86C6.29,15.45 5.96,16.24 5.96,17.06C5.96,17.88 6.29,18.67 6.88,19.26C7.47,19.85 8.26,20.18 9.08,20.18C9.9,20.18 10.69,19.85 11.28,19.26L20.47,10.06C21.4,9.13 21.93,7.89 21.93,6.6C21.93,5.31 21.4,4.07 20.47,3.14C19.54,2.21 18.3,1.68 17.01,1.68C15.72,1.68 14.48,2.21 13.55,3.14L4.35,12.34C3.14,13.55 2.46,15.13 2.46,16.78C2.46,18.43 3.14,20.01 4.35,21.22C5.56,22.43 7.14,23.11 8.79,23.11C10.44,23.11 12.02,22.43 13.23,21.22L22.42,12.03L21.44,11.05Z" />
                                </svg>
                            </button>
                            <input type="file" name="file" accept="image/*,.pdf,.doc,.docx" id="fileInput">
                        </div>

                        <textarea name="message" class="chat-input-field" placeholder="Aa" rows="1" id="messageInput"></textarea>

                        <button type="submit" class="btn-send" id="sendBtn" disabled>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M2,21L23,12L2,3V10L17,12L2,14V21Z" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div class="chat-closed">
                <p>💬 Chat đã kết thúc, không thể gửi tin nhắn.</p>
            </div>
        @endif
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

            if (!chatForm || !chatBox) return;

            // Get data attributes
            const currentUserId = chatForm.dataset.currentUserId;
            const currentUserName = chatForm.dataset.currentUserName;
            const chatUserName = chatForm.dataset.chatUserName;

            // Get last message timestamp
            const lastMessageEl = chatBox.querySelector('.message:last-child');
            let lastMessageAt = lastMessageEl?.dataset.createdAt || null;
            const processedKeys = new Set();
            const recentKeys = new Map(); // normalized -> timestamp
            // seed keys from current DOM
            chatBox.querySelectorAll('.message').forEach(el => {
                const createdAt = el.dataset.createdAt || '';
                const senderId = el.dataset.senderId || '';
                const text = el.querySelector('.message-bubble')?.innerText || '';
                processedKeys.add(senderId + '|' + createdAt + '|' + text);
            });
            let isSubmitting = false;

            // Scroll to bottom
            chatBox.scrollTop = chatBox.scrollHeight;

            // Auto resize textarea
            messageInput.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 100) + 'px';
                updateSendButtonState();
            });

            // File input change
            fileInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    fileName.textContent = '📎 ' + this.files[0].name;
                    filePreview.classList.add('show');
                } else {
                    filePreview.classList.remove('show');
                }
                updateSendButtonState();
            });

            // Remove file function
            window.removeFile = function() {
                fileInput.value = '';
                filePreview.classList.remove('show');
                updateSendButtonState();
            };

            // Update send button state
            function updateSendButtonState() {
                const hasText = messageInput.value.trim() !== '';
                const hasFile = fileInput.files.length > 0;
                sendButton.disabled = !(hasText || hasFile) || isSubmitting;
            }

            // Enter to send (Shift+Enter for new line)
            messageInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    if (!sendButton.disabled && !isSubmitting) {
                        chatForm.dispatchEvent(new Event('submit', {
                            bubbles: true,
                            cancelable: true
                        }));
                    }
                }
            });

            // Submit form
            chatForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                // Prevent double submit
                if (isSubmitting) return;

                const hasText = messageInput.value.trim() !== '';
                const hasFile = fileInput.files.length > 0;

                if (!hasText && !hasFile) {
                    alert('Vui lòng nhập tin nhắn hoặc chọn tệp!');
                    return;
                }

                isSubmitting = true;
                sendButton.disabled = true;

                const formData = new FormData(this);

                try {
                    const response = await fetch(this.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                ?.content || '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData,
                        credentials: 'same-origin'
                    });

                    // Check HTTP status
                    if (!response.ok) {
                        let errorMessage = `Lỗi HTTP ${response.status}`;
                        try {
                            const errorData = await response.json();
                            errorMessage = errorData.message || errorMessage;
                        } catch (e) {
                            const errorText = await response.text();
                            console.error('Server response:', errorText);
                        }
                        throw new Error(errorMessage);
                    }

                    // Parse JSON response
                    let data;
                    try {
                        data = await response.json();
                    } catch (parseError) {
                        console.error('JSON parse error:', parseError);
                        throw new Error('Server không trả về dữ liệu hợp lệ');
                    }

                    // Handle success
                    if (data.success && data.data) {
                        appendMessages([data.data]);

                        // Reset form
                        this.reset();
                        messageInput.style.height = 'auto';
                        filePreview.classList.remove('show');
                        messageInput.focus();
                    } else {
                        throw new Error(data.message || 'Gửi tin nhắn thất bại');
                    }

                } catch (error) {
                    console.error('Send message error:', error);
                    alert('Lỗi: ' + (error.message || 'Không thể gửi tin nhắn'));
                } finally {
                    isSubmitting = false;
                    updateSendButtonState();
                }
            });

            // Polling: lấy tin mới mỗi 3s
            const pollingInterval = setInterval(async () => {
                if (isSubmitting) return; // tránh đúp khi đang gửi
                try {
                    const url = "{{ route('staff.chats.messages', $chat->id) }}";
                    const params = lastMessageAt ?
                        `?last_message_at=${encodeURIComponent(lastMessageAt)}` : '';

                    const response = await fetch(url + params, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        const data = await response.json();
                        if (data.success && data.messages && data.messages.length > 0) {
                            appendMessages(data.messages);
                        }
                    }
                } catch (error) {
                    console.error('Polling error:', error);
                }
            }, 3000);

            // Clear interval when page unloads
            window.addEventListener('beforeunload', () => {
                clearInterval(pollingInterval);
            });

            // Append messages function
            function appendMessages(messages) {
                if (!Array.isArray(messages) || messages.length === 0) return;

                messages.forEach(msg => {
                    if (!msg || !msg.created_at) return;

                    const isMe = msg.sender_id == currentUserId;
                    const sender = msg.sender_name || (isMe ? currentUserName : chatUserName);
                    const senderInitial = (sender.charAt(0) || '?').toUpperCase();

                    // Determine message content
                    let content = '';
                    if (msg.type === 'text') {
                        content = escapeHtml(msg.content || '');
                    } else if (msg.type === 'image' && msg.file_path) {
                        content =
                            `<img src="/storage/${escapeHtml(msg.file_path)}" alt="${escapeHtml(msg.file_name || 'Image')}">`;
                    } else if (msg.file_path) {
                        content =
                            `<a href="/storage/${escapeHtml(msg.file_path)}" target="_blank">📎 ${escapeHtml(msg.file_name || 'Tệp đính kèm')}</a>`;
                    }

                    // Duplicate guard by composite key + time window
                    const key = (msg.sender_id || '') + '|' + (msg.created_at || '') + '|' + (msg.file_path || '') + '|' + (msg.content || '');
                    if (processedKeys.has(key)) return;
                    const normalized = (msg.sender_id || '') + '|' + (msg.file_path || '') + '|' + String(msg.content || '').trim();
                    const nowMs = Date.now();
                    const lastMs = recentKeys.get(normalized);
                    if (lastMs && (nowMs - lastMs) < 8000) return;

                    // Format time
                    let timeStr = 'Vừa xong';
                    try {
                        const date = new Date(msg.created_at);
                        timeStr = date.toLocaleString('vi-VN', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                    } catch (e) {
                        console.error('Time format error:', e);
                    }

                    // Create message HTML
                    const messageHtml = `
                <div class="message ${isMe ? 'sent' : 'received'}" data-created-at="${msg.created_at}" data-sender-id="${msg.sender_id || ''}">
                    <div class="message-avatar">${senderInitial}</div>
                    <div class="message-content">
                        <div class="message-bubble">${content}</div>
                        <div class="message-time">${escapeHtml(sender)} • ${timeStr}</div>
                    </div>
                </div>
            `;

                    chatBox.insertAdjacentHTML('beforeend', messageHtml);
                    processedKeys.add(key);
                    recentKeys.set(normalized, nowMs);
                });

                // Update last message timestamp
                if (messages.length > 0) {
                    lastMessageAt = messages[messages.length - 1].created_at;
                }

                // Scroll to bottom
                chatBox.scrollTop = chatBox.scrollHeight;
            }

            // Escape HTML to prevent XSS
            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            // Initialize
            updateSendButtonState();
        });
    </script>
@endpush
