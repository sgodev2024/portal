<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatCustomerController extends Controller
{
    /**
     * Hiển thị form chat (popup)
     */
    public function index()
    {
        // Tìm hoặc tạo phòng chat cho user hiện tại
        $chat = Chat::firstOrCreate(
            ['user_id' => Auth::id()],
            [
                'status' => 'pending',
                'content' => [],
                // Không set last_message_at khi chỉ mở khung chat
                'last_message_at' => null,
            ]
        );

        return view('customer.chat.index', compact('chat'));
    }

    /**
     * Lấy danh sách tin nhắn của user hiện tại
     */
    public function getMessages(Request $request)
    {
        $customerId = Auth::id();
        $chat = Chat::where('user_id', $customerId)->firstOrFail();
        $messages = $chat->content ?? [];

        // Bổ sung phòng trường hợp tin cũ chưa có sender_name
        foreach ($messages as &$msg) {
            if (empty($msg['sender_name']) && !empty($msg['sender_id'])) {
                $msg['sender_name'] = optional(\App\Models\User::find($msg['sender_id']))->name ?? 'Ẩn danh';
            }
            // Hiển thị phía khách hàng: mọi tin nhắn không phải của khách hàng đều gắn nhãn "Nhân viên"
            if (!empty($msg['sender_id']) && $msg['sender_id'] != $customerId) {
                $msg['sender_name'] = 'Nhân viên';
            }
        }

        return response()->json([
            'success' => true,
            'messages' => $messages
        ]);
    }

    /**
     * Gửi tin nhắn từ phía khách hàng
     */
    public function send(Request $request)
    {
        $request->validate([
            'message' => 'nullable|string',
            'file' => 'nullable|file|max:10240',
        ]);

        $chat = Chat::where('user_id', Auth::id())->firstOrFail();
        $messages = $chat->content ?? [];

        $user = Auth::user();

        // Gán tên và vai trò cho tin nhắn
        $roleLabel = match ($user->role) {
            1 => 'admin',
            2 => 'staff',
            3 => 'customer',
            default => 'user',
        };

        $newMessage = [
            'sender_id'   => $user->id,
            'sender_name' => $user->name ?? 'Người dùng',
            'sender_role' => $roleLabel,
            'type'        => 'text',
            'content'     => $request->message,
            'file_path'   => null,
            'file_name'   => null,
            'created_at'  => now()->toDateTimeString(),
        ];

        // Nếu gửi file
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('chat_uploads', 'public');

            $newMessage['type'] = str_starts_with($file->getMimeType(), 'image/')
                ? 'image' : 'file';
            $newMessage['file_path'] = $path;
            $newMessage['file_name'] = $file->getClientOriginalName();
            $newMessage['content'] = null;
        }

        // Lưu tin nhắn mới
        $messages[] = $newMessage;
        $chat->update([
            'content' => $messages,
            'last_message_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => $newMessage,
        ]);
    }
}
