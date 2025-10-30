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
        Schema::create('user_project_group', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('ID khách hàng');
            $table->foreignId('project_group_id')->constrained('project_groups')->onDelete('cascade')->comment('ID nhóm dự án');
            $table->timestamps();
            
            // Đảm bảo không có duplicate
            $table->unique(['user_id', 'project_group_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_project_group');
    }
};
