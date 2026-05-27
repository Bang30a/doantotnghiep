<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_answers', function (Blueprint $table) {
            // Thêm cột lưu điểm (chữ số thập phân, VD: 2.5)
            $table->decimal('score', 5, 2)->nullable()->after('content');
            // Thêm cột lưu lời phê của Giảng viên
            $table->text('feedback')->nullable()->after('score');
        });
    }

    public function down(): void
    {
        Schema::table('student_answers', function (Blueprint $table) {
            $table->dropColumn(['score', 'feedback']);
        });
    }
};