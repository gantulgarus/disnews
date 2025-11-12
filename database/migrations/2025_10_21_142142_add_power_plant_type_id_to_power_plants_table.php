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
        Schema::table('power_plants', function (Blueprint $table) {
            $table->foreignId('power_plant_type_id')
                ->nullable()
                ->constrained('power_plant_types')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('power_plants', function (Blueprint $table) {
            $table->dropForeign(['power_plant_type_id']);
            $table->dropColumn('power_plant_type_id');
        });
    }
};
