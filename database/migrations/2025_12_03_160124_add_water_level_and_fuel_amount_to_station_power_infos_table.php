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
            $table->float('water_level')->nullable()->after('distributed_energy');   // Усны төвшин /м/
            $table->float('fuel_amount')->nullable()->after('water_level');         // Түлшний нөөц /л/
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('station_power_infos', function (Blueprint $table) {
            $table->dropColumn(['water_level', 'fuel_amount']);
        });
    }
};
