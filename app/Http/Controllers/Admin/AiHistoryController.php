<?php

namespace App\Http\Controllers\Admin; 

use App\Http\Controllers\Controller;
use App\Services\Admin\AiHistoryService;
use App\Exports\SystemExport;

use Illuminate\Http\Request;

use Maatwebsite\Excel\Facades\Excel;


class AiHistoryController extends Controller
{
    protected $aiHistoryService;

    // Tiêm Service vào Controller
    public function __construct(AiHistoryService $aiHistoryService)
    {
        $this->aiHistoryService = $aiHistoryService;
    }

    public function index(Request $request)
    {
        // Nhận số lượng hiển thị từ thanh chọn (mặc định là 10)
        $perPage = $request->get('per_page', 10);

        // Truyền cái $perPage này vào trong Service
        $data = $this->aiHistoryService->getHistoryStats($perPage);

        return view('dashboards.admin.admin_ai_history', $data);
    }

    // ==========================================
    // Hàm Xuất Báo cáo CSV
    // ==========================================
    public function exportCsv()
    {
        // Lấy mảng dữ liệu đã được Service map sẵn
        $data = $this->aiHistoryService->getExportData();

        $headings = [
            'ID', 'Người yêu cầu', 'Hành động', 'Model AI', 
            'Trạng thái', 'Số Tokens', 'Chi phí (USD)', 'Thời gian'
        ];

        $filename = "Bao_cao_AI_API_" . date('Ymd_His') . ".xlsx";
        $title = "Báo cáo lịch sử AI API";

        return Excel::download(new SystemExport($title, $headings, $data), $filename);
    }
}