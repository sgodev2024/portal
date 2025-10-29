<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add waiting_customer to enum list
        DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM('new', 'in_progress', 'waiting_customer', 'completed', 'closed') DEFAULT 'new'");
    }

    public function down(): void
    {
        // Remove waiting_customer from enum list
        DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM('new', 'in_progress', 'completed', 'closed') DEFAULT 'new'");
    }
};
