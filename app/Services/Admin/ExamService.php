<?php

namespace App\Services\Admin;

use App\Models\Exam;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ExamService
{
    /**
     * Lấy danh sách đề thi có phân trang và tìm kiếm
     */
    public function getPaginatedExams($searchQuery = null, $perPage = 10)
    {
        $query = Exam::with('teacher')->withCount('questions');

        if (!empty($searchQuery)) {
            $query->where('title', 'like', '%' . $searchQuery . '%');
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Lấy chi tiết đề thi kèm câu hỏi, đáp án, người tạo và lớp học
     */
    public function getExamDetail($id)
    {
        return Exam::with(['questions.answers', 'teacher', 'classroom'])->findOrFail($id);
    }

    /**
     * Xóa đề thi và ghi log hệ thống
     */
    public function deleteExam($id)
    {
        $exam = Exam::findOrFail($id);
        $examTitle = $exam->title; 
        
        $exam->delete();

        ActivityLog::create([
            'type' => 'admin_deleted_exam',
            'title' => 'Admin xóa đề thi',
            'description' => 'Quản trị viên <strong>' . Auth::user()->name . '</strong> đã xóa vĩnh viễn đề thi <span class="text-danger fw-bold">"' . $examTitle . '"</span> khỏi hệ thống.',
            'icon_class' => 'bi-trash2-fill',
            'color_theme' => 'danger'
        ]);
    }
}