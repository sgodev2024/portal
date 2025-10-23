
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Thay đổi enum status
        DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM('new', 'in_progress', 'completed', 'closed') DEFAULT 'new'");
        
        // Update dữ liệu cũ
        DB::table('tickets')->where('status', 'open')->update(['status' => 'new']);
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM('open', 'in_progress', 'closed') DEFAULT 'open'");
        DB::table('tickets')->where('status', 'new')->update(['status' => 'open']);
    }
};
