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
        Schema::table('power_distribution_works', function (Blueprint $table) {
            $table->boolean('send_telegram')->default(false)->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('power_distribution_works', function (Blueprint $table) {
            $table->dropColumn('send_telegram');
        });
    }
};
