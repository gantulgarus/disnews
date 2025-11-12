<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_balance_journals', function (Blueprint $table) {
            // Хуучин багануудыг устгах
            $table->dropColumn([
                'time_range',
                'entry_date_time',
                'positive_deviation',
                'negative_deviation_spot',
                'negative_deviation_import',
                'positive_resolution',
                'negative_resolution',
            ]);

            // Шинэ date талбар нэмэх
            $table->date('date')->after('power_plant_id')->nullable();

            // Шинэ 15 баганууд нэмэх
            $table->float('positive_deviation_00_08')->nullable();
            $table->float('positive_deviation_08_16')->nullable();
            $table->float('positive_deviation_16_24')->nullable();

            $table->float('negative_deviation_spot_00_08')->nullable();
            $table->float('negative_deviation_spot_08_16')->nullable();
            $table->float('negative_deviation_spot_16_24')->nullable();

            $table->float('negative_deviation_import_00_08')->nullable();
            $table->float('negative_deviation_import_08_16')->nullable();
            $table->float('negative_deviation_import_16_24')->nullable();

            $table->float('positive_resolution_00_08')->nullable();
            $table->float('positive_resolution_08_16')->nullable();
            $table->float('positive_resolution_16_24')->nullable();

            $table->float('negative_resolution_00_08')->nullable();
            $table->float('negative_resolution_08_16')->nullable();
            $table->float('negative_resolution_16_24')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('daily_balance_journals', function (Blueprint $table) {
            // Хуучин талбаруудыг сэргээх
            $table->timestamp('entry_date_time')->nullable();
            $table->string('time_range')->nullable();

            $table->float('positive_deviation')->nullable();
            $table->float('negative_deviation_spot')->nullable();
            $table->float('negative_deviation_import')->nullable();
            $table->float('positive_resolution')->nullable();
            $table->float('negative_resolution')->nullable();

            // Шинээр нэмсэн талбаруудыг устгах
            $table->dropColumn([
                'date',
                'positive_deviation_00_08',
                'positive_deviation_08_16',
                'positive_deviation_16_24',
                'negative_deviation_spot_00_08',
                'negative_deviation_spot_08_16',
                'negative_deviation_spot_16_24',
                'negative_deviation_import_00_08',
                'negative_deviation_import_08_16',
                'negative_deviation_import_16_24',
                'positive_resolution_00_08',
                'positive_resolution_08_16',
                'positive_resolution_16_24',
                'negative_resolution_00_08',
                'negative_resolution_08_16',
                'negative_resolution_16_24',
            ]);
        });
    }
};