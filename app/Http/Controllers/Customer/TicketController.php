<?php

namespace App\Http\Controllers\Customer;

use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Models\TicketMessage;
use App\Models\EmailTemplate;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\GenericMail;

class TicketController extends Controller
{
    // Danh sách ticket của user
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Lấy tickets của user và tickets của nhóm mà user thuộc về
        $query = Ticket::where(function($q) use ($user) {
            $q->where('user_id', $user->id)
              ->orWhereHas('user', function($userQuery) use ($user) {
                  $userQuery->whereHas('groups', function($groupQuery) use ($user) {
                      $groupQuery->whereIn('customer_group_id', $user->groups()->pluck('customer_group_id'));
                  });
              });
        })->with(['messages', 'user', 'assignedStaff']);

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('id', $search)
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $tickets = $query->latest()->paginate(10);

        return view('customer.tickets.index', compact('tickets'));
    }

    // Form tạo ticket
    public function create()
    {
        $categories = Ticket::getCategories();
        $priorities = Ticket::getPriorities();
        
        return view('customer.tickets.create', compact('categories', 'priorities'));
    }

    // Lưu ticket mới
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'category' => 'required|in:' . implode(',', array_keys(Ticket::getCategories())),
            'priority' => 'required|in:' . implode(',', array_keys(Ticket::getPriorities())),
            'description' => 'required|string',
        ], [
            'subject.required' => 'Vui lòng nhập tiêu đề ticket',
            'category.required' => 'Vui lòng chọn danh mục',
            'priority.required' => 'Vui lòng chọn mức độ ưu tiên',
            'description.required' => 'Vui lòng nhập mô tả chi tiết',
        ]);

        $ticket = Ticket::create([
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'category' => $request->category,
            'priority' => $request->priority,
            'description' => $request->description,
            'status' => Ticket::STATUS_NEW,
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'sender_id' => Auth::id(),
            'message' => $request->description,
        ]);

        // Gửi mail thông báo ticket được tạo
        try {
            $template = EmailTemplate::where('code', 'ticket_created')
                ->where('is_active', true)
                ->first();

            if ($template) {
                $ticketLink = route('customer.tickets.show', $ticket->id);
                Mail::to(Auth::user()->email)->queue(new GenericMail(
                    $template,
                    [
                        'user_name' => Auth::user()->name,
                        'ticket_id' => $ticket->id,
                        'ticket_subject' => $ticket->subject,
                        'ticket_link' => $ticketLink,
                        'app_name' => config('app.name'),
                    ]
                ));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send ticket_created email: ' . $e->getMessage());
        }

        return redirect()->route('customer.tickets.index')->with('success', 'Ticket đã được gửi thành công!');
    }

    // Xem chi tiết ticket + hội thoại
    public function show($id)
    {
        $user = Auth::user();
        
        // Lấy ticket của user hoặc ticket của nhóm mà user thuộc về
        $ticket = Ticket::where(function($q) use ($user) {
            $q->where('user_id', $user->id)
              ->orWhereHas('user', function($userQuery) use ($user) {
                  $userQuery->whereHas('groups', function($groupQuery) use ($user) {
                      $groupQuery->whereIn('customer_group_id', $user->groups()->pluck('customer_group_id'));
                  });
              });
        })->with(['messages.sender', 'assignedStaff', 'user'])
          ->findOrFail($id);
          
        return view('customer.tickets.show', compact('ticket'));
    }

    // Gửi phản hồi
    public function reply(Request $request, $id)
    {
        $request->validate(['message' => 'required|string']);

        $ticket = Ticket::where('user_id', Auth::id())->findOrFail($id);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'sender_id' => Auth::id(),
            'message' => $request->message,
        ]);

        // Cập nhật trạng thái nếu đang là "new"
        if ($ticket->status === Ticket::STATUS_NEW) {
            $ticket->update(['status' => Ticket::STATUS_IN_PROGRESS]);
        }

        return back()->with('success', 'Phản hồi đã được gửi.');
    }

    // API: Lấy tin nhắn mới (cho real-time polling)
    public function getMessages($id, Request $request)
    {
        $ticket = Ticket::where('user_id', Auth::id())->findOrFail($id);

        $lastMessageId = $request->query('last_id', 0);

        $messages = $ticket->messages()
            ->where('id', '>', $lastMessageId)
            ->with('sender')
            ->get();

        return response()->json([
            'messages' => $messages,
            'ticket_status' => $ticket->status
        ]);
    }
}