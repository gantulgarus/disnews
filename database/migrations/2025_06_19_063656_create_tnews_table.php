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
        Schema::create('tnews', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('time');
            $table->string('TZE');
            $table->string('tasralt');
            $table->text('ArgaHemjee')->nullable();
            $table->string('HyzErchim')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tnews');
    }
};
