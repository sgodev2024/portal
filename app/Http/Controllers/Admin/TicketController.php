<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\TicketRead;
use App\Models\User;
use App\Models\CustomerGroup;
use App\Models\EmailTemplate;
use App\Mail\GenericMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Services\TicketNotificationService;

class TicketController extends Controller
{


    public function index(Request $request)
    {
        $user = Auth::user();
        $status = $request->query('status');
        $search = $request->query('search');
        $assignedTo = $request->query('assigned_to');
        $sort = $request->query('sort', 'created_at');
        $order = $request->query('order', 'desc');

        $query = Ticket::with(['user', 'assignedStaff']);

        // Cả Admin và Nhân viên đều xem tất cả tickets
        // Nhân viên có thể tự chọn (claim) ticket chưa có người phụ trách
        // hoặc xem tickets đã được gán cho họ

        // Filter by status
        if ($status) {
            $query->where('status', $status);
        }

        // Filter by assigned staff (Admin only)
        if ($assignedTo && $user->role == 1) {
            if ($assignedTo === 'unassigned') {
                $query->whereNull('assigned_staff_id');
            } else {
                $query->where('assigned_staff_id', $assignedTo);
            }
        }

        // Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhere('id', $search)
                    ->orWhereHas('user', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $tickets = $query->orderBy($sort, $order)
            ->paginate(10)
            ->withQueryString();

        // Lấy danh sách nhân viên (chỉ admin dùng để gán)
        $staffList = collect();
        if ($user->role == 1) {
            $staffList = User::where('role', 2)
                ->where('is_active', 1)
                ->orderBy('name')
                ->get();
        }

        return view('backend.ticket.index', compact('tickets', 'status', 'search', 'sort', 'order', 'staffList'));
    }

    public function show($id)
    {
        $user = Auth::user();
        $query = Ticket::with(['user', 'messages.sender', 'assignedStaff']);

        // Nhân viên chỉ xem ticket được gán cho họ
        if ($user->role == 2) {
            $query->where('assigned_staff_id', $user->id);
        }

        $ticket = $query->findOrFail($id);

        // Lấy danh sách nhân viên (chỉ admin cần)
        $staffList = collect();
        if ($user->role == 1) {
            $staffList = User::where('role', 2)
                ->where('is_active', 1)
                ->orderBy('name')
                ->get();
        }

        return view('backend.ticket.show', compact('ticket', 'staffList'));
    }

    public function reply(Request $request, $id)
    {
        $request->validate(['message' => 'required|string']);

        $user = Auth::user();
        $query = Ticket::query();

        // Nhân viên chỉ reply ticket được gán cho họ
        if ($user->role == 2) {
            $query->where('assigned_staff_id', $user->id);
        }

        $ticket = $query->findOrFail($id);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'sender_id' => Auth::id(),
            'message' => $request->message,
        ]);
        
        // Sau khi nhân viên/Admin trả lời, cập nhật last_staff_response_at và chuyển sang đã phản hồi
        if ($user->role == 1 || $user->role == 2) {
            if ($ticket->status !== Ticket::STATUS_CLOSED) {
                $ticket->update([
                    'status' => Ticket::STATUS_RESPONDED,
                    'last_staff_response_at' => now()
                ]);
            }
        }

        // Gửi thông báo cho khách hàng
        TicketNotificationService::notifyStaffReply($ticket, $user);

        // Gửi mail thông báo có reply mới
        try {
            $template = EmailTemplate::where('code', 'ticket_replied')
                ->where('is_active', true)
                ->first();

            if ($template && $ticket->user) {
                $ticketLink = $ticket->user->role == 3 
                    ? route('customer.tickets.show', $ticket->id)
                    : route('admin.tickets.show', $ticket->id);
                
                Mail::to($ticket->user->email)->queue(new GenericMail(
                    $template,
                    [
                        'user_name' => $ticket->user->name,
                        'ticket_id' => $ticket->id,
                        'ticket_subject' => $ticket->subject,
                        'sender_name' => Auth::user()->name,
                        'ticket_link' => $ticketLink,
                        'app_name' => config('app.name'),
                    ]
                ));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send ticket_replied email: ' . $e->getMessage());
        }

        return back()->with('success', 'Phản hồi đã được gửi!');
    }

