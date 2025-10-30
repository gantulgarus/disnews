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
        Schema::create('telephone_messages', function (Blueprint $table) {
            $table->id();

            // Төлөв: илгээсэн, хүлээн авсан, устгасан гэх мэт
            $table->enum('status', ['Шинээр ирсэн', 'Хүлээн авсан'])->default('Шинээр ирсэн');

            // Илгээсэн байгууллага
            $table->foreignId('sender_org_id')->constrained('organizations')->onDelete('cascade');

            // Хүлээн авагч байгууллагууд (олон байж болох тул JSON хадгална)
            $table->json('receiver_org_ids')->nullable();

            // Мэдээний агуулга
            $table->text('content');

            // Файл хавсралт (PDF эсвэл зураг)
            $table->string('attachment')->nullable();

            // Илгээсэн хэрэглэгч
            $table->foreignId('created_user_id')->constrained('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telephone_messages');
    }
};
