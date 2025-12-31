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
        Schema::create('power_plant_thermo_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('power_plant_id')->constrained();
            $table->foreignId('power_plant_thermo_equipment_id')->constrained('power_plant_thermo_equipments');

            $table->date('infodate');
            $table->time('infotime');
            $table->decimal('value', 12, 3)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('power_plant_thermo_data');
    }
};
