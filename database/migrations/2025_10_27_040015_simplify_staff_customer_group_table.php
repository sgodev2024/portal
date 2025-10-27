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
        Schema::table('staff_customer_group', function (Blueprint $table) {

            $table->dropColumn('is_primary');
            $table->unique('customer_group_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('staff_customer_group', function (Blueprint $table) {
            $table->dropUnique(['customer_group_id']);
            $table->boolean('is_primary')->default(false);
        });
    }
};
