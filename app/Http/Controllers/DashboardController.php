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
                    'closed' => Ticket::where('status', Ticket::STATUS_CLOSED)->count(),
                    'unassigned' => Ticket::whereNull('assigned_staff_id')->count(),
                ],
                
                'recent_tickets' => Ticket::with(['user', 'assignedStaff', 'messages'])
                    ->latest()
                    ->limit(5)
                    ->get(),
            ];
        }

        // Dữ liệu cho STAFF
        if ($user->role == 2) {
            // Query tickets của staff (bao gồm cả tickets của nhóm)
            $staffTicketsQuery = Ticket::where(function($q) use ($user) {
                $q->where('assigned_staff_id', $user->id)
                  ->orWhereHas('user.groups', function($groupQuery) use ($user) {
                      $groupQuery->whereHas('staff', function($staffQuery) use ($user) {
                          $staffQuery->where('staff_id', $user->id);
                      });
                  });
            });

            $data = [
                'my_customers' => User::where('role', 3)
                    ->whereHas('groups.staff', function($q) use ($user) {
                        $q->where('staff_id', $user->id);
                    })
                    ->count(),
                
                // Thống kê ticket của nhân viên (cả assigned và nhóm)
                'ticket_stats' => [
                    'total' => (clone $staffTicketsQuery)->count(),
                    'new' => (clone $staffTicketsQuery)->where('status', Ticket::STATUS_NEW)->count(),
                    'in_progress' => (clone $staffTicketsQuery)->where('status', Ticket::STATUS_IN_PROGRESS)->count(),
                    'closed' => (clone $staffTicketsQuery)->where('status', Ticket::STATUS_CLOSED)->count(),
                    'assigned_to_me' => Ticket::where('assigned_staff_id', $user->id)->count(),
                ],
                
                'recent_tickets' => (clone $staffTicketsQuery)
                    ->with(['user', 'assignedStaff', 'messages'])
                    ->latest()
                    ->limit(5)
                    ->get(),
            ];
        }

        // Dữ liệu cho CUSTOMER
        if ($user->role == 3) {
            // Query tickets của customer (cả cá nhân và nhóm)
            $customerTicketsQuery = Ticket::where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhereHas('user', function($userQuery) use ($user) {
                      $userQuery->whereHas('groups', function($groupQuery) use ($user) {
                          $groupQuery->whereIn('customer_group_id', $user->groups()->pluck('customer_group_id'));
                      });
                  });
            });

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
                    'closed' => (clone $customerTicketsQuery)->where('status', Ticket::STATUS_CLOSED)->count(),
                ],
            ];
        }

        // Trả về view CHUNG cho cả 3 role
        return view('backend.dashboard', $data);
    }
}