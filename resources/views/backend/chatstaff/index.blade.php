@extends('backend.layouts.master')

@section('title', 'Danh sách Chat Khách hàng')

@push('styles')
<style>
    .chat-list-container {
        max-width: 100%;
        margin: 0 auto;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    .chat-list-header {
        padding: 20px 24px;
        border-bottom: 1px solid #e4e6eb;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .chat-list-header h4 {
        margin: 0;
        font-size: 28px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .chat-list-header p {
        margin: 8px 0 0 0;
        opacity: 0.9;
        font-size: 14px;
    }

    .chat-tab-btn {
        padding: 10px 20px;
        border-radius: 20px;
        background: white;
        border: 2px solid #e4e6eb;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 14px;
        color: #050505;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .chat-tab-btn:hover {
        background: #f0f2f5;
        transform: translateY(-2px);
    }

    .chat-tab-btn.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-color: transparent;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .chat-tab-badge {
        background: rgba(255,255,255,0.3);
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 700;
    }

    .chat-tab-btn.active .chat-tab-badge {
        background: rgba(255,255,255,0.25);
    }

    .chat-list-body {
        padding: 16px;
        max-height: calc(100vh - 300px);
        overflow-y: auto;
    }

    .chat-list-empty {
        text-align: center;
        padding: 60px 20px;
        color: #65676b;
    }

    .empty-icon {
        width: 100px;
        height: 100px;
        margin: 0 auto 20px;
        border-radius: 50%;
        background: linear-gradient(135deg, #e4e6eb 0%, #f0f2f5 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
    }

    .chat-list-empty h5 {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 8px;
        color: #050505;
    }

    .chat-list-item {
        display: flex;
        align-items: center;
        padding: 16px;
        margin-bottom: 8px;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s;
        background: white;
        border: 2px solid transparent;
        text-decoration: none;
        color: inherit;
    }

    .chat-list-item:hover {
        background: #f0f2f5;
        transform: translateX(4px);
        border-color: #e4e6eb;
    }

    .chat-list-item.disabled {
        opacity: 0.6;
        cursor: not-allowed;
        background: #f8f9fa;
    }

    .chat-list-item.disabled:hover {
        transform: none;
        border-color: transparent;
    }

    .chat-item-avatar {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 24px;
        margin-right: 16px;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        position: relative;
    }

    .chat-item-avatar.closed {
        background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
        box-shadow: 0 4px 12px rgba(149, 165, 166, 0.3);
    }

    .avatar-status {
        position: absolute;
        bottom: 2px;
        right: 2px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 3px solid white;
    }

    .avatar-status.online {
        background: #31a24c;
    }

    .avatar-status.offline {
        background: #95a5a6;
    }

    .chat-item-info {
        flex: 1;
        min-width: 0;
    }

    .chat-item-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 6px;
    }

    .chat-item-name {
        font-weight: 600;
        font-size: 16px;
        color: #050505;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .chat-item-time {
        font-size: 12px;
        color: #65676b;
        white-space: nowrap;
    }

    .chat-item-preview {
        font-size: 14px;
        color: #65676b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 6px;
    }

    .chat-item-footer {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-badge.processing {
        background: #d1ecf1;
        color: #0c5460;
    }

    .status-badge.closed {
        background: #d4edda;
        color: #155724;
    }

    .status-badge i {
        font-size: 10px;
    }

    .action-btn {
        padding: 8px 16px;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }

    .action-btn.primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    }

    .action-btn.primary:hover {
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        transform: translateY(-2px);
    }

    .action-btn.disabled {
        background: #e4e6eb;
        color: #65676b;
        cursor: not-allowed;
        box-shadow: none;
    }

    /* Scrollbar styling */
    .chat-list-body::-webkit-scrollbar {
        width: 8px;
    }

    .chat-list-body::-webkit-scrollbar-track {
        background: #f0f2f5;
        border-radius: 4px;
    }

    .chat-list-body::-webkit-scrollbar-thumb {
        background: #bcc0c4;
        border-radius: 4px;
    }

    .chat-list-body::-webkit-scrollbar-thumb:hover {
        background: #95999d;
    }

    /* Tab content */
    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    /* Animation */
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

    .chat-list-item {
        animation: slideIn 0.3s ease-out;
    }
</style>
@endpush

@section('content')
    <!-- Chat List Container -->
    <div class="chat-list-container">
        <div class="chat-list-header">
            <h4>
                <i class="fas fa-comments"></i>
                Chat Khách hàng
            </h4>
            <p>Quản lý các cuộc trò chuyện với khách hàng</p>
        </div>
        <div class="chat-list-body">
            <!-- Tab: Đang xử lý -->
            <div class="tab-content active" data-content="processing">
                @php
                    $processingChats = $chats->where('status', 'processing');
                @endphp

                @if($processingChats->isEmpty())
                    <div class="chat-list-empty">
                        <div class="empty-icon">💬</div>
                        <h5>Chưa có chat đang xử lý</h5>
                        <p>Các cuộc trò chuyện mới sẽ hiển thị ở đây</p>
                    </div>
                @else
                    @foreach($processingChats as $chat)
                        <a href="{{ route('staff.chats.show', $chat->id) }}" class="chat-list-item">
                            <div class="chat-item-avatar">
                                {{ strtoupper(substr($chat->user->name ?? 'U', 0, 1)) }}
                                <div class="avatar-status online"></div>
                            </div>
                            <div class="chat-item-info">
                                <div class="chat-item-header">
                                    <div class="chat-item-name">
                                        {{ $chat->user->name ?? 'Khách vãng lai' }}
                                    </div>
                                    <div class="chat-item-time">
                                        {{ $chat->last_message_at ? \Carbon\Carbon::parse($chat->last_message_at)->diffForHumans() : 'Vừa xong' }}
                                    </div>
                                </div>
                                <div class="chat-item-preview">
                                    Nhấn để xem và trả lời tin nhắn
                                </div>
                                <div class="chat-item-footer">
                                    <span class="status-badge processing">
                                        <i class="fas fa-circle"></i>
                                        Đang xử lý
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                @endif
            </div>

            <!-- Tab: Đã kết thúc -->
            <div class="tab-content" data-content="closed">
                @php
                    $closedChats = $chats->where('status', 'closed');
                @endphp

                @if($closedChats->isEmpty())
                    <div class="chat-list-empty">
                        <div class="empty-icon">✅</div>
                        <h5>Chưa có chat đã kết thúc</h5>
                        <p>Các cuộc trò chuyện đã hoàn thành sẽ hiển thị ở đây</p>
                    </div>
                @else
                    @foreach($closedChats as $chat)
                        <div class="chat-list-item disabled">
                            <div class="chat-item-avatar closed">
                                {{ strtoupper(substr($chat->user->name ?? 'U', 0, 1)) }}
                                <div class="avatar-status offline"></div>
                            </div>
                            <div class="chat-item-info">
                                <div class="chat-item-header">
                                    <div class="chat-item-name">
                                        {{ $chat->user->name ?? 'Khách vãng lai' }}
                                    </div>
                                    <div class="chat-item-time">
                                        {{ $chat->last_message_at ? \Carbon\Carbon::parse($chat->last_message_at)->diffForHumans() : '---' }}
                                    </div>
                                </div>
                                <div class="chat-item-preview">
                                    Cuộc trò chuyện đã kết thúc
                                </div>
                                <div class="chat-item-footer">
                                    <span class="status-badge closed">
                                        <i class="fas fa-check"></i>
                                        Đã kết thúc
                                    </span>
                                    <span class="action-btn disabled">
                                        <i class="fas fa-ban"></i>
                                        Không thể chat
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Handle tab switching
    $('.chat-tab-btn').click(function() {
        const tab = $(this).data('tab');

        // Update active tab button
        $('.chat-tab-btn').removeClass('active');
        $(this).addClass('active');

        // Update active tab content
        $('.tab-content').removeClass('active');
        $(`.tab-content[data-content="${tab}"]`).addClass('active');
    });
});
</script>
@endpush
