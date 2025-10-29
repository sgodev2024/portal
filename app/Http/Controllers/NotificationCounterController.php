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

    public function markAsRead($notificationId)
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // Tìm UserNotification của user hiện tại với notification_id
        $userNotification = UserNotification::where('user_id', $userId)
            ->where('notification_id', $notificationId)
            ->first();

        if (!$userNotification) {
            return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
        }

        // Đánh dấu là đã đọc
        $userNotification->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    public function deleteNotification($notificationId)
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // Tìm và xóa UserNotification của user hiện tại
        $userNotification = UserNotification::where('user_id', $userId)
            ->where('notification_id', $notificationId)
            ->first();

        if (!$userNotification) {
            return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
        }

        // Xóa thông báo
        $userNotification->delete();

        return response()->json(['success' => true, 'message' => 'Notification deleted successfully']);
    }
}