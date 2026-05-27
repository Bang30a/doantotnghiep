<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Services\Shared\ClassroomService;
use App\Models\Classroom;
use App\Models\ActivityLog;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassroomController extends Controller
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
        $user = Auth::user();

        $classroom = Classroom::with(['teacher', 'users', 'exams'])->findOrFail($id);

        if ($user->role === 'student') {
            $isJoined = $user->classrooms()
                ->where('classrooms.id', $classroom->id)
                ->exists();

            if (!$isJoined) {
                abort(403, 'Ban chua tham gia lop hoc nay.');
            }
        }

        if ($user->role === 'teacher') {
            if ($classroom->teacher_id != $user->id) {
                abort(403, 'Ban khong co quyen xem lop hoc nay.');
            }
        }

        return $classroom;
    }

    public function teacherIndex()
    {
        $user = Auth::user();

        $classrooms = Classroom::with('teacher')
            ->withCount(['users', 'exams'])
            ->where('teacher_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('classrooms.teacher.teacher_index', compact('classrooms'));
    }

    public function studentIndex()
    {
        $user = Auth::user();

        $classrooms = $user->classrooms()
            ->with('teacher')
            ->where('classrooms.status', 1)
            ->orderBy('classrooms.created_at', 'desc')
            ->paginate(10);

        return view('classrooms.student.student_index', compact('classrooms'));
    }

    public function show($id)
    {
        $classroom = $this->getClassroomDetail($id);

        $user = Auth::user();

        if ($user->role === 'student') {
            $examIds = $classroom->exams->pluck('id');

            $totalExams = $classroom->exams->count();

            // Load them exam.questions de biet bai nao la tu luan
            $results = Result::with('exam.questions')
                ->where('user_id', $user->id)
                ->whereIn('exam_id', $examIds)
                ->get();

            $completedExams = $results->count();

            $pendingExams = $totalExams - $completedExams;

            $averageScore = 0;

            if ($completedExams > 0) {
                $totalScore = $results->sum(function ($result) {
                    $hasEssay = $result->exam
                        && $result->exam->questions
                        && $result->exam->questions->where('type', 'essay')->count() > 0;

                    if ($hasEssay) {
                        // Tu luan: score da la diem he 10
                        return max(0, min(10, floatval($result->score)));
                    }

                    // Trac nghiem: score la so cau dung, can quy doi ve he 10
                    return max(0, min(10, (floatval($result->score) / max(1, intval($result->total_questions))) * 10));
                });

                $averageScore = $totalScore / $completedExams;
            }

            return view('classrooms.student.student_show', compact(
                'classroom',
                'results',
                'totalExams',
                'completedExams',
                'pendingExams',
                'averageScore'
            ));
        }

        if ($user->role === 'teacher') {
            return view('classrooms.show', compact('classroom'));
        }

        return view('classrooms.show', compact('classroom'));
    }

    public function store(Request $request, ClassroomService $classroomService)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'Vui long nhap ten lop hoc.',
            'name.max' => 'Ten lop hoc khong duoc qua 255 ky tu.',
        ]);

        try {
            $classroomService->createClassroom($request->only('name'));

            return redirect()
                ->route('teacher.classrooms')
                ->with('success', 'Tao lop hoc thanh cong!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function join(Request $request, ClassroomService $classroomService)
    {
        $request->validate([
            'code' => 'required|string|max:20',
        ], [
            'code.required' => 'Vui long nhap ma lop.',
            'code.max' => 'Ma lop khong hop le.',
        ]);

        try {
            $classroom = $classroomService->joinClassroom($request->code, Auth::user());

            return redirect()
                ->route('classrooms.show', $classroom->id)
                ->with('success', 'Tham gia lop hoc thanh cong!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function statistics()
{
    $user = Auth::user();

    $classrooms = Classroom::with(['users', 'exams.questions'])
        ->withCount(['users', 'exams'])
        ->where('teacher_id', $user->id)
        ->get();

    $classCount = $classrooms->count();
    $studentCount = $classrooms->sum('users_count');
    $examCount = $classrooms->sum('exams_count');

    $examIds = $classrooms
        ->flatMap(function ($classroom) {
            return $classroom->exams->pluck('id');
        })
        ->unique()
        ->values();

    $results = Result::with(['user', 'exam.questions'])
        ->whereIn('exam_id', $examIds)
        ->get();

    // Diem trung binh he 10
    $averageScore = $results->count() > 0
        ? $results->avg(fn($result) => $this->score10($result))
        : 0;

    // Tong luot bai can nop = tong hoc vien moi lop * so de moi lop
    $totalAssigned = $classrooms->sum(function ($classroom) {
        return $classroom->users_count * $classroom->exams_count;
    });

    $completionRate = $totalAssigned > 0
        ? min(100, ($results->count() / $totalAssigned) * 100)
        : 0;

    $activeStudentsCount = $results->pluck('user_id')->unique()->count();

    $totalStudents = $classrooms
        ->flatMap(function ($classroom) {
            return $classroom->users;
        })
        ->unique('id')
        ->count();

    // Thong ke hoc vien
    $studentStats = [];

    foreach ($results as $result) {
        if (!$result->user) {
            continue;
        }

        $uid = $result->user_id;

        if (!isset($studentStats[$uid])) {
            $studentStats[$uid] = [
                'user' => $result->user,
                'total_score' => 0,
                'exams_taken' => 0,
                'average' => 0,
            ];
        }

        $studentStats[$uid]['total_score'] += $this->score10($result);
        $studentStats[$uid]['exams_taken'] += 1;
    }

    foreach ($studentStats as &$stat) {
        $stat['average'] = $stat['exams_taken'] > 0
            ? $stat['total_score'] / $stat['exams_taken']
            : 0;
    }
    unset($stat);

    $studentCollection = collect($studentStats);

    $topStudents = $studentCollection
        ->sortByDesc('average')
        ->take(5);

    $weakStudents = $studentCollection
        ->where('average', '<', 6.0)
        ->sortBy('average');

    // Diem trung binh theo lop
    $classLabels = [];
    $classScores = [];

    foreach ($classrooms as $classroom) {
        $ids = $classroom->exams->pluck('id');
        $classResults = $results->whereIn('exam_id', $ids);

        $classLabels[] = $classroom->name;
        $classScores[] = $classResults->count() > 0
            ? round($classResults->avg(fn($r) => $this->score10($r)), 1)
            : 0;
    }

    // Phan bo diem
    $distribution = [
        '0-4' => 0,
        '5-6' => 0,
        '7-8' => 0,
        '9-10' => 0,
    ];

    foreach ($results as $result) {
        $score = $this->score10($result);

        if ($score < 5) {
            $distribution['0-4']++;
        } elseif ($score < 7) {
            $distribution['5-6']++;
        } elseif ($score < 9) {
            $distribution['7-8']++;
        } else {
            $distribution['9-10']++;
        }
    }

    $allQuestions = $classrooms->flatMap(function ($classroom) {
        return $classroom->exams->flatMap->questions;
    });

    // Ty le dung TB chi tinh cho trac nghiem
    $mcqResults = $results->filter(function ($result) {
        return !$this->hasEssay($result);
    });

    $totalCorrect = $mcqResults->sum('score');
    $totalQuestionsAnswered = $mcqResults->sum('total_questions');

    $avgCorrectRate = $totalQuestionsAnswered > 0
        ? round(($totalCorrect / $totalQuestionsAnswered) * 100)
        : 0;

    $chartData = [
        'classLabels' => $classLabels,
        'classScores' => $classScores,

        'distributionLabels' => array_keys($distribution),
        'distributionData' => array_values($distribution),

        'completionLabels' => ['Đã nộp', 'Chưa nộp'],
        'completionData' => [
            $results->count(),
            max(0, $totalAssigned - $results->count()),
        ],

        'difficultyLabels' => ['Dễ', 'Trung bình', 'Khó'],
        'difficultyData' => [30, 50, 20],

        'avgCorrectRate' => $avgCorrectRate,
        'totalQuestions' => $allQuestions->count(),
    ];

    return view('dashboards.teacher.teacher_statistics', compact(
        'classrooms',
        'classCount',
        'studentCount',
        'examCount',
        'averageScore',
        'completionRate',
        'activeStudentsCount',
        'totalStudents',
        'topStudents',
        'weakStudents',
        'chartData'
    ));
}

    public function toggleLock($id)
    {
        $classroom = Classroom::findOrFail($id);

        $newStatus = ($classroom->status == 1) ? 0 : 1;

        $classroom->update([
            'status' => $newStatus
        ]);

        $actionText = ($newStatus == 0) ? 'khoa' : 'mo khoa';
        $colorTheme = ($newStatus == 0) ? 'warning' : 'success';
        $iconClass = ($newStatus == 0) ? 'bi-lock-fill' : 'bi-unlock-fill';

        ActivityLog::create([
            'type' => 'admin_classroom_status',
            'title' => 'Thay doi trang thai lop hoc',
            'description' => 'Quan tri vien <strong>' . Auth::user()->name . '</strong> da ' . $actionText . ' lop hoc <span class="text-dark fw-bold">"' . $classroom->name . '"</span>.',
            'icon_class' => $iconClass,
            'color_theme' => $colorTheme
        ]);

        return ($newStatus == 0) ? 'Da khoa lop hoc thanh cong!' : 'Da mo khoa lop hoc!';
    }

    public function deleteClassroom($id)
    {
        $classroom = Classroom::findOrFail($id);
        $className = $classroom->name;

        $classroom->delete();

        ActivityLog::create([
            'type' => 'admin_deleted_classroom',
            'title' => 'Admin xoa lop hoc',
            'description' => 'Quan tri vien <strong>' . Auth::user()->name . '</strong> da xoa vinh vien lop hoc <span class="text-danger fw-bold">"' . $className . '"</span> khoi he thong.',
            'icon_class' => 'bi-trash3-fill',
            'color_theme' => 'danger'
        ]);

        return back()->with('success', 'Xoa lop hoc thanh cong!');
    }
    private function hasEssay($result)
    {
        $result->loadMissing('exam.questions');

        return $result->exam
            && $result->exam->questions
            && $result->exam->questions->where('type', 'essay')->count() > 0;
    }

    private function score10($result)
    {
        if ($this->hasEssay($result)) {
            // Tu luan: score da la diem he 10
            return max(0, min(10, floatval($result->score)));
        }

        // Trac nghiem: score la so cau dung
        return max(0, min(10, (floatval($result->score) / max(1, intval($result->total_questions))) * 10));
    }
    public function createAssignment($classroomId)
    {
        $classroom = Classroom::with('exams')->findOrFail($classroomId);

        if ($classroom->teacher_id != Auth::id()) {
            abort(403, 'Ban khong co quyen giao bai cho lop nay.');
        }

        $exams = \App\Models\Exam::where('teacher_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboards.teacher.teacher_assignment_create', compact('classroom', 'exams'));
    }

    public function storeAssignment(Request $request, $classroomId)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'deadline' => 'nullable|date',
        ], [
            'exam_id.required' => 'Vui long chon de thi.',
            'exam_id.exists' => 'De thi khong hop le.',
            'deadline.date' => 'Han nop khong hop le.',
        ]);

        $classroom = Classroom::findOrFail($classroomId);

        if ($classroom->teacher_id != Auth::id()) {
            abort(403, 'Ban khong co quyen giao bai cho lop nay.');
        }

        $exam = \App\Models\Exam::where('teacher_id', Auth::id())
            ->where('id', $request->exam_id)
            ->firstOrFail();

        $exam->update([
            'classroom_id' => $classroom->id,
            'deadline' => $request->deadline,
        ]);

        return redirect()
        ->route('classrooms.show', $classroom->id)
        ->with('success', 'Giao bai tap cho lop thanh cong!');
    }
}