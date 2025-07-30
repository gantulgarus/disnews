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
        Schema::create('order_journals', function (Blueprint $table) {
            $table->id();
            $table->string('order_number'); // Дугаар
            $table->unsignedTinyInteger('status')->default(0); // Төлөв
            $table->unsignedBigInteger('organization_id'); // Байгууллагын ID
            $table->enum('order_type', ['Энгийн', 'Аваарын']); // Захиалгын төрөл
            $table->text('content')->nullable(); // Засварын ажлын агуулга

            $table->date('planned_start_date')->nullable(); // Захиалгат эхлэх хугацаа
            $table->date('planned_end_date')->nullable(); // Захиалгат дуусах хугацаа

            $table->string('approver_name')->nullable(); // Баталсан хүний нэр
            $table->string('approver_position')->nullable(); // Баталсан хүний албан тушаал

            $table->date('real_start_date')->nullable(); // Бодит эхлэсэн хугацаа
            $table->date('real_end_date')->nullable(); // Бодит дууссан хугацаа

            $table->unsignedBigInteger('created_user_id'); // Бүртгэсэн хүний ID

            $table->timestamps();

            // Optional: foreign key тохиргоо
            $table->foreign('organization_id')->references('id')->on('organizations');
            $table->foreign('created_user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_journals');
    }
};