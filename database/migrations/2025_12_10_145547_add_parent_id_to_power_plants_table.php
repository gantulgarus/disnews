<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('power_plants', function (Blueprint $table) {
            // parent_id нэмэх
            $table->foreignId('parent_id')
                ->nullable()
                ->after('id')
                ->constrained('power_plants')
                ->nullOnDelete(); // Үндсэн станц устахад дэд станцууд мөн устахгүй
        });
    }

    public function down(): void
    {
        Schema::table('power_plants', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }
};
