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
        Schema::table('forecast_data', function (Blueprint $table) {
            $table->decimal('system_load', 10, 2)->nullable()->after('actual_load');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forecast_data', function (Blueprint $table) {
            $table->dropColumn('system_load');
        });
    }
};