    public function close($id)
    {
        $user = Auth::user();
        $query = Ticket::query();

        // Nhân viên chỉ đóng ticket được gán cho họ
        if ($user->role == 2) {
            $query->where('assigned_staff_id', $user->id);
        }

        $ticket = $query->findOrFail($id);
        $ticket->update(['status' => Ticket::STATUS_CLOSED]);

        // Gửi thông báo cho khách hàng
        TicketNotificationService::notifyTicketClosed($ticket);

        // Gửi mail thông báo ticket được đóng
        try {
            $template = EmailTemplate::where('code', 'ticket_closed')
                ->where('is_active', true)
                ->first();

            if ($template && $ticket->user) {
                $ticketLink = $ticket->user->role == 3 
                    ? route('customer.tickets.show', $ticket->id)
                    : route('admin.tickets.show', $ticket->id);
                
                Mail::to($ticket->user->email)->queue(new GenericMail(
                    $template,
                    [
                        'user_name' => $ticket->user->name,
                        'ticket_id' => $ticket->id,
                        'ticket_subject' => $ticket->subject,
                        'ticket_link' => $ticketLink,
                        'app_name' => config('app.name'),
                    ]
                ));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send ticket_closed email: ' . $e->getMessage());
        }

        return back()->with('success', 'Ticket đã được đóng.');
    }


    public function assign(Request $request, $id)
    {
        if (Auth::user()->role != 1) {
            abort(403, 'Chỉ Admin mới có quyền gán ticket.');
        }

        $request->validate([
            'assigned_to' => 'required|exists:users,id'
        ]);

        $ticket = Ticket::findOrFail($id);
        $staff = User::where('id', $request->assigned_to)
            ->where('role', 2)
            ->where('is_active', 1)
            ->firstOrFail();

        $ticket->update([
            'assigned_staff_id' => $staff->id,
            'assignment_type' => Ticket::ASSIGNMENT_INDIVIDUAL,
            'status' => in_array($ticket->status, [Ticket::STATUS_NEW]) ? Ticket::STATUS_IN_PROGRESS : $ticket->status
        ]);

        // Gửi thông báo cho khách hàng và nhân viên
        TicketNotificationService::notifyTicketAssigned($ticket, $staff);

        // Gửi mail thông báo ticket được gán
        try {
            $template = EmailTemplate::where('code', 'ticket_assigned')
                ->where('is_active', true)
                ->first();

            if ($template && $ticket->user) {
                $ticketLink = $ticket->user->role == 3 
                    ? route('customer.tickets.show', $ticket->id)
                    : route('admin.tickets.show', $ticket->id);
                
                Mail::to($ticket->user->email)->queue(new GenericMail(
                    $template,
                    [
                        'user_name' => $ticket->user->name,
                        'ticket_id' => $ticket->id,
                        'ticket_subject' => $ticket->subject,
                        'staff_name' => $staff->name,
                        'ticket_link' => $ticketLink,
                        'app_name' => config('app.name'),
                    ]
                ));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send ticket_assigned email: ' . $e->getMessage());
        }

        return back()->with('success', 'Đã gán ticket cho nhân viên ' . $staff->name);
    }

    public function claim($id)
    {
        $user = Auth::user();
        if ($user->role != 2) {
            abort(403, 'Chỉ nhân viên mới có thể nhận ticket.');
        }

        $ticket = Ticket::findOrFail($id);

        if (!is_null($ticket->assigned_staff_id)) {
            return back()->with('error', 'Ticket đã có người phụ trách.');
        }

        $ticket->update([
            'assigned_staff_id' => $user->id,
            'assignment_type' => Ticket::ASSIGNMENT_INDIVIDUAL,
            'status' => in_array($ticket->status, [Ticket::STATUS_NEW]) ? Ticket::STATUS_IN_PROGRESS : $ticket->status
        ]);

        // Gửi thông báo cho khách hàng
        TicketNotificationService::notifyTicketClaimed($ticket, $user);

        return back()->with('success', 'Bạn đã nhận xử lý ticket #' . $ticket->id);
    }

    // API lấy messages mới (cho chat real-time polling)
    public function getMessages($id, Request $request)
    {
        $user = Auth::user();
        $query = Ticket::query();

        if ($user->role == 2) {
            $query->where('assigned_staff_id', $user->id);
        }

        $ticket = $query->findOrFail($id);

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

    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();
        
        // Lấy tất cả tickets mà user có quyền xem
        $query = Ticket::query();
        
        if ($user->role == 2) { // Staff
            $query->where('assigned_staff_id', $user->id);
        }
        
        $tickets = $query->get();
        
        foreach ($tickets as $ticket) {
            TicketRead::updateOrCreate(
                [
                    'ticket_id' => $ticket->id,
                    'user_id' => $user->id,
                ],
                [
                    'read_at' => now(),
                ]
            );
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Tất cả tickets đã được đánh dấu là đã đọc.'
        ]);
    }
}
