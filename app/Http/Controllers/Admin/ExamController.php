<?php

namespace App\Http\Controllers\Admin; 

use App\Http\Controllers\Controller; 
use App\Services\Admin\ExamService;

use Illuminate\Http\Request;

class ExamController extends Controller
{
    protected $examService;

    // Inject Service thông qua Constructor
    public function __construct(ExamService $examService)
    {
        $this->examService = $examService;
    }

    // Hiển thị toàn bộ đề thi trên hệ thống
    public function index(Request $request)
    {
        // 1. Nhận số lượng hiển thị từ URL, mặc định là 10
        $perPage = $request->get('per_page', 10);

        // 2. Truyền tham số $perPage vào Service
        $exams = $this->examService->getPaginatedExams($request->search, $perPage);

        return view('dashboards.admin.admin_exams', compact('exams'));
    }

    // Xem chi tiết đề thi dành riêng cho Admin
    public function preview($id)
    {
        $exam = $this->examService->getExamDetail($id);

        return view('dashboards.admin.admin_exam_detail', compact('exam'));
    }

    // Xóa vĩnh viễn đề thi
    public function destroy($id)
    {
        $this->examService->deleteExam($id);

        return back()->with('success', 'Đã xóa vĩnh viễn đề thi khỏi hệ thống!');
    }
}