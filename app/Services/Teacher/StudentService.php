<?php

namespace App\Services\Teacher;

use App\Models\User;
use App\Models\Result;
use App\Models\Classroom;
use Illuminate\Support\Facades\Auth;

class StudentService
{
    /**
     * Hàm dùng chung để tạo query lấy danh sách học viên của một Giáo viên
     */
    private function getBaseStudentQuery($teacherId)
    {
        return User::where('role', 'student')
            ->whereHas('classrooms', function ($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId);
            });
    }

    public function getStudentsData($teacherId)
    {
        // 1. Lấy danh sách phân trang (kèm theo class và results)
        $students = $this->getBaseStudentQuery($teacherId)
            ->with(['classrooms' => function ($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId); // Chỉ lấy lớp của GV này
            }, 'results' => function ($query) use ($teacherId) {
                $query->whereHas('exam', function ($examQuery) use ($teacherId) {
                    $examQuery->where('teacher_id', $teacherId);
                })->with('exam.questions');
            }])
            ->paginate(10);

        $students->getCollection()->each(function ($student) {
            $completedCount = $student->results->count();
            $totalScore = $student->results->sum(function ($result) {
                return $this->score10($result);
            });

            $student->setAttribute('completed_count', $completedCount);
            $student->setAttribute('average_score10', $completedCount > 0 ? round($totalScore / $completedCount, 1) : 0);
        });

        // 2. Tổng số học viên (Lấy luôn từ object Paginator, tiết kiệm 1 query)
        $totalStudents = $students->total();

        // 3. Đếm học viên mới trong 7 ngày
        $newStudentsThisWeek = $this->getBaseStudentQuery($teacherId)
            ->where('users.created_at', '>=', now()->subDays(7))
            ->count();

        return compact('students', 'totalStudents', 'newStudentsThisWeek');
    }

    public function getExportData($teacherId)
    {
        $students = $this->getBaseStudentQuery($teacherId)
            ->with(['results' => function ($query) use ($teacherId) {
                $query->whereHas('exam', function ($examQuery) use ($teacherId) {
                    $examQuery->where('teacher_id', $teacherId);
                })->with('exam.questions');
            }])
            ->get();

        $data = [];
        foreach ($students as $student) {
            $completed = $student->results->count();
            $totalScore = 0;
            
            foreach ($student->results as $r) {
                $totalScore += $this->score10($r);
            }
            
            $avg = $completed > 0 ? round($totalScore / $completed, 1) : 0;

            $data[] = [
                $student->id, 
                $student->name, 
                $student->email, 
                $completed, 
                $avg
            ];
        }

        return $data;
    }

    public function getStudentDetailData($studentId, $teacherId)
    {
        $student = User::where('role', 'student')->findOrFail($studentId);

        // Lấy danh sách kết quả bài thi thuộc về giáo viên hiện tại
        $results = Result::with(['exam.questions', 'exam.classroom'])
            ->where('user_id', $studentId)
            ->whereHas('exam', function($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId);
            })->latest()->get();

        $completedCount = $results->count();
        $totalScore = 0;

        if ($completedCount > 0) {
            foreach($results as $r) {
                $totalScore += $this->score10($r);
            }
            $avg = round($totalScore / $completedCount, 1);
        } else {
            $avg = 0;
        }

        return compact('student', 'results', 'avg', 'completedCount');
    }

    private function score10(Result $result): float
    {
        $result->loadMissing('exam.questions');

        $hasEssay = $result->exam
            && $result->exam->questions
            && $result->exam->questions->where('type', 'essay')->count() > 0;

        if ($hasEssay) {
            return max(0, min(10, (float) $result->score));
        }

        return max(0, min(10, ((float) $result->score / max(1, (int) $result->total_questions)) * 10));
    }
}
