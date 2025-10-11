<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chat Hỗ Trợ</title>
</head>
<body>
<div class="chat-container">
    <!-- Messages Area -->
    <div id="chat-messages" class="chat-messages">
        <div class="welcome-message">
            <div class="welcome-icon">💬</div>
            <h4>Chào mừng bạn!</h4>
            <p>Chúng tôi sẵn sàng hỗ trợ bạn</p>
        </div>
    </div>

    <!-- Input Area -->
    <div class="chat-input-wrapper">
        <form id="chat-form" class="chat-form">
            <div id="file-preview" class="file-preview"></div>
            <div class="input-container">
                <label for="file-input" class="file-upload-btn" title="Đính kèm tệp">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M21.44,11.05L12.25,20.24C11.32,21.17 10.08,21.7 8.79,21.7C7.5,21.7 6.26,21.17 5.33,20.24C4.4,19.31 3.87,18.07 3.87,16.78C3.87,15.5 4.4,14.26 5.33,13.33L14.53,4.13C15.19,3.47 16.11,3.1 17.06,3.1C18,3.1 18.93,3.47 19.59,4.13C20.25,4.79 20.62,5.72 20.62,6.66C20.62,7.61 20.25,8.53 19.59,9.19L10.4,18.39C10.07,18.72 9.63,18.91 9.17,18.91C8.71,18.91 8.27,18.72 7.94,18.39C7.61,18.06 7.42,17.62 7.42,17.16C7.42,16.7 7.61,16.26 7.94,15.93L15.83,8.04L14.77,6.97L6.88,14.86C6.29,15.45 5.96,16.24 5.96,17.06C5.96,17.88 6.29,18.67 6.88,19.26C7.47,19.85 8.26,20.18 9.08,20.18C9.9,20.18 10.69,19.85 11.28,19.26L20.47,10.06C21.4,9.13 21.93,7.89 21.93,6.6C21.93,5.31 21.4,4.07 20.47,3.14C19.54,2.21 18.3,1.68 17.01,1.68C15.72,1.68 14.48,2.21 13.55,3.14L4.35,12.34C3.14,13.55 2.46,15.13 2.46,16.78C2.46,18.43 3.14,20.01 4.35,21.22C5.56,22.43 7.14,23.11 8.79,23.11C10.44,23.11 12.02,22.43 13.23,21.22L22.42,12.03L21.44,11.05Z"/>
                    </svg>
                </label>
                <input type="file" id="file-input" accept="image/*,.pdf,.doc,.docx" style="display: none;">
                <textarea id="message-input" class="message-input" placeholder="Aa" rows="1"></textarea>
                <button type="submit" class="send-btn" disabled>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M2,21L23,12L2,3V10L17,12L2,14V21Z"/>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        overflow: hidden;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    .chat-container {
        display: flex;
        flex-direction: column;
        height: 100vh;
        background: #fff;
    }

    /* Messages Area */
    .chat-messages {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 16px;
        background: linear-gradient(to bottom, #f8f9fa 0%, #f0f2f5 100%);
        scroll-behavior: auto;
        will-change: scroll-position;
    }

    .chat-messages::-webkit-scrollbar {
        width: 6px;
    }

    .chat-messages::-webkit-scrollbar-track {
        background: transparent;
    }

    .chat-messages::-webkit-scrollbar-thumb {
        background: #bcc0c4;
        border-radius: 3px;
    }

    .chat-messages::-webkit-scrollbar-thumb:hover {
        background: #a0a4a8;
    }

    .welcome-message {
        text-align: center;
        padding: 60px 20px;
        color: #65676b;
    }

    .welcome-icon {
        font-size: 56px;
        margin-bottom: 14px;
        animation: bounce 2s ease-in-out infinite;
    }

    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }

    .welcome-message h4 {
        font-size: 22px;
        color: #1c1e21;
        margin-bottom: 6px;
        font-weight: 600;
    }

    .welcome-message p {
        font-size: 15px;
        color: #65676b;
    }

    .message-wrapper {
        display: flex;
        align-items: flex-end;
        margin-bottom: 14px;
        animation: slideIn 0.25s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(6px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .message-wrapper.customer {
        flex-direction: row-reverse;
    }

    .message-avatar {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 13px;
        font-weight: 600;
        flex-shrink: 0;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    .message-wrapper.customer .message-avatar {
        margin-left: 8px;
        background: linear-gradient(135deg, #0084ff 0%, #00a8ff 100%);
    }

    .message-wrapper.staff .message-avatar {
        margin-right: 8px;
    }

    .message-content-wrapper {
        max-width: 70%;
        display: flex;
        flex-direction: column;
    }

    .message-wrapper.customer .message-content-wrapper {
        align-items: flex-end;
    }

    .message-wrapper.staff .message-content-wrapper {
        align-items: flex-start;
    }

    .message-bubble {
        padding: 9px 13px;
        border-radius: 18px;
        word-wrap: break-word;
        word-break: break-word;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
        display: inline-block;
        line-height: 1.4;
    }

    .message-bubble.customer {
        background: linear-gradient(135deg, #0084ff 0%, #0073e6 100%);
        color: #fff;
    }

    .message-bubble.staff {
        background: #fff;
        color: #1c1e21;
    }

    .message-bubble img {
        max-width: 200px;
        height: auto;
        border-radius: 10px;
        display: block;
        margin-top: 4px;
    }

    .message-bubble a {
        color: #0084ff;
        text-decoration: none;
        font-weight: 500;
        word-break: break-all;
    }

    .message-bubble.customer a {
        color: #fff;
        text-decoration: underline;
    }

    .message-time {
        font-size: 11px;
        color: #8a8d91;
        margin-top: 4px;
        padding: 0 10px;
    }

    .no-messages, .error-message {
        text-align: center;
        padding: 50px 20px;
        color: #65676b;
        font-size: 14px;
    }

    .error-message {
        color: #ff4d4f;
    }

    /* Input Area */
    .chat-input-wrapper {
        padding: 12px 16px;
        border-top: 1px solid #e4e6eb;
        background: #fff;
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.04);
        flex-shrink: 0;
    }

    .chat-form {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .file-preview {
        display: none;
        font-size: 12px;
        color: #65676b;
        padding: 8px 12px;
        background: linear-gradient(135deg, #e7f3ff 0%, #d4ebff 100%);
        border-radius: 10px;
        align-items: center;
        gap: 8px;
        border: 1px solid #b3d9ff;
    }

    .file-preview.show {
        display: flex;
    }

    .file-preview .remove-file {
        cursor: pointer;
        color: #0084ff;
        font-weight: bold;
        margin-left: auto;
        padding: 4px 8px;
        border-radius: 6px;
        transition: background 0.2s;
    }

    .file-preview .remove-file:hover {
        background: rgba(0, 132, 255, 0.1);
    }

    .input-container {
        display: flex;
        align-items: flex-end;
        background: #f0f2f5;
        border-radius: 22px;
        padding: 8px 12px;
        gap: 8px;
        transition: all 0.2s;
    }

    .input-container:focus-within {
        background: #e8eaed;
        box-shadow: 0 0 0 2px rgba(0, 132, 255, 0.15);
    }

    .file-upload-btn {
        background: transparent;
        border: none;
        color: #0084ff;
        cursor: pointer;
        padding: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 50%;
        transition: all 0.2s;
    }

    .file-upload-btn:hover {
        background: rgba(0, 132, 255, 0.1);
        transform: scale(1.08);
    }

    .message-input {
        flex: 1;
        border: none;
        background: transparent;
        outline: none;
        padding: 5px 8px;
        font-size: 15px;
        resize: none;
        max-height: 100px;
        font-family: inherit;
        line-height: 1.4;
        color: #1c1e21;
    }

    .message-input::placeholder {
        color: #8a8d91;
    }

    .send-btn {
        background: linear-gradient(135deg, #0084ff 0%, #0073e6 100%);
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
        box-shadow: 0 2px 8px rgba(0, 132, 255, 0.3);
    }

    .send-btn:hover:not(:disabled) {
        background: linear-gradient(135deg, #0073e6 0%, #0062cc 100%);
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0, 132, 255, 0.4);
    }

    .send-btn:active:not(:disabled) {
        transform: scale(0.95);
    }

    .send-btn:disabled {
        background: #bcc0c4;
        cursor: not-allowed;
        box-shadow: none;
    }
</style>

<script>
(function() {
    'use strict';

    let lastMessageCount = 0;
    let isLoadingMessages = false;

    document.addEventListener('DOMContentLoaded', function() {
        const messagesDiv = document.getElementById('chat-messages');
        const form = document.getElementById('chat-form');
        const input = document.getElementById('message-input');
        const fileInput = document.getElementById('file-input');
        const sendBtn = form.querySelector('.send-btn');
        const filePreview = document.getElementById('file-preview');
        const currentUserId = {{ Auth::id() }};

        // Auto resize textarea
        input.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 100) + 'px';
            updateSendButtonState();
        });

        // File input change
        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                const file = this.files[0];
                const fileSize = (file.size / 1024).toFixed(2);
                filePreview.innerHTML = `
                    📎 <strong>${file.name}</strong> (${fileSize} KB)
                    <span class="remove-file">✕</span>
                `;
                filePreview.classList.add('show');
            } else {
                filePreview.classList.remove('show');
            }
            updateSendButtonState();
        });

        // Remove file
        filePreview.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-file')) {
                fileInput.value = '';
                filePreview.classList.remove('show');
                updateSendButtonState();
            }
        });

        // Update send button state
        function updateSendButtonState() {
            const hasText = input.value.trim() !== '';
            const hasFile = fileInput.files.length > 0;
            sendBtn.disabled = !(hasText || hasFile);
        }

        // Enter to send
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (!sendBtn.disabled) {
                    form.dispatchEvent(new Event('submit'));
                }
            }
        });

        // Load messages (OPTIMIZED - No flicker)
        function loadMessages(forceScroll = false) {
            if (isLoadingMessages) return;
            isLoadingMessages = true;

            fetch("{{ route('customer.chatcustomer.messages') }}")
                .then(res => res.json())
                .then(data => {
                    const messages = data.messages || [];

                    // Check if messages changed
                    if (messages.length === lastMessageCount && !forceScroll) {
                        isLoadingMessages = false;
                        return; // No changes, don't update DOM
                    }

                    const wasAtBottom = isScrolledToBottom();
                    lastMessageCount = messages.length;

                    // Clear or show welcome
                    if (messages.length === 0) {
                        messagesDiv.innerHTML = `
                            <div class="welcome-message">
                                <div class="welcome-icon">💬</div>
                                <h4>Chào mừng bạn!</h4>
                                <p>Gửi tin nhắn đầu tiên để bắt đầu trò chuyện</p>
                            </div>
                        `;
                        isLoadingMessages = false;
                        return;
                    }

                    // Build messages HTML
                    const fragment = document.createDocumentFragment();

                    messages.forEach(msg => {
                        const isCustomer = msg.sender_id == currentUserId;
                        const msgWrapper = document.createElement('div');
                        msgWrapper.className = `message-wrapper ${isCustomer ? 'customer' : 'staff'}`;

                        const avatar = document.createElement('div');
                        avatar.className = 'message-avatar';
                        const senderName = msg.sender_name || (isCustomer ? 'Bạn' : 'Hỗ trợ');
                        avatar.textContent = senderName.charAt(0).toUpperCase();

                        const contentWrapper = document.createElement('div');
                        contentWrapper.className = 'message-content-wrapper';

                        const bubble = document.createElement('div');
                        bubble.className = `message-bubble ${isCustomer ? 'customer' : 'staff'}`;

                        // Message content
                        if (msg.type === 'text') {
                            bubble.textContent = msg.content;
                        } else if (msg.type === 'image') {
                            const img = document.createElement('img');
                            img.src = `/storage/${msg.file_path}`;
                            img.alt = msg.file_name || 'Ảnh';
                            img.loading = 'lazy';
                            bubble.appendChild(img);
                        } else if (msg.type === 'file') {
                            const link = document.createElement('a');
                            link.href = `/storage/${msg.file_path}`;
                            link.target = '_blank';
                            link.textContent = `📎 ${msg.file_name || 'Tệp đính kèm'}`;
                            bubble.appendChild(link);
                        }

                        const time = document.createElement('div');
                        time.className = 'message-time';
                        const msgDate = new Date(msg.created_at);
                        time.textContent = msgDate.toLocaleString('vi-VN', {
                            day: '2-digit',
                            month: '2-digit',
                            hour: '2-digit',
                            minute: '2-digit'
                        });

                        contentWrapper.appendChild(bubble);
                        contentWrapper.appendChild(time);
                        msgWrapper.appendChild(avatar);
                        msgWrapper.appendChild(contentWrapper);
                        fragment.appendChild(msgWrapper);
                    });

                    // Update DOM once
                    messagesDiv.innerHTML = '';
                    messagesDiv.appendChild(fragment);

                    // Scroll if needed
                    if (forceScroll || wasAtBottom) {
                        scrollToBottom();
                    }

                    isLoadingMessages = false;
                })
                .catch(err => {
                    messagesDiv.innerHTML = '<p class="error-message">❌ Không thể tải tin nhắn!</p>';
                    console.error('Load error:', err);
                    isLoadingMessages = false;
                });
        }

        // Check if scrolled to bottom
        function isScrolledToBottom() {
            const threshold = 50;
            return messagesDiv.scrollHeight - messagesDiv.scrollTop - messagesDiv.clientHeight < threshold;
        }

        // Smooth scroll to bottom
        function scrollToBottom() {
            requestAnimationFrame(() => {
                messagesDiv.scrollTop = messagesDiv.scrollHeight;
            });
        }

        // Send message
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const message = input.value.trim();
            const file = fileInput.files[0];
            if (!message && !file) return;

            sendBtn.disabled = true;

            const formData = new FormData();
            if (message) formData.append('message', message);
            if (file) formData.append('file', file);

            fetch("{{ route('customer.chatcustomer.send') }}", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                body: formData
            })
                .then(res => {
                    if (!res.ok) throw new Error('Failed');
                    return res.json();
                })
                .then(() => {
                    input.value = '';
                    input.style.height = 'auto';
                    fileInput.value = '';
                    filePreview.classList.remove('show');
                    updateSendButtonState();
                    loadMessages(true);
                })
                .catch(err => {
                    alert('❌ Không thể gửi tin nhắn!');
                    console.error('Send error:', err);
                })
                .finally(() => {
                    sendBtn.disabled = false;
                });
        });

        // Initialize
        loadMessages(true);
        setInterval(() => loadMessages(false), 3000);
        updateSendButtonState();
    });
})();
</script>
</body>
</html>
