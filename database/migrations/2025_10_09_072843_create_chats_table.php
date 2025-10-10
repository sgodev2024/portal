<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('staff_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['pending', 'processing'])->default('pending')
                ->comment('pending=chờ xử lý, processing=đang xử lý');
            $table->longText('content')->nullable()->comment('Lưu tất cả tin nhắn dưới dạng JSON');
            $table->timestamp('last_message_at')->nullable()->comment('Thời điểm tin nhắn mới nhất');
            $table->timestamps();

            $table->index(['status', 'last_message_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
