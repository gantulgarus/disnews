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
        Schema::create('power_distribution_works', function (Blueprint $table) {
            $table->id();
            $table->string('tze'); // ТЗЭ
            $table->string('repair_work'); // Засварын ажлын утга
            $table->text('description')->nullable(); // Тайлбар
            $table->float('restricted_energy')->nullable(); // Хязгаарласан эрчим хүч
            $table->date('date'); // Огноо
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // user_id
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('power_distribution_works');
    }
};
