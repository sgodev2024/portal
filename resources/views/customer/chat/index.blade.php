<div class="chat-container">
    <!-- Vùng hiển thị tin nhắn -->
    <div id="chat-messages" class="chat-messages">
        <p>Đang tải tin nhắn...</p>
    </div>

    <!-- Form gửi tin nhắn -->
    <form id="chat-form" class="chat-form">
        <div class="input-container">
            <input type="text" id="message-input" placeholder="Nhập tin nhắn..." autocomplete="off">
            <label for="file-input" class="file-upload-label">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#666" stroke-width="2">
                    <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                </svg>
            </label>
            <input type="file" id="file-input" accept="image/*,.pdf,.doc,.docx" style="display: none;">
        </div>
        <button type="submit">Gửi</button>
    </form>
</div>

<style>
    .chat-container {
        display: flex;
        flex-direction: column;
        height: 100%;
        max-width: 800px;
        margin: 0 auto;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        background: #fff;
    }

    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        background: #f5f6f8;
        scroll-behavior: smooth;
    }

    .chat-messages::-webkit-scrollbar {
        width: 8px;
    }

    .chat-messages::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 4px;
    }

    .chat-form {
        display: flex;
        align-items: center;
        border-top: 1px solid #e0e0e0;
        background: #fff;
        padding: 10px;
    }

    .input-container {
        flex: 1;
        display: flex;
        align-items: center;
        position: relative;
    }

    .chat-form input[type="text"] {
        flex: 1;
        padding: 12px 40px 12px 12px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        outline: none;
        font-size: 14px;
        transition: border-color 0.3s;
    }

    .chat-form input[type="text"]:focus {
        border-color: #0084ff;
    }

    .file-upload-label {
        position: absolute;
        right: 10px;
        cursor: pointer;
        padding: 5px;
        display: flex;
        align-items: center;
    }

    .file-upload-label:hover svg {
        stroke: #0084ff;
    }

    .chat-form button {
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        background: #0084ff;
        color: #fff;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s;
        margin-left: 10px;
    }

    .chat-form button:hover {
        background: #0066cc;
    }

    .message-wrapper {
        display: flex;
        flex-direction: column;
        margin-bottom: 12px;
    }

    .message-wrapper.customer {
        align-items: flex-end;
    }

    .message-wrapper.staff {
        align-items: flex-start;
    }

    .message-bubble {
        max-width: 70%;
        padding: 10px 15px;
        border-radius: 12px;
        word-wrap: break-word;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        position: relative;
    }

    .message-bubble.customer {
        background: #0084ff;
        color: #fff;
        border-bottom-right-radius: 4px;
    }

    .message-bubble.staff {
        background: #fff;
        color: #333;
        border-bottom-left-radius: 4px;
    }

    .message-sender {
        font-size: 12px;
        color: #666;
        margin-bottom: 4px;
    }

    .message-content img {
        max-width: 200px;
        border-radius: 8px;
        display: block;
    }

    .message-content a {
        color: #0084ff;
        text-decoration: none;
        font-weight: 500;
    }

    .message-content a:hover {
        text-decoration: underline;
    }

    .no-messages {
        text-align: center;
        color: #999;
        font-style: italic;
    }

    .error-message {
        text-align: center;
        color: #ff4d4f;
        font-weight: 500;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const messagesDiv = document.getElementById('chat-messages');
        const form = document.getElementById('chat-form');
        const input = document.getElementById('message-input');
        const fileInput = document.getElementById('file-input');

        const currentUserId = {{ Auth::id() }};

        // Hàm tải tin nhắn
        function loadMessages() {
            fetch("{{ route('customer.chatcustomer.messages') }}")
                .then(res => res.json())
                .then(data => {
                    messagesDiv.innerHTML = '';

                    const messages = data.messages || [];

                    if (messages.length === 0) {
                        messagesDiv.innerHTML = '<p class="no-messages">Chưa có tin nhắn nào.</p>';
                        return;
                    }

                    messages.forEach(msg => {
                        const isCustomer = msg.sender_id == currentUserId;

                        const msgWrapper = document.createElement('div');
                        msgWrapper.className = `message-wrapper ${isCustomer ? 'customer' : 'staff'}`;

                        const bubble = document.createElement('div');
                        bubble.className = `message-bubble ${isCustomer ? 'customer' : 'staff'}`;

                        const name = document.createElement('div');
                        name.className = 'message-sender';
                        name.textContent = msg.sender_name || (isCustomer ? 'Bạn' : 'Nhân viên');

                        const content = document.createElement('div');
                        content.className = 'message-content';

                        if (msg.type === 'text') {
                            content.textContent = msg.content;
                        } else if (msg.type === 'image') {
                            const img = document.createElement('img');
                            img.src = `/storage/${msg.file_path}`;
                            img.alt = msg.file_name || 'Ảnh';
                            content.appendChild(img);
                        } else if (msg.type === 'file') {
                            const a = document.createElement('a');
                            a.href = `/storage/${msg.file_path}`;
                            a.textContent = msg.file_name || 'Tệp đính kèm';
                            a.target = '_blank';
                            content.appendChild(a);
                        }

                        bubble.appendChild(name);
                        bubble.appendChild(content);
                        msgWrapper.appendChild(bubble);
                        messagesDiv.appendChild(msgWrapper);
                    });

                    messagesDiv.scrollTop = messagesDiv.scrollHeight;
                })
                .catch(err => {
                    messagesDiv.innerHTML = '<p class="error-message">Không thể tải tin nhắn!</p>';
                    console.error(err);
                });
        }

        // Gửi tin nhắn hoặc file
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const message = input.value.trim();
            const file = fileInput.files[0];

            if (!message && !file) return;

            const formData = new FormData();
            if (message) formData.append('message', message);
            if (file) formData.append('file', file);

            fetch("{{ route('customer.chatcustomer.send') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(() => {
                    input.value = '';
                    fileInput.value = '';
                    loadMessages();
                })
                .catch(err => {
                    messagesDiv.innerHTML = '<p class="error-message">Không thể gửi tin nhắn hoặc file!</p>';
                    console.error(err);
                });
        });

        // Tải tin nhắn lần đầu và cập nhật tự động
        loadMessages();
        setInterval(loadMessages, 3000);
    });
</script>
