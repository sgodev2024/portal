<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_id')->constrained('chats')->onDelete('cascade');
            $table->foreignId('changed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action')->comment('assigned_to_staff, staff_left, closed, reopened...');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index('chat_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_logs');
    }
};
