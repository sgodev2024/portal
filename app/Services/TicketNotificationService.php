<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\UserNotification;
use App\Models\User;
use App\Models\Ticket;

class TicketNotificationService
{
    /**
     * Tạo thông báo khi có ticket mới (gửi cho Admin và Staff)
     */
    public static function notifyNewTicket(Ticket $ticket)
    {
        $notification = Notification::create([
            'title' => "Ticket mới #" . $ticket->id . ": " . $ticket->subject,
            'content' => "Khách hàng " . $ticket->user->name . " vừa tạo ticket mới.",
            'target_role' => 1, // Admin
            'created_by' => $ticket->user_id,
            'is_sent' => true,
            'sent_at' => now(),
        ]);

        // Gửi cho tất cả Admin (role 1) và Staff (role 2)
        $recipients = User::whereIn('role', [1, 2])
            ->where('is_active', 1)
            ->get();

        foreach ($recipients as $user) {
            UserNotification::create([
                'user_id' => $user->id,
                'notification_id' => $notification->id,
                'is_read' => false,
            ]);
        }
    }

    /**
     * Thông báo khi khách hàng reply (gửi cho Admin, Staff và người được gán)
     */
    public static function notifyCustomerReply(Ticket $ticket)
    {
        $notification = Notification::create([
            'title' => "Khách phản hồi Ticket #" . $ticket->id,
            'content' => "Khách hàng " . $ticket->user->name . " vừa phản hồi ticket.",
            'target_role' => 1, // Admin
            'created_by' => $ticket->user_id,
            'is_sent' => true,
            'sent_at' => now(),
        ]);

        // Gửi cho Admin và Staff (ưu tiên người được gán)
        $recipients = collect();
        
        if ($ticket->assigned_staff_id) {
            $recipients->push(User::find($ticket->assigned_staff_id));
        }
        
        // Thêm tất cả admin
        $admins = User::where('role', 1)->where('is_active', 1)->get();
        $recipients = $recipients->merge($admins)->unique('id');

        foreach ($recipients as $user) {
            if ($user) {
                UserNotification::create([
                    'user_id' => $user->id,
                    'notification_id' => $notification->id,
                    'is_read' => false,
                ]);
            }
        }
    }

    /**
     * Thông báo khi Admin gán ticket cho nhân viên (gửi cho Khách và Nhân viên được gán)
     */
    public static function notifyTicketAssigned(Ticket $ticket, User $staff)
    {
        // Thông báo cho khách hàng
        $customerNotification = Notification::create([
            'title' => "Ticket #" . $ticket->id . " đã được gán",
            'content' => "Ticket của bạn đã được gán cho nhân viên " . $staff->name . ".",
            'target_role' => 3,
            'created_by' => null,
            'is_sent' => true,
            'sent_at' => now(),
        ]);

        UserNotification::create([
            'user_id' => $ticket->user_id,
            'notification_id' => $customerNotification->id,
            'is_read' => false,
        ]);

        // Thông báo cho nhân viên được gán
        $staffNotification = Notification::create([
            'title' => "Bạn được gán Ticket #" . $ticket->id,
            'content' => "Admin đã gán ticket \"" . $ticket->subject . "\" cho bạn.",
            'target_role' => 2,
            'created_by' => 1, // System/Admin
            'is_sent' => true,
            'sent_at' => now(),
        ]);

        UserNotification::create([
            'user_id' => $staff->id,
            'notification_id' => $staffNotification->id,
            'is_read' => false,
        ]);
    }

    /**
     * Thông báo khi Nhân viên claim ticket (gửi cho Khách)
     */
    public static function notifyTicketClaimed(Ticket $ticket, User $staff)
    {
        $notification = Notification::create([
            'title' => "Ticket #" . $ticket->id . " đã được nhận xử lý",
            'content' => "Nhân viên " . $staff->name . " đã nhận xử lý ticket của bạn.",
            'target_role' => 3,
            'created_by' => $staff->id,
            'is_sent' => true,
            'sent_at' => now(),
        ]);

        UserNotification::create([
            'user_id' => $ticket->user_id,
            'notification_id' => $notification->id,
            'is_read' => false,
        ]);
    }

