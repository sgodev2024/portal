@extends('backend.layouts.master')

@section('title', 'Quản lý Chat')

@push('styles')
    <style>
        .chat-container {
            height: calc(100vh - 200px);
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
            background: linear-gradient(135deg, #0084ff 0%, #00a8ff 100%);
            color: white;
        }

        .chat-sidebar-header h5 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
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
            text-decoration: none;
            color: inherit;
            position: relative;
        }

        .chat-item:hover {
            background: #f2f3f5;
        }

        .chat-item.active {
            background: #e7f3ff;
        }

        /* Tin nhắn chưa đọc */
        .chat-item.unread {
            background: #f0f8ff;
        }

        .chat-item.unread .chat-name span:first-child,
        .chat-item.unread .chat-preview {
            font-weight: 700;
            color: #050505;
        }

        .chat-item.unread::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 60%;
            background: #0084ff;
            border-radius: 0 4px 4px 0;
        }

        /* Badge số tin nhắn chưa đọc */
        .unread-badge {
            background: #0084ff;
            color: white;
            font-size: 12px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 12px;
            min-width: 20px;
            text-align: center;
            margin-right: 4px;
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
            position: relative;
        }

        /* Chấm xanh online */
        .chat-avatar.online::after {
            content: '';
            position: absolute;
            bottom: 2px;
            right: 2px;
            width: 14px;
            height: 14px;
            background: #31a24c;
            border: 2px solid white;
            border-radius: 50%;
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
            white-space: nowrap;
        }

        .chat-item.unread .chat-time {
            color: #0084ff;
            font-weight: 600;
        }

        .chat-preview {
            font-size: 13px;
            color: #65676b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Khung chat chính */
        .chat-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #fff;
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

        /* Animation cho tin nhắn mới */
        @keyframes newMessage {
            0% {
                background: #e7f3ff;
            }
            50% {
                background: #cce7ff;
            }
            100% {
                background: #f0f8ff;
            }
        }

        .chat-item.new-message {
            animation: newMessage 1s ease-in-out;
        }

        /* Scrollbar */
        .chat-list::-webkit-scrollbar {
            width: 8px;
        }

        .chat-list::-webkit-scrollbar-track {
            background: transparent;
        }

        .chat-list::-webkit-scrollbar-thumb {
            background: #bcc0c4;
            border-radius: 4px;
        }

        .chat-list::-webkit-scrollbar-thumb:hover {
            background: #a0a4a8;
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .chat-sidebar {
                width: 100%;
            }

            .chat-main {
                display: none;
            }
        }
    </style>
@endpush

@section('content')
    <div class="chat-container">
        <!-- Sidebar danh sách chat -->
        <div class="chat-sidebar">
            <div class="chat-sidebar-header">
                <h5>Danh sách chat</h5>
            </div>

            <div class="chat-list" id="chatList">
                @php
                    // Sắp xếp chat theo thời gian tin nhắn mới nhất
                    $sortedChats = $chats->sortByDesc(function ($chat) {
                        $messages = $chat->content ?? [];
                        if (!empty($messages)) {
                            $lastMsg = end($messages);
                            return $lastMsg['created_at'] ?? ($chat->last_message_at ?? $chat->created_at);
                        }
                        return $chat->last_message_at ?? $chat->created_at;
                    });
                @endphp

                @forelse($sortedChats as $chat)
                    @php
                        // Lấy tin nhắn mới nhất
                        $messages = $chat->content ?? [];
                        $lastMessage = !empty($messages) ? end($messages) : null;

                        // Xác định preview
                        $preview = 'Chưa có tin nhắn';
                        if ($lastMessage) {
                            if (($lastMessage['type'] ?? '') === 'text') {
                                $preview = $lastMessage['content'] ?? 'Tin nhắn văn bản';
                            } elseif (($lastMessage['type'] ?? '') === 'image') {
                                $preview = '📷 Hình ảnh';
                            } else {
                                $preview = '📎 Tệp đính kèm';
                            }
                        }

                        // Thời gian tin nhắn cuối
                        $lastTime = $lastMessage['created_at'] ?? ($chat->last_message_at ?? $chat->created_at);

                        // Kiểm tra tin nhắn chưa đọc
                        $unreadCount = $chat->unread_count ?? 0;
                        $isUnread = $unreadCount > 0;
                    @endphp

                    <a href="{{ route('staff.chats.show', $chat->id) }}"
                       class="chat-item {{ $isUnread ? 'unread' : '' }}"
                       data-chat-id="{{ $chat->id }}"
                       data-last-time="{{ $lastTime }}">
                        <div class="chat-avatar {{ rand(0, 1) ? 'online' : '' }}">
                            {{ strtoupper(substr($chat->user->name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="chat-info">
                            <div class="chat-name">
                                <span>{{ $chat->user->name ?? 'Khách hàng' }}</span>
                                <div style="display: flex; align-items: center; gap: 4px;">
                                    @if($isUnread)
                                        <span class="unread-badge">{{ $unreadCount }}</span>
                                    @endif
                                    <span class="chat-time">
                                        {{ \Carbon\Carbon::parse($lastTime)->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                            <div class="chat-preview">
                                {{ Str::limit($preview, 35) }}
                            </div>
                        </div>
                    </a>
                @empty
                    <div style="padding: 32px; text-align: center; color: #65676b;">
                        <p>Chưa có cuộc trò chuyện nào</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Khung chat chính -->
        <div class="chat-main">
            <div class="chat-empty">
                <div class="chat-empty-icon">💬</div>
                <h5>Chọn một cuộc trò chuyện</h5>
                <p>Chọn từ danh sách bên trái để bắt đầu</p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let isLoadingMessages = false;

            // Highlight active chat
            const currentPath = window.location.pathname;
            $('.chat-item').each(function() {
                if ($(this).attr('href') === currentPath) {
                    $(this).addClass('active');
                }
            });

            // Hàm format thời gian
            function formatTime(timestamp) {
                const now = new Date();
                const time = new Date(timestamp);
                const diff = Math.floor((now - time) / 1000);

                if (diff < 60) return 'Vừa xong';
                if (diff < 3600) return Math.floor(diff / 60) + ' phút trước';
                if (diff < 86400) return Math.floor(diff / 3600) + ' giờ trước';
                if (diff < 604800) return Math.floor(diff / 86400) + ' ngày trước';

                return time.toLocaleDateString('vi-VN');
            }

            // Hàm sắp xếp chat list
            function sortChatList() {
                const $chatList = $('#chatList');
                const $items = $chatList.find('.chat-item').get();

                if ($items.length === 0) return;

                $items.sort(function(a, b) {
                    const timeA = new Date($(a).data('last-time'));
                    const timeB = new Date($(b).data('last-time'));
                    return timeB - timeA;
                });

                $.each($items, function(idx, item) {
                    $chatList.append(item);
                });
            }

            // Hàm cập nhật thời gian hiển thị
            function updateTimeDisplay() {
                $('.chat-item').each(function() {
                    const timestamp = $(this).data('last-time');
                    if (timestamp) {
                        $(this).find('.chat-time').text(formatTime(timestamp));
                    }
                });
            }

            // Hàm load tin nhắn mới
            function loadNewMessages() {
                if (isLoadingMessages) {
                    console.log('Đang load, bỏ qua...');
                    return;
                }

                isLoadingMessages = true;

                $.ajax({
                    url: '{{ route("staff.chats.index") }}',
                    method: 'GET',
                    dataType: 'json',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        console.log('✅ Load thành công:', response);

                        if (response.success && response.chats && response.chats.length > 0) {
                            let hasUpdate = false;

                            response.chats.forEach(function(chat) {
                                if (updateChatItem(chat)) {
                                    hasUpdate = true;
                                }
                            });

                            if (hasUpdate) {
                                sortChatList();
                                console.log('🔄 Đã cập nhật danh sách chat');
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('❌ Lỗi khi tải tin nhắn:', error);
                        console.error('Response:', xhr.responseText);
                    },
                    complete: function() {
                        isLoadingMessages = false;
                    }
                });
            }

            // Hàm cập nhật chat item
            function updateChatItem(chatData) {
                const $chatItem = $(`.chat-item[data-chat-id="${chatData.id}"]`);

                if ($chatItem.length === 0) {
                    console.log('Chat item không tồn tại:', chatData.id);
                    return false;
                }

                const oldTime = $chatItem.attr('data-last-time');
                const newTime = chatData.last_message_time;

                // Kiểm tra xem có tin nhắn mới không
                const hasNewMessage = new Date(newTime) > new Date(oldTime);

                if (!hasNewMessage && chatData.unread_count === 0) {
                    return false; // Không có gì thay đổi
                }

                // Cập nhật thời gian
                $chatItem.attr('data-last-time', newTime);
                $chatItem.find('.chat-time').text(formatTime(newTime));

                // Cập nhật preview
                $chatItem.find('.chat-preview').text(chatData.last_message_preview);

                // Xử lý unread
                if (chatData.unread_count > 0) {
                    $chatItem.addClass('unread');

                    // Nếu có tin nhắn mới
                    if (hasNewMessage) {
                        $chatItem.addClass('new-message');
                        setTimeout(function() {
                            $chatItem.removeClass('new-message');
                        }, 1000);

                        // Phát âm thanh
                        playNotificationSound();
                    }

                    // Cập nhật badge
                    let $badge = $chatItem.find('.unread-badge');
                    if ($badge.length) {
                        $badge.text(chatData.unread_count);
                    } else {
                        const $nameDiv = $chatItem.find('.chat-name > div');
                        $nameDiv.prepend(`<span class="unread-badge">${chatData.unread_count}</span>`);
                    }
                } else {
                    $chatItem.removeClass('unread');
                    $chatItem.find('.unread-badge').remove();
                }

                return true;
            }

            // Hàm phát âm thanh thông báo
            function playNotificationSound() {
                try {
                    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                    const oscillator = audioContext.createOscillator();
                    const gainNode = audioContext.createGain();

                    oscillator.connect(gainNode);
                    gainNode.connect(audioContext.destination);

                    oscillator.frequency.value = 800;
                    oscillator.type = 'sine';

                    gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);

                    oscillator.start(audioContext.currentTime);
                    oscillator.stop(audioContext.currentTime + 0.3);
                } catch (e) {
                    console.log('Không thể phát âm thanh:', e);
                }
            }

            // Cập nhật thời gian mỗi phút
            setInterval(updateTimeDisplay, 60000);

            // Load tin nhắn mới mỗi 5 giây
            setInterval(loadNewMessages, 5000);

            // Load tin nhắn mới ngay khi trang load (sau 2 giây)
            setTimeout(function() {
                console.log('🚀 Bắt đầu load tin nhắn...');
                loadNewMessages();
            }, 2000);

            // Khi click vào chat item, đánh dấu đã đọc
            $('.chat-item').on('click', function(e) {
                const chatId = $(this).data('chat-id');
                const $this = $(this);

                // Remove unread styling ngay lập tức
                $this.removeClass('unread');
                $this.find('.unread-badge').remove();

                // Gửi request đánh dấu đã đọc
                $.ajax({
                    url: '{{ route("staff.chats.mark-read", ":id") }}'.replace(':id', chatId),
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        console.log('✅ Đã đánh dấu đọc chat:', chatId);
                    },
                    error: function(xhr, status, error) {
                        console.error('❌ Lỗi đánh dấu đã đọc:', error);
                    }
                });
            });
        });
    </script>
@endpush
