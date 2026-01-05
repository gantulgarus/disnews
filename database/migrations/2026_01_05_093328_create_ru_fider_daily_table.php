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
        Schema::create('ru_fider_daily', function (Blueprint $table) {
            $table->id();
            $table->date('ognoo');
            $table->string('time_display', 5); // 00:00
            $table->unsignedTinyInteger('time_interval'); // 1-48
            $table->integer('fider');
            $table->decimal('import_kwt', 12, 2)->default(0);
            $table->decimal('export_kwt', 12, 2)->default(0);
            $table->timestamps();

            $table->unique(['ognoo', 'time_interval', 'fider']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ru_fider_daily');
    }
};
