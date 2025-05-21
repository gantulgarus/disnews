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
        Schema::table('power_plant_daily_reports', function (Blueprint $table) {
            $table->unsignedBigInteger('power_plant_id')->after('id');

            $table->foreign('power_plant_id')
                ->references('id')
                ->on('power_plants')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('power_plant_daily_reports', function (Blueprint $table) {
            $table->dropForeign(['power_plant_id']);
            $table->dropColumn('power_plant_id');
        });
    }
};
