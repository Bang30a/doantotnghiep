<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            if (!Schema::hasColumn('exams', 'source_exam_id')) {
                $table->foreignId('source_exam_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('exams')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('exams', 'variant_group')) {
                $table->string('variant_group')->nullable()->after('source_exam_id');
            }

            if (!Schema::hasColumn('exams', 'variant_number')) {
                $table->unsignedInteger('variant_number')->nullable()->after('variant_group');
            }

            if (!Schema::hasColumn('exams', 'variant_count')) {
                $table->unsignedInteger('variant_count')->nullable()->after('variant_number');
            }

            if (!Schema::hasColumn('exams', 'shuffle_questions')) {
                $table->boolean('shuffle_questions')->default(false)->after('variant_count');
            }
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            if (Schema::hasColumn('exams', 'source_exam_id')) {
                $table->dropConstrainedForeignId('source_exam_id');
            }

            foreach (['variant_group', 'variant_number', 'variant_count', 'shuffle_questions'] as $column) {
                if (Schema::hasColumn('exams', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
