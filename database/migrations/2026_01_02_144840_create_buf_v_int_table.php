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
        Schema::create('buf_v_int', function (Blueprint $table) {
            $table->bigIncrements('id'); // Laravel default PK (хэрэв хэрэггүй бол авч хаяж болно)

            $table->integer('syb_rnk')->nullable();
            $table->integer('n_ob')->nullable();
            $table->integer('n_fid')->nullable();
            $table->integer('n_gr_ty')->nullable();
            $table->integer('n_sh')->nullable();

            $table->date('dd_mm_yyyy')->nullable();

            $table->integer('n_inter_ras')->nullable();

            $table->decimal('kol_db', 18, 6)->nullable();
            $table->decimal('kol', 18, 6)->nullable();
            $table->decimal('val', 18, 6)->nullable();

            $table->string('stat', 1)->nullable(); // <-- STAT varchar2(1)

            $table->decimal('min_0', 18, 6)->nullable();
            $table->decimal('min_1', 18, 6)->nullable();

            $table->integer('interv')->nullable();

            $table->decimal('ak_sum', 18, 6)->nullable();
            $table->decimal('pok_start', 18, 6)->nullable();
            $table->decimal('rash_poln', 18, 6)->nullable();

            $table->integer('impulses')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buf_v_int');
    }
};
