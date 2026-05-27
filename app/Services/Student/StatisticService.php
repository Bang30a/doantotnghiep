<?php

namespace App\Services\Student;

use App\Models\Result;

class StatisticService
{
    public function getHistoryStats($userId)
    {
        $results = Result::where('user_id', $userId)->with('exam')->latest()->get();

        $totalExams = $results->count();
        $averageScore = 0;
        $highestScore = 0;

        if ($totalExams > 0) {
            // Đã thêm max(1, $r->total_questions) để chống lỗi chia cho 0
            $averageScore = $results->sum(function($r) { 
                return ($r->score / max(1, $r->total_questions)) * 10; 
            }) / $totalExams;
            
            $highestScore = $results->max(function($r) { 
                return ($r->score / max(1, $r->total_questions)) * 10; 
            });
        }

        return compact('results', 'totalExams', 'averageScore', 'highestScore');
    }

    public function getDashboardStats($userId)
    {
        $results = Result::with('exam')->where('user_id', $userId)->get();
        
        $totalCompleted = $results->count();
        $averageScore = 0; 
        $accuracyRate = 0; 
        $totalTime = 0;
        
        if ($totalCompleted > 0) {
            $averageScore = $results->sum(function($r) { 
                return ($r->score / max(1, $r->total_questions)) * 10; 
            }) / $totalCompleted;
            
            $accuracyRate = ($results->sum('score') / max(1, $results->sum('total_questions'))) * 100;
            
            $totalTime = $results->sum(function($r) { 
                return $r->exam ? $r->exam->duration : 0; 
            });
        }
        
        $hours = floor($totalTime / 60);
        $minutes = $totalTime % 60;

        $chartData = $this->prepareChartData($results);
        $recentResults = Result::with('exam')->where('user_id', $userId)->latest()->take(5)->get();

        return compact('totalCompleted', 'averageScore', 'accuracyRate', 'hours', 'minutes', 'chartData', 'recentResults');
    }

    // Tách riêng hàm xử lý biểu đồ cho dễ đọc
    private function prepareChartData($results)
    {
        $months = []; 
        $scoreData = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $months[] = 'Tháng ' . $month->format('n');
            
            $monthlyResults = $results->filter(function($r) use ($month) { 
                return $r->created_at->format('Y-m') === $month->format('Y-m'); 
            });
            
            if ($monthlyResults->count() > 0) {
                $avg = $monthlyResults->sum(function($r) { 
                    return ($r->score / max(1, $r->total_questions)) * 10; 
                }) / $monthlyResults->count();
                $scoreData[] = round($avg, 1);
            } else {
                $scoreData[] = 0;
            }
        }

        $subjectCounts = [];
        foreach ($results as $r) {
            $subject = ($r->exam && $r->exam->subject) ? $r->exam->subject : 'Môn chung';
            if (!isset($subjectCounts[$subject])) $subjectCounts[$subject] = 0;
            $subjectCounts[$subject]++;
        }
        
        return [
            'line' => ['labels' => $months, 'data' => $scoreData],
            'pie' => ['labels' => array_keys($subjectCounts), 'data' => array_values($subjectCounts)]
        ];
    }
}