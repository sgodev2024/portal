<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customer_attribute_user', function (Blueprint $table) {
            $table->id();

            // Khóa ngoại users (bigint)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->foreignId('customer_attribute_id')
                ->constrained('customer_attributes')
                ->onDelete('cascade');

            $table->timestamps();

            $table->unique(['user_id', 'customer_attribute_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_attribute_user');
    }
};
