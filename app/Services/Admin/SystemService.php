<?php

namespace App\Services\Admin;

use App\Models\User;
use App\Models\Exam;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class SystemService
{
    // Tối ưu hóa đếm User (Không tải toàn bộ vào RAM)
    public function getDashboardStats()
    {
        return [
            'totalUsers' => User::count(),
            'studentsCount' => User::where('role', 'student')->count(),
            'teachersCount' => User::where('role', 'teacher')->count(),
            'todayActivity' => ActivityLog::whereDate('created_at', Carbon::today())->count(),
            'recentActivities' => ActivityLog::latest()->take(5)->get() // Model ActivityLog đã có hàm getTimeAgoAttribute từ bài trước
        ];
    }

    public function toggleUserLock($id)
    {
        $user = User::findOrFail($id);
        
        // Xử lý cả 2 trường hợp status là số hoặc chữ
        if (is_numeric($user->status)) {
            $newStatus = ($user->status == 1) ? 0 : 1;
        } else {
            $newStatus = ($user->status === 'locked') ? 'active' : 'locked';
        }
        
        // Dùng Eloquent thay vì DB::table
        $user->update(['status' => $newStatus]);

        $isLocked = ($newStatus == 0 || $newStatus === 'locked');
        
        ActivityLog::create([
            'type' => 'user_status_changed',
            'title' => 'Thay đổi trạng thái tài khoản',
            'description' => 'Quản trị viên <strong>' . Auth::user()->name . '</strong> đã ' . ($isLocked ? 'khóa' : 'mở khóa') . ' tài khoản <span class="text-dark fw-bold">"' . $user->name . '"</span>.',
            'icon_class' => $isLocked ? 'bi-lock-fill' : 'bi-unlock-fill',
            'color_theme' => $isLocked ? 'danger' : 'success'
        ]);

        return $isLocked ? 'Đã khóa tài khoản thành công!' : 'Đã mở khóa tài khoản!';
    }

    public function getSystemSettings()
    {
        $settings = DB::table('settings')->pluck('value', 'key')->toArray();
        $examCount = Exam::count(); 
        
        $documentCount = Schema::hasTable('documents') ? DB::table('documents')->count() : 0;
        $questionCount = Schema::hasTable('questions') ? DB::table('questions')->count() : 0;

        // Tính dung lượng (nên có cơ chế Cache lại thay vì quét liên tục, nhưng tạm thời để nguyên logic của bác)
        $storagePath = storage_path('app');
        $usedSpaceBytes = 0;
        
        if (file_exists($storagePath)) {
            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($storagePath)) as $file) {
                if ($file->isFile()) {
                    $usedSpaceBytes += $file->getSize();
                }
            }
        }
        
        $usedSpaceMB = round($usedSpaceBytes / 1048576, 2);
        $totalSpaceGB = 10; 
        $usedPercentage = min(100, round(($usedSpaceMB / ($totalSpaceGB * 1024)) * 100, 2));

        return compact('settings', 'examCount', 'documentCount', 'questionCount', 'usedSpaceMB', 'totalSpaceGB', 'usedPercentage');
    }

    public function updateSettings(array $data)
    {
        $data['allow_registration'] = !empty($data['allow_registration']) ? '1' : '0';
        $data['require_email_verify'] = !empty($data['require_email_verify']) ? '1' : '0';

        foreach ($data as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key], 
                ['value' => $value, 'updated_at' => now()] 
            );
        }

        ActivityLog::create([
            'type' => 'system_settings_updated',
            'title' => 'Cập nhật cấu hình hệ thống',
            'description' => 'Quản trị viên <strong>' . Auth::user()->name . '</strong> đã lưu thay đổi ở trang Cài đặt chung.',
            'icon_class' => 'bi-gear-fill',
            'color_theme' => 'secondary'
        ]);
    }
}