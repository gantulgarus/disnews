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
        Schema::create('forecast_data', function (Blueprint $table) {
            $table->id();
            $table->datetime('time');
            $table->decimal('actual_load', 10, 2)->nullable();
            $table->decimal('daily_forecast', 10, 2)->nullable();
            $table->decimal('hourly_forecast', 10, 2)->nullable();
            $table->boolean('is_actual')->default(false);
            $table->string('forecast_type')->nullable(); // 'daily' or 'hourly'
            $table->timestamps();

            $table->index('time');
            $table->index('forecast_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forecast_data');
    }
};
