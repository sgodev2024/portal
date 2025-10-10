<?php

namespace App\Http\Controllers\Staff;

use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Chat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChatControllers extends Controller
{
    public function index()
    {
        $chats = Chat::with('user')
            ->where('staff_id', Auth::id())
            ->orderByDesc('updated_at')
            ->get();

        return view('backend.chatstaff.index', compact('chats'));
    }


    public function show($id)
    {
        $chat = Chat::with('user')
            ->where('staff_id', Auth::id())
            ->findOrFail($id);
        $messages = $chat->content ?? [];

        return view('backend.chatstaff.show', compact('chat', 'messages'));
    }

    public function send(Request $request, $id)
    {
        $request->validate([
            'message' => 'nullable|string|max:5000',
            'file' => 'nullable|file|max:10240',
        ]);

        $chat = Chat::where('staff_id', Auth::id())->findOrFail($id);

        $messages = $chat->content ?? [];
        $newMessage = [
            'sender_id' => Auth::id(),
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

        // Cập nhật chat
        $chat->update([
            'content' => $messages,
            'last_message_at' => now(),
        ]);
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $newMessage,
            ]);
        }

        // Redirect nếu không phải AJAX
        return redirect()->route('staff.chats.show', $chat->id)
            ->with('success', 'Tin nhắn đã được gửi!');
    }
    // Lấy tin nhắn mới từ thời điểm client gửi
    public function getMessages(Request $request, $id)
    {
        $chat = Chat::where('staff_id', Auth::id())->findOrFail($id);
        $messages = $chat->content ?? [];

        $lastMessageAt = $request->query('last_message_at');
        if ($lastMessageAt) {
            $messages = array_filter($messages, fn($msg) => $msg['created_at'] > $lastMessageAt);
        }

        return response()->json([
            'success' => true,
            'messages' => array_values($messages),
        ]);
    }
}
