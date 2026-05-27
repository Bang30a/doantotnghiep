<?php

namespace App\Services\Student;

use App\Models\Exam;
use App\Models\Question;
use App\Models\Answer;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SelfPracticeService
{
    public function createPracticeExam(array $data, $userId)
    {
        $questionsData = json_decode($data['ai_questions_data'], true);
        if (!$questionsData || count($questionsData) == 0) {
            throw new \Exception('Chưa có câu hỏi!');
        }

        return DB::transaction(function () use ($data, $questionsData, $userId) {
            $exam = Exam::create([
                'teacher_id' => $userId, 
                'classroom_id' => null,     
                'document_id' => $data['document_id'] ?? null,
                'title' => $data['title'],
                'subject' => $data['subject'] ?? null,
                'duration' => $data['duration'],
            ]);

            foreach ($questionsData as $qData) {
                $question = Question::create([
                    'exam_id' => $exam->id,
                    'content' => $qData['content'] ?? 'Câu hỏi lỗi',
                    'difficulty' => 'medium',
                    'type' => $qData['type'] ?? 'multiple_choice',
                    'ai_explanation' => $qData['ai_explanation'] ?? null,
                ]);

                if (isset($qData['answers']) && is_array($qData['answers'])) {
                    foreach ($qData['answers'] as $aData) {
                        Answer::create([
                            'question_id' => $question->id,
                            'content' => $aData['content'] ?? 'Đáp án lỗi',
                            'is_correct' => filter_var($aData['is_correct'], FILTER_VALIDATE_BOOLEAN), 
                        ]);
                    }
                }
            }

            ActivityLog::create([
                'type' => 'student_exam_created',
                'title' => 'Học viên tạo đề tự luyện',
                'description' => 'Học viên <strong>' . Auth::user()->name . '</strong> đã sử dụng AI tạo đề tự luyện <span class="text-primary fw-bold">"' . $exam->title . '"</span>.',
                'icon_class' => 'bi-robot',
                'color_theme' => 'purple'
            ]);

            return $exam;
        });
    }

    public function deletePracticeExam(Exam $exam)
    {
        $examTitle = $exam->title;
        $exam->delete();

        ActivityLog::create([
            'type' => 'student_exam_deleted',
            'title' => 'Học viên xóa đề tự luyện',
            'description' => 'Học viên <strong>' . Auth::user()->name . '</strong> đã xóa đề <span class="text-danger fw-bold">"' . $examTitle . '"</span> trong kho lưu trữ.',
            'icon_class' => 'bi-trash3-fill',
            'color_theme' => 'danger'
        ]);
    }
}