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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');                     // Tiêu đề thông báo
            $table->text('content')->nullable();         // Nội dung thông báo
            $table->string('attachment_path')->nullable(); // File đính kèm (nếu có)
            $table->enum('target_role', ['admin', 'staff', 'user', 'all'])->default('user');
            // Gửi cho vai trò nào (mở rộng dễ)
            $table->unsignedBigInteger('created_by');    // Ai tạo thông báo
            $table->boolean('is_sent')->default(false);  // Đã gửi mail hay chưa
            $table->timestamp('sent_at')->nullable();    // Thời điểm gửi mail
            $table->timestamps();

            // Chỉ mục giúp truy vấn nhanh
            $table->index(['target_role', 'is_sent']);
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
