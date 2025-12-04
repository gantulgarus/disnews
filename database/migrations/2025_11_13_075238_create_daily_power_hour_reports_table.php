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
        Schema::create('daily_power_hour_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('power_plant_id');
            $table->unsignedBigInteger('daily_power_equipment_id');
            $table->decimal('power_value', 8, 2)->nullable();
            $table->date('date');
            $table->time('time');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            // Гадаад холбоосууд (хүсвэл дараа нь FK нэмэж болно)
            $table->foreign('power_plant_id')->references('id')->on('power_plants');
            $table->foreign('daily_power_equipment_id')->references('id')->on('daily_power_equipments');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_power_hour_reports');
    }
};
