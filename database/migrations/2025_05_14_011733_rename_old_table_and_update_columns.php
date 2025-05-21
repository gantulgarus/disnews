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
        // Хүснэгтийн нэрийг солих
        Schema::rename('disnews_m_plants', 'power_plants');

        // Багануудыг өөрчлөх
        Schema::table('power_plants', function (Blueprint $table) {
            // Хуучин primary key-г устгах
            $table->dropPrimary(); // зөвхөн single primary key байсан тохиолдолд

            // ID талбарыг short_name болгож rename хийх
            $table->renameColumn('ID', 'short_name');

            // Name талбарыг name болгож rename хийх
            $table->renameColumn('Name', 'name');

            // Шинэ primary key нэмэх
            $table->bigIncrements('id')->first();

            // created_at, updated_at баганууд нэмэх
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('power_plants', function (Blueprint $table) {
            $table->dropColumn(['id', 'created_at', 'updated_at']);
            $table->renameColumn('short_name', 'ID');
            $table->renameColumn('name', 'Name');
            $table->dropPrimary(['id']); // id-г primary болгоод устгаж байгаа
        });

        Schema::rename('power_plants', 'disnews_m_plants');
    }
};
