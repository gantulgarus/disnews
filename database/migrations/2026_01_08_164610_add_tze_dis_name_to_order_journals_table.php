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
        Schema::table('order_journals', function (Blueprint $table) {
            $table->string('tze_dis_name')->nullable()->after('dut_dispatcher_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_journals', function (Blueprint $table) {
            $table->dropColumn('tze_dis_name');
        });
    }
};
