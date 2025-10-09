<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('company_address')->nullable();
            $table->string('company_phone', 20)->nullable();
            $table->string('company_email')->nullable();
            $table->string('company_website')->nullable();
            $table->string('company_logo')->nullable();
            $table->string('tax_id', 50)->nullable();
            $table->string('vat_rate', 10)->nullable();
            $table->string('representative_name')->nullable();
            $table->string('representative_position')->nullable();
            $table->string('representative_phone', 20)->nullable();
            $table->string('representative_email')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
