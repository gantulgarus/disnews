<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) { 
             //$table->string('permission_code')->nullable();
             $table->foreign('permission_code')
                  ->references('code')
                  ->on('permission_levels')
                  ->onUpdate('cascade')
                  ->onDelete('set null');
            
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['permission_code']);
            $table->dropColumn('permission_code');
        });
    }
};
