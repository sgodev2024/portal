<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use App\Models\CustomerGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $query = Ticket::with(['user', 'assignedStaff', 'user.groups']);

        // PHÂN QUYỀN: Nhân viên chỉ xem ticket được gán
        if ($user->role == 2) { // role 2 = staff
            $query->where(function($q) use ($user) {
                $q->where('assigned_staff_id', $user->id)
                  ->orWhereHas('user.groups', function($groupQuery) use ($user) {
                      $groupQuery->whereHas('staff', function($staffQuery) use ($user) {
                          $staffQuery->where('staff_id', $user->id);
                      });
                  });
            });
        }
        // Admin (role 1) xem tất cả - không cần điều kiện

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
        $query = Ticket::with(['user', 'messages.sender', 'assignedStaff', 'user.groups']);

        // Nhân viên chỉ xem ticket của mình
        if ($user->role == 2) {
            $query->where(function($q) use ($user) {
                $q->where('assigned_staff_id', $user->id)
                  ->orWhereHas('user.groups', function($groupQuery) use ($user) {
                      $groupQuery->whereHas('staff', function($staffQuery) use ($user) {
                          $staffQuery->where('staff_id', $user->id);
                      });
                  });
            });
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

        // Nhân viên chỉ reply ticket của mình
        if ($user->role == 2) {
            $query->where(function($q) use ($user) {
                $q->where('assigned_staff_id', $user->id)
                  ->orWhereHas('user.groups', function($groupQuery) use ($user) {
                      $groupQuery->whereHas('staff', function($staffQuery) use ($user) {
                          $staffQuery->where('staff_id', $user->id);
                      });
                  });
            });
        }

        $ticket = $query->findOrFail($id);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'sender_id' => Auth::id(),
            'message' => $request->message,
        ]);
        if ($ticket->status === Ticket::STATUS_NEW) {
            $ticket->update(['status' => Ticket::STATUS_IN_PROGRESS]);
        }
        return back()->with('success', 'Phản hồi đã được gửi!');
    }

    public function close($id)
    {
        $user = Auth::user();
        $query = Ticket::query();

        // Nhân viên chỉ đóng ticket của mình
        if ($user->role == 2) {
            $query->where(function($q) use ($user) {
                $q->where('assigned_staff_id', $user->id)
                  ->orWhereHas('user.groups', function($groupQuery) use ($user) {
                      $groupQuery->whereHas('staff', function($staffQuery) use ($user) {
                          $staffQuery->where('staff_id', $user->id);
                      });
                  });
            });
        }

        $ticket = $query->findOrFail($id);
        $ticket->update(['status' => Ticket::STATUS_CLOSED]);

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
            'status' => $ticket->status === 'new' ? Ticket::STATUS_IN_PROGRESS : $ticket->status
        ]);

        return back()->with('success', 'Đã gán ticket cho nhân viên ' . $staff->name);
    }

    // API lấy messages mới (cho chat real-time polling)
    public function getMessages($id, Request $request)
    {
        $user = Auth::user();
        $query = Ticket::query();

        if ($user->role == 2) {
            $query->where(function($q) use ($user) {
                $q->where('assigned_staff_id', $user->id)
                  ->orWhereHas('user.groups', function($groupQuery) use ($user) {
                      $groupQuery->whereHas('staff', function($staffQuery) use ($user) {
                          $staffQuery->where('staff_id', $user->id);
                      });
                  });
            });
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
}
