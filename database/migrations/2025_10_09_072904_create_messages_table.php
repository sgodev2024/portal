<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_id')->constrained('chats')->onDelete('cascade');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['text', 'image', 'file'])->default('text');
            $table->text('content')->nullable()->comment('Nội dung tin nhắn (text)');
            $table->string('file_path')->nullable()->comment('Đường dẫn file/ảnh');
            $table->string('file_name')->nullable()->comment('Tên file gốc');
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->index(['chat_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
