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
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dis_coal', function (Blueprint $table) {
             $table->dropColumn([
            'date',
            'CAME_TRAIN',
            'UNLOADING_TRAIN',
            'ULDSEIN_TRAIN',
            'COAL_INCOME',
            'COAL_OUTCOME',
            'COAL_TRAIN_QUANTITY',
            'COAL_REMAIN',
            'COAL_REMAIN_BYDAY',
            'COAL_REMAIN_BYWINTERDAY',
            'MAZUT_INCOME',
            'MAZUT_OUTCOME',
            'MAZUT_TRAIN_QUANTITY',
            'MAZUT_REMAIN',
            'BAGANUUR_MINING_COAL_D',
            'SHARINGOL_MINING_COAL_D',
            'SHIVEEOVOO_MINING_COAL',
            'OTHER_MINIG_COAL_SUPPLY',
            'FUEL_SENDING_EMPL',
            'FUEL_RECEIVER_EMPL',
            'ORG_CODE',
            'ORG_NAME',
        ]);
        });
    }
};
