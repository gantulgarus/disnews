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
        Schema::create('altai_region_capacities', function (Blueprint $table) {
            $table->id();

            // Огноо
            $table->date('date')->index();

            // Хамгийн их ачаалал (МВт)
            $table->decimal('max_load', 8, 3)->nullable();

            // Хамгийн бага ачаалал (МВт)
            $table->decimal('min_load', 8, 3)->nullable();

            // ББЭХС-ээс авсан (МВт)
            $table->decimal('import_from_bbexs', 8, 3)->nullable();

            // ТБНС-ээс авсан (МВт) — сөрөг байж болно
            $table->decimal('import_from_tbns', 8, 3)->nullable();

            // Тайлбар
            $table->text('remark')->nullable();

            $table->timestamps();

            // Нэг өдөрт 1 бүртгэл (давхардахаас сэргийлнэ)
            $table->unique('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('altai_region_capacities');
    }
};