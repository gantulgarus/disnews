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
        Schema::table('tnews', function (Blueprint $table) {
            $table->boolean('send_telegram')->default(false)->after('HyzErchim');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tnews', function (Blueprint $table) {
            $table->dropColumn('send_telegram');
        });
    }
};
