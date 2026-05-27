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
    Schema::create('activity_logs', function (Blueprint $table) {
        $table->id();
        $table->string('type'); // Ví dụ: 'user_registered', 'exam_created', 'system_warning'
        $table->string('title'); // Tiêu đề log (Ví dụ: "Người dùng mới đăng ký")
        $table->text('description'); // Chi tiết (Ví dụ: "Học viên Nguyễn Văn A...")
        $table->string('icon_class')->nullable(); // Class Bootstrap Icon (Ví dụ: "bi-person-plus-fill")
        $table->string('color_theme')->default('primary'); // Màu sắc (emerald, purple, orange...)
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
