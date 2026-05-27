<?php

namespace App\Services\Admin;

use App\Models\Classroom;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ClassroomService
{
    public function getPaginatedClassrooms($searchQuery = null, $perPage = 10)
    {
        $query = Classroom::with('teacher')->withCount(['users', 'exams']);

        if (!empty($searchQuery)) {
            $query->where('name', 'like', '%' . $searchQuery . '%');
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function getClassroomDetail($id)
    {
        return Classroom::with(['teacher', 'users', 'exams'])->findOrFail($id);
    }

    public function toggleLock($id)
    {
        $classroom = Classroom::findOrFail($id);
        
        $newStatus = ($classroom->status == 1) ? 0 : 1;
        
        // Dùng luôn Eloquent để update thay vì DB::table
        $classroom->update(['status' => $newStatus]);

        $actionText = ($newStatus == 0) ? 'khóa' : 'mở khóa';
        $colorTheme = ($newStatus == 0) ? 'warning' : 'success';
        $iconClass = ($newStatus == 0) ? 'bi-lock-fill' : 'bi-unlock-fill';

        ActivityLog::create([
            'type' => 'admin_classroom_status',
            'title' => 'Thay đổi trạng thái lớp học',
            'description' => 'Quản trị viên <strong>' . Auth::user()->name . '</strong> đã ' . $actionText . ' lớp học <span class="text-dark fw-bold">"' . $classroom->name . '"</span>.',
            'icon_class' => $iconClass,
            'color_theme' => $colorTheme
        ]);

        return ($newStatus == 0) ? 'Đã khóa lớp học thành công!' : 'Đã mở khóa lớp học!';
    }

    public function deleteClassroom($id)
    {
        $classroom = Classroom::findOrFail($id);
        $className = $classroom->name; 
        
        $classroom->delete();

        ActivityLog::create([
            'type' => 'admin_deleted_classroom',
            'title' => 'Admin xóa lớp học',
            'description' => 'Quản trị viên <strong>' . Auth::user()->name . '</strong> đã xóa vĩnh viễn lớp học <span class="text-danger fw-bold">"' . $className . '"</span> khỏi hệ thống.',
            'icon_class' => 'bi-trash3-fill',
            'color_theme' => 'danger'
        ]);
    }
}