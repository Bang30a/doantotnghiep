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
    Schema::table('student_answers', function (Blueprint $table) {
        $table->float('ai_score')->nullable()->after('score');
        $table->text('ai_feedback')->nullable()->after('feedback');
    });
}

public function down()
{
    Schema::table('student_answers', function (Blueprint $table) {
        $table->dropColumn(['ai_score', 'ai_feedback']);
    });
}
};
