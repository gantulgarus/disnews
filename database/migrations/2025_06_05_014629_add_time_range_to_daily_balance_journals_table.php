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
        Schema::table('daily_balance_journals', function (Blueprint $table) {
            $table->string('time_range')->after('entry_date_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_balance_journals', function (Blueprint $table) {
            $table->dropColumn('time_range');
        });
    }
};
