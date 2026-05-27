<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Phóng to cột content thành TEXT để chứa văn bản dài (Bareme, Bài làm tự luận, Câu hỏi dài)
        DB::statement('ALTER TABLE answers MODIFY content TEXT');
        DB::statement('ALTER TABLE questions MODIFY content TEXT');
        
        // Mở rộng luôn bảng bài làm của sinh viên (cho phép Null vì trắc nghiệm chỉ lưu answer_id)
        DB::statement('ALTER TABLE student_answers MODIFY content TEXT NULL');
        
        // Mở rộng cột giải thích của AI (nếu có)
        DB::statement('ALTER TABLE questions MODIFY ai_explanation TEXT NULL');
    }

    public function down(): void
    {
        // Rollback lại VARCHAR(255) nếu cần
        DB::statement('ALTER TABLE answers MODIFY content VARCHAR(255)');
        DB::statement('ALTER TABLE questions MODIFY content VARCHAR(255)');
        DB::statement('ALTER TABLE student_answers MODIFY content VARCHAR(255) NULL');
        DB::statement('ALTER TABLE questions MODIFY ai_explanation VARCHAR(255) NULL');
    }
};