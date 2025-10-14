<?php
namespace App\Http\Controllers\Customer;

use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Models\TicketMessage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    // Danh sách ticket của user
    public function index()
    {
        $tickets = Ticket::where('user_id', Auth::id())->latest()->get();
        return view('customer.tickets.index', compact('tickets'));
    }

    // Form tạo ticket
    public function create()
    {
        return view('customer.tickets.create');
    }

    // Lưu ticket mới
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $ticket = Ticket::create([
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'description' => $request->description,
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'sender_id' => Auth::id(),
            'message' => $request->description,
        ]);

        return redirect()->route('customer.tickets.index')->with('success', 'Ticket đã được gửi!');
    }

    // Xem chi tiết ticket + hội thoại
    public function show($id)
    {
        $ticket = Ticket::where('user_id', Auth::id())->with('messages.sender')->findOrFail($id);
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

        $ticket->update(['status' => 'in_progress']);

        return back()->with('success', 'Phản hồi đã được gửi.');
    }
}
