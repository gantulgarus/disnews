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
       Schema::table('daily_power_equipments', function (Blueprint $table) {
        $table->string('equipment_name')->nullable()->after('power_equipment');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_power_equipments', function (Blueprint $table) {
        $table->dropColumn('equipment_name');
    });
    }
};
