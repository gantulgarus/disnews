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
        Schema::create('gr_gr', function (Blueprint $table) {
            // Oracle NUMBER(22,8,0) -> MySQL BIGINT
            $table->bigInteger('N_OB');

            // Oracle NUMBER(22,3,0) -> MySQL INTEGER
            $table->integer('SYB_RNK');

            // Oracle NUMBER(22,4,0) -> MySQL INTEGER
            $table->integer('N_GR_INTEGR');
            $table->integer('TYP_POK');

            // Oracle NUMBER(22,3,0) nullable -> MySQL INTEGER nullable
            $table->integer('INTERV')->nullable();

            // Oracle NUMBER(22,8,0) -> MySQL BIGINT
            $table->bigInteger('N_OB_TY');

            // Oracle NUMBER(22,3,0) -> MySQL INTEGER
            $table->integer('SYB_RNK_TY');

            // Oracle NUMBER(22,4,0) -> MySQL INTEGER
            $table->integer('N_FID');

            // Oracle NUMBER(22,3,0) -> MySQL INTEGER
            $table->integer('N_GR_TY');

            // Oracle NUMBER(22,2,0) -> MySQL TINYINT
            $table->tinyInteger('ZNAK');

            // Oracle NUMBER(22,10,0) -> MySQL BIGINT
            $table->bigInteger('N_SH');

            // Oracle NUMBER(22,3,0) nullable -> MySQL INTEGER nullable
            $table->integer('INTERV_TY')->nullable();

            // Oracle VARCHAR2(1) nullable -> MySQL CHAR(1) nullable
            $table->char('SV', 1)->nullable();

            // Laravel timestamps (optional)
            $table->timestamps();

            // Indexes - Performance-ийн тулд МАШ ЧУХАЛ!
            $table->index('N_FID', 'idx_n_fid');
            $table->index('ZNAK', 'idx_znak');
            $table->index(['N_FID', 'ZNAK'], 'idx_fid_znak');

            // JOIN-д ашиглагдах composite index
            $table->index(['N_OB', 'SYB_RNK', 'N_SH'], 'idx_join_composite');

            // Primary key (optional - хэрэв Oracle дээр байгаа бол)
            // $table->primary(['N_OB', 'SYB_RNK', 'N_SH']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gr_gr');
    }
};
