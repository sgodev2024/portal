<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserNotification;
use Carbon\Carbon;

class NotificationCounterController extends Controller
{
    public function unreadCount(Request $request)
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['count' => 0]);
        }

        $count = UserNotification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    public function recentNotifications()
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['notifications' => []]);
        }

        $notifications = UserNotification::with(['notification'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($userNotification) {
                $notification = $userNotification->notification;
                
                // Xác định route dựa trên role
                $link = '#';
                $userRole = Auth::user()->role;
                
                if ($userRole == 1) {
                    // Admin
                    $link = route('admin.notifications.show', $notification->id);
                } elseif ($userRole == 2) {
                    // Staff
                    $link = route('staff.notifications.show', $notification->id);
                } elseif ($userRole == 3) {
                    // Customer
                    $link = route('customer.notifications.show', $notification->id);
                }
                
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'content' => $notification->content,
                    'is_read' => $userNotification->is_read,
                    'created_at' => Carbon::parse($notification->created_at)->diffForHumans(),
                    'link' => $link
                ];
            });

        return response()->json(['notifications' => $notifications]);
    }

    public function markAllAsRead()
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['success' => false]);
        }

        UserNotification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}