<?php

namespace App\Services\Student;

use App\Models\Exam;
use App\Models\Answer;
use App\Models\Result;
use App\Models\StudentAnswer;
use App\Models\ActivityLog;
use App\Support\RichTextSanitizer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ExamSubmissionService
{
    public function submitExam(Exam $exam, array $inputData, $userId)
    {
        // Gói toàn bộ thao tác chấm điểm vào Transaction
        return DB::transaction(function () use ($exam, $inputData, $userId) {
            $score = 0;
            $totalQuestions = $exam->questions->count();

            $result = Result::create([
                'user_id' => $userId,
                'exam_id' => $exam->id,
                'score' => 0,
                'total_questions' => $totalQuestions,
            ]);

            foreach ($exam->questions as $question) {
                $selectedInput = $inputData['question_' . $question->id] ?? null;

                if ($selectedInput) {
                    if ($question->type === 'essay') {
                        $content = RichTextSanitizer::sanitize($selectedInput);

                        if (RichTextSanitizer::isBlank($content)) {
                            continue;
                        }

                        StudentAnswer::create([
                            'exam_result_id' => $result->id,
                            'question_id' => $question->id,
                            'content' => $content,
                            'answer_id' => null
                        ]);
                    } else {
                        // Trắc nghiệm
                        $isCorrect = Answer::where('id', $selectedInput)->where('is_correct', true)->exists();
                        if ($isCorrect) $score++;

                        StudentAnswer::create([
                            'exam_result_id' => $result->id,
                            'question_id' => $question->id,
                            'answer_id' => $selectedInput, 
                            'content' => null
                        ]);
                    }
                }
            }

            // Cập nhật điểm cuối cùng
            $result->update(['score' => $score]);

            ActivityLog::create([
                'type' => 'exam_submitted',
                'title' => 'Học viên nộp bài thi',
                'description' => 'Học viên <strong>' . Auth::user()->name . '</strong> vừa nộp bài thi <span class="text-primary fw-bold">"' . $exam->title . '"</span>.',
                'icon_class' => 'bi-send-check-fill',
                'color_theme' => 'emerald'
            ]);

            return $result;
        });
    }
}
