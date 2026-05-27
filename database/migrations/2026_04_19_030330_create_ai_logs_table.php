<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('ai_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Ai là người dùng API
            $table->string('action'); // Hành động (VD: Tạo đề Toán lớp 10)
            $table->string('model')->default('gpt-3.5-turbo'); // Dùng AI nào
            $table->integer('tokens')->default(0); // Số token đã tiêu thụ
            $table->string('status')->default('success'); // success hoặc failed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_logs');
    }
};
