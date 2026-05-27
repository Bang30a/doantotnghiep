<?php

namespace App\Services\Admin;

use App\Models\AiLog;

class AiHistoryService
{
    // Định nghĩa hằng số giá token tại đây. Sau này đổi giá chỉ cần sửa 1 dòng này!
    const COST_PER_1K_TOKENS = 0.002; 

    public function getHistoryStats($perPage = 10)
    {
        // ĐÃ SỬA: Dùng AiLog thay vì \App\Models\AiHistory
        // Kèm thêm with('user') để tối ưu truy vấn database (chống lỗi N+1)
        $histories = AiLog::with('user')->orderBy('created_at', 'desc')->paginate($perPage);
        
        $totalTokens = AiLog::where('status', 'success')->sum('tokens');
        
        // Sử dụng hằng số để tính toán
        $totalCost = ($totalTokens / 1000) * self::COST_PER_1K_TOKENS;

        return compact('histories', 'totalTokens', 'totalCost');
    }

    public function getExportData()
    {
        $logs = AiLog::with('user')->orderBy('created_at', 'desc')->get();

        $data = [];
        foreach ($logs as $log) {
            $cost = 0;
            if ($log->status == 'success') {
                // Sử dụng lại hằng số tính tiền
                $cost = ($log->tokens / 1000) * self::COST_PER_1K_TOKENS;
            }

            $data[] = [
                $log->id,
                $log->user->name ?? 'User đã xóa',
                $log->action,
                $log->model,
                $log->status,
                $log->tokens ?? 0,
                number_format($cost, 4),
                $log->created_at->format('d/m/Y H:i:s')
            ];
        }

        return $data;
    }
}