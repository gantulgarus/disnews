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
        Schema::create('power_plant_daily_reports', function (Blueprint $table) {
            $table->id();
            $table->date('report_date');

            // аль тоног төхөөрөмжийн мэдээлэл гэдгийг заагч
            $table->foreignId('boiler_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('turbine_generator_id')->nullable()->constrained()->onDelete('cascade');

            $table->enum('status', ['Ажилд', 'Бэлтгэлд', 'Засварт']);
            $table->text('notes')->nullable(); // нэмэлт тайлбар
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('power_plant_daily_reports');
    }
};
