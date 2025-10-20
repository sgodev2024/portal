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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('account_id')->unique()->comment('Mã tài khoản = Số điện thoại, hiển thị toàn hệ thống');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('company')->nullable();
            $table->string('department')->nullable();
            $table->string('position')->nullable();
            $table->string('address')->nullable();
            $table->string('password');
            $table->unsignedTinyInteger('role')->default(3)->comment('1=Admin, 2=Nhân viên, 3=Khách hàng');
            $table->boolean('is_active')->default(true);
            $table->boolean('must_update_profile')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
