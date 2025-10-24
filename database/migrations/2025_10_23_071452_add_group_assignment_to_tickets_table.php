<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('assigned_group_id')->nullable()->constrained('customer_groups')->onDelete('set null');
            $table->foreignId('assigned_staff_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('assignment_type', ['individual', 'group'])->default('individual');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['assigned_group_id']);
            $table->dropForeign(['assigned_staff_id']);
            $table->dropColumn(['assigned_group_id', 'assigned_staff_id', 'assignment_type']);
        });
    }
};
