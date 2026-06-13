<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prompts', function (Blueprint $table) {
            if (!Schema::hasColumn('prompts', 'exam_type')) {
                $table->string('exam_type')->default('both')->after('model_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('prompts', function (Blueprint $table) {
            if (Schema::hasColumn('prompts', 'exam_type')) {
                $table->dropColumn('exam_type');
            }
        });
    }
};
