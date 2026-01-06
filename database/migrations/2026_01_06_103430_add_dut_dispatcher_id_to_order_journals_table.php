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
            $table->unsignedBigInteger('dut_dispatcher_id')->nullable()->after('organization_id');

            // Хэрвээ Users хүснэгттэй relation үүсгэх бол
            $table->foreign('dut_dispatcher_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_journals', function (Blueprint $table) {
            $table->dropForeign(['dut_dispatcher_id']);
            $table->dropColumn('dut_dispatcher_id');
        });
    }
};
