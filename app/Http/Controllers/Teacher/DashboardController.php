<?php

namespace App\Http\Controllers\Teacher;

use Illuminate\Support\Facades\Auth;
use App\Exports\SystemExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\Teacher\StudentService;
use App\Http\Controllers\Controller;
class DashboardController extends Controller
{
    protected $studentService;

    // Inject Service vào Controller
    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    // ==========================================
    // 1. DANH SÁCH HỌC VIÊN
    // ==========================================
    public function studentsIndex()
    {
        $data = $this->studentService->getStudentsData(Auth::id());
        
        return view('classrooms.teacher.students.index', $data);
    }

    // ==========================================
    // 2. TÍNH NĂNG XUẤT EXCEL (CSV) DANH SÁCH HỌC VIÊN
    // ==========================================
    public function exportStudents()
    {
        $data = $this->studentService->getExportData(Auth::id());
        
        $headings = ['ID', 'Họ và Tên', 'Email', 'Số bài đã nộp', 'Điểm Trung Bình'];
        $title = 'Danh sách học viên của ' . Auth::user()->name;
        $filename = 'Danh_sach_hoc_vien_' . date('Ymd_His') . '.xlsx';
        
        return Excel::download(new SystemExport($title, $headings, $data), $filename);
    }

    // ==========================================
    // 3. XEM CHI TIẾT 1 HỌC VIÊN
    // ==========================================
    public function showStudent($id)
    {
        $data = $this->studentService->getStudentDetailData($id, Auth::id());

        return view('dashboards.teacher.teacher_student_details', $data);
    }
}