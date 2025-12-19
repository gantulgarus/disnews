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
        Schema::table('dis_coal', function (Blueprint $table) {
            // organization_id устгах
            if (Schema::hasColumn('dis_coal', 'organization_id')) {
                $table->dropForeign(['organization_id']);
                $table->dropColumn('organization_id');
            }

            // power_plant_id нэмэх
            $table->unsignedBigInteger('power_plant_id')->nullable()->after('ORG_NAME');

            $table->foreign('power_plant_id')
                ->references('id')
                ->on('power_plants')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dis_coal', function (Blueprint $table) {
            // power_plant_id устгах
            $table->dropForeign(['power_plant_id']);
            $table->dropColumn('power_plant_id');

            // organization_id буцааж нэмэх
            $table->unsignedBigInteger('organization_id')->nullable();

            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->nullOnDelete();
        });
    }
};
