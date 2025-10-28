<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketRead;
use Illuminate\Support\Facades\Auth;

class TicketStatsController extends Controller
{
    public function getStats()
    {
        $userId = Auth::id();
        $userRole = Auth::user()->role;
        
        if (in_array($userRole, [1, 2])) {
            return $this->getAdminStaffStats($userId, $userRole);
        } elseif ($userRole == 3) {
            return $this->getCustomerStats($userId);
        }
        
        return response()->json([
            'total' => 0,
            'open' => 0,
            'pending' => 0,
            'closed' => 0,
            'unread' => 0
        ]);
    }
    
    private function getAdminStaffStats($userId, $userRole)
    {
        $ticketsQuery = Ticket::query();
        
        if ($userRole == 2) {
            // Staff chỉ thấy tickets được assign hoặc của nhóm mình
            $ticketsQuery->where(function($q) use ($userId) {
                $q->where('assigned_to', $userId)
                  ->orWhereHas('customer.groups.staffs', function($query) use ($userId) {
                      $query->where('user_id', $userId);
                  });
            });
        }
        
        $tickets = $ticketsQuery->with('messages')->get();
        
        $stats = [
            'total' => $tickets->count(),
            'open' => $tickets->where('status', 'open')->count(),
            'pending' => $tickets->where('status', 'pending')->count(),
            'closed' => $tickets->where('status', 'closed')->count(),
            'unread' => 0
        ];
        
        // Đếm tickets chưa đọc
        foreach ($tickets as $ticket) {
            $lastRead = TicketRead::where('ticket_id', $ticket->id)
                ->where('user_id', $userId)
                ->first();
            
            $lastMessage = $ticket->messages()->latest()->first();
            
            if ($lastMessage) {
                if (!$lastRead || !$lastRead->read_at || $lastMessage->created_at > $lastRead->read_at) {
                    $stats['unread']++;
                }
            }
        }
        
        return response()->json($stats);
    }
    
    private function getCustomerStats($userId)
    {
        $tickets = Ticket::where('customer_id', $userId)->with('messages')->get();
        
        $stats = [
            'total' => $tickets->count(),
            'open' => $tickets->where('status', 'open')->count(),
            'pending' => $tickets->where('status', 'pending')->count(),
            'closed' => $tickets->where('status', 'closed')->count(),
            'unread_replies' => 0
        ];
        
        // Đếm tickets có reply mới chưa đọc
        foreach ($tickets as $ticket) {
            $lastRead = TicketRead::where('ticket_id', $ticket->id)
                ->where('user_id', $userId)
                ->first();
            
            $lastMessage = $ticket->messages()
                ->where('user_id', '!=', $userId)
                ->latest()
                ->first();
            
            if ($lastMessage) {
                if (!$lastRead || !$lastRead->read_at || $lastMessage->created_at > $lastRead->read_at) {
                    $stats['unread_replies']++;
                }
            }
        }
        
        return response()->json($stats);
    }
}