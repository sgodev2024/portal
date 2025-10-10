@extends('backend.layouts.master')

@section('title', 'Quản lý Chat')

@push('styles')
    <style>
        .chat-container {
            height: calc(150vh - 200px);
            display: flex;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        /* Sidebar danh sách chat */
        .chat-sidebar {
            width: 360px;
            border-right: 1px solid #e4e6eb;
            display: flex;
            flex-direction: column;
        }

        .chat-sidebar-header {
            padding: 16px;
            border-bottom: 1px solid #e4e6eb;
        }

        .chat-sidebar-header h5 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }

        .chat-tabs {
            display: flex;
            padding: 8px 16px;
            border-bottom: 1px solid #e4e6eb;
            gap: 8px;
        }

        .chat-tab {
            padding: 8px 16px;
            border-radius: 20px;
            background: #e4e6eb;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 14px;
        }

        .chat-tab.active {
            background: #0084ff;
            color: white;
        }

        .chat-list {
            flex: 1;
            overflow-y: auto;
        }

        .chat-item {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            cursor: pointer;
            transition: background 0.2s;
            border-bottom: 1px solid #f0f2f5;
        }

        .chat-item:hover {
            background: #f2f3f5;
        }

        .chat-item.active {
            background: #e7f3ff;
        }

        .chat-avatar {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 20px;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .chat-info {
            flex: 1;
            min-width: 0;
        }

        .chat-name {
            font-weight: 600;
            font-size: 15px;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .chat-time {
            font-size: 12px;
            color: #65676b;
            margin-top: 4px;
        }

        .chat-preview {
            font-size: 13px;
            color: #65676b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .chat-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            margin-left: 8px;
        }

        .badge-pending {
            background: #fff3cd;
            color: #856404;
        }

        .badge-processing {
            background: #d1ecf1;
            color: #0c5460;
        }

        /* Khung chat chính */
        .chat-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #fff;
        }

        .chat-main-header {
            padding: 12px 16px;
            border-bottom: 1px solid #e4e6eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .chat-main-user {
            display: flex;
            align-items: center;
        }

        .chat-main-avatar {
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

        .chat-main-info h6 {
            margin: 0;
            font-weight: 600;
            font-size: 15px;
        }

        .chat-main-status {
            font-size: 12px;
            color: #65676b;
        }

        .chat-actions {
            display: flex;
            gap: 8px;
        }

        .btn-chat-action {
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

        .btn-chat-action:hover {
            background: #f2f3f5;
        }

        /* Khung tin nhắn */
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 16px;
            background: #f0f2f5;
        }

        .chat-empty {
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #65676b;
        }

        .chat-empty-icon {
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

        .message {
            margin-bottom: 12px;
            display: flex;
            align-items: flex-end;
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
            padding: 4px 12px;
            background: #e7f3ff;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .file-preview .remove-file {
            cursor: pointer;
            color: #0084ff;
            font-weight: bold;
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
        }

        .btn-send:disabled {
            background: #bcc0c4;
            cursor: not-allowed;
        }

        /* Assign form */
        .assign-form {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 12px;
            max-width: 400px;
            margin: 0 auto;
        }

        .assign-form select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #e4e6eb;
            border-radius: 8px;
            margin-bottom: 12px;
            font-size: 14px;
        }

        .assign-form button {
            width: 100%;
            padding: 10px 16px;
            background: #0084ff;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s;
        }

        .assign-form button:hover {
            background: #0073e6;
        }
    </style>
@endpush

@section('content')
    {{-- <div class="page-inner"> --}}
    {{-- <div class="page-header mb-3">
        <h4 class="page-title">Quản lý Chat khách hàng</h4>
    </div> --}}

    <div class="chat-container">
        <!-- Sidebar danh sách chat -->
        <div class="chat-sidebar">
            <div class="chat-sidebar-header">
                <h5>Danh sách chat</h5>
            </div>

            <div class="chat-tabs">
                <button class="chat-tab active" data-tab="pending">
                    Chờ xử lý ({{ count($pendingChats) }})
                </button>
                <button class="chat-tab" data-tab="processing">
                    Đang xử lý ({{ count($processingChats) }})
                </button>
            </div>

            <div class="chat-list">
                <!-- Danh sách chat chờ xử lý -->
                <div class="tab-content-list active" data-content="pending">
                    @forelse($pendingChats as $chat)
                        <div class="chat-item" data-chat-id="{{ $chat->id }}" data-status="pending"
                            data-user-name="{{ $chat->user->name ?? 'Khách hàng' }}">
                            <div class="chat-avatar">
                                {{ strtoupper(substr($chat->user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="chat-info">
                                <div class="chat-name">
                                    <span>{{ $chat->user->name ?? 'Khách hàng' }}</span>
                                    <span class="chat-badge badge-pending">Chờ</span>
                                </div>
                                <div class="chat-preview">
                                    Tin nhắn mới từ khách hàng
                                </div>
                                <div class="chat-time">
                                    {{ $chat->last_message_at ? \Carbon\Carbon::parse($chat->last_message_at)->diffForHumans() : 'Vừa xong' }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="padding: 32px; text-align: center; color: #65676b;">
                            Không có chat chờ xử lý
                        </div>
                    @endforelse
                </div>

                <!-- Danh sách chat đang xử lý -->
                <div class="tab-content-list" data-content="processing" style="display: none;">
                    @forelse($processingChats as $chat)
                        @php
                            $messages = $chat->content ?? [];
                            $lastMessageAt = !empty($messages) ? end($messages)['created_at'] : $chat->last_message_at;
                        @endphp
                        <div class="chat-item" data-chat-id="{{ $chat->id }}" data-status="processing"
                            data-user-name="{{ $chat->user->name ?? 'Khách hàng' }}"
                            data-staff-name="{{ $chat->staff->name ?? 'Nhân viên' }}">
                            <div class="chat-avatar">
                                {{ strtoupper(substr($chat->user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="chat-info">
                                <div class="chat-name">
                                    <span>{{ $chat->user->name ?? 'Khách hàng' }}</span>
                                    <span class="chat-badge badge-processing">Đang xử lý</span>
                                </div>
                                <div class="chat-preview">
                                    Nhân viên: {{ $chat->staff->name ?? 'Chưa gán' }}
                                </div>
                                <div class="chat-time">
                                    {{ $lastMessageAt ? \Carbon\Carbon::parse($lastMessageAt)->diffForHumans() : 'Vừa xong' }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="padding: 32px; text-align: center; color: #65676b;">
                            Không có chat đang xử lý
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Khung chat chính -->
        <div class="chat-main">
            <div class="chat-main-header" style="display: none;" id="chatHeader">
                <div class="chat-main-user">
                    <div class="chat-main-avatar" id="chatUserAvatar">U</div>
                    <div class="chat-main-info">
                        <h6 id="chatUserName">Chọn một cuộc trò chuyện</h6>
                        <div class="chat-main-status" id="chatStatus">Offline</div>
                    </div>
                </div>
                <div class="chat-actions" id="chatActions"></div>
            </div>

            <div class="chat-messages" id="chatMessages">
                <div class="chat-empty">
                    <div class="chat-empty-icon">💬</div>
                    <h5>Chọn một cuộc trò chuyện</h5>
                    <p>Chọn từ danh sách bên trái để bắt đầu</p>
                </div>
            </div>

            <div class="chat-input" style="display: none;" id="chatInput">
                <form class="chat-input-form" id="chatForm">
                    @csrf
                    <div id="filePreview" style="display: none;"></div>
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
        </div>
    </div>
    {{-- </div> --}}
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let currentChatId = null;
            let currentChatStatus = null;
            let lastMessageAt = null;
            let pollingInterval = null;

            const chatMessages = $('#chatMessages');
            const chatForm = $('#chatForm');
            const messageInput = $('#messageInput');
            const fileInput = $('#fileInput');
            const sendButton = $('#sendBtn');
            const filePreview = $('#filePreview');

            // Xử lý tabs
            $('.chat-tab').click(function() {
                $('.chat-tab').removeClass('active');
                $(this).addClass('active');

                const tab = $(this).data('tab');
                $('.tab-content-list').hide();
                $(`.tab-content-list[data-content="${tab}"]`).show();

                resetChatView();
            });

            // Xử lý click vào chat item
            $('.chat-item').click(function() {
                $('.chat-item').removeClass('active');
                $(this).addClass('active');

                currentChatId = $(this).data('chat-id');
                currentChatStatus = $(this).data('status');

                loadChatDetail(currentChatId, currentChatStatus, $(this));
            });

            function resetChatView() {
                currentChatId = null;
                currentChatStatus = null;
                lastMessageAt = null;

                if (pollingInterval) {
                    clearInterval(pollingInterval);
                    pollingInterval = null;
                }

                $('#chatHeader').hide();
                $('#chatInput').hide();
                chatMessages.html(`
            <div class="chat-empty">
                <div class="chat-empty-icon">💬</div>
                <h5>Chọn một cuộc trò chuyện</h5>
                <p>Chọn từ danh sách bên trái để bắt đầu</p>
            </div>
        `);
            }

            function loadChatDetail(chatId, status, chatItem) {
                $('#chatHeader').show();

                const userName = chatItem.data('user-name');
                const userInitial = chatItem.find('.chat-avatar').text().trim();

                $('#chatUserName').text(userName);
                $('#chatUserAvatar').text(userInitial);
                $('#chatStatus').text(status === 'pending' ? 'Chờ xử lý' : 'Đang xử lý');

                if (status === 'pending') {
                    $('#chatActions').html('');
                    $('#chatInput').hide();
                    showAssignFormInChat(chatId);
                } else {
                    $('#chatActions').html('');
                    $('#chatInput').show();
                    loadMessages(chatId, chatItem.data('staff-name'));
                    startPolling(chatId, chatItem.data('staff-name'));
                }
            }

            function showAssignFormInChat(chatId) {
                chatMessages.html(`
            <div class="chat-empty">
                <div class="chat-empty-icon">👤</div>
                <h5>Phân công nhân viên</h5>
                <div class="assign-form">
                    <form action="/admin/chat/${chatId}/assign" method="POST">
                        @csrf
                        <select name="staff_id" required>
                            <option value="">Chọn nhân viên</option>
                            @foreach ($staffs as $staff)
                                <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit">Phân công ngay</button>
                    </form>
                </div>
            </div>
        `);
            }

            function loadMessages(chatId, staffName) {
                console.log('Loading messages for chat:', chatId);
                $.ajax({
                    url: `/admin/chat/${chatId}/messages`,
                    method: 'GET',
                    success: function(data) {
                        console.log('Messages loaded:', data);
                        if (data.success) {
                            renderMessages(data.messages, staffName);
                            if (data.messages.length > 0) {
                                lastMessageAt = data.messages[data.messages.length - 1].created_at;
                            }
                        }
                    },
                    error: function(xhr) {
                        console.error('Load messages error:', xhr);
                        chatMessages.html(`
                    <div class="chat-empty">
                        <div class="chat-empty-icon">⚠️</div>
                        <h5>Không thể tải tin nhắn</h5>
                        <p>Vui lòng thử lại sau</p>
                    </div>
                `);
                    }
                });
            }

            function renderMessages(messages, staffName) {
                if (messages.length === 0) {
                    chatMessages.html(`
                <div class="chat-empty">
                    <div class="chat-empty-icon">💬</div>
                    <h5>Chưa có tin nhắn</h5>
                    <p>Bắt đầu cuộc trò chuyện</p>
                </div>
            `);
                    return;
                }

                let html = '';
                messages.forEach(msg => {
                    const isMe = msg.sender_id == {{ auth()->id() }};
                    const senderName = msg.sender_name || 'Người dùng';
                    const senderInitial = senderName.charAt(0).toUpperCase();

                    let content = '';
                    if (msg.type === 'text') {
                        content = msg.content || '';
                    } else if (msg.type === 'image') {
                        content = `<img src="/storage/${msg.file_path}" alt="${msg.file_name || ''}">`;
                    } else if (msg.file_path) {
                        content =
                            `<a href="/storage/${msg.file_path}" target="_blank">📎 ${msg.file_name || 'Tệp đính kèm'}</a>`;
                    }

                    html += `
                <div class="message ${isMe ? 'sent' : 'received'}" data-created-at="${msg.created_at}">
                    <div class="message-avatar">${senderInitial}</div>
                    <div class="message-content">
                        <div class="message-bubble">${content}</div>
                        <div class="message-time">${senderName} • ${formatDateTime(msg.created_at)}</div>
                    </div>
                </div>
            `;
                });

                chatMessages.html(html);
                scrollToBottom();
            }

            function appendMessage(msg, staffName) {
                const isMe = msg.sender_id == {{ auth()->id() }};
                const senderName = msg.sender_name || (isMe ? 'Bạn' : staffName || 'Khách hàng');
                const senderInitial = senderName.charAt(0).toUpperCase();

                let content = '';
                if (msg.type === 'text') {
                    content = msg.content || '';
                } else if (msg.type === 'image') {
                    content = `<img src="/storage/${msg.file_path}" alt="${msg.file_name || ''}">`;
                } else if (msg.file_path) {
                    content =
                        `<a href="/storage/${msg.file_path}" target="_blank">📎 ${msg.file_name || 'Tệp đính kèm'}</a>`;
                }

                const html = `
        <div class="message ${isMe ? 'sent' : 'received'}" data-created-at="${msg.created_at}">
            <div class="message-avatar">${senderInitial}</div>
            <div class="message-content">
                <div class="message-bubble">${content}</div>
                <div class="message-time">${senderName} • ${formatDateTime(msg.created_at)}</div>
            </div>
        </div>
    `;

                if (chatMessages.find('.chat-empty').length) {
                    chatMessages.html(html);
                } else {
                    chatMessages.append(html);
                }

                scrollToBottom();
            }


            function startPolling(chatId, staffName) {
                if (pollingInterval) clearInterval(pollingInterval);

                pollingInterval = setInterval(function() {
                    if (currentChatId !== chatId) return;

                    $.ajax({
                        url: `/admin/chat/${chatId}/messages`,
                        method: 'GET',
                        data: {
                            last_message_at: lastMessageAt
                        },
                        success: function(data) {
                            if (data.success && data.messages.length > 0) {
                                data.messages.forEach(msg => appendMessage(msg, staffName));
                                lastMessageAt = data.messages[data.messages.length - 1]
                                    .created_at;
                            }
                        }
                    });
                }, 3000);
            }

            function scrollToBottom() {
                chatMessages.scrollTop(chatMessages[0].scrollHeight);
            }

            function formatDateTime(dateStr) {
                const date = new Date(dateStr);
                return date.toLocaleString('vi-VN', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }

            // Auto resize textarea
            messageInput.on('input', function() {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 100) + 'px';
                updateSendButtonState();
            });

            // File input change
            fileInput.on('change', function() {
                if (this.files.length > 0) {
                    const fileName = this.files[0].name;
                    filePreview.html(`
                <div class="file-preview">
                    📎 ${fileName}
                    <span class="remove-file">✕</span>
                </div>
            `).show();
                } else {
                    filePreview.hide();
                }
                updateSendButtonState();
            });

            // Remove file
            $(document).on('click', '.remove-file', function() {
                fileInput.val('');
                filePreview.hide();
                updateSendButtonState();
            });

            // Update send button state
            function updateSendButtonState() {
                const hasText = messageInput.val().trim() !== '';
                const hasFile = fileInput[0].files.length > 0;
                sendButton.prop('disabled', !(hasText || hasFile));
            }

            // Enter to send (Shift+Enter for new line)
            messageInput.on('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    if (!sendButton.prop('disabled')) {
                        chatForm.submit();
                    }
                }
            });

            // Submit form
            chatForm.on('submit', function(e) {
                e.preventDefault();

                if (!currentChatId) {
                    console.error('No chat selected');
                    return;
                }

                const hasText = messageInput.val().trim() !== '';
                const hasFile = fileInput[0].files.length > 0;

                if (!hasText && !hasFile) {
                    alert('Vui lòng nhập tin nhắn hoặc chọn tệp trước khi gửi!');
                    return;
                }

                sendButton.prop('disabled', true);
                const formData = new FormData(this);

                const staffName = $(`.chat-item[data-chat-id="${currentChatId}"]`).data('staff-name');

                console.log('Sending message to chat:', currentChatId);

                $.ajax({
                    url: `/admin/chat/${currentChatId}/send`,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        console.log('Message sent:', data);
                        if (data.success) {
                            appendMessage(data.data, staffName);
                            lastMessageAt = data.data.created_at;
                            chatForm[0].reset();
                            messageInput.css('height', 'auto');
                            filePreview.hide();
                            updateSendButtonState();
                        } else {
                            alert('Gửi tin nhắn thất bại!');
                        }
                    },
                    error: function(xhr) {
                        console.error('Send message error:', xhr);
                        alert('Lỗi kết nối khi gửi tin nhắn: ' + (xhr.responseJSON?.message ||
                            'Unknown error'));
                    },
                    complete: function() {
                        sendButton.prop('disabled', false);
                    }
                });
            });

            // Initialize
            updateSendButtonState();
        });
    </script>
@endpush
