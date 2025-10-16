<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserNotification;

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
}


