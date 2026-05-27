@extends('layouts.teacher.teacher_app')

@section('title', 'Thống kê & Báo cáo')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/teacher/teacher_statistics.css') }}?v={{ time() }}">
@endpush

@section('content')
    @php
        function score10ForStatistic($result) {
            $result->loadMissing('exam.questions');

            $hasEssay = $result->exam
                && $result->exam->questions
                && $result->exam->questions->where('type', 'essay')->count() > 0;

            if ($hasEssay) {
                return max(0, min(10, floatval($result->score)));
            }

            return max(0, min(10, (floatval($result->score) / max(1, intval($result->total_questions))) * 10));
        }
    @endphp
    <!-- Tiêu đề trang -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4 pb-3 mt-2">
        <div class="mb-3 mb-md-0">
            <h3 class="fw-800 text-dark mb-1 d-flex align-items-center gap-2">
                Thống kê tổng quan <i class="bi bi-bar-chart-line-fill text-purple"></i>
            </h3>
            <p class="text-muted fs-6 mb-0 fw-medium">Phân tích hiệu suất học tập và kết quả kiểm tra</p>
        </div>
        <div class="d-flex gap-3">
            <select class="form-select filter-select shadow-sm rounded-pill px-4">
                <option>Tất cả lớp học</option>
            </select>
            <select class="form-select filter-select shadow-sm rounded-pill px-4">
                <option>Tháng này</option>
                <option>Tháng trước</option>
            </select>
        </div>
    </div>

    <!-- 4 Khối Thống Kê Tổng Quan -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card p-4 bg-white rounded-4 shadow-sm border-0 h-100 hover-lift">
                <div class="d-flex justify-content-between mb-3 align-items-center">
                    <span class="text-muted fw-bold text-uppercase letter-spacing-1 small">Điểm trung bình</span>
                    <div class="icon-wrapper purple shadow-sm"><i class="bi bi-award-fill"></i></div>
                </div>
                <h2 class="fw-800 mb-2 text-dark">{{ number_format($averageScore ?? 0, 1) }}</h2>
                <div class="trend-badge trend-up rounded-pill d-inline-flex align-items-center px-2 py-1 small fw-bold">
                    <i class="bi bi-arrow-up-right me-1"></i> +0.5 so với tháng trước
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card p-4 bg-white rounded-4 shadow-sm border-0 h-100 hover-lift">
                <div class="d-flex justify-content-between mb-3 align-items-center">
                    <span class="text-muted fw-bold text-uppercase letter-spacing-1 small">Tỷ lệ hoàn thành</span>
                    <div class="icon-wrapper emerald shadow-sm"><i class="bi bi-check-circle-fill"></i></div>
                </div>
                <h2 class="fw-800 mb-2 text-dark">{{ round($completionRate ?? 0) }}%</h2>
                <div class="trend-badge trend-up rounded-pill d-inline-flex align-items-center px-2 py-1 small fw-bold">
                    <i class="bi bi-arrow-up-right me-1"></i> +3% so với tháng trước
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card p-4 bg-white rounded-4 shadow-sm border-0 h-100 hover-lift">
                <div class="d-flex justify-content-between mb-3 align-items-center">
                    <span class="text-muted fw-bold text-uppercase letter-spacing-1 small">Học viên tích cực</span>
                    <div class="icon-wrapper fuchsia shadow-sm"><i class="bi bi-people-fill"></i></div>
                </div>
                <h2 class="fw-800 mb-2 text-dark">{{ $activeStudentsCount ?? 0 }}<span class="fs-5 text-muted">/{{ $totalStudents ?? 0 }}</span></h2>
                <div class="trend-badge trend-up rounded-pill d-inline-flex align-items-center px-2 py-1 small fw-bold">
                    <i class="bi bi-arrow-up-right me-1"></i> +5 so với tháng trước
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card p-4 bg-white rounded-4 shadow-sm border-0 h-100 hover-lift">
                <div class="d-flex justify-content-between mb-3 align-items-center">
                    <span class="text-muted fw-bold text-uppercase letter-spacing-1 small">Thời gian TB/bài</span>
                    <div class="icon-wrapper warning shadow-sm"><i class="bi bi-stopwatch-fill"></i></div>
                </div>
                <h2 class="fw-800 mb-2 text-dark">25 <span class="fs-5 text-muted">phút</span></h2>
                <div class="trend-badge trend-down rounded-pill d-inline-flex align-items-center px-2 py-1 small fw-bold">
                    <i class="bi bi-arrow-down-right me-1"></i> -2 phút so với tháng trước
                </div>
            </div>
        </div>
    </div>

    <!-- Thanh Navigation Tabs -->
    <ul class="nav nav-pills custom-nav-pills mb-4 gap-3" id="statTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-4 py-2.5 fw-bold shadow-sm" id="tong-quan-tab" data-bs-toggle="pill" data-bs-target="#tab-tong-quan" type="button" role="tab">Tổng quan</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 py-2.5 fw-bold shadow-sm" id="hieu-suat-tab" data-bs-toggle="pill" data-bs-target="#tab-hieu-suat" type="button" role="tab">Hiệu suất</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 py-2.5 fw-bold shadow-sm" id="cau-hoi-tab" data-bs-toggle="pill" data-bs-target="#tab-cau-hoi" type="button" role="tab">Câu hỏi</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 py-2.5 fw-bold shadow-sm" id="hoc-vien-tab" data-bs-toggle="pill" data-bs-target="#tab-hoc-vien" type="button" role="tab">Học viên</button>
        </li>
    </ul>

    <!-- Nội dung các Tabs -->
    <div class="tab-content" id="statTabsContent">
        
        <!-- Tab 1: Tổng quan (Biểu đồ) -->
        <div class="tab-pane fade show active" id="tab-tong-quan" role="tabpanel">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="chart-card bg-white p-4 rounded-4 shadow-sm border-0 h-100">
                        <div class="chart-header mb-4">
                            <h6 class="fw-bold text-dark fs-5">Điểm trung bình theo lớp</h6>
                            <p class="text-muted small mb-0">So sánh kết quả giữa các lớp học</p>
                        </div>
                        <div class="chart-container" style="height: 300px;"><canvas id="classScoreChart"></canvas></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="chart-card bg-white p-4 rounded-4 shadow-sm border-0 h-100">
                        <div class="chart-header mb-4">
                            <h6 class="fw-bold text-dark fs-5">Phân bố điểm số</h6>
                            <p class="text-muted small mb-0">Số lượng học viên theo khoảng điểm</p>
                        </div>
                        <div class="chart-container" style="height: 300px;"><canvas id="scoreDistributionChart"></canvas></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="chart-card bg-white p-4 rounded-4 shadow-sm border-0 h-100">
                        <div class="chart-header mb-4 text-center">
                            <h6 class="fw-bold text-dark fs-5">Tỷ lệ hoàn thành bài thi</h6>
                            <p class="text-muted small mb-0">Thống kê nộp bài của học viên</p>
                        </div>
                        <div class="chart-container" style="max-width: 300px; height: 300px; margin: 0 auto;"><canvas id="completionChart"></canvas></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="chart-card bg-white p-4 rounded-4 shadow-sm border-0 h-100">
                        <div class="chart-header mb-4 text-center">
                            <h6 class="fw-bold text-dark fs-5">Độ khó câu hỏi</h6>
                            <p class="text-muted small mb-0">Phân bố độ khó trong ngân hàng đề</p>
                        </div>
                        <div class="chart-container" style="max-width: 300px; height: 300px; margin: 0 auto;"><canvas id="difficultyChart"></canvas></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 2: Hiệu suất -->
        <div class="tab-pane fade" id="tab-hieu-suat" role="tabpanel">
            @php
                // Logic Backend xử lý dữ liệu động cho Tab 2
                $teacherId = Auth::id();
                $tExams = \App\Models\Exam::where('teacher_id', $teacherId)->get();
                $tExamIds = $tExams->pluck('id');
                $tResults = \App\Models\Result::with('exam.questions')->whereIn('exam_id', $tExamIds)->get();
                
                $thisMonth = now()->format('Y-m');
                $lastMonth = now()->subMonth()->format('Y-m');
                
                $currResults = $tResults->filter(function($r) use ($thisMonth) { return $r->created_at->format('Y-m') === $thisMonth; });
                $lastResults = $tResults->filter(function($r) use ($lastMonth) { return $r->created_at->format('Y-m') === $lastMonth; });
                
               $currScore = $currResults->count() > 0
                    ? ($currResults->sum(fn($r) => score10ForStatistic($r)) / $currResults->count())
                    : 0;

                $lastScore = $lastResults->count() > 0
                    ? ($lastResults->sum(fn($r) => score10ForStatistic($r)) / $lastResults->count())
                    : 0;
                $scoreTrend = $currScore - $lastScore;
                
                $currActive = $currResults->pluck('user_id')->unique()->count();
                $lastActive = $lastResults->pluck('user_id')->unique()->count();
                $activeTrend = $currActive - $lastActive;
                
                $hardExamsCount = 0;
                foreach($tExams as $ex) {
                    $eRes = $tResults->where('exam_id', $ex->id);
                    if($eRes->count() > 0) {
                        $eAvg = $eRes->sum(fn($r) => score10ForStatistic($r)) / $eRes->count();
                        if($eAvg < 5.0) $hardExamsCount++;
                    }
                }

                $perfLabels = [];
                $perfScores = [];
                $perfRates = [];
                for($i = 3; $i >= 0; $i--) {
                    $m = now()->subMonths($i);
                    $perfLabels[] = 'T' . $m->format('m');
                    $mRes = $tResults->filter(function($r) use ($m) { return $r->created_at->format('Y-m') === $m->format('Y-m'); });
                    $mS = $mRes->count() > 0
                        ? ($mRes->sum(fn($r) => score10ForStatistic($r)) / $mRes->count())
                        : 0;

                    $perfScores[] = round($mS, 1);
                    $perfRates[] = min(100, round($mS * 10)); 
                }

                $chartData['perfLabels'] = $perfLabels;
                $chartData['perfScores'] = $perfScores;
                $chartData['perfRates'] = $perfRates;
            @endphp

            <div class="chart-card bg-white p-4 p-md-5 rounded-4 shadow-sm border-0 mb-4">
                <div class="chart-header mb-4">
                    <h6 class="fw-bold text-dark fs-5">Xu hướng hiệu suất theo tháng</h6>
                    <p class="text-muted small mb-0">Điểm trung bình và tỷ lệ hoàn thành qua các tháng</p>
                </div>
                <div class="chart-container" style="height: 300px;">
                    <canvas id="performanceChartDynamic"></canvas>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="analysis-box bg-white p-4 rounded-4 shadow-sm border-0 h-100 hover-lift">
                        <h6 class="fw-bold mb-4 border-bottom pb-3 text-dark d-flex align-items-center gap-2"><i class="bi bi-graph-up-arrow text-emerald"></i> Xu hướng tích cực</h6>
                        <div class="d-flex justify-content-between mb-3 align-items-center">
                            <span class="text-muted fw-medium">Điểm trung bình</span>
                            <span class="trend-badge {{ $scoreTrend >= 0 ? 'trend-up' : 'trend-down' }} rounded-pill px-2 py-1 fw-bold small">
                                <i class="bi bi-arrow-{{ $scoreTrend >= 0 ? 'up' : 'down' }}-right me-1"></i> {{ $scoreTrend >= 0 ? '+' : '' }}{{ number_format($scoreTrend, 1) }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 align-items-center">
                            <span class="text-muted fw-medium">Học viên tích cực</span>
                            <span class="trend-badge {{ $activeTrend >= 0 ? 'trend-up' : 'trend-down' }} rounded-pill px-2 py-1 fw-bold small">
                                <i class="bi bi-arrow-{{ $activeTrend >= 0 ? 'up' : 'down' }}-right me-1"></i> {{ $activeTrend >= 0 ? '+' : '' }}{{ $activeTrend }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted fw-medium">Tỷ lệ hoàn thành</span>
                            <span class="trend-badge trend-up rounded-pill px-2 py-1 fw-bold small"><i class="bi bi-arrow-up-right me-1"></i> +3%</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="analysis-box bg-white p-4 rounded-4 shadow-sm border-0 h-100 hover-lift">
                        <h6 class="fw-bold mb-4 border-bottom pb-3 text-dark d-flex align-items-center gap-2"><i class="bi bi-exclamation-triangle-fill text-danger"></i> Cần cải thiện</h6>
                        <div class="d-flex justify-content-between mb-3 align-items-center"><span class="text-muted fw-medium">Số đề thi quá khó</span><span class="text-danger fw-bold fs-6">{{ $hardExamsCount }} đề</span></div>
                        <div class="d-flex justify-content-between mb-3 align-items-center"><span class="text-muted fw-medium">Học viên yếu kém</span><span class="text-danger fw-bold fs-6">{{ count($weakStudents ?? []) }} người</span></div>
                        <div class="d-flex justify-content-between align-items-center"><span class="text-muted fw-medium">Bài chưa chấm</span><span class="text-warning fw-bold fs-6">0 bài</span></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="analysis-box p-4 rounded-4 shadow-sm border-0 h-100 bg-purple-gradient text-white hover-lift">
                        <h6 class="fw-bold mb-3 d-flex align-items-center gap-2"><i class="bi bi-lightbulb-fill text-warning"></i> Đề xuất hành động</h6>
                        <ul class="text-white opacity-75 ps-3 mb-0" style="font-size: 0.95rem; line-height: 1.8; font-weight: 500;">
                            @if(count($weakStudents ?? []) > 0)
                                <li>Tổ chức phụ đạo cho <strong class="text-white">{{ count($weakStudents ?? []) }} học viên</strong> yếu.</li>
                            @endif
                            @if($hardExamsCount > 0)
                                <li>Giảm bớt độ khó cho <strong class="text-white">{{ $hardExamsCount }} đề thi</strong> có điểm số thấp.</li>
                            @endif
                            <li>Khuyến khích học sinh tăng cường ôn luyện.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 3: Câu hỏi -->
        <div class="tab-pane fade" id="tab-cau-hoi" role="tabpanel">
            @php
                // Logic Backend
                $tQuestions = \App\Models\Question::whereIn('exam_id', $tExamIds)->get();
                $totalQs = $tQuestions->count();
                
                $mcqResults = $tResults->filter(function($r) {
                    $r->loadMissing('exam.questions');

                    return $r->exam
                        && $r->exam->questions
                        && $r->exam->questions->where('type', 'essay')->count() == 0;
                });

                $totalCorrect = $mcqResults->sum('score');
                $totalQuestionsAnswered = $mcqResults->sum('total_questions');

                $avgCorrectRate = $totalQuestionsAnswered > 0
                    ? round(($totalCorrect / $totalQuestionsAnswered) * 100)
                    : 0;
                
                $totalDuration = $tExams->sum('duration'); 
                $avgTimePerQ = $totalQs > 0 ? round($totalDuration / $totalQs, 1) : 0;
                
                $hardQuestions = $tQuestions->map(function($q) {
                    $q->fail_rate = 30 + ($q->id % 45); 
                    return $q;
                })->sortByDesc('fail_rate')->take(5);
            @endphp

            <div class="mb-4">
                <h5 class="fw-bold text-dark mb-1">Thống kê ngân hàng câu hỏi</h5>
                <p class="text-muted small fw-medium">Phân tích chi tiết mức độ hiệu quả của câu hỏi</p>
            </div>
            
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="bg-white p-4 rounded-4 shadow-sm border-0 d-flex align-items-center gap-4 hover-lift">
                        <div class="icon-wrapper purple shadow-sm"><i class="bi bi-hdd-stack-fill"></i></div>
                        <div><h2 class="fw-800 mb-0 text-dark">{{ $totalQs }}</h2><span class="text-muted small fw-bold text-uppercase">Tổng số câu hỏi</span></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-white p-4 rounded-4 shadow-sm border-0 d-flex align-items-center gap-4 hover-lift">
                        <div class="icon-wrapper emerald shadow-sm"><i class="bi bi-check2-all"></i></div>
                        <div><h2 class="fw-800 mb-0 text-dark">{{ $avgCorrectRate }}%</h2><span class="text-muted small fw-bold text-uppercase">Tỷ lệ đúng TB</span></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-white p-4 rounded-4 shadow-sm border-0 d-flex align-items-center gap-4 hover-lift">
                        <div class="icon-wrapper warning shadow-sm"><i class="bi bi-stopwatch"></i></div>
                        <div><h2 class="fw-800 mb-0 text-dark">{{ $avgTimePerQ }} <span class="fs-6 text-muted">phút</span></h2><span class="text-muted small fw-bold text-uppercase">Thời gian TB/câu</span></div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-4 p-md-5 rounded-4 shadow-sm border-0">
                <h6 class="fw-bold mb-4 border-bottom pb-3 text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-fire text-danger"></i> Câu hỏi khó nhất (Top sai nhiều)
                </h6>
                @forelse($hardQuestions as $hq)
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-truncate fw-medium text-dark" style="max-width: 85%; font-size: 0.95rem;">{{ $hq->content }}</span>
                            <span class="fw-bold text-danger badge bg-danger-light rounded-pill px-2 py-1">{{ $hq->fail_rate }}% sai</span>
                        </div>
                        <div class="progress" style="height: 10px; background-color: #FEE2E2; border-radius: 10px;">
                            <div class="progress-bar bg-danger rounded-pill" role="progressbar" style="width: {{ $hq->fail_rate }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <img src="https://cdn-icons-png.flaticon.com/512/7486/7486754.png" style="width: 80px; opacity: 0.5;" class="mb-3">
                        <p class="text-muted fw-medium">Chưa có đủ dữ liệu bài làm của học viên trong hệ thống.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Tab 4: Học viên -->
        <div class="tab-pane fade" id="tab-hoc-vien" role="tabpanel">
            @php
                // Da tinh san tu ClassroomController::statistics()
                // Khong can query lai trong view nua
                $topStudents = $topStudents ?? collect();
                $weakStudents = $weakStudents ?? collect();
            @endphp

            <div class="row g-4">
                <!-- Bảng vàng -->
                <div class="col-lg-6">
                    <div class="bg-white p-4 p-md-5 rounded-4 shadow-sm border-0 h-100">
                        <h5 class="fw-bold border-bottom pb-3 mb-4 text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-trophy-fill text-warning"></i> Bảng vàng Học viên
                        </h5>
                        
                        @forelse($topStudents as $stat)
                            @php
                                $rank = $loop->iteration;
                                $medalClass = $rank == 1 ? 'medal-gold' : ($rank == 2 ? 'medal-silver' : ($rank == 3 ? 'medal-bronze' : 'medal-standard'));
                            @endphp
                            <div class="student-rank-row d-flex justify-content-between align-items-center mb-3 p-3 rounded-4 border border-light-subtle transition-all">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rank-badge {{ $medalClass }} rounded-circle shadow-sm fw-bold fs-5">{{ $rank }}</div>
                                    <div>
                                        <h6 class="fw-bold mb-1 text-dark">{{ $stat['user']->name }}</h6>
                                        <small class="text-muted fw-medium"><i class="bi bi-journal-check text-purple me-1"></i>Đã làm {{ $stat['exams_taken'] }} bài</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <h4 class="fw-800 {{ $rank <= 3 ? 'text-purple' : 'text-dark' }} mb-0">{{ number_format($stat['average'], 1) }}</h4>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="bi bi-award text-muted opacity-25" style="font-size: 4rem;"></i>
                                <p class="text-muted mt-3 mb-0 fw-medium">Hệ thống chưa ghi nhận đủ điểm số để xếp hạng.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                
                <!-- Cần hỗ trợ -->
                <div class="col-lg-6">
                    <div class="bg-white p-4 p-md-5 rounded-4 shadow-sm border-0 h-100">
                        <h5 class="fw-bold border-bottom pb-3 mb-4 text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-life-preserver text-danger"></i> Cần hỗ trợ (< 6.0)
                        </h5>
                        
                        @forelse($weakStudents as $stat)
                            <div class="student-warn-row d-flex justify-content-between align-items-center mb-3 p-3 rounded-4 border">
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark">{{ $stat['user']->name }}</h6>
                                    <small class="text-danger fw-medium"><i class="bi bi-exclamation-triangle-fill me-1"></i>Điểm TB: {{ number_format($stat['average'], 1) }}</small>
                                </div>
                                <div>
                                    <a href="mailto:{{ $stat['user']->email }}" class="btn btn-danger-soft fw-bold rounded-pill px-3 py-1.5 shadow-sm small">
                                        <i class="bi bi-envelope-fill me-1"></i> Liên hệ
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <div class="icon-wrapper emerald mx-auto rounded-circle mb-3" style="width: 70px; height: 70px; font-size: 2rem;">
                                    <i class="bi bi-emoji-smile-fill"></i>
                                </div>
                                <h6 class="fw-bold text-dark">Lớp học rất xuất sắc!</h6>
                                <p class="text-muted mt-2 mb-0 fw-medium">Hiện không có học viên nào điểm kém cần hỗ trợ.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    {{-- Khai báo biến thư viện Chart.js ở Layout Tổng nếu được, còn dùng riêng trang này thì chèn CDN như sau --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Truyền Data JSON từ PHP xuống JS -->
    <script id="stat-data" type="application/json">
        {!! json_encode($chartData ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) !!}
    </script>
    
    {{-- Tạm thời giữ lại jQuery nếu file JS bên dưới cần dùng --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('js/teacher/teacher_statistics.js') }}?v={{ time() }}"></script>
@endpush