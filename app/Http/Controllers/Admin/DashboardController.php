<?php

namespace App\Http\Controllers\Admin; 

use App\Http\Controllers\Controller;
use App\Services\Admin\DashboardService;

use Illuminate\Http\Request;


class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        // Lấy toàn bộ cục data từ Service ném sang View
        $data = $this->dashboardService->getDashboardStats();

        return view('dashboards.admin.admin_dashboard', $data);
    }

    public function activities(Request $request)
    {
        $activities = $this->dashboardService->getPaginatedActivities(15);

        return view('dashboards.admin.admin_activities', compact('activities'));
    }
    public function settings()
    {
        // Thêm (array) để ép Object thành Mảng, giúp file Blade đọc được bình thường
        $settings = (array) \Illuminate\Support\Facades\DB::table('settings')->first(); 

        // ==========================================
        // TÍNH TỔNG DUNG LƯỢNG FILE THỰC TẾ TRÊN Ổ CỨNG
        // ==========================================
        $totalBytes = 0;
        $documents = \App\Models\Document::all();
        
        foreach ($documents as $doc) {
            $path = public_path($doc->file_path);
            if (!empty($doc->file_path) && file_exists($path) && is_file($path)) {
                $totalBytes += filesize($path);
            }
        }
        
        $totalMB = number_format($totalBytes / 1048576, 2);

        return view('dashboards.admin.admin_settings', compact('settings', 'totalMB'));
    }
}