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
        Schema::create('station_thermo_data', function (Blueprint $table) {
            $table->id();
            $table->date('infodate');
            $table->integer('infotime');

            $table->float('pp2p1')->nullable();
            $table->float('pp2p2')->nullable();
            $table->float('pp2t1')->nullable();
            $table->float('pp2t2')->nullable();
            $table->float('pp2g1')->nullable();
            $table->float('pp2g2')->nullable();
            $table->float('pp2gn')->nullable();

            $table->float('pp3hp1')->nullable();
            $table->float('pp3hp2')->nullable();
            $table->float('pp3ht1')->nullable();
            $table->float('pp3ht2')->nullable();
            $table->float('pp3hg1')->nullable();
            $table->float('pp3hg2')->nullable();
            $table->float('pp3hgn')->nullable();
            $table->float('pp3lp1')->nullable();
            $table->float('pp3lp2')->nullable();
            $table->float('pp3lt1')->nullable();
            $table->float('pp3lt2')->nullable();
            $table->float('pp3lg1')->nullable();
            $table->float('pp3lg2')->nullable();
            $table->float('pp3lgn')->nullable();

            $table->float('pp4p1')->nullable();
            $table->float('pp4p2')->nullable();
            $table->float('pp4t1')->nullable();
            $table->float('pp4700t2')->nullable();
            $table->float('pp41000t2')->nullable();
            $table->float('pp41200t2')->nullable();
            $table->float('pp4y700t2')->nullable();
            $table->float('pp4700g1')->nullable();
            $table->float('pp41000g1')->nullable();
            $table->float('pp41200g1')->nullable();
            $table->float('pp4y700g1')->nullable();
            $table->float('pp4700g2')->nullable();
            $table->float('pp41000g2')->nullable();
            $table->float('pp41200g2')->nullable();
            $table->float('pp4y700g2')->nullable();
            $table->float('pp4gn')->nullable();
            $table->float('pp4g')->nullable();
            $table->float('pp4210t2')->nullable();
            $table->float('pp4210g1')->nullable();
            $table->float('pp4210g2')->nullable();

            $table->float('amp1')->nullable();
            $table->float('amp2')->nullable();
            $table->float('amt1')->nullable();
            $table->float('amt2')->nullable();
            $table->float('amt2_2')->nullable();
            $table->float('amg1')->nullable();
            $table->float('amg2')->nullable();
            $table->float('amgn')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('station_thermo_data');
    }
};