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
        Schema::create('power_energy_adjustments', function (Blueprint $table) {
            $table->id();
            // Хязгаарласан ЦЭХ (кВт.цаг)
            $table->decimal('restricted_kwh', 15, 3)->default(0);

            // Хөнгөлсөн ЦЭХ (кВт.цаг)
            $table->decimal('discounted_kwh', 15, 3)->default(0);

            // Огноо
            $table->date('date')->unique();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('power_energy_adjustments');
    }
};
