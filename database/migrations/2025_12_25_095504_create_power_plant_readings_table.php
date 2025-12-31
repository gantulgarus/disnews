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
        Schema::create('power_plant_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('power_plant_thermo_equipment_id')
                ->constrained('power_plant_thermo_equipments')
                ->onDelete('cascade');
            $table->date('reading_date');
            $table->integer('reading_hour'); // 1-24 цаг
            $table->decimal('value', 10, 2);
            $table->timestamps();

            // Давхардахаас сэргийлэх
            $table->unique(['power_plant_thermo_equipment_id', 'reading_date', 'reading_hour'], 'unique_reading');

            // Хайлтын индекс
            $table->index(['reading_date', 'reading_hour']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('power_plant_readings');
    }
};
