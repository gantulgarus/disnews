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
        Schema::create('electric_daily_regimes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('power_plant_id');
            $table->unsignedBigInteger('user_id');
            $table->date('date');

            // Техникийн чадал
            $table->decimal('technical_pmax', 8, 2)->nullable();
            $table->decimal('technical_pmin', 8, 2)->nullable();

            // Горимоор өгсөн чадал
            $table->decimal('pmax', 8, 2)->nullable();
            $table->decimal('pmin', 8, 2)->nullable();

            // 24 цагийн ачаалал (мВт)
            for ($i = 1; $i <= 24; $i++) {
                $table->decimal('hour_' . $i, 8, 2)->nullable();
            }

            // Нийт үйлдвэрлэл (мян.кВт.ц)
            $table->decimal('total_mwh', 10, 3)->nullable();

            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('electric_daily_regimes');
    }
};
