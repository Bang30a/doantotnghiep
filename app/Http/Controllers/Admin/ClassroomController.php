<?php

namespace App\Http\Controllers\Admin; 

use App\Http\Controllers\Controller;
use App\Services\Admin\ClassroomService;

use Illuminate\Http\Request;


class ClassroomController extends Controller
{
    protected $classroomService;

    public function __construct(ClassroomService $classroomService)
    {
        $this->classroomService = $classroomService;
    }

    // Hiển thị danh sách tất cả lớp học
    public function index(Request $request)
    {
        // Nhận số lượng hiển thị từ URL, mặc định là 10
        $perPage = $request->get('per_page', 10);
        
        // Truyền thêm $perPage vào hàm của Service
        $classrooms = $this->classroomService->getPaginatedClassrooms($request->search, $perPage);
        
        return view('dashboards.admin.admin_classrooms', compact('classrooms'));
    }

    // Hiển thị trang Chi tiết Lớp học
    public function showClassroom($id)
    {
        $classroom = $this->classroomService->getClassroomDetail($id);
        
        return view('dashboards.admin.admin_classroom_detail', compact('classroom'));
    }

    // Xử lý Khóa/Mở khóa lớp học
    public function toggleLock($id)
    {
        $message = $this->classroomService->toggleLock($id);

        return back()->with('success', $message);
    }

    // Xóa vĩnh viễn lớp học
    public function destroy($id)
    {
        $this->classroomService->deleteClassroom($id);

        return back()->with('success', 'Đã xóa lớp học vĩnh viễn!');
    }
}