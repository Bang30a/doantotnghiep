<?php

namespace App\Http\Controllers\Student;

use App\Services\Student\StatisticService;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class StatisticController extends Controller
{
    protected $statisticService;

    public function __construct(StatisticService $statisticService)
    {
        $this->statisticService = $statisticService;
    }

    public function history()
    {
        // Gọi Service để lấy toàn bộ dữ liệu thống kê lịch sử
        $stats = $this->statisticService->getHistoryStats(Auth::id());

        return view('dashboards.student.student_history', $stats);
    }

    public function statistics()
    {
        // Gọi Service để lấy toàn bộ dữ liệu cho trang Dashboard
        $stats = $this->statisticService->getDashboardStats(Auth::id());
        
        // Thêm cài đặt global
        $stats['globalSettings'] = ['site_name' => 'EduQuiz AI'];

        return view('dashboards.student.student_statistics', $stats);
    }
}