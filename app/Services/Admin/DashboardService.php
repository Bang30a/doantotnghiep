<?php

namespace App\Services\Admin;

use App\Models\User;
use App\Models\Classroom;
use App\Models\Exam;
use App\Models\Document;
use App\Models\AiLog;
use App\Models\ActivityLog;

class DashboardService
{
    public function getDashboardStats()
    {
        // 1. Thống kê số lượng
        $totalTeachers = User::where('role', 'teacher')->count();
        $totalStudents = User::where('role', 'student')->count();
        $totalClassrooms = Classroom::count();
        $totalExams = Exam::count();
        $totalDocuments = Document::count();

        // 2. Thống kê chi phí AI
        $totalAiTokens = AiLog::where('status', 'success')->sum('tokens');
        $totalAiCost = ($totalAiTokens / 1000) * 0.002; 

        // 3. Lấy dữ liệu gần đây
        $recentAiLogs = AiLog::with('user')->orderBy('created_at', 'desc')->take(5)->get();
        $recentActivities = ActivityLog::latest()->take(5)->get(); // Đã chuyển sang dùng Model

        return compact(
            'totalTeachers', 'totalStudents', 'totalClassrooms', 
            'totalExams', 'totalDocuments', 'totalAiTokens', 
            'totalAiCost', 'recentAiLogs', 'recentActivities'
        );
    }

    public function getPaginatedActivities($perPage = 15)
    {
        // Chuyển sang dùng Model thay cho DB::table
        return ActivityLog::latest()->paginate($perPage);
    }
}