<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixTicketStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update tất cả tickets chưa có người phụ trách về status 'new'
        $updated = DB::table('tickets')
            ->whereNull('assigned_staff_id')
            ->whereIn('status', ['in_progress', 'waiting_customer', 'completed'])
            ->update(['status' => 'new']);

        $this->command->info("✅ Đã cập nhật {$updated} tickets chưa gán về trạng thái 'new'");
        
        // Hiển thị kết quả
        $unassignedTickets = DB::table('tickets')
            ->whereNull('assigned_staff_id')
            ->select('id', 'subject', 'status', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        $this->command->info("\n📋 10 tickets chưa gán gần nhất:");
        foreach ($unassignedTickets as $ticket) {
            $this->command->line("  - #{$ticket->id}: {$ticket->subject} → {$ticket->status}");
        }
    }
}
