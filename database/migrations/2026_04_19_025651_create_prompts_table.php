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
        Schema::create('prompts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tên gợi nhớ (VD: Tạo đề Toán)
            $table->text('description')->nullable(); // Mô tả chi tiết
            $table->string('model_type')->default('gpt-3.5-turbo'); // Loại AI sử dụng
            $table->text('prompt_text'); // Nội dung câu lệnh gốc gửi cho AI
            $table->string('status')->default('active'); // Trạng thái: active, testing, hidden
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prompts');
    }
};
