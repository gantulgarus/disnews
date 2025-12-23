<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  
    public function up(): void
    {
        Schema::create('power_locations', function (Blueprint $table) {
            $table->id();
            $table->string('plant_name')->comment('Станцын нэр');
            $table->decimal('latitude', 10, 7)->comment('Өргөрөг');
            $table->decimal('longitude', 10, 7)->comment('Уртраг');
            $table->timestamps();


        });
    }


    public function down(): void
    {
        Schema::dropIfExists('power_locations');
    }
};
