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
        Schema::create('equipment_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('power_plant_id')->constrained()->onDelete('cascade');
            $table->foreignId('equipment_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->enum('status', ['Ажилд', 'Бэлтгэлд', 'Засварт']);
            $table->text('remark')->nullable(); // Тайлбар
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_statuses');
    }
};
