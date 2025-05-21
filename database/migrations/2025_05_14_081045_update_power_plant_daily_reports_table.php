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
        Schema::table('power_plant_daily_reports', function (Blueprint $table) {
            // Drop foreign key constraints before dropping the columns
            $table->dropForeign(['boiler_id']);
            $table->dropForeign(['turbine_generator_id']);

            // Drop old columns
            $table->dropColumn(['boiler_id', 'turbine_generator_id', 'status']);

            // Add new columns for the boiler and turbine statuses
            $table->json('boiler_working')->nullable();
            $table->json('boiler_preparation')->nullable();
            $table->json('boiler_repair')->nullable();
            $table->json('turbine_working')->nullable();
            $table->json('turbine_preparation')->nullable();
            $table->json('turbine_repair')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('power_plant_daily_reports', function (Blueprint $table) {
            // Revert the changes if necessary
            $table->integer('boiler_id')->nullable()->after('report_date');
            $table->integer('turbine_generator_id')->nullable()->after('boiler_id');
            $table->string('status')->nullable()->after('turbine_generator_id');

            // Add foreign keys back (optional)
            $table->foreign('boiler_id')->references('id')->on('boilers')->onDelete('cascade');
            $table->foreign('turbine_generator_id')->references('id')->on('turbine_generators')->onDelete('cascade');

            // Drop the newly added columns
            $table->dropColumn([
                'boiler_working',
                'boiler_preparation',
                'boiler_repair',
                'turbine_working',
                'turbine_preparation',
                'turbine_repair'
            ]);
        });
    }
};
