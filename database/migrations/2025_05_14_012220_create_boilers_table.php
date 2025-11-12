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
        Schema::create('boilers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('power_plant_id')->constrained()->onDelete('cascade'); // аль станцтай хамаарах
            $table->string('name'); // Зуухны нэр (Жишээ нь: Boiler #1)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boilers');
    }
};
