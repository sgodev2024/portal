<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use App\Models\CustomerGroup;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $data = [];

        // Dữ liệu cho ADMIN
        if ($user->role == 1) {
            $data = [
                'total_customers' => User::where('role', 3)->count(),
                'total_staff' => User::where('role', 2)->count(),
                'active_users' => User::where('is_active', 1)->count(),
                
                // Thống kê ticket
                'ticket_stats' => [
                    'total' => Ticket::count(),
                    'new' => Ticket::where('status', Ticket::STATUS_NEW)->count(),
                    'in_progress' => Ticket::where('status', Ticket::STATUS_IN_PROGRESS)->count(),
                    'responded' => Ticket::where('status', Ticket::STATUS_RESPONDED)->count(),
                    'closed' => Ticket::where('status', Ticket::STATUS_CLOSED)->count(),
                    'unassigned' => Ticket::whereNull('assigned_staff_id')->count(),
                ],
                
                'recent_tickets' => Ticket::with(['user', 'assignedStaff', 'messages'])
                    ->latest()
                    ->limit(5)
                    ->get(),
            ];
        }

        // Dữ liệu cho STAFF - Xem tất cả tickets
        if ($user->role == 2) {
            $data = [
                'total_customers' => User::where('role', 3)->count(),
                
                // Thống kê tất cả tickets
                'ticket_stats' => [
                    'total' => Ticket::count(),
                    'new' => Ticket::where('status', Ticket::STATUS_NEW)->count(),
                    'in_progress' => Ticket::where('status', Ticket::STATUS_IN_PROGRESS)->count(),
                    'responded' => Ticket::where('status', Ticket::STATUS_RESPONDED)->count(),
                    'closed' => Ticket::where('status', Ticket::STATUS_CLOSED)->count(),
                    'assigned_to_me' => Ticket::where('assigned_staff_id', $user->id)->count(),
                ],
                
                // Hiển thị tất cả tickets gần đây
                'recent_tickets' => Ticket::with(['user', 'assignedStaff', 'messages'])
                    ->latest()
                    ->limit(5)
                    ->get(),
            ];
        }

        // Dữ liệu cho CUSTOMER - Chỉ xem tickets của mình
        if ($user->role == 3) {
            // Query chỉ tickets của customer này
            $customerTicketsQuery = Ticket::where('user_id', $user->id);

            $data = [
                'my_tickets' => (clone $customerTicketsQuery)
                    ->with(['messages', 'assignedStaff'])
                    ->latest()
                    ->limit(5)
                    ->get(),
                    
                'ticket_stats' => [
                    'total' => (clone $customerTicketsQuery)->count(),
                    'new' => (clone $customerTicketsQuery)->where('status', Ticket::STATUS_NEW)->count(),
                    'in_progress' => (clone $customerTicketsQuery)->where('status', Ticket::STATUS_IN_PROGRESS)->count(),
                    'responded' => (clone $customerTicketsQuery)->where('status', Ticket::STATUS_RESPONDED)->count(),
                    'closed' => (clone $customerTicketsQuery)->where('status', Ticket::STATUS_CLOSED)->count(),
                ],
            ];
        }

        // Trả về view CHUNG cho cả 3 role
        return view('backend.dashboard', $data);
    }
}