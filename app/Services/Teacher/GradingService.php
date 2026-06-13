<?php

namespace App\Services\Teacher;

use App\Models\Result;
use App\Models\StudentAnswer;
use App\Models\Question;
use App\Models\Answer;
use App\Models\ActivityLog;
use App\Support\RichTextSanitizer;
use Illuminate\Support\Facades\Auth;

class GradingService
{
    public function updateGrade($result_id, $question_id, $score, $feedback)
    {
        // 1. Cập nhật điểm câu tự luận
        StudentAnswer::where('exam_result_id', $result_id)
            ->where('question_id', $question_id)
            ->update([
                'score' => $score,
                'feedback' => RichTextSanitizer::sanitize($feedback)
            ]);

        // 2. Tính lại tổng điểm
        $result = Result::findOrFail($result_id);
        $totalScore = 0;
        $allAnswers = StudentAnswer::where('exam_result_id', $result_id)->get();
        
        foreach($allAnswers as $ans) {
            $question = Question::find($ans->question_id);
            if($question) {
                if($question->type == 'multiple_choice') {
                    $isCorrect = Answer::where('id', $ans->answer_id)->where('is_correct', true)->exists();
                    if($isCorrect) $totalScore += 1; 
                } else if ($question->type == 'essay') {
                    $totalScore += floatval($ans->score);
                }
            }
        }
        
        $result->update(['score' => $totalScore]);

        // 3. Ghi Log
        ActivityLog::create([
            'type' => 'exam_graded',
            'title' => 'Giảng viên chấm điểm',
            'description' => 'Giảng viên <strong>' . Auth::user()->name . '</strong> đã chấm điểm bài tự luận cho học viên <strong>' . $result->user->name . '</strong>.',
            'icon_class' => 'bi-check2-all',
            'color_theme' => 'success'
        ]);
    }
}
