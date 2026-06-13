<?php

namespace App\Http\Controllers\Teacher;

use Illuminate\Http\Request;
use App\Http\Requests\Teacher\StoreExamRequest;
use App\Models\Exam;
use App\Models\Classroom;
use App\Models\Document;
use App\Models\Result;
use App\Models\StudentAnswer;
use App\Services\Teacher\ExamManagementService;
use App\Services\Teacher\GradingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ExamController extends Controller
{
    protected $examService;
    protected $gradingService;

    // Inject các Services vào Controller
    public function __construct(ExamManagementService $examService, GradingService $gradingService)
    {
        $this->examService = $examService;
        $this->gradingService = $gradingService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Exam::where('teacher_id', $user->id)->with('classroom')->withCount('questions');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $exams = $query->latest()->paginate(10);
        $totalExams = Exam::where('teacher_id', $user->id)->count();
        $assignedExams = Exam::where('teacher_id', $user->id)->whereNotNull('classroom_id')->count();
        $bankExams = $totalExams - $assignedExams;

        return view('dashboards.teacher.teacher_exams', compact('exams', 'totalExams', 'assignedExams', 'bankExams'));
    }

    public function create()
    {
        $user = Auth::user();
        $classrooms = Classroom::where('teacher_id', $user->id)->get();
        $documents = Document::where('user_id', $user->id)->latest()->get();
        
        return view('dashboards.teacher.teacher_create_exam', compact('classrooms', 'documents'));
    }

    public function store(StoreExamRequest $request)
    {
        try {
            $this->examService->storeExam($request->validated());
            return back()->with('exam_saved', true);
        } catch (\Exception $e) {
            return back()->withErrors([$e->getMessage()])->withInput();
        }
    }

    public function edit($id)
    {
        $exam = Exam::with('questions.answers')->findOrFail($id);
        $teacherId = Auth::id();
        $classrooms = Classroom::where('teacher_id', $teacherId)->get();
        $documents = Document::where('user_id', $teacherId)->get();

        return view('dashboards.teacher.teacher_create_exam', compact('exam', 'classrooms', 'documents'));
    }

    public function update(StoreExamRequest $request, $id)
    {
        $exam = Exam::where('id', $id)->where('teacher_id', Auth::id())->firstOrFail();
        
        try {
            $this->examService->updateExam($exam, $request->validated());
            return redirect()->route('teacher.exams.index')->with('success', 'Đã cập nhật đề thi thành công!');
        } catch (\Exception $e) {
            return back()->withErrors([$e->getMessage()]);
        }
    }

    public function show($id)
    {
        $exam = Exam::with(['questions.answers', 'classroom'])->findOrFail($id);
        $this->authorizeAccess($exam);

        $results = Result::with('user')->where('exam_id', $id)->latest()->get();
        $averageScore = $this->calculateAverageScore($results);
        
        return view('dashboards.teacher.teacher_exam_details', compact('exam', 'results', 'averageScore'));
    }

    public function results($id)
    {
        $exam = Exam::with(['classroom'])->findOrFail($id);
        $this->authorizeAccess($exam);

        $results = Result::with('user')->where('exam_id', $id)->latest()->get();
        $averageScore = $this->calculateAverageScore($results);

        return view('dashboards.teacher.teacher_results', compact('exam', 'results', 'averageScore'));
    }

    public function destroy($id)
    {
        $exam = Exam::where('id', $id)->where('teacher_id', Auth::id())->firstOrFail();
        $this->examService->deleteExam($exam);

        return back()->with('success', 'Đã xóa đề thi thành công!');
    }

    public function grading($result_id)
    {
        $result = Result::with(['user', 'exam.questions.answers'])->findOrFail($result_id);
        $this->authorizeAccess($result->exam);

        $aiSuggestions = [];

        foreach ($result->exam->questions as $question) {
            if ($question->type !== 'essay') {
                continue;
            }

            $studentAnswer = StudentAnswer::where('exam_result_id', $result->id)
                ->where('question_id', $question->id)
                ->first();

            if (!$studentAnswer || empty(trim($studentAnswer->content ?? ''))) {
                $aiSuggestions[$question->id] = [
                    'score' => null,
                    'feedback' => 'Hoc vien chua co bai lam nen AI chua the cham.',
                ];
                continue;
            }

            $bareme = $question->answers->first()->content ?? 'Chua co bareme goi y.';

            $aiSuggestions[$question->id] = $this->generateAiSuggestion(
                $question->content,
                $bareme,
                $studentAnswer->content
            );
        }

        return view('dashboards.teacher.teacher_grading', compact('result', 'aiSuggestions'));
    }
    private function generateAiSuggestion($questionContent, $bareme, $studentAnswer)
        {
            try {
                return app(\App\Services\AiExamService::class)->gradeEssayAnswer(
                    $questionContent,
                    $bareme,
                    $studentAnswer
                );
            } catch (\Throwable $e) {
                Log::warning('AI essay grading suggestion failed', [
                    'error' => $e->getMessage(),
                ]);

                return [
                    'score' => null,
                    'feedback' => 'AI chưa thể phân tích bài làm lúc này. Giảng viên có thể chấm thủ công hoặc thử lại sau.',
                ];
            }
        }
    public function saveGrade(Request $request, $result_id)
        {
            $this->gradingService->updateGrade(
                $result_id, 
                $request->input('question_id'), 
                $request->input('score_' . $request->input('question_id')), 
                $request->input('feedback_' . $request->input('question_id'))
            );

            return back()->with('success', 'Đã lưu điểm và nhận xét thành công!');
        }
        
    private function calculateAverageScore($results)
    {
        if ($results->isEmpty()) {
            return 0;
        }

        $totalScore10 = $results->sum(function ($r) {
            $exam = $r->exam;

            if ($exam && $exam->questions->where('type', 'essay')->count() > 0) {
                return floatval($r->score);
            }

            return ($r->score / max(1, $r->total_questions)) * 10;
        });

        return $totalScore10 / $results->count();
    }

    private function authorizeAccess($exam)
    {
        if (!$exam || $exam->teacher_id != Auth::id()) {
            abort(403, 'Ban khong co quyen truy cap de thi nay.');
        }
    }
}
