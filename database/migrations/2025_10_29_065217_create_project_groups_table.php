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
        Schema::create('project_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Tên nhóm dự án');
            $table->string('code')->unique()->comment('Mã nhóm dự án');
            $table->text('description')->nullable()->comment('Mô tả');
            $table->string('location')->nullable()->comment('Vị trí dự án');
            $table->integer('total_units')->nullable()->comment('Tổng số căn hộ');
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('Trạng thái');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_groups');
    }
};
