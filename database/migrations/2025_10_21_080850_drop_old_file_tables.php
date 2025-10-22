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
        // Xóa các bảng cũ không còn sử dụng
        Schema::dropIfExists('report_file_recipients');
        Schema::dropIfExists('report_file_logs');
        Schema::dropIfExists('document_template_downloads');
        Schema::dropIfExists('report_files');
        Schema::dropIfExists('document_templates');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Không thể reverse vì đã xóa dữ liệu
        // Cần restore từ backup nếu cần
    }
};
