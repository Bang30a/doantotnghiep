<?php

namespace App\Services\Teacher;

use App\Models\Exam;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ExamManagementService
{
    public function storeExam(array $data)
    {
        $questions = json_decode($data['ai_questions_data'], true);
        if (empty($questions)) {
            throw new \Exception('Dữ liệu câu hỏi bị trống.');
        }

        $exam = Exam::create([
            'teacher_id' => Auth::id(), 
            'classroom_id' => $data['classroom_id'] ?? null,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'duration' => $data['duration'],
            'total_questions' => count($questions),
        ]);

        $this->saveQuestions($exam, $questions);
        $this->logActivity('exam_created', 'Giảng viên tạo đề thi mới', 'vừa tạo đề thi', $exam->title, 'bi-journal-plus', 'purple');

        return $exam;
    }

    public function updateExam(Exam $exam, array $data)
    {
        $questions = json_decode($data['ai_questions_data'], true);
        if (empty($questions)) {
            throw new \Exception('Dữ liệu câu hỏi bị trống.');
        }

        $exam->update([
            'classroom_id' => $data['classroom_id'] ?? null,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'duration' => $data['duration'],
            'total_questions' => count($questions),
        ]);

        // Xóa câu hỏi cũ và tạo lại
        $exam->questions()->delete();
        $this->saveQuestions($exam, $questions);
        $this->logActivity('exam_updated', 'Giảng viên cập nhật đề thi', 'vừa chỉnh sửa đề thi', $exam->title, 'bi-pencil-square', 'info');

        return $exam;
    }

    public function deleteExam(Exam $exam)
    {
        $title = $exam->title;
        $exam->delete();
        $this->logActivity('exam_deleted', 'Giảng viên xóa đề thi', 'đã xóa đề thi', $title, 'bi-trash-fill', 'danger');
    }

    private function saveQuestions(Exam $exam, array $questions)
    {
        foreach ($questions as $qData) {
            $question = $exam->questions()->create([
                'content' => $qData['content'],
                'type' => $qData['type'] ?? 'multiple_choice',
                'ai_explanation' => $qData['explanation'] ?? ($qData['ai_explanation'] ?? null),
            ]);

            if (!empty($qData['answers'])) {
                foreach ($qData['answers'] as $ansData) {
                    $question->answers()->create([
                        'content' => $ansData['content'],
                        'is_correct' => $ansData['is_correct']
                    ]);
                }
            }
        }
    }

    private function logActivity($type, $title, $actionText, $examTitle, $icon, $color)
    {
        ActivityLog::create([
            'type' => $type,
            'title' => $title,
            'description' => 'Giảng viên <strong>' . Auth::user()->name . '</strong> ' . $actionText . ' <span class="text-' . ($color == 'danger' ? 'danger' : 'primary') . ' fw-bold">"' . $examTitle . '"</span>.',
            'icon_class' => $icon,
            'color_theme' => $color
        ]);
    }
}