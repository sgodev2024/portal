<?php

namespace App\Http\Controllers\Admin;

use App\Models\Chat;
use App\Models\User;
use App\Models\ChatLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $staffs = User::where('role', 2)->where('is_active', true)->get();

        $baseQuery = Chat::with(['user', 'staff'])
            ->when(Auth::user()->role == 2, fn($q) => $q->where('staff_id', Auth::id()))
            ->when(
                $search,
                fn($q) =>
                $q->whereHas('user', fn($sub) => $sub->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('staff', fn($sub) => $sub->where('name', 'like', "%{$search}%"))
                    ->orWhere('id', 'like', "%{$search}%")
            );

        $pendingChats = (clone $baseQuery)
            ->where('status', 'pending')
            ->whereNotNull('last_message_at')
            ->orderByDesc('last_message_at')
            ->get();
        $processingChats = (clone $baseQuery)->where('status', 'processing')->orderByDesc('last_message_at')->get();

        return view('backend.chatadmin.index', compact('pendingChats', 'processingChats', 'search', 'staffs'));
    }

    public function assign(Request $request, $id)
    {
        $chat = Chat::findOrFail($id);

        if ($chat->status !== 'pending') {
            return back()->with('error', 'Chat đã được xử lý hoặc kết thúc!');
        }

        $request->validate(['staff_id' => 'required|exists:users,id']);

        $staffId = $request->input('staff_id');

        $chat->update([
            'staff_id' => $staffId,
            'status' => 'processing',
        ]);

        ChatLog::create([
            'chat_id' => $chat->id,
            'changed_by' => Auth::id(),
            'action' => 'assigned_to_staff',
            'note' => 'Admin phân công chat cho nhân viên ID: ' . $staffId,
        ]);

        return redirect()->route('chat.index')->with('success', 'Chat đã được phân công cho nhân viên!');
    }

    public function getMessages(Request $request, $id)
    {
        $chat = Chat::findOrFail($id);
        $lastMessageAt = $request->query('last_message_at');
        $messages = $chat->content ?? [];
        if ($lastMessageAt) {
            $messages = array_filter($messages, function ($msg) use ($lastMessageAt) {
                return isset($msg['created_at']) && $msg['created_at'] > $lastMessageAt;
            });
        }
        foreach ($messages as &$msg) {
            if (empty($msg['sender_name']) && !empty($msg['sender_id'])) {
                $user = User::find($msg['sender_id']);
                $msg['sender_name'] = $user?->name ?? 'Người dùng';
            }
        }

        return response()->json([
            'success' => true,
            'messages' => array_values($messages),
        ]);
    }
    public function getListUpdates(Request $request)
    {
        $tab = $request->get('tab', 'pending');

        if ($tab === 'pending') {
            $chats = Chat::where('status', 'pending')
                ->with('user')
                ->whereNotNull('last_message_at')
                ->latest('last_message_at')
                ->get();
        } else {
            $chats = Chat::where('status', 'processing')
                ->with(['user', 'staff'])
                ->latest('last_message_at')
                ->get();
        }

        return response()->json([
            'success' => true,
            'chats' => $chats->map(function ($chat) use ($tab) {
                return [
                    'id' => $chat->id,
                    'last_message_at' => $chat->last_message_at,
                    'has_new_message' => $chat->last_message_at ? Carbon::parse($chat->last_message_at)->gt(now()->subSeconds(10)) : false,
                    'user_name' => optional($chat->user)->name ?? 'Khách hàng',
                    'staff_name' => optional($chat->staff)->name,
                    'status' => $tab, // 'pending' or 'processing'
                ];
            })
        ]);
    }
    public function sendMessage(Request $request, $id)
    {
        $request->validate([
            'message' => 'nullable|string',
            'file' => 'nullable|file|max:10240',
        ]);

        $chat = Chat::findOrFail($id);
        $messages = $chat->content ?? [];

        $user = Auth::user();
        $roleLabel = match ($user->role) {
            1 => 'admin',
            2 => 'staff',
            3 => 'customer',
            default => 'user',
        };

        $newMessage = [
            'sender_id'   => $user->id,
            'sender_name' => $user->name,
            'sender_role' => $roleLabel,
            'type'        => 'text',
            'content'     => $request->message, // giữ lại làm caption khi có file
            'file_path'   => null,
            'file_name'   => null,
            'created_at'  => now()->toDateTimeString(),
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('chat_uploads', 'public');
            $newMessage['type'] = str_starts_with($file->getMimeType(), 'image/') ? 'image' : 'file';
            $newMessage['file_path'] = $path;
            $newMessage['file_name'] = $file->getClientOriginalName();
            // Không xóa content để hiển thị ảnh + chú thích
        }

        $messages[] = $newMessage;

        $chat->update([
            'content' => $messages,
            'last_message_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã gửi tin nhắn',
            'data' => $newMessage,
        ]);
    }
}
