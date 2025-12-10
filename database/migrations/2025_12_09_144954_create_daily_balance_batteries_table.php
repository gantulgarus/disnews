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
        Schema::create('daily_balance_batteries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('power_plant_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->float('energy_given')->default(0); // өгсөн (Discharge)
            $table->float('energy_taken')->default(0); // авсан (Charge)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_balance_batteries');
    }
};
