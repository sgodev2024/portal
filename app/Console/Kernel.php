<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Tự động đóng ticket đã phản hồi sau 3 ngày không có phản hồi từ khách hàng
        $schedule->call(function () {
            $tickets = \App\Models\Ticket::where('status', \App\Models\Ticket::STATUS_RESPONDED)
                ->where('last_staff_response_at', '<=', now()->subDays(3))
                ->get();
            
            foreach ($tickets as $ticket) {
                // Cập nhật trạng thái
                $ticket->update(['status' => \App\Models\Ticket::STATUS_CLOSED]);
                
                // Gửi thông báo cho khách hàng
                \App\Services\TicketNotificationService::notifyTicketAutoClosed($ticket);
            }
        })->dailyAt('01:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
