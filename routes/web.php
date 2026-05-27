<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// ==========================================
// KHO KHAI BÁO CONTROLLERS (ĐÃ SỬ DỤNG ALIAS ĐỂ TRÁNH TRÙNG LẶP)
// ==========================================

// 1. Nhóm Core (Dùng chung)
use App\Http\Controllers\Core\AuthController;
use App\Http\Controllers\Core\ClassroomController;
use App\Http\Controllers\Core\DocumentController;
use App\Http\Controllers\Core\ProfileController;
use App\Http\Controllers\Core\ReportController;
use App\Http\Controllers\Core\BackupController;

// 2. Nhóm Teacher
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Teacher\ExamController as TeacherExamController;

// 3. Nhóm Student
use App\Http\Controllers\Student\ExamController as StudentExamController;
use App\Http\Controllers\Student\StatisticController as StudentStatisticController;

// 4. Nhóm API
use App\Http\Controllers\Api\AiApiController;

// 5. Nhóm Admin
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ClassroomController as AdminClassroomController;
use App\Http\Controllers\Admin\ExamController as AdminExamController;
use App\Http\Controllers\Admin\DocumentController as AdminDocumentController;
use App\Http\Controllers\Admin\PromptController as AdminPromptController;
use App\Http\Controllers\Admin\AiHistoryController as AdminAiHistoryController;
use App\Http\Controllers\Admin\SettingController;



