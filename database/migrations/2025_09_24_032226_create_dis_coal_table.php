<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('dis_coal', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->integer('CAME_TRAIN')->nullable();
            $table->integer('UNLOADING_TRAIN')->nullable();
            $table->integer('ULDSEIN_TRAIN')->nullable();
            $table->integer('COAL_INCOME')->nullable();
            $table->integer('COAL_OUTCOME')->nullable();
            $table->float('COAL_TRAIN_QUANTITY', 12, 2)->nullable();
            $table->integer('COAL_REMAIN')->nullable();
            $table->float('COAL_REMAIN_BYDAY', 12, 2)->nullable();
            $table->integer('COAL_REMAIN_BYWINTERDAY')->nullable();
            $table->integer('MAZUT_INCOME')->nullable();
            $table->integer('MAZUT_OUTCOME')->nullable();
            $table->integer('MAZUT_TRAIN_QUANTITY')->nullable();
            $table->integer('MAZUT_REMAIN')->nullable();
            $table->integer('BAGANUUR_MINING_COAL_D')->nullable();
            $table->integer('SHARINGOL_MINING_COAL_D')->nullable();
            $table->integer('SHIVEEOVOO_MINING_COAL')->nullable();
            $table->integer('OTHER_MINIG_COAL_SUPPLY')->nullable();
            $table->integer('FUEL_SENDING_EMPL')->nullable();
            $table->integer('FUEL_RECEIVER_EMPL')->nullable();
            $table->integer('ORG_CODE')->nullable();
            $table->string('ORG_NAME')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('dis_coal');
    }
};
