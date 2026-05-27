<?php
namespace App\Services\Teacher;

use App\Models\Classroom;
use App\Models\Exam;
use App\Models\Result;

class ClassroomStatisticsService
{
    public function getStudentClassroomStats($classroom, $user)
    {
        $examIds = $classroom->exams->pluck('id');
        $results = Result::where('user_id', $user->id)->whereIn('exam_id', $examIds)->get();
        
        $totalExams = $classroom->exams->count();
        $completedExams = $results->count();
        $pendingExams = $totalExams - $completedExams;
        
        $averageScore = 0;
        if ($completedExams > 0) {
            $totalScore10 = $results->sum(function($r) {
                return ($r->score / max(1, $r->total_questions)) * 10;
            });
            $averageScore = $totalScore10 / $completedExams;
        }

        return compact('results', 'totalExams', 'completedExams', 'pendingExams', 'averageScore');
    }

    public function getTeacherDashboardStats($teacherId)
    {
        $classrooms = Classroom::where('teacher_id', $teacherId)
            ->withCount(['users', 'exams'])->with(['exams', 'users'])->get();

        $examIds = Exam::where('teacher_id', $teacherId)->pluck('id');
        $results = Result::whereIn('exam_id', $examIds)->get();

        $totalStudents = $classrooms->sum('users_count');
        $activeStudentsCount = $results->pluck('user_id')->unique()->count();
        
        // Tính điểm trung bình
        $averageScore = 0;
        if ($results->count() > 0) {
            $totalScore10 = $results->sum(function($r) {
                return ($r->score / max(1, $r->total_questions)) * 10;
            });
            $averageScore = $totalScore10 / $results->count();
        }

        // Tỷ lệ hoàn thành
        $totalExpectedSubmissions = $classrooms->sum(function($room) {
            return $room->users_count * $room->exams_count;
        });
        $completionRate = $totalExpectedSubmissions > 0 
            ? min(100, ($results->count() / $totalExpectedSubmissions) * 100) 
            : 0;

        // Xử lý dữ liệu Chart
        $chartData = $this->prepareChartData($classrooms, $results);

        return compact('totalStudents', 'activeStudentsCount', 'averageScore', 'completionRate', 'chartData');
    }

    private function prepareChartData($classrooms, $results)
    {
        $chartClassNames = [];
        $chartClassScores = [];

        foreach ($classrooms as $room) {
            $chartClassNames[] = $room->name;
            $roomExamIds = $room->exams->pluck('id');
            $roomResults = Result::whereIn('exam_id', $roomExamIds)->get();
            
            if ($roomResults->count() > 0) {
                $score = $roomResults->sum(function($r) { 
                    return ($r->score / max(1, $r->total_questions)) * 10; 
                }) / $roomResults->count();
                $chartClassScores[] = round($score, 1);
            } else {
                $chartClassScores[] = 0;
            }
        }

        $scoreDistribution = ['0-4' => 0, '4-5' => 0, '5-6' => 0, '6-7' => 0, '7-8' => 0, '8-9' => 0, '9-10' => 0];
        foreach ($results as $r) {
            $score = ($r->score / max(1, $r->total_questions)) * 10;
            if ($score < 4) $scoreDistribution['0-4']++;
            elseif ($score < 5) $scoreDistribution['4-5']++;
            elseif ($score < 6) $scoreDistribution['5-6']++;
            elseif ($score < 7) $scoreDistribution['6-7']++;
            elseif ($score < 8) $scoreDistribution['7-8']++;
            elseif ($score < 9) $scoreDistribution['8-9']++;
            else $scoreDistribution['9-10']++;
        }

        return [
            'classNames' => $chartClassNames,
            'classScores' => $chartClassScores,
            'scoreDistributionKeys' => array_keys($scoreDistribution),
            'scoreDistributionValues' => array_values($scoreDistribution),
        ];
    }
}