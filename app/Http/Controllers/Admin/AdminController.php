<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateTeacherRequest;

use App\Services\Admin\SystemService;

use App\Models\ActivityLog;
use App\Models\User;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class AdminController extends Controller
{
    protected $systemService;

    public function __construct(SystemService $systemService)
    {
        $this->systemService = $systemService;
    }

    // ==========================================
    // TRANG CHỦ BẢNG ĐIỀU KHIỂN (DASHBOARD)
    // ==========================================
    public function index()
    {
        $stats = $this->systemService->getDashboardStats();
        return view('dashboards.admin.admin_dashboard', $stats);
    }

    // ==========================================
    // QUẢN LÝ GIẢNG VIÊN 
    // ==========================================
    public function showTeacher($id)
    {
        $teacher = User::findOrFail($id);
        return view('dashboards.admin.admin_teacher_detail', compact('teacher'));
    }

    public function updateTeacher(UpdateTeacherRequest $request, $id)
    {
        $teacher = User::findOrFail($id);
        $teacher->name = $request->name;
        $teacher->phone = $request->phone;
        $teacher->status = $request->status;

        if ($request->filled('password')) {
            $teacher->password = Hash::make($request->password); // Dùng Hash::make thay cho bcrypt
        }
        $teacher->save();

        ActivityLog::create([
            'type' => 'admin_updated_teacher',
            'title' => 'Admin cập nhật giảng viên',
            'description' => 'Quản trị viên <strong>' . Auth::user()->name . '</strong> đã cập nhật thông tin của giảng viên <span class="text-primary fw-bold">"' . $teacher->name . '"</span>.',
            'icon_class' => 'bi-person-workspace',
            'color_theme' => 'primary'
        ]);

        return back()->with('success', 'Đã cập nhật thông tin giảng viên thành công!');
    }

    // ==========================================
    // QUẢN LÝ NGƯỜI DÙNG CHUNG (KHÓA/MỞ KHÓA)
    // ==========================================
    public function manageUsers()
    {
        // Lấy danh sách users không cần tải toàn bộ relation nếu không cần thiết
        $users = User::latest()->get();
        
        $totalUsers = $users->count();
        $activeUsers = $users->where('status', '!=', 'locked')->where('status', '!=', 0)->count(); 
        $lockedUsers = $totalUsers - $activeUsers;

        return view('dashboards.admin.admin_users', compact('users', 'totalUsers', 'activeUsers', 'lockedUsers'));
    }

    public function toggleLock($id)
    {
        $message = $this->systemService->toggleUserLock($id);
        
        return back()->with('success', $message);
    }

    // ==========================================
    // CÀI ĐẶT HỆ THỐNG
    // ==========================================
    public function settings() 
    {
        $settingsData = $this->systemService->getSystemSettings();
        
        return view('dashboards.admin.admin_settings', $settingsData);
    }

    public function updateSettings(Request $request) 
    {
        $this->systemService->updateSettings($request->except('_token'));

        return back()->with('success', 'Đã lưu cấu hình tab Chung thành công!');
    }
}