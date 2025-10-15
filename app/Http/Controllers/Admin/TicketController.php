<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $search = $request->query('search');
        $sort = $request->query('sort', 'created_at');
        $order = $request->query('order', 'desc');

        $query = Ticket::with('user');

        if ($status) {
            $query->where('status', $status);
        }

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

        return view('backend.ticket.index', compact('tickets', 'status', 'search', 'sort', 'order'));
    }

    public function show($id)
    {
        $ticket = Ticket::with(['user', 'messages.sender'])->findOrFail($id);
        return view('backend.ticket.show', compact('ticket'));
    }

    public function reply(Request $request, $id)
    {
        $request->validate(['message' => 'required|string']);

        $ticket = Ticket::findOrFail($id);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'sender_id' => Auth::id(),
            'message' => $request->message,
        ]);

        $ticket->update(['status' => 'in_progress']);

        return back()->with('success', 'Phản hồi đã được gửi!');
    }

    public function close($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->update(['status' => 'closed']);

        return back()->with('success', 'Ticket đã được đóng.');
    }
}
