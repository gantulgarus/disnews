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
        Schema::create('power_plant_thermo_equipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('power_plant_id')->constrained()->cascadeOnDelete();

            $table->string('code');
            // P1, P2, T1, T2, GSUL, GNU, GUR

            $table->string('name');
            // Даралт P1, Температур T1, Сүлжээний усны зарцуулалт гэх мэт

            $table->string('unit')->nullable(); // МПа, °C, т/ц
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('power_plant_thermo_equipments');
    }
};
