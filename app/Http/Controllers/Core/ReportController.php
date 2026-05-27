<?php

namespace App\Http\Controllers\Core;

use App\Models\Exam;
use App\Models\Classroom;
use App\Models\Result;
use App\Exports\SystemExport;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    // ==========================================
    // 1. HIỂN THỊ GIAO DIỆN SỔ ĐIỂM TRÊN WEB
    // ==========================================
    public function teacherIndex(Request $request)
    {
        $teacherId = Auth::id();
        
        // Lấy danh sách lớp để đưa vào bộ lọc
        $classrooms = Classroom::where('teacher_id', $teacherId)->get();

        // Query lấy đề thi (kèm thêm questions để kiểm tra Tự luận/Trắc nghiệm)
        $query = Exam::where('teacher_id', $teacherId)
                    ->with(['classroom', 'questions'])
                    ->withCount('results');

        if ($request->has('classroom_id') && $request->classroom_id != '') {
            $query->where('classroom_id', $request->classroom_id);
        }

        $exams = $query->latest()->paginate(10);

        // TÍNH ĐIỂM TRUNG BÌNH CHUẨN XÁC
        foreach ($exams as $exam) {
            $results = Result::where('exam_id', $exam->id)->get();
            
            // Kiểm tra xem đề này có câu tự luận nào không?
            $hasEssay = $exam->questions->where('type', 'essay')->count() > 0;

            if ($results->count() > 0) {
                $totalScore = $results->sum(function($r) use ($hasEssay) {
                    // Nếu có Tự luận -> Lấy điểm gốc. Nếu Trắc nghiệm 100% -> Nhân quy đổi hệ 10
                    return $hasEssay ? $r->score : ($r->score / max(1, $r->total_questions)) * 10;
                });
                $exam->avg_score = $totalScore / $results->count();
            } else {
                $exam->avg_score = 0;
            }
        }

        return view('dashboards.teacher.teacher_reports', compact('exams', 'classrooms'));
    }

    // ==========================================
    // 2. XUẤT FILE EXCEL BÁO CÁO (ĐÃ CẬP NHẬT LOGIC ĐIỂM)
    // ==========================================
    public function export(Request $request)
    {
        $teacherId = Auth::id();
        
        $query = Exam::where('teacher_id', $teacherId)
                    ->with(['classroom', 'questions'])
                    ->withCount('results');

        if ($request->has('classroom_id') && $request->classroom_id != '') {
            $query->where('classroom_id', $request->classroom_id);
            $className = Classroom::find($request->classroom_id)->name ?? 'Tất cả';
            $title = "BÁO CÁO ĐIỂM SỐ - LỚP: " . mb_strtoupper($className, 'UTF-8');
            $filename = 'Bao_cao_diem_lop_' . $request->classroom_id . '_' . date('Ymd_His') . '.xlsx';
        } else {
            $title = "BÁO CÁO ĐIỂM SỐ TỔNG HỢP";
            $filename = 'Bao_cao_diem_tong_hop_' . date('Ymd_His') . '.xlsx';
        }

        $exams = $query->latest()->get();

        // TÍNH ĐIỂM TRUNG BÌNH CHUẨN XÁC (Y hệt như lúc hiển thị web)
        foreach ($exams as $exam) {
            $results = Result::where('exam_id', $exam->id)->get();
            $hasEssay = $exam->questions->where('type', 'essay')->count() > 0;

            if ($results->count() > 0) {
                $totalScore = $results->sum(function($r) use ($hasEssay) {
                    return $hasEssay ? $r->score : ($r->score / max(1, $r->total_questions)) * 10;
                });
                $exam->avg_score = $totalScore / $results->count();
            } else {
                $exam->avg_score = 0;
            }
        }

        $headings = ['STT', 'Tên Bài Kiểm Tra', 'Phân Loại Lớp', 'Số Bài Đã Nộp', 'Điểm Trung Bình (Hệ 10)', 'Ngày Tạo'];

        $data = [];
        $stt = 1;
        foreach ($exams as $exam) {
            $data[] = [
                $stt++,
                $exam->title,
                $exam->classroom ? $exam->classroom->name : 'Ngân hàng đề chung',
                $exam->results_count,
                number_format($exam->avg_score, 1),
                $exam->created_at->format('d/m/Y')
            ];
        }

        return Excel::download(new SystemExport($title, $headings, $data), $filename);
    }
}