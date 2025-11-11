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
        Schema::create('western_region_capacities', function (Blueprint $table) {
            $table->id();
            // Хүчин чадлын параметрүүд (мегаватт / тохируулж болно)
            $table->decimal('p_max', 14, 3)->default(0)->comment('Хамгийн их хүчин чадал (MW)');
            $table->decimal('p_min', 14, 3)->default(0)->comment('Хамгийн бага хүчин чадал (MW)');
            $table->decimal('p_imp_max', 14, 3)->default(0)->comment('Импортын хамгийн их (MW)');
            $table->decimal('p_imp_min', 14, 3)->default(0)->comment('Импортын хамгийн бага (MW)');

            // Импортын энерги/ЦЭХ (жишээгээр MWh эсвэл өөр нэгжээр)
            $table->decimal('import_received', 16, 3)->default(0)->comment('Импортоор авсан ЦЭХ (MWh)');
            $table->decimal('import_distributed', 16, 3)->default(0)->comment('Импортоор түгээсэн ЦЭХ (MWh)');

            // Нэмэлт: огноо/цаг (хэрэв тухайн snapshot-ийг тэмдэглэх бол)
            $table->date('date')->nullable()->comment('Огноо');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('western_region_capacities');
    }
};