    /**
     * Thông báo khi Nhân viên/Admin reply (gửi cho Khách)
     */
    public static function notifyStaffReply(Ticket $ticket, User $sender)
    {
        $notification = Notification::create([
            'title' => "Nhân viên phản hồi Ticket #" . $ticket->id,
            'content' => $sender->name . " vừa phản hồi ticket của bạn.",
            'target_role' => 3,
            'created_by' => $sender->id,
            'is_sent' => true,
            'sent_at' => now(),
        ]);

        UserNotification::create([
            'user_id' => $ticket->user_id,
            'notification_id' => $notification->id,
            'is_read' => false,
        ]);
    }

    /**
     * Thông báo khi ticket chuyển sang Hoàn thành (gửi cho Khách)
     */
    public static function notifyTicketCompleted(Ticket $ticket)
    {
        $notification = Notification::create([
            'title' => "Ticket #" . $ticket->id . " đã hoàn thành",
            'content' => "Ticket \"" . $ticket->subject . "\" đã được đánh dấu hoàn thành.",
            'target_role' => 3,
            'created_by' => 1, // System
            'is_sent' => true,
            'sent_at' => now(),
        ]);

        UserNotification::create([
            'user_id' => $ticket->user_id,
            'notification_id' => $notification->id,
            'is_read' => false,
        ]);
    }

    /**
     * Thông báo khi ticket bị đóng (gửi cho Khách)
     */
    public static function notifyTicketClosed(Ticket $ticket)
    {
        $notification = Notification::create([
            'title' => "Ticket #" . $ticket->id . " đã đóng",
            'content' => "Ticket \"" . $ticket->subject . "\" đã được đóng.",
            'target_role' => 3,
            'created_by' => 1, // System
            'is_sent' => true,
            'sent_at' => now(),
        ]);

        UserNotification::create([
            'user_id' => $ticket->user_id,
            'notification_id' => $notification->id,
            'is_read' => false,
        ]);
    }

    /**
     * Thông báo khi ticket tự động đóng sau 3 ngày không phản hồi (gửi cho Khách)
     */
    public static function notifyTicketAutoClosed(Ticket $ticket)
    {
        $notification = Notification::create([
            'title' => "Ticket #" . $ticket->id . " đã tự động đóng",
            'content' => "Ticket \"" . $ticket->subject . "\" đã được tự động đóng do không có phản hồi sau 3 ngày.",
            'target_role' => 3,
            'created_by' => null, // System
            'is_sent' => true,
            'sent_at' => now(),
        ]);

        UserNotification::create([
            'user_id' => $ticket->user_id,
            'notification_id' => $notification->id,
            'is_read' => false,
        ]);

        // Gửi email thông báo
        try {
            $template = \App\Models\EmailTemplate::where('code', 'ticket_auto_closed')
                ->where('is_active', true)
                ->first();

            if (!$template) {
                // Fallback to ticket_closed template
                $template = \App\Models\EmailTemplate::where('code', 'ticket_closed')
                    ->where('is_active', true)
                    ->first();
            }

            if ($template && $ticket->user) {
                $ticketLink = route('customer.tickets.show', $ticket->id);
                
                \Illuminate\Support\Facades\Mail::to($ticket->user->email)->queue(
                    new \App\Mail\GenericMail(
                        $template,
                        [
                            'user_name' => $ticket->user->name,
                            'ticket_id' => $ticket->id,
                            'ticket_subject' => $ticket->subject,
                            'ticket_link' => $ticketLink,
                            'app_name' => config('app.name'),
                            'close_reason' => 'Ticket đã được tự động đóng do không có phản hồi từ bạn sau 3 ngày.',
                        ]
                    )
                );
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send auto-close email: ' . $e->getMessage());
        }
    }
}
