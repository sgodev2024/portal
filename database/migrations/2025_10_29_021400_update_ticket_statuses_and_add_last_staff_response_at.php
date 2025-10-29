<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Bước 1: Thay đổi ENUM để bao gồm cả giá trị cũ và mới
        DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM('new', 'in_progress', 'completed', 'closed', 'waiting_customer', 'responded') DEFAULT 'new'");

        // Bước 2: Cập nhật các ticket có status 'waiting_customer' thành 'responded'
        DB::table('tickets')
            ->where('status', 'waiting_customer')
            ->update(['status' => 'responded']);

        // Bước 3: Cập nhật các ticket có status 'completed' thành 'closed'
        DB::table('tickets')
            ->where('status', 'completed')
            ->update(['status' => 'closed']);

        // Bước 4: Thay đổi ENUM chỉ giữ lại các giá trị mới
        DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM('new', 'in_progress', 'responded', 'closed') DEFAULT 'new'");

        // Bước 5: Thêm cột last_staff_response_at
        Schema::table('tickets', function (Blueprint $table) {
            $table->timestamp('last_staff_response_at')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Khôi phục lại status cũ (nếu cần)
        DB::table('tickets')
            ->where('status', 'responded')
            ->update(['status' => 'waiting_customer']);

        DB::table('tickets')
            ->where('status', 'closed')
            ->update(['status' => 'completed']);

        // Xóa cột last_staff_response_at
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('last_staff_response_at');
        });
    }
};
