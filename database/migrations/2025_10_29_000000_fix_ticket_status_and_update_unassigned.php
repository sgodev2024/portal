<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Cập nhật enum để thêm 'responded' và giữ các status hiện tại
        DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM('new', 'in_progress', 'responded', 'waiting_customer', 'completed', 'closed') DEFAULT 'new'");
        
        // 2. Update tất cả tickets chưa có người phụ trách về status 'new'
        DB::table('tickets')
            ->whereNull('assigned_staff_id')
            ->whereIn('status', ['in_progress', 'waiting_customer', 'completed'])
            ->update(['status' => 'new']);
    }

    public function down(): void
    {
        // Rollback enum về trạng thái cũ
        DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM('new', 'in_progress', 'waiting_customer', 'completed', 'closed') DEFAULT 'new'");
    }
};
