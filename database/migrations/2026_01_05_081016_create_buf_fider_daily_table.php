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
        Schema::create('buf_fider_daily', function (Blueprint $table) {
            $table->id(); // BIGINT AUTO_INCREMENT PRIMARY KEY

            // Main columns
            $table->date('OGNOO');
            $table->integer('TIME_INTERVAL');
            $table->string('TIME_DISPLAY', 20);
            $table->integer('OBEKT');
            $table->integer('SULJEE');
            $table->integer('FIDER');
            $table->string('LINE_NAME', 50)->nullable();
            $table->decimal('IMPORT_KWT', 12, 2)->default(0);
            $table->decimal('EXPORT_KWT', 12, 2)->default(0);
            $table->integer('TOOTSOOLUUR_COUNT')->default(0);

            // Timestamps
            $table->timestamps(); // created_at, updated_at

            // UNIQUE constraint - давхардал хориглох
            $table->unique(['OGNOO', 'TIME_INTERVAL', 'OBEKT', 'SULJEE', 'FIDER'], 'unique_record');

            // Performance indexes
            $table->index('OGNOO', 'idx_date');
            $table->index('FIDER', 'idx_fider');
            $table->index(['OGNOO', 'FIDER'], 'idx_date_fider');
            $table->index('TIME_INTERVAL', 'idx_time_interval');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buf_fider_daily');
    }
};
