<?php

namespace App\Http\Controllers\Staff;

use Carbon\Carbon;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChatControllers extends Controller
{
    public function index(Request $request)
    {
        $chats = Chat::with('user')
            ->where('staff_id', Auth::id())
            ->orderBy('updated_at', 'desc')
            ->get();

        if ($request->ajax() || $request->wantsJson()) {
            $chatData = $chats->map(function ($chat) {
                $messages = $chat->content ?? [];
                $lastMessage = !empty($messages) ? end($messages) : null;

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

                return [
                    'id' => $chat->id,
                    'user_name' => $chat->user->name ?? 'Khách hàng',
                    'unread_count' => $chat->unread_count ?? 0,
                    'last_message_time' => $lastMessage['created_at'] ?? $chat->updated_at,
                    'last_message_preview' => Str::limit($preview, 35)
                ];
            });

            return response()->json([
                'success' => true,
                'chats' => $chatData
            ]);
        }

        return view('backend.chatstaff.index', compact('chats'));
    }

    public function show($id)
    {
        $chat = Chat::with('user', 'staff')
            ->where('staff_id', Auth::id())
            ->findOrFail($id);

        $messages = $chat->content ?? [];
        $messages = array_map(function ($msg) use ($chat) {
            if (empty($msg['sender_name'])) {
                $msg['sender_name'] = match ($msg['sender_id']) {
                    $chat->user_id => $chat->user->name ?? 'Khách hàng',
                    $chat->staff_id => $chat->staff->name ?? Auth::user()->name,
                    default => User::find($msg['sender_id'])->name ?? 'Người dùng',
                };
            }
            return $msg;
        }, $messages);

        return view('backend.chatstaff.show', compact('chat', 'messages'));
    }


    public function send(Request $request, $id)
    {
        $request->validate([
            'message' => 'nullable|string|max:5000',
            'file' => 'nullable|file|max:10240',
        ]);

        $chat = Chat::with('user', 'staff')
            ->where('staff_id', Auth::id())
            ->findOrFail($id);

        $messages = $chat->content ?? [];
        $newMessage = [
            'sender_id' => Auth::id(),
            'sender_name' => Auth::user()->name,
            'type' => 'text',
            'content' => $request->message,
            'file_path' => null,
            'file_name' => null,
            'created_at' => now()->toDateTimeString(),
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('chat_uploads', 'public');
            $newMessage['type'] = str_starts_with($file->getMimeType(), 'image/') ? 'image' : 'file';
            $newMessage['file_path'] = $path;
            $newMessage['file_name'] = $file->getClientOriginalName();
            $newMessage['content'] = null;
        }

        $messages[] = $newMessage;

        $chat->update([
            'content' => $messages,
            'last_message_at' => now(),
            'updated_at' => now(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $newMessage,
            ]);
        }

        return redirect()->route('staff.chats.show', $chat->id)
            ->with('success', 'Tin nhắn đã được gửi!');
    }


    public function getMessages(Request $request, $id)
    {
        $chat = Chat::with('user', 'staff')
            ->where('staff_id', Auth::id())
            ->findOrFail($id);

        $messages = $chat->content ?? [];

        $lastMessageAt = $request->query('last_message_at');
        if ($lastMessageAt) {
            $messages = array_filter($messages, fn($msg) => $msg['created_at'] > $lastMessageAt);
        }

        $messages = array_map(function ($msg) use ($chat) {
            if (empty($msg['sender_name'])) {
                $msg['sender_name'] = match ($msg['sender_id']) {
                    $chat->user_id => $chat->user->name ?? 'Khách hàng',
                    $chat->staff_id => $chat->staff->name ?? 'Nhân viên',
                    default => User::find($msg['sender_id'])->name ?? 'Người dùng',
                };
            }
            return $msg;
        }, $messages);

        return response()->json([
            'success' => true,
            'messages' => array_values($messages),
        ]);
    }
    public function markAsRead($id)
    {
        try {
            $chat = Chat::findOrFail($id);
            $chat->update(['unread_count' => 0]);

            return response()->json([
                'success' => true,
                'message' => 'Đã đánh dấu đã đọc'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra'
            ], 500);
        }
    }
}
