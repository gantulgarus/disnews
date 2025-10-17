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
        Schema::table('users', function (Blueprint $table) {
            // integer төрлийн permission_level_id багана нэмэх
            $table->unsignedBigInteger('permission_level_id')->nullable()->after('id');

            // foreign key холбоос нэмэх
            $table->foreign('permission_level_id')
                ->references('id')
                ->on('permission_levels')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['permission_level_id']);
            $table->dropColumn('permission_level_id');
        });
    }
};
