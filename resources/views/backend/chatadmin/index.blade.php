@extends('backend.layouts.master')

@section('title', 'Quản lý Chat')

@push('styles')
    <style>
        .chat-container {
            height: calc(100vh - 150px);
            display: flex;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            position: relative;
        }

        /* Sidebar */
        .chat-sidebar {
            width: 360px;
            border-right: 1px solid #e4e6eb;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
            background: white;
            z-index: 10;
        }

        .chat-sidebar-header {
            padding: 16px;
            border-bottom: 1px solid #e4e6eb;
            flex-shrink: 0;
        }

        .chat-sidebar-header h5 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
        }

        .chat-tabs {
            display: flex;
            padding: 12px 16px;
            border-bottom: 1px solid #e4e6eb;
            gap: 8px;
            flex-shrink: 0;
        }

        .chat-tab {
            flex: 1;
            padding: 8px 12px;
            border-radius: 20px;
            background: #f0f2f5;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 13px;
            white-space: nowrap;
            text-align: center;
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
            transition: all 0.2s;
            border-bottom: 1px solid #f0f2f5;
        }

        .chat-item:hover {
            background: #f2f3f5;
        }

        .chat-item.active {
            background: #e7f3ff;
            border-left: 3px solid #0084ff;
        }

        .chat-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 18px;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .chat-info {
            flex: 1;
            min-width: 0;
        }

        .chat-name {
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .chat-name span:first-child {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .chat-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
            white-space: nowrap;
        }

        .badge-pending {
            background: #fff3cd;
            color: #856404;
        }

        .badge-processing {
            background: #d1ecf1;
            color: #0c5460;
        }

        .chat-preview {
            font-size: 12px;
            color: #65676b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 2px;
        }

        .chat-time {
            font-size: 11px;
            color: #95999d;
        }

        /* Main chat area */
        .chat-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .chat-main-header {
            padding: 12px 16px;
            border-bottom: 1px solid #e4e6eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
            gap: 12px;
        }

        .chat-main-user {
            display: flex;
            align-items: center;
            min-width: 0;
            flex: 1;
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
            flex-shrink: 0;
        }

        .chat-main-info {
            min-width: 0;
            flex: 1;
        }

        .chat-main-info h6 {
            margin: 0;
            font-weight: 600;
            font-size: 15px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .chat-main-status {
            font-size: 12px;
            color: #65676b;
        }

        .chat-actions {
            display: flex;
            gap: 8px;
            flex-shrink: 0;
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
            white-space: nowrap;
        }

        .btn-chat-action:hover {
            background: #f2f3f5;
        }

        /* Messages area */
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
            text-align: center;
            padding: 20px;
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
            max-width: 70%;
        }

        .message-bubble {
            background: white;
            padding: 10px 14px;
            border-radius: 18px;
            display: inline-block;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            word-wrap: break-word;
            font-size: 14px;
            line-height: 1.4;
        }

        .message-bubble img {
            max-width: 100%;
            max-height: 300px;
            border-radius: 12px;
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
            padding: 0 8px;
        }

        /* Input area */
        .chat-input {
            padding: 12px 16px;
            border-top: 1px solid #e4e6eb;
            background: white;
            flex-shrink: 0;
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

        /* Assign form */
        .assign-form {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 12px;
            max-width: 400px;
            margin: 20px auto;
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

        /* Mobile toggle */
        .chat-mobile-toggle {
            display: none;
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 56px;
            height: 56px;
            background: #0084ff;
            color: white;
            border: none;
            border-radius: 50%;
            box-shadow: 0 4px 12px rgba(0, 132, 255, 0.4);
            cursor: pointer;
            z-index: 1000;
            font-size: 24px;
        }

        /* Scrollbar */
        .chat-list::-webkit-scrollbar,
        .chat-messages::-webkit-scrollbar {
            width: 6px;
        }

        .chat-list::-webkit-scrollbar-track,
        .chat-messages::-webkit-scrollbar-track {
            background: transparent;
        }

        .chat-list::-webkit-scrollbar-thumb,
        .chat-messages::-webkit-scrollbar-thumb {
            background: #bcc0c4;
            border-radius: 3px;
        }

        .chat-list::-webkit-scrollbar-thumb:hover,
        .chat-messages::-webkit-scrollbar-thumb:hover {
            background: #95999d;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .chat-container {
                height: calc(100vh - 100px);
            }

            .chat-sidebar {
                position: absolute;
                left: 0;
                top: 0;
                bottom: 0;
                transform: translateX(-100%);
                box-shadow: 2px 0 8px rgba(0, 0, 0, 0.15);
            }

            .chat-sidebar.show {
                transform: translateX(0);
            }

            .chat-mobile-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .chat-main-header {
                padding: 8px 12px;
            }

            .chat-main-avatar {
                width: 36px;
                height: 36px;
            }

            .chat-main-info h6 {
                font-size: 14px;
            }

            .message-content {
                max-width: 80%;
            }
        }

        @media (max-width: 576px) {
            .chat-sidebar {
                width: 280px;
            }

            .chat-tab {
                font-size: 12px;
                padding: 6px 8px;
            }

            .chat-item {
                padding: 10px 12px;
            }

            .chat-avatar {
                width: 40px;
                height: 40px;
                font-size: 16px;
            }

            .chat-name {
                font-size: 13px;
            }

            .chat-preview {
                font-size: 11px;
            }

            .message-bubble {
                font-size: 13px;
            }

            .btn-chat-action {
                padding: 6px 12px;
                font-size: 12px;
            }
        }

        /* Loading state */
        .loading {
            text-align: center;
            padding: 20px;
            color: #65676b;
        }

        .loading::after {
            content: '...';
            animation: dots 1.5s steps(3, end) infinite;
        }

        @keyframes dots {

            0%,
            20% {
                content: '.';
            }

            40% {
                content: '..';
            }

            60%,
            100% {
                content: '...';
            }
        }
    </style>
@endpush

@section('content')
    <div class="chat-container">
        <!-- Sidebar -->
        <div class="chat-sidebar" id="chatSidebar">
            <div class="chat-sidebar-header">
                <h5>Danh sách chat</h5>
            </div>

            <div class="chat-tabs">
                <button class="chat-tab active" data-tab="pending">
                    Chờ ({{ count($pendingChats) }})
                </button>
                <button class="chat-tab" data-tab="processing">
                    Đang xử lý ({{ count($processingChats) }})
                </button>
            </div>

            <div class="chat-list">
                <!-- Pending chats -->
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
                                <div class="chat-preview">Tin nhắn mới từ khách hàng</div>
                                <div class="chat-time">
                                    {{ $chat->last_message_at ? \Carbon\Carbon::parse($chat->last_message_at)->diffForHumans() : 'Vừa xong' }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="chat-empty">
                            <p>Không có chat chờ xử lý</p>
                        </div>
                    @endforelse
                </div>

                <!-- Processing chats -->
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
                                <div class="chat-preview">NV: {{ $chat->staff->name ?? 'Chưa gán' }}</div>
                                <div class="chat-time">
                                    {{ $lastMessageAt ? \Carbon\Carbon::parse($lastMessageAt)->diffForHumans() : 'Vừa xong' }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="chat-empty">
                            <p>Không có chat đang xử lý</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Main chat -->
        <div class="chat-main">
            <div class="chat-main-header" style="display: none;" id="chatHeader">
                <div class="chat-main-user">
                    <div class="chat-main-avatar" id="chatUserAvatar">U</div>
                    <div class="chat-main-info">
                        <h6 id="chatUserName">Chọn cuộc trò chuyện</h6>
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
                    <div id="filePreview" class="file-preview">
                        <span id="fileName"></span>
                        <span class="remove-file">✕</span>
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
        </div>
    </div>
    <!-- Mobile toggle -->
    <button class="chat-mobile-toggle" id="mobileChatToggle">💬</button>
@endsection

@push('scripts')
    <script>
        (function() {
            'use strict';

            // Cache DOM elements
            const elements = {
                sidebar: $('#chatSidebar'),
                chatMessages: $('#chatMessages'),
                chatForm: $('#chatForm'),
                messageInput: $('#messageInput'),
                fileInput: $('#fileInput'),
                sendButton: $('#sendBtn'),
                filePreview: $('#filePreview'),
                fileName: $('#fileName'),
                chatHeader: $('#chatHeader'),
                chatInput: $('#chatInput'),
                mobileToggle: $('#mobileChatToggle')
            };

            // State
            let state = {
                currentChatId: null,
                currentChatStatus: null,
                lastMessageAt: null,
                pollingInterval: null,
                isSubmitting: false
            };

            // Initialize
            function init() {
                bindEvents();
                updateSendButtonState();
            }

            // Bind all events
            function bindEvents() {
                // Tab switching
                $('.chat-tab').on('click', handleTabSwitch);

                // Chat item click
                $('.chat-item').on('click', handleChatItemClick);

                // Message input
                elements.messageInput.on('input', handleMessageInput);
                elements.messageInput.on('keydown', handleMessageKeydown);

                // File input
                elements.fileInput.on('change', handleFileChange);
                $(document).on('click', '.remove-file', handleFileRemove);

                // Form submit
                elements.chatForm.on('submit', handleFormSubmit);

                // Mobile toggle
                elements.mobileToggle.on('click', toggleSidebar);

                // Close sidebar on chat selection (mobile)
                if (window.innerWidth <= 992) {
                    $('.chat-item').on('click', () => elements.sidebar.removeClass('show'));
                }
            }

            // Tab switching
            function handleTabSwitch() {
                $('.chat-tab').removeClass('active');
                $(this).addClass('active');

                const tab = $(this).data('tab');
                $('.tab-content-list').hide();
                $(`.tab-content-list[data-content="${tab}"]`).show();

                resetChatView();
            }

            // Chat item click
            function handleChatItemClick() {
                $('.chat-item').removeClass('active');
                $(this).addClass('active');

                state.currentChatId = $(this).data('chat-id');
                state.currentChatStatus = $(this).data('status');

                loadChatDetail(state.currentChatId, state.currentChatStatus, $(this));
            }

            // Message input
            function handleMessageInput() {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 100) + 'px';
                updateSendButtonState();
            }

            // Message keydown
            function handleMessageKeydown(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    if (!elements.sendButton.prop('disabled') && !state.isSubmitting) {
                        elements.chatForm.trigger('submit');
                    }
                }
            }

            // File change
            function handleFileChange() {
                if (this.files.length > 0) {
                    elements.fileName.text('📎 ' + this.files[0].name);
                    elements.filePreview.addClass('show');
                } else {
                    elements.filePreview.removeClass('show');
                }
                updateSendButtonState();
            }

            // File remove
            function handleFileRemove() {
                elements.fileInput.val('');
                elements.filePreview.removeClass('show');
                updateSendButtonState();
            }

            // Form submit
            async function handleFormSubmit(e) {
                e.preventDefault();

                if (!state.currentChatId || state.isSubmitting) return;

                const hasText = elements.messageInput.val().trim() !== '';
                const hasFile = elements.fileInput[0].files.length > 0;

                if (!hasText && !hasFile) {
                    alert('Vui lòng nhập tin nhắn hoặc chọn tệp!');
                    return;
                }

                state.isSubmitting = true;
                elements.sendButton.prop('disabled', true);

                const formData = new FormData(this);
                const staffName = $(`.chat-item[data-chat-id="${state.currentChatId}"]`).data('staff-name');

                try {
                    const response = await $.ajax({
                        url: `/admin/chat/${state.currentChatId}/send`,
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (response.success) {
                        appendMessage(response.data, staffName);
                        state.lastMessageAt = response.data.created_at;

                        // Reset form
                        elements.chatForm[0].reset();
                        elements.messageInput.css('height', 'auto');
                        elements.filePreview.removeClass('show');
                        elements.messageInput.focus();
                    } else {
                        throw new Error(response.message || 'Gửi tin nhắn thất bại');
                    }
                } catch (error) {
                    console.error('Send message error:', error);
                    alert('Lỗi: ' + (error.responseJSON?.message || error.message || 'Không thể gửi tin nhắn'));
                } finally {
                    state.isSubmitting = false;
                    updateSendButtonState();
                }
            }

            // Toggle sidebar (mobile)
            function toggleSidebar() {
                elements.sidebar.toggleClass('show');
            }

            // Reset chat view
            function resetChatView() {
                state.currentChatId = null;
                state.currentChatStatus = null;
                state.lastMessageAt = null;

                if (state.pollingInterval) {
                    clearInterval(state.pollingInterval);
                    state.pollingInterval = null;
                }

                elements.chatHeader.hide();
                elements.chatInput.hide();
                elements.chatMessages.html(`
            <div class="chat-empty">
                <div class="chat-empty-icon">💬</div>
                <h5>Chọn một cuộc trò chuyện</h5>
                <p>Chọn từ danh sách bên trái để bắt đầu</p>
            </div>
        `);
            }

            // Load chat detail
            function loadChatDetail(chatId, status, chatItem) {
                elements.chatHeader.show();

                const userName = chatItem.data('user-name');
                const userInitial = chatItem.find('.chat-avatar').text().trim();

                $('#chatUserName').text(userName);
                $('#chatUserAvatar').text(userInitial);
                $('#chatStatus').text(status === 'pending' ? 'Chờ xử lý' : 'Đang xử lý');

                if (status === 'pending') {
                    $('#chatActions').empty();
                    elements.chatInput.hide();
                    showAssignForm(chatId);
                } else {
                    $('#chatActions').empty();
                    elements.chatInput.show();
                    loadMessages(chatId, chatItem.data('staff-name'));
                    startPolling(chatId, chatItem.data('staff-name'));
                }
            }

            // Show assign form
            function showAssignForm(chatId) {
                elements.chatMessages.html(`
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

            // Load messages
            async function loadMessages(chatId, staffName) {
                elements.chatMessages.html('<div class="loading">Đang tải tin nhắn</div>');

                try {
                    const response = await $.ajax({
                        url: `/admin/chat/${chatId}/messages`,
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (response.success) {
                        renderMessages(response.messages, staffName);
                        if (response.messages.length > 0) {
                            state.lastMessageAt = response.messages[response.messages.length - 1].created_at;
                        }
                    }
                } catch (error) {
                    console.error('Load messages error:', error);
                    elements.chatMessages.html(`
                <div class="chat-empty">
                    <div class="chat-empty-icon">⚠️</div>
                    <h5>Không thể tải tin nhắn</h5>
                    <p>Vui lòng thử lại sau</p>
                </div>
            `);
                }
            }

            // Render messages
            function renderMessages(messages, staffName) {
                if (!messages || messages.length === 0) {
                    elements.chatMessages.html(`
                <div class="chat-empty">
                    <div class="chat-empty-icon">💬</div>
                    <h5>Chưa có tin nhắn</h5>
                    <p>Bắt đầu cuộc trò chuyện</p>
                </div>
            `);
                    return;
                }

                const html = messages.map(msg => createMessageHTML(msg, staffName)).join('');
                elements.chatMessages.html(html);
                scrollToBottom();
            }

            // Append message
            function appendMessage(msg, staffName) {
                const html = createMessageHTML(msg, staffName);

                if (elements.chatMessages.find('.chat-empty').length) {
                    elements.chatMessages.html(html);
                } else {
                    elements.chatMessages.append(html);
                }

                scrollToBottom();
            }

            // Create message HTML
            function createMessageHTML(msg, staffName) {
                const isMe = msg.sender_id == {{ auth()->id() }};
                const senderName = msg.sender_name || (isMe ? 'Bạn' : staffName || 'Khách hàng');
                const senderInitial = escapeHtml(senderName.charAt(0).toUpperCase());

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

                return `
            <div class="message ${isMe ? 'sent' : 'received'}" data-created-at="${msg.created_at}">
                <div class="message-avatar">${senderInitial}</div>
                <div class="message-content">
                    <div class="message-bubble">${content}</div>
                    <div class="message-time">${escapeHtml(senderName)} • ${formatDateTime(msg.created_at)}</div>
                </div>
            </div>
        `;
            }

            // Start polling
            function startPolling(chatId, staffName) {
                if (state.pollingInterval) {
                    clearInterval(state.pollingInterval);
                }

                state.pollingInterval = setInterval(async () => {
                    if (state.currentChatId !== chatId) return;

                    try {
                        const response = await $.ajax({
                            url: `/admin/chat/${chatId}/messages`,
                            method: 'GET',
                            data: {
                                last_message_at: state.lastMessageAt
                            },
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (response.success && response.messages && response.messages.length > 0) {
                            response.messages.forEach(msg => appendMessage(msg, staffName));
                            state.lastMessageAt = response.messages[response.messages.length - 1]
                                .created_at;
                        }
                    } catch (error) {
                        console.error('Polling error:', error);
                    }
                }, 3000);
            }

            // Update send button state
            function updateSendButtonState() {
                const hasText = elements.messageInput.val().trim() !== '';
                const hasFile = elements.fileInput[0]?.files.length > 0;
                elements.sendButton.prop('disabled', !(hasText || hasFile) || state.isSubmitting);
            }

            // Scroll to bottom
            function scrollToBottom() {
                elements.chatMessages.scrollTop(elements.chatMessages[0].scrollHeight);
            }

            // Format datetime
            function formatDateTime(dateStr) {
                try {
                    const date = new Date(dateStr);
                    return date.toLocaleString('vi-VN', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                } catch (e) {
                    return 'Vừa xong';
                }
            }

            // Escape HTML
            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            // Cleanup on page unload
            $(window).on('beforeunload', function() {
                if (state.pollingInterval) {
                    clearInterval(state.pollingInterval);
                }
            });

            // Initialize on document ready
            $(document).ready(init);
        })();
    </script>
@endpush
