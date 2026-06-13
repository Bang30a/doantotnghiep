<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Result;
use App\Models\Document;
use App\Http\Requests\Student\StoreSelfPracticeRequest;
use App\Services\Student\ExamSubmissionService;
use App\Services\Student\SelfPracticeService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    protected $submissionService;
    protected $practiceService;

    public function __construct(ExamSubmissionService $submissionService, SelfPracticeService $practiceService)
    {
        $this->submissionService = $submissionService;
        $this->practiceService = $practiceService;
    }

    // ==========================================
    // MODULE LÀM BÀI THI
    // ==========================================
    public function play($id)
    {
        $exam = Exam::with(['questions.answers' => function($query) {
            $query->inRandomOrder();
        }])->findOrFail($id);
        
        return view('exams.play', compact('exam'));
    }

    public function submit(Request $request, $id)
    {
        $exam = Exam::with('questions')->findOrFail($id);
        
        try {
            $result = $this->submissionService->submitExam($exam, $request->all(), Auth::id());
            return redirect()
                ->route('exams.result', ['id' => $exam->id, 'result_id' => $result->id])
                ->with('success', 'Nộp bài thành công!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Lỗi nộp bài: ' . $e->getMessage()]);
        }
    }

    public function result($id, $result_id = null)
    {
        $exam = Exam::with(['questions.answers', 'classroom'])->findOrFail($id);
        $resultQuery = Result::where('user_id', Auth::id())->where('exam_id', $id);

        if ($result_id) {
            $resultQuery->where('id', $result_id);
        } else {
            $resultQuery->latest();
        }

        $result = $resultQuery->firstOrFail();
        
        return view('exams.result', compact('exam', 'result'));
    }

    // ==========================================
    // MODULE KHO ĐỀ TỰ LUYỆN (AI GENERATED)
    // ==========================================
    public function questionBanks()
    {
        $userId = Auth::id();
        $questionBanks = Exam::where('teacher_id', $userId)->withCount('questions')->orderBy('created_at', 'desc')->paginate(6); 
        $subjects = Exam::where('teacher_id', $userId)->whereNotNull('subject')->where('subject', '!=', '')->distinct()->pluck('subject');
        $globalSettings = ['site_name' => 'EduQuiz AI'];

        return view('dashboards.student.student_question_banks', compact('questionBanks', 'subjects', 'globalSettings'));
    }

    public function createSelfPractice()
    {
        $documents = Document::where('user_id', Auth::id())->get();
        return view('dashboards.student.student_create_exam', compact('documents'));
    }

    public function storeSelfPractice(StoreSelfPracticeRequest $request)
    {
        try {
            $this->practiceService->createPracticeExam($request->validated(), Auth::id());
            return redirect()->route('student.exams.create')->with('exam_saved', true);
        } catch (\Exception $e) {
            return back()->withErrors(['system_error' => 'Lỗi hệ thống: ' . $e->getMessage()])->withInput();
        }
    }

    public function previewBank($id)
    {
        $exam = Exam::with(['questions.answers'])->where('teacher_id', Auth::id())->findOrFail($id);
        $globalSettings = ['site_name' => 'EduQuiz AI'];
        
        return view('dashboards.student.student_preview_exam', compact('exam', 'globalSettings'));
    }

    public function destroyBank($id)
    {
        try {
            $exam = Exam::where('teacher_id', Auth::id())->findOrFail($id);
            $this->practiceService->deletePracticeExam($exam);

            return response()->json(['success' => true, 'message' => 'Đã xóa đề thi!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Không thể xóa.'], 500);
        }
    }
}
