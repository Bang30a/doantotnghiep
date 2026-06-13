<?php

namespace App\Http\Controllers\Admin; 

use App\Http\Controllers\Controller; 
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Services\Admin\UserService;

use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function manageUsers()
    {
        return redirect()->route('admin.teachers');
    }

    // ==========================================
    // 1. QUẢN LÝ GIẢNG VIÊN
    // ==========================================
    public function teacherIndex(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $teachers = $this->userService->getPaginatedUsers('teacher', $request->search, $perPage);
        return view('dashboards.admin.admin_teachers', compact('teachers'));
    }

    public function showTeacher($id)
    {
        // Gọi Service lấy thông tin giảng viên
        $teacher = $this->userService->getTeacherDetail($id);
        
        return view('dashboards.admin.admin_teacher_detail', compact('teacher'));
    }

    public function updateTeacher(UpdateUserRequest $request, $id)
    {
        $teacher = User::findOrFail($id);
        
        // Gọi Service xử lý data đã được validation
        $this->userService->updateUser($teacher, $request->validated());
        
        return back()->with('success', 'Đã cập nhật thông tin giảng viên thành công!');
    }

    public function destroyTeacher($id)
    {
        $message = $this->userService->deleteUserByRole($id, 'teacher');

        return redirect()->route('admin.teachers')->with('success', $message);
    }

    // ==========================================
    // 2. QUẢN LÝ HỌC VIÊN
    // ==========================================
    public function studentIndex(Request $request)
    {
        // Nhận số lượng hiển thị từ URL, mặc định là 10
        $perPage = $request->get('per_page', 10);
        
        // Truyền thêm tham số $perPage vào hàm của Service
        $students = $this->userService->getPaginatedUsers('student', $request->search, $perPage);
        
        return view('dashboards.admin.admin_students', compact('students'));
    }

    // ==========================================
    // 3. XEM & SỬA HỌC VIÊN
    // ==========================================
    public function showStudent($id)
    {
        $student = $this->userService->getStudentDetail($id);
        
        return view('dashboards.admin.admin_student_detail', compact('student'));
    }

    public function updateStudent(UpdateUserRequest $request, $id)
    {
        $student = User::findOrFail($id);
        
        // Gọi Service xử lý data đã được validation
        $this->userService->updateUser($student, $request->validated());
        
        return back()->with('success', 'Đã cập nhật thông tin học viên thành công!');
    }

    public function destroyStudent($id)
    {
        $message = $this->userService->deleteUserByRole($id, 'student');

        return redirect()->route('admin.students')->with('success', $message);
    }
    // ==========================================
    // 4. KHÓA / MỞ KHÓA TÀI KHOẢN (GIẢNG VIÊN & HỌC VIÊN)
    // ==========================================
    public function toggleLock($id)
    {
        // Gọi Service xử lý logic và nhận lại câu thông báo
        $message = $this->userService->toggleLock($id);

        if (request()->expectsJson()) {
            $user = User::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => $message,
                'is_locked' => $user->status === 'locked',
            ]);
        }
        
        return back()->with('success', $message);
    }
}
