<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
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
                    'open' => Ticket::where('status', 'open')->count(),
                    'in_progress' => Ticket::where('status', 'in_progress')->count(),
                    'closed' => Ticket::where('status', 'closed')->count(),
                    'unassigned' => Ticket::whereNull('assigned_to')->count(),
                ],
                
                'recent_tickets' => Ticket::with(['user', 'assignedStaff', 'messages'])
                    ->latest()
                    ->limit(5)
                    ->get(),
            ];
        }

        // Dữ liệu cho STAFF
        if ($user->role == 2) {
            $data = [
                'my_customers' => User::where('role', 3)->count(),
                
                // Thống kê ticket của nhân viên
                'ticket_stats' => [
                    'total' => Ticket::where('assigned_to', $user->id)->count(),
                    'open' => Ticket::where('assigned_to', $user->id)
                        ->where('status', 'open')->count(),
                    'in_progress' => Ticket::where('assigned_to', $user->id)
                        ->where('status', 'in_progress')->count(),
                    'closed' => Ticket::where('assigned_to', $user->id)
                        ->where('status', 'closed')->count(),
                ],
                
                'recent_tickets' => Ticket::where('assigned_to', $user->id)
                    ->with(['user', 'messages'])
                    ->latest()
                    ->limit(5)
                    ->get(),
            ];
        }

        // Dữ liệu cho CUSTOMER
        if ($user->role == 3) {
            $data = [
                'my_tickets' => Ticket::where('user_id', $user->id)
                    ->with('messages')
                    ->latest()
                    ->limit(5)
                    ->get(),
                    
                'ticket_stats' => [
                    'total' => Ticket::where('user_id', $user->id)->count(),
                    'open' => Ticket::where('user_id', $user->id)
                        ->where('status', 'open')->count(),
                    'in_progress' => Ticket::where('user_id', $user->id)
                        ->where('status', 'in_progress')->count(),
                    'closed' => Ticket::where('user_id', $user->id)
                        ->where('status', 'closed')->count(),
                ],
            ];
        }

        // Trả về view CHUNG cho cả 3 role
        return view('backend.dashboard', $data);
    }
}