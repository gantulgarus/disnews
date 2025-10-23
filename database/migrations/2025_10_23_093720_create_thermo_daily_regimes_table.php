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
        Schema::create('thermo_daily_regimes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('power_plant_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->enum('time_range', ['0-8', '8-16', '16-24']);
            $table->float('temperature')->nullable();
            $table->float('t1')->nullable();
            $table->float('t2')->nullable();
            $table->float('p1')->nullable();
            $table->float('p2')->nullable();
            $table->float('d')->nullable();
            $table->float('g')->nullable();
            $table->float('q')->nullable();
            $table->float('q_total')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thermo_daily_regimes');
    }
};
