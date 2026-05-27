<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_answers', function (Blueprint $table) {
            // 1. Gỡ bỏ khóa ngoại cũ đang trỏ sai (trỏ vào exam_results)
            $table->dropForeign('student_answers_exam_result_id_foreign');
            
            // 2. Cắm lại khóa ngoại mới, trỏ chuẩn xác vào bảng results
            $table->foreign('exam_result_id')
                  ->references('id')
                  ->on('results')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('student_answers', function (Blueprint $table) {
            $table->dropForeign(['exam_result_id']);
            // Phục hồi lại như cũ nếu rollback
            $table->foreign('exam_result_id')->references('id')->on('exam_results')->onDelete('cascade');
        });
    }
};