// ==========================================
// ROUTES DÀNH CHO KHÁCH (CHƯA ĐĂNG NHẬP)
// ==========================================
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/refresh-csrf', function (\Illuminate\Http\Request $request) {
    if (Auth::check()) {
        $request->session()->regenerate();
    }

    $request->session()->regenerateToken();

    return response()->json([
        'success' => true,
        'csrf_token' => csrf_token(),
        'authenticated' => Auth::check(),
    ]);
})->name('refresh.csrf');;

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Social Login
Route::get('/auth/{provider}/redirect', [AuthController::class, 'redirectToProvider'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [AuthController::class, 'handleProviderCallback'])->name('social.callback');
// Route xử lý đăng xuất tự động khi hết giờ (Dùng GET để không bị lỗi 419 CSRF)
Route::get('/auto-logout', function (\Illuminate\Http\Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login?t=' . time());
});

// ==========================================
// ROUTES BẮT BUỘC ĐĂNG NHẬP (AUTH MIDDLEWARE)
// ==========================================
Route::middleware('auth')->group(function () {
    
    // --- AUTHENTICATION ---
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // --- DASHBOARD CHUNG (DIEU HUONG TUY VAI TRO) ---
Route::get('/dashboard', function () {
    $user = Auth::user();

    // Ham quy doi diem ve he 10 dung cho ca trac nghiem va tu luan
    $score10 = function ($result) {
        $result->loadMissing('exam.questions');

        $hasEssay = $result->exam
            && $result->exam->questions
            && $result->exam->questions->where('type', 'essay')->count() > 0;

        if ($hasEssay) {
            // Tu luan: score da la diem he 10
            return max(0, min(10, floatval($result->score)));
        }

        // Trac nghiem: score la so cau dung
        return max(0, min(10, (floatval($result->score) / max(1, intval($result->total_questions))) * 10));
    };

    if ($user->role === 'teacher') {
        $classrooms = \App\Models\Classroom::where('teacher_id', $user->id)
            ->withCount('users')
            ->withCount('exams')
            ->latest()
            ->get();

        $classCount = $classrooms->count();
        $studentCount = $classrooms->sum('users_count');

        $exams = \App\Models\Exam::where('teacher_id', $user->id)->get();

        $examCount = $exams->count();
        $assignedExamCount = $exams->whereNotNull('classroom_id')->count();
        $examsThisWeek = $exams->where('created_at', '>=', now()->subDays(7))->count();
        $recentClasses = $classrooms->take(3);

        $examIds = $exams->pluck('id');

        $results = \App\Models\Result::with('exam.questions')
            ->whereIn('exam_id', $examIds)
            ->get();

        $averageScore = 0;
        $completionRate = 0;

        if ($results->count() > 0) {
            $totalScore10 = $results->sum(function ($r) use ($score10) {
                return $score10($r);
            });

            $averageScore = $totalScore10 / $results->count();

            $expectedSubmissions = $studentCount * $assignedExamCount;

            if ($expectedSubmissions > 0) {
                $completionRate = min(100, ($results->count() / $expectedSubmissions) * 100);
            }
        }

        return view('dashboards.teacher.teacher', compact(
            'classCount',
            'studentCount',
            'examCount',
            'assignedExamCount',
            'recentClasses',
            'averageScore',
            'completionRate',
            'examsThisWeek',
            'results'
        ));
    }

    elseif ($user->role === 'student') {
        $results = \App\Models\Result::with('exam.questions')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $completedExamsCount = $results->count();

        $averageScore = 0;

        if ($completedExamsCount > 0) {
            $totalScore = $results->sum(function ($r) use ($score10) {
                return $score10($r);
            });

            $averageScore = $totalScore / $completedExamsCount;
        }

        $joinedClassroomIds = \Illuminate\Support\Facades\DB::table('classroom_user')
            ->where('user_id', $user->id)
            ->pluck('classroom_id');

        $classroomsCount = $joinedClassroomIds->count();

        $recentResults = $results->take(3);

        $upcomingExams = \App\Models\Exam::whereIn('classroom_id', $joinedClassroomIds)
            ->whereNotIn('id', $results->pluck('exam_id'))
            ->with('classroom')
            ->latest()
            ->take(3)
            ->get();

        return view('dashboards.student.student', compact(
            'completedExamsCount',
            'averageScore',
            'classroomsCount',
            'recentResults',
            'upcomingExams'
        ));
    }

    elseif ($user->role === 'admin') {
        $users = \App\Models\User::latest()->get();

        $totalUsers = $users->count();
        $studentsCount = $users->where('role', 'student')->count();
        $teachersCount = $users->where('role', 'teacher')->count();

        $todayActivity = \App\Models\Result::whereDate('created_at', today())->count()
            + \App\Models\Classroom::whereDate('created_at', today())->count();

        $activeUsers = $totalUsers;
        $lockedUsers = 0;

        $recentActivities = [];

        if (\Illuminate\Support\Facades\Schema::hasTable('activity_logs')) {
            $recentActivities = \Illuminate\Support\Facades\DB::table('activity_logs')
                ->latest()
                ->take(5)
                ->get();

            foreach ($recentActivities as $activity) {
                $activity->time_ago = \Carbon\Carbon::parse($activity->created_at)
                    ->locale('vi')
                    ->diffForHumans();
            }
        }

        return view('dashboards.admin.admin', compact(
            'totalUsers',
            'studentsCount',
            'teachersCount',
            'todayActivity',
            'users',
            'activeUsers',
            'lockedUsers',
            'recentActivities'
        ));
    }

    return redirect('/');
})->name('dashboard');

    // --- LỚP HỌC (DÙNG CHUNG) ---
    Route::post('/classrooms', [ClassroomController::class, 'store'])->name('classrooms.store');
    Route::post('/classrooms/join', [ClassroomController::class, 'join'])->name('classrooms.join');
    Route::get('/classrooms/{id}', [ClassroomController::class, 'show'])->name('classrooms.show');
    
    // --- THI CỬ VÀ ĐIỂM (DÙNG CHUNG) ---
    Route::get('/exams/{id}/details', [TeacherExamController::class, 'show'])->name('exams.show');
    Route::get('/exams/{id}/results-board', [TeacherExamController::class, 'results'])->name('exams.teacher_results');
    Route::get('/exams/{id}/play', [StudentExamController::class, 'play'])->name('exams.play');
    Route::post('/exams/{id}/submit', [StudentExamController::class, 'submit'])->name('exams.submit');
    Route::get('/exams/{id}/result', [StudentExamController::class, 'result'])->name('exams.result');

    // ==========================================
    // ROUTES DÀNH RIÊNG CHO GIẢNG VIÊN (TEACHER)
    // ==========================================
    Route::prefix('teacher')->name('teacher.')->group(function () {
        // Quản lý lớp & học viên
        Route::get('/classrooms', [ClassroomController::class, 'teacherIndex'])->name('classrooms');
        Route::get('/students', [TeacherDashboardController::class, 'studentsIndex'])->name('students.index');
        Route::get('/students/export', [TeacherDashboardController::class, 'exportStudents'])->name('students.export');
        Route::get('/students/{id}', [TeacherDashboardController::class, 'showStudent'])->name('students.show');
        Route::get('/classrooms/{id}', [ClassroomController::class, 'show'])->name('classrooms.show');
        Route::get('/classrooms/{classroom}/assignments/create', [ClassroomController::class, 'createAssignment'])
            ->name('classrooms.assignments.create');
        Route::post('/classrooms/{classroom}/assignments/store', [ClassroomController::class, 'storeAssignment'])
            ->name('classrooms.assignments.store');
        // Kho tài liệu
        Route::get('/documents', [DocumentController::class, 'teacherIndex'])->name('documents.index');
        Route::post('/documents/store', [DocumentController::class, 'teacherStore'])->name('documents.store');
        Route::get('/documents/{id}/preview', [DocumentController::class, 'teacherPreview'])->name('documents.preview');
        Route::delete('/documents/{id}', [DocumentController::class, 'teacherDestroy'])->name('documents.destroy');
        
        // Quản lý đề thi
        Route::get('/exams', [TeacherExamController::class, 'index'])->name('exams.index');
        Route::get('/exams/create', [TeacherExamController::class, 'create'])->name('exams.create');
        Route::post('/exams', [TeacherExamController::class, 'store'])->name('exams.store'); 
        Route::get('/exams/{id}/edit', [TeacherExamController::class, 'edit'])->name('exams.edit');
        Route::put('/exams/{id}', [TeacherExamController::class, 'update'])->name('exams.update');
        Route::delete('/exams/{id}', [TeacherExamController::class, 'destroy'])->name('exams.destroy');
        Route::get('/results/{result_id}', [TeacherExamController::class, 'showStudentResult'])->name('results.show');
        Route::get('/exams/grading/{result_id}', [TeacherExamController::class, 'grading'])->name('exams.grading');
        Route::post('/exams/grading/{result_id}', [TeacherExamController::class, 'saveGrade'])->name('exams.save_grade');

        // Thống kê & Báo cáo
        Route::get('/statistics', [ClassroomController::class, 'statistics'])->name('statistics');
        Route::get('/reports', [ReportController::class, 'teacherIndex'])->name('reports.index');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
        
        // Cài đặt cá nhân
        Route::get('/settings', [ProfileController::class, 'teacherEdit'])->name('settings.edit');
        Route::put('/settings/profile', [ProfileController::class, 'teacherUpdateProfile'])->name('settings.update.profile');
        Route::put('/settings/password', [ProfileController::class, 'teacherUpdatePassword'])->name('settings.update.password');
    });

    // ==========================================
    // ROUTES DÀNH RIÊNG CHO HỌC VIÊN (STUDENT)
    // ==========================================
    Route::prefix('student')->name('student.')->group(function () {
        Route::get('/classrooms', [ClassroomController::class, 'studentIndex'])->name('classrooms');
        
        // Lịch sử & Thống kê
        Route::get('/history', [StudentStatisticController::class, 'history'])->name('history');
        Route::get('/statistics', [StudentStatisticController::class, 'statistics'])->name('statistics');
        
        // Kho tài liệu
        Route::get('/documents', [DocumentController::class, 'index'])->name('documents');
        Route::post('/documents/upload', [DocumentController::class, 'store'])->name('documents.store');
        Route::delete('/documents/{id}', [DocumentController::class, 'destroy'])->name('documents.destroy');
        
        // Đề thi tự luyện (Sinh bằng AI)
        Route::get('/exams/create', [StudentExamController::class, 'createSelfPractice'])->name('exams.create');
        Route::post('/exams/store', [StudentExamController::class, 'storeSelfPractice'])->name('exams.store');
        Route::get('/question-banks', [StudentExamController::class, 'questionBanks'])->name('question-banks');
        Route::delete('/question-banks/{id}', [StudentExamController::class, 'destroyBank'])->name('question-banks.destroy');
        Route::get('/question-banks/{id}/preview', [StudentExamController::class, 'previewBank'])->name('question-banks.preview');
        
        // Cài đặt cá nhân
        Route::get('/settings', [ProfileController::class, 'studentEdit'])->name('settings.edit');
        Route::put('/settings/profile', [ProfileController::class, 'studentUpdateProfile'])->name('settings.update.profile');
        Route::put('/settings/password', [ProfileController::class, 'studentUpdatePassword'])->name('settings.update.password');
    });

    // ==========================================
    // API DÙNG CHUNG CHO AJAX SINH CÂU HỎI AI
    // ==========================================
    Route::post('/exams/generate-ai', [AiApiController::class, 'generate'])->name('exams.generate-ai');
    Route::post('/student/exams/generate-ai', [AiApiController::class, 'generate'])->name('student.exams.generate-ai');

    // ==========================================
    // ROUTES DÀNH RIÊNG CHO ADMIN
    // ==========================================
    Route::prefix('admin')->name('admin.')->group(function () {
        
        // Dashboard & Cài đặt hệ thống
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/settings', [SettingController::class, 'index'])->name('settings');
        Route::post('/settings/test-email', [SettingController::class, 'testEmail'])->name('settings.test-email');
        Route::post('/settings/clear-cache', [\App\Http\Controllers\Admin\SettingController::class, 'clearCache'])->name('settings.clear-cache');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::get('/activities', [AdminDashboardController::class, 'activities'])->name('activities');
        
        // Backup
        Route::get('/backup/database', [BackupController::class, 'downloadDatabase'])->name('backup.database');
        
        // Quản lý Users (Giảng viên & Học viên)
        Route::get('/teachers', [AdminUserController::class, 'teacherIndex'])->name('teachers');
        Route::get('/teachers/detail/{id}', [AdminUserController::class, 'showTeacher'])->name('teachers.show');
        Route::put('/teachers/update/{id}', [AdminUserController::class, 'updateTeacher'])->name('teachers.update');
        Route::post('/teachers/{id}/lock', [AdminUserController::class, 'toggleLock'])->name('teachers.toggle_lock');

        Route::get('/students', [AdminUserController::class, 'studentIndex'])->name('students');
        Route::get('/students/detail/{id}', [AdminUserController::class, 'showStudent'])->name('students.show');
        Route::put('/students/update/{id}', [AdminUserController::class, 'updateStudent'])->name('students.update');
        Route::post('/students/{id}/lock', [AdminUserController::class, 'toggleLock'])->name('students.toggle_lock'); 
        
        Route::get('/users', [AdminUserController::class, 'manageUsers'])->name('users');
        Route::post('/users/{id}/toggle-lock', [AdminUserController::class, 'toggleLock'])->name('users.toggle_lock');
        
        // Kiểm duyệt & Hệ thống
        Route::get('/classrooms', [AdminClassroomController::class, 'index'])->name('classrooms');
        Route::get('/classrooms/detail/{id}', [AdminClassroomController::class, 'showClassroom'])->name('classrooms.show');
        Route::post('/classrooms/{id}/lock', [AdminClassroomController::class, 'toggleLock'])->name('classrooms.toggle_lock');
        Route::delete('/classrooms/{id}', [AdminClassroomController::class, 'destroy'])->name('classrooms.destroy');
        
        Route::get('/exams', [AdminExamController::class, 'index'])->name('exams');
        Route::get('/exams/{id}/preview', [AdminExamController::class, 'preview'])->name('exams.preview');
        Route::delete('/exams/{id}', [AdminExamController::class, 'destroy'])->name('exams.destroy');

        Route::get('/documents', [AdminDocumentController::class, 'index'])->name('documents');
        Route::get('/documents/{id}/preview', [AdminDocumentController::class, 'preview'])->name('documents.preview');
        Route::get('/documents/{id}/download', [AdminDocumentController::class, 'download'])->name('documents.download');
        Route::delete('/documents/{id}', [AdminDocumentController::class, 'destroy'])->name('documents.destroy');
        
        // Cấu hình Prompt AI & Lịch sử
        Route::get('/prompts', [AdminPromptController::class, 'index'])->name('prompts');
        Route::post('/prompts/store', [AdminPromptController::class, 'store'])->name('prompts.store');       
        Route::put('/prompts/{id}', [AdminPromptController::class, 'update'])->name('prompts.update');
        Route::delete('/prompts/{id}', [AdminPromptController::class, 'destroy'])->name('prompts.destroy');
        
        Route::get('/ai-history', [AdminAiHistoryController::class, 'index'])->name('ai_history');
        Route::get('/ai-history/export', [AdminAiHistoryController::class, 'exportCsv'])->name('ai_history.export');
    });

});