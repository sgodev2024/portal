<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StaffNotificationController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $notifications = Notification::whereHas('userNotifications', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->with(['creator', 'userNotifications' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        if (request()->ajax()) {
            return view('backend.notificationsstaff.partials.table', compact('notifications'));
        }

        return view('backend.notificationsstaff.index', compact('notifications'));
    }

    public function data()
    {
        $userId = Auth::id();
        $draw = (int) request('draw');
        $start = (int) request('start', 0);
        $length = (int) request('length', 10);
        $search = request('search.value');

        $baseQuery = Notification::whereHas('userNotifications', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->with(['creator', 'userNotifications' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }]);

        $recordsTotal = (clone $baseQuery)->count();

        if (!empty($search)) {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = (clone $baseQuery)->count();

        $items = $baseQuery->orderBy('created_at', 'desc')
            ->skip($start)
            ->take($length)
            ->get();

        $data = $items->map(function ($item, $index) use ($start) {
            $userNotify = $item->userNotifications->first();
            $isRead = $userNotify && $userNotify->is_read;
            return [
                'index' => $start + $index + 1,
                'title' => $item->title,
                'content' => Str::limit($item->content, 80),
                'creator' => optional($item->creator)->name ?? 'Hệ thống',
                'created_at' => $item->created_at?->format('d/m/Y H:i'),
                'status' => $isRead ? '<span class="badge bg-success">Đã đọc</span>' : '<span class="badge bg-warning text-dark">Chưa đọc</span>',
                'actions' => '<a href="'.route('staff.notifications.show', $item->id).'" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>',
                'rowClass' => $isRead ? '' : 'fw-bold',
            ];
        });

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }


    public function show($id)
    {
        $userId = Auth::id();
        $notification = Notification::with('creator')
            ->whereHas('userNotifications', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->findOrFail($id);
        UserNotification::where('user_id', $userId)
            ->where('notification_id', $id)
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('backend.notificationsstaff.show', compact('notification'));
    }
}
