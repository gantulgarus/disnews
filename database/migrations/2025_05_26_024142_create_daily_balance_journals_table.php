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
        Schema::create('daily_balance_journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('power_plant_id')->constrained()->onDelete('cascade');
            $table->dateTime('entry_date_time'); // 🕒 бүртгэлийн огноо + цаг минут
            $table->decimal('processed_amount', 10, 2)->nullable(); // Боловсруулалт
            $table->decimal('distribution_amount', 10, 2)->nullable(); // Түгээлт
            $table->decimal('internal_demand', 10, 2)->nullable(); // Дотоод хэрэгцээ
            $table->decimal('percent', 5, 2)->nullable(); // %
            $table->decimal('positive_deviation', 10, 2)->nullable(); // "+" зөрчил
            $table->decimal('negative_deviation_spot', 10, 2)->nullable(); // "-" зөрчил спот
            $table->decimal('negative_deviation_import', 10, 2)->nullable(); // "-" зөрчил импорт
            $table->decimal('positive_resolution', 10, 2)->nullable(); // "+" шийд
            $table->decimal('negative_resolution', 10, 2)->nullable(); // "-" шийд
            $table->text('deviation_reason')->nullable(); // "-" зөрчил авсан шалтгаан
            $table->decimal('by_consumption_growth', 10, 2)->nullable(); // Хэрэглээний өсөлтөөр
            $table->decimal('by_other_station_issue', 10, 2)->nullable(); // Бусад станцын доголдолоор
            $table->string('dispatcher_name'); // Диспетчер нэр
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_balance_journals');
    }
};
