<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserNotification;
use App\Models\EmailTemplate;
use App\Mail\GenericMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AdminNotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('backend.notificationsadmin.index', compact('notifications'));
    }

    public function create()
    {
        return view('backend.notificationsadmin.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'target_role' => 'required|in:staff,user,all',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:5120',
        ]);

        try {
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')->store('attachments', 'public');
            }
            $notification = Notification::create([
                'title' => $request->title,
                'content' => $request->content,
                'attachment_path' => $attachmentPath,
                'target_role' => $request->target_role,
                'created_by' => auth()->id(),
            ]);
            $roleMap = [
                'staff' => 2,
                'user' => 3,
            ];
            $usersQuery = User::query();
            if ($request->target_role !== 'all') {
                $usersQuery->where('role', $roleMap[$request->target_role]);
            }
            $users = $usersQuery->get();

            if ($users->isEmpty()) {
                $notification->delete();
                if ($attachmentPath) {
                    Storage::disk('public')->delete($attachmentPath);
                }
                return back()->with('error', 'Không tìm thấy người nhận phù hợp.');
            }
            $template = EmailTemplate::where('code', 'notification')
                ->where('is_active', true)
                ->first();

            if (!$template) {
                return back()->with('error', 'Không tìm thấy mẫu email "notification".');
            }

            $successCount = 0;
            $failCount = 0;

            foreach ($users as $user) {
                UserNotification::create([
                    'user_id' => $user->id,
                    'notification_id' => $notification->id,
                ]);
                $notificationLink = match ($user->role) {
                    1 => route('admin.dashboard'),
                    2 => route('staff.dashboard'),
                    3 => route('customer.dashboard'),
                    default => '#',
                };

                $emailData = [
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'notification_title' => $notification->title,
                    'notification_content' => $notification->content ?? 'Bạn có thông báo mới.',
                    'notification_link' => $notificationLink,
                    'app_name' => config('app.name'),
                ];

                if ($attachmentPath) {
                    $emailData['attachment_path'] = storage_path('app/public/' . $attachmentPath);
                    $emailData['attachment_name'] = basename($attachmentPath);
                }
                try {
                    Mail::to($user->email)->queue(new GenericMail($template, $emailData));
                    $successCount++;
                } catch (\Exception $e) {
                    Log::error("Gửi mail thất bại cho {$user->email}: " . $e->getMessage());
                    $failCount++;
                }
            }

            $notification->update([
                'is_sent' => true,
                'sent_at' => now(),
            ]);
            $totalUsers = $users->count();
            if ($failCount > 0 && $successCount > 0) {
                return redirect()->route('admin.notifications.index')
                    ->with('warning', "Đã gửi thành công {$successCount}/{$totalUsers} email. {$failCount} email thất bại.");
            }
            if ($failCount > 0 && $successCount == 0) {
                return redirect()->route('admin.notifications.index')
                    ->with('error', "Gửi mail thất bại cho tất cả {$totalUsers} người nhận.");
            }

            return redirect()->route('admin.notifications.index')
                ->with('success', "Gửi thông báo thành công tới {$successCount} người!");
        } catch (\Exception $e) {
            Log::error("Lỗi tạo thông báo: " . $e->getMessage());
            if ($attachmentPath) {
                Storage::disk('public')->delete($attachmentPath);
            }
            return back()->with('error', 'Có lỗi xảy ra khi tạo thông báo.')->withInput();
        }
    }


    public function show($id)
    {
        $notification = Notification::with('userNotifications.user')->findOrFail($id);
        return view('backend.notificationsadmin.show', compact('notification'));
    }

    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);

        // Xóa file đính kèm nếu có
        if ($notification->attachment_path) {
            Storage::disk('public')->delete($notification->attachment_path);
        }

        $notification->delete();

        return back()->with('success', 'Xóa thông báo thành công!');
    }
}
