<?php

namespace App\Services\Admin;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Lấy danh sách User phân trang theo Role, có tìm kiếm và tùy chỉnh số lượng hiển thị.
     */
    public function getPaginatedUsers($role, $searchQuery = null, $perPage = 10)
    {
        $query = User::where('role', $role);

        // Xử lý tìm kiếm
        if (!empty($searchQuery)) {
            $query->where(function($q) use ($searchQuery) {
                $q->where('name', 'like', '%' . $searchQuery . '%')
                  ->orWhere('email', 'like', '%' . $searchQuery . '%');
            });
        }

        // Tối ưu: Chỉ Học viên mới cần join lấy thống kê bài thi
        if ($role === 'student') {
            $query->withCount('results as mock_exams_done')
                  ->withAvg('results as mock_avg_score', 'score');
        }

        // Trả về phân trang với số lượng tùy biến từ request
        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Lấy chi tiết thông tin một học viên kèm thống kê.
     */
    public function getStudentDetail($id)
    {
        return User::withCount('results as mock_exams_done')
                   ->withAvg('results as mock_avg_score', 'score')
                   ->findOrFail($id);
    }
    /**
     * Lấy chi tiết thông tin một giảng viên kèm thống kê lớp và đề thi.
     */
    public function getTeacherDetail($id)
    {
        return User::withCount(['taughtClassrooms as classrooms_count', 'exams as exams_count'])
                   ->findOrFail($id);
    }

    /**
     * Cập nhật thông tin người dùng và ghi lại lịch sử hoạt động.
     */
    public function updateUser(User $user, array $data)
    {
        $user->name = $data['name'];
        if (isset($data['phone'])) $user->phone = $data['phone'];
        if (isset($data['status'])) $user->status = $data['status'];

        // Cập nhật mật khẩu nếu có (Sử dụng Hash::make chuẩn Laravel)
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        // Ghi Log hoạt động vào hệ thống
        ActivityLog::create([
            'type' => 'admin_updated_user',
            'title' => 'Admin cập nhật tài khoản',
            'description' => 'Quản trị viên <strong>' . Auth::user()->name . '</strong> đã chỉnh sửa thông tin của người dùng <span class="text-primary fw-bold">"' . $user->name . '"</span>.',
            'icon_class' => 'bi-person-gear',
            'color_theme' => 'primary'
        ]);

        return $user;
    }

    public function deleteUserByRole($id, string $role): string
    {
        $user = User::where('role', $role)->findOrFail($id);
        $name = $user->name;
        $roleLabel = $role === 'teacher' ? 'giảng viên' : 'học viên';

        $user->delete();

        ActivityLog::create([
            'type' => 'admin_deleted_user',
            'title' => 'Admin xóa tài khoản',
            'description' => 'Quản trị viên <strong>' . Auth::user()->name . '</strong> đã xóa vĩnh viễn tài khoản ' . $roleLabel . ' <span class="text-danger fw-bold">"' . $name . '"</span>.',
            'icon_class' => 'bi-trash3-fill',
            'color_theme' => 'danger'
        ]);

        return 'Đã xóa tài khoản ' . $roleLabel . ' thành công!';
    }
    /**
     * Khóa hoặc Mở khóa tài khoản người dùng
     */
    /**
     * Khóa hoặc Mở khóa tài khoản người dùng
     */
    public function toggleLock($id)
    {
        $user = User::findOrFail($id);
        
        // 1. Kiểm tra chính xác theo kiểu chuỗi (VARCHAR) trong Database của bác
        $isCurrentlyActive = ($user->status === 'active');
        
        // 2. Gán trạng thái mới bằng chữ: Đang 'active' -> đổi thành 'locked', ngược lại thì 'active'
        $newStatus = $isCurrentlyActive ? 'locked' : 'active';
        
        // 3. Gán và lưu lại
        $user->status = $newStatus;
        $user->save();

        // 4. Chuẩn bị nội dung để ghi Log (Dựa theo chữ active/locked)
        $actionText = ($newStatus === 'locked') ? 'khóa' : 'mở khóa';
        $colorTheme = ($newStatus === 'locked') ? 'warning' : 'success';
        $iconClass = ($newStatus === 'locked') ? 'bi-lock-fill' : 'bi-unlock-fill';

        // Ghi lại lịch sử hệ thống
        ActivityLog::create([
            'type' => 'admin_user_status',
            'title' => 'Thay đổi trạng thái tài khoản',
            'description' => 'Quản trị viên <strong>' . Auth::user()->name . '</strong> đã ' . $actionText . ' tài khoản <span class="text-dark fw-bold">"' . $user->name . '"</span>.',
            'icon_class' => $iconClass,
            'color_theme' => $colorTheme
        ]);

        return ($newStatus === 'locked') ? 'Đã khóa tài khoản thành công!' : 'Đã mở khóa tài khoản thành công!';
    }
}
