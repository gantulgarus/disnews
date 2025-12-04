<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_power_equipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('power_plant_id')
                  ->constrained('power_plants')
                  ->onDelete('cascade');
            $table->string('power_equipment');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_power_equipments');
    }
};
