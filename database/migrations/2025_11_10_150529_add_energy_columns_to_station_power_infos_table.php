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
        Schema::table('station_power_infos', function (Blueprint $table) {
            $table->decimal('produced_energy', 10, 3)->nullable()->after('p_min'); // Үйлдвэрлэсэн ЦЭХ
            $table->decimal('distributed_energy', 10, 3)->nullable()->after('produced_energy'); // Түгээсэн ЦЭХ
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('station_power_infos', function (Blueprint $table) {
            $table->dropColumn(['produced_energy', 'distributed_energy']);
        });
    }
};
