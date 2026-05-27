@extends('layouts.student.student_app')

@section('title', 'Kết quả thi: ' . ($exam->title ?? 'Chi tiết bài làm'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/exam_result.css') }}?v={{ time() }}">
@endpush

@section('content')
    <div class="container-fluid mt-4 px-0" style="max-width: 1200px;">
        
        @php
            // 1. TINH TOAN VA PHAN LOAI DE THI
            $totalQ = max(1, $result->total_questions ?? 1);
            $essayCount = isset($exam->questions) ? $exam->questions->where('type', 'essay')->count() : 0;

            $hasEssay = ($essayCount > 0);
            $isPureEssay = isset($exam->questions) && $exam->questions->count() > 0
                ? ($essayCount == $exam->questions->count())
                : false;

            $rawScore = floatval($result->score ?? 0);

            /*
                Trac nghiem:
                - score = so cau dung
                - can quy doi ve thang 10

                Tu luan:
                - score da la diem he 10
                - khong duoc nhan them * 10 nua
            */
            if ($hasEssay) {
                $score10 = $rawScore;
            } else {
                $score10 = ($rawScore / $totalQ) * 10;
            }

            // Chan diem trong khoang 0 - 10
            $score10 = max(0, min(10, $score10));

            // Phan tram de ve vong tron
            $percentage = ($score10 / 10) * 100;
            $percentage = max(0, min(100, $percentage));

            $studentAnswers = \App\Models\StudentAnswer::where('exam_result_id', $result->id ?? 0)->get()->keyBy('question_id');

            // 3. THONG DIEP & MAU SAC DONG
            if ($isPureEssay && $rawScore == 0) {
                $statusColor = 'warning';
                $msg = 'Chờ giảng viên chấm';
                $msgIcon = 'bi-hourglass-split';
            } elseif ($score10 >= 8) {
                $statusColor = 'success';
                $msg = 'Xuất sắc! Kiến thức rất vững';
                $msgIcon = 'bi-star-fill';
            } elseif ($score10 >= 5) {
                $statusColor = 'info';
                $msg = 'Làm tốt lắm, cố gắng thêm nhé!';
                $msgIcon = 'bi-hand-thumbs-up-fill';
            } else {
                $statusColor = 'danger';
                $msg = 'Đừng nản lòng, hãy ôn tập lại!';
                $msgIcon = 'bi-shield-fill-exclamation';
            }
        @endphp

        <div class="row g-4 align-items-start">
            
            <div class="col-xl-8 col-lg-7">
                <div class="d-flex align-items-center justify-content-between mb-4 pb-3">
                    <div>
                        <a href="javascript:history.back()" class="text-muted text-decoration-none small fw-bold d-inline-flex align-items-center gap-2 hover-text-primary transition-all mb-3">
                            <div class="back-btn-circle d-flex align-items-center justify-content-center bg-white shadow-sm rounded-circle" style="width: 32px; height: 32px;"><i class="bi bi-arrow-left"></i></div> Quay lại
                        </a>
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <span class="premium-badge bg-theme-primary bg-opacity-10 text-theme-primary px-3 py-1 rounded-pill fw-bold text-uppercase letter-spacing-1 small">Chi tiết bài làm</span>
                        </div>
                        <h3 class="fw-900 theme-text-dark mb-0 lh-base">
                            {{ $exam->title ?? 'Bài kiểm tra' }}
                        </h3>
                    </div>
                </div>

                <div class="questions-list mb-5">
                    @php $labels = ['A', 'B', 'C', 'D', 'E', 'F']; @endphp

                    @forelse($exam->questions ?? [] as $index => $question)
                        @php
                            $userAnswer = $studentAnswers->get($question->id);
                            $userAnswerId = $userAnswer ? $userAnswer->answer_id : null;
                            $isEssay = $question->type === 'essay';
                            
                            $isCorrectQuestion = false;
                            if ($userAnswerId && !$isEssay) {
                                $pickedAns = $question->answers->where('id', $userAnswerId)->first();
                                $isCorrectQuestion = $pickedAns ? $pickedAns->is_correct : false;
                            }

                            // Xác định Header Status cho câu hỏi
                            if ($isEssay) {
                                $qStatusClass = 'bg-info bg-opacity-10 text-info';
                                $qStatusIcon = 'bi-pencil-square';
                                $qStatusText = 'Tự luận';
                            } elseif (!$userAnswerId) {
                                $qStatusClass = 'bg-secondary bg-opacity-10 text-secondary';
                                $qStatusIcon = 'bi-dash-circle';
                                $qStatusText = 'Bỏ trống';
                            } elseif ($isCorrectQuestion) {
                                $qStatusClass = 'bg-success bg-opacity-10 text-success';
                                $qStatusIcon = 'bi-check-circle-fill';
                                $qStatusText = 'Chính xác';
                            } else {
                                $qStatusClass = 'bg-danger bg-opacity-10 text-danger';
                                $qStatusIcon = 'bi-x-circle-fill';
                                $qStatusText = 'Sai';
                            }
                        @endphp

                        <div class="premium-question-card bg-white p-4 p-md-5 mb-4 position-relative">
                            
                            <div class="d-flex align-items-center gap-3 mb-4">
                                <div class="q-number-circle fw-bold fs-5 shadow-sm d-flex align-items-center justify-content-center flex-shrink-0">
                                    {{ $index + 1 }}
                                </div>
                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                    <span class="badge {{ $qStatusClass }} rounded-pill px-3 py-2 fw-bold fs-6 d-flex align-items-center gap-2">
                                        <i class="bi {{ $qStatusIcon }}"></i> {{ $qStatusText }}
                                    </span>
                                </div>
                            </div>
                            
                            <h5 class="fw-bold theme-text-dark lh-base mb-4 fs-5">{{ $question->content }}</h5>

                            @if(!$isEssay)
                                <div class="row g-3 ms-md-4">
                                    @foreach($question->answers as $aIndex => $ans)
                                        @php
                                            $isCorrectAns = $ans->is_correct; 
                                            $isUserPicked = ($userAnswerId == $ans->id); 
                                            
                                            // Dynamic Classes cho từng Đáp án (XÓA VIỀN CỨNG)
                                            $ansBoxClass = 'ans-box-neutral';
                                            $ansTextClass = 'text-dark';
                                            $ansIcon = '<i class="bi bi-circle opacity-25"></i>';

                                            if ($isUserPicked && $isCorrectAns) {
                                                $ansBoxClass = 'ans-box-correct';
                                                $ansTextClass = 'text-success fw-bold';
                                                $ansIcon = '<i class="bi bi-check-circle-fill text-success fs-5"></i>';
                                            } elseif ($isUserPicked && !$isCorrectAns) {
                                                $ansBoxClass = 'ans-box-wrong';
                                                $ansTextClass = 'text-danger text-decoration-line-through opacity-75';
                                                $ansIcon = '<i class="bi bi-x-circle-fill text-danger fs-5"></i>';
                                            } elseif (!$isUserPicked && $isCorrectAns) {
                                                $ansBoxClass = 'ans-box-highlight'; // Highlight đáp án đúng mà user bỏ qua
                                                $ansTextClass = 'text-success fw-bold';
                                                $ansIcon = '<i class="bi bi-check2 text-success fs-4 fw-bold"></i>';
                                            }
                                        @endphp
                                        
                                        <div class="col-12">
                                            <div class="premium-ans-box d-flex align-items-center p-3 p-md-4 {{ $ansBoxClass }}">
                                                <div class="ans-icon-wrapper me-3 flex-shrink-0 d-flex align-items-center justify-content-center">
                                                    {!! $ansIcon !!}
                                                </div>
                                                <div class="flex-grow-1" style="line-height: 1.6;">
                                                    <span class="fw-900 me-2 {{ $ansTextClass }}">{{ $labels[$aIndex] ?? '•' }}.</span>
                                                    <span class="{{ $ansTextClass }}">{{ $ans->content }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="ms-md-5">
                                    <div class="premium-essay-box bg-light p-4 mb-3">
                                        <div class="d-flex align-items-center gap-2 mb-3 text-muted small fw-bold text-uppercase">
                                            <i class="bi bi-person-workspace fs-5"></i> Bài làm của bạn
                                        </div>

                                        <div class="fw-medium text-dark lh-lg fs-6">
                                            {!! nl2br(e($userAnswer->content ?? 'Bạn đã bỏ trống.')) !!}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if(!empty($question->ai_explanation))
                                <div class="ai-explain-box mt-4 ms-md-4 p-4 p-md-5 position-relative overflow-hidden">
                                    <div class="ai-glow-effect"></div>
                                    <div class="d-flex gap-3 position-relative z-1">
                                        <div class="ai-avatar flex-shrink-0 d-flex align-items-center justify-content-center text-white fw-bold shadow-sm">
                                            AI
                                        </div>
                                        <div>
                                            <h6 class="fw-900 text-purple-dark mb-2 text-uppercase letter-spacing-1 d-flex align-items-center gap-2">
                                                <i class="bi bi-stars text-warning fs-5"></i> Trợ lý AI giải thích
                                            </h6>
                                            <div class="text-dark opacity-75 lh-lg fw-medium fs-6 ai-text-content">
                                                {!! nl2br(e($question->ai_explanation)) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                        </div>
                    @empty
                        <div class="text-center py-5 bg-white rounded-4 shadow-sm">
                            <i class="bi bi-inbox fs-1 text-muted mb-3 d-block opacity-25"></i>
                            <p class="text-muted fw-medium">Không tìm thấy chi tiết bài làm.</p>
                        </div>
                    @endforelse
                </div>

                <div class="d-flex flex-column flex-md-row justify-content-center gap-3 mb-5 pb-5">
                    <a href="{{ route('dashboard') ?? '#' }}" class="btn btn-light rounded-pill px-5 py-3 fw-bold hover-lift text-muted">
                        <i class="bi bi-house-door-fill me-2"></i> Trang chủ
                    </a>
                    <a href="{{ route('student.exams.create') ?? '#' }}" class="btn btn-theme-primary rounded-pill px-5 py-3 fw-bold shadow-sm hover-lift text-white d-flex align-items-center gap-2">
                        <i class="bi bi-arrow-repeat"></i> Làm đề khác
                    </a>
                </div>
            </div>

            <div class="col-xl-4 col-lg-5">
                <div class="position-sticky" style="top: 80px;">
                    
                    <div class="premium-result-card shadow-lg mb-4 status-{{ $statusColor }}" id="result-summary" data-score="{{ $percentage }}">
                        <div class="premium-glass-overlay"></div>
                        <div class="card-body p-4 p-md-5 text-center position-relative z-1">
                            
                            <h6 class="fw-bold text-white text-uppercase letter-spacing-1 mb-4 opacity-75">
                                {{ ($isPureEssay && ($result->score ?? 0) == 0) ? 'Trạng thái' : 'Tổng điểm' }}
                            </h6>

                            <div class="score-circle-wrapper mx-auto mb-4 position-relative d-flex align-items-center justify-content-center">
                                <svg class="progress-ring" width="160" height="160">
                                    <circle class="progress-ring-bg" stroke="rgba(255,255,255,0.2)" stroke-width="8" fill="transparent" r="74" cx="80" cy="80"/>
                                    <circle class="progress-ring-fill" stroke="#ffffff" stroke-width="8" fill="transparent" r="74" cx="80" cy="80" style="stroke-dasharray: 465; stroke-dashoffset: {{ 465 - (465 * $percentage) / 100 }};"/>
                                </svg>
                                <div class="score-value-inner d-flex flex-column align-items-center justify-content-center text-white">
                                    @if($isPureEssay && ($result->score ?? 0) == 0)
                                        <i class="bi bi-hourglass-split" style="font-size: 3rem;"></i>
                                    @else
                                        <span class="fw-900 lh-1" style="font-size: 3.5rem;">{{ number_format($score10, 1) }}</span>
                                    @endif
                                </div>
                            </div>

                            <h4 class="fw-800 mb-2 text-white">{{ $msg }} <i class="bi {{ $msgIcon }} ms-1"></i></h4>
                            
                            @if($isPureEssay && ($result->score ?? 0) == 0)
                                <p class="small text-white opacity-75 mb-4 fw-medium">
                                    <i class="bi bi-info-circle me-1"></i> Bài tự luận đang chờ giảng viên chấm.
                                </p>
                            @else
                                <div class="mb-4"></div>
                            @endif
                                                        
                            <div class="glass-stats-panel rounded-4 p-3 mb-2 text-start text-white">
                                @if(!$isPureEssay)
                                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom border-white border-opacity-10">
                                        <span class="opacity-75 fw-medium"><i class="bi bi-check2-circle me-2"></i>Câu đúng</span>
                                        <span class="fw-bold fs-5">{{ $result->score ?? 0 }}/{{ $totalQ }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom border-white border-opacity-10">
                                        <span class="opacity-75 fw-medium"><i class="bi bi-percent me-2"></i>Tỷ lệ đúng</span>
                                        <span class="fw-bold fs-5">{{ round($percentage) }}%</span>
                                    </div>
                                @else
                                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom border-white border-opacity-10">
                                        <span class="opacity-75 fw-medium"><i class="bi bi-journal-text me-2"></i>Loại bài thi</span>
                                        <span class="fw-bold">100% Tự luận</span>
                                    </div>
                                @endif
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="opacity-75 fw-medium"><i class="bi bi-calendar-event me-2"></i>Ngày nộp</span>
                                    <span class="fw-bold">{{ isset($result->created_at) ? $result->created_at->format('d/m/Y') : date('d/m/Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($hasEssay)
    @php
        $essayQuestion = $exam->questions->where('type', 'essay')->first();

        $essayAnswer = $essayQuestion
            ? \App\Models\StudentAnswer::where('exam_result_id', $result->id)
                ->where('question_id', $essayQuestion->id)
                ->first()
            : null;

        $essayBareme = $essayQuestion && $essayQuestion->answers->first()
            ? $essayQuestion->answers->first()->content
            : 'Chưa có đáp án gợi ý.';
    @endphp

    <div class="result-side-card p-4 mb-4">
        <div class="result-side-card-header">
            <h6 class="result-side-title">
                <i class="bi bi-chat-square-text-fill"></i>
                Nhận xét giảng viên
            </h6>

            <span class="result-score-badge">
                {{ $essayAnswer && $essayAnswer->score !== null ? number_format($essayAnswer->score, 1) : 'Chưa chấm' }}/10
            </span>
        </div>

        <div class="teacher-feedback-content">
            @if($essayAnswer && !empty($essayAnswer->feedback))
                {!! nl2br(e($essayAnswer->feedback)) !!}
            @else
                <span class="text-muted fst-italic">Giảng viên chưa nhập nhận xét cho bài làm này.</span>
            @endif
        </div>
    </div>

    <div class="result-side-card p-4 mb-4">
        <details class="bareme-details">
            <summary class="bareme-summary">
                <i class="bi bi-check2-all"></i>
                Xem gợi ý đáp án / Bareme
            </summary>

            <div class="bareme-scroll">
                {!! nl2br(e($essayBareme)) !!}
            </div>
        </details>
    </div>
@endif
                    <a href="{{ route('student.statistics') ?? '#' }}" class="btn bg-white w-100 rounded-pill fw-bold py-3 theme-text-primary shadow-sm hover-lift d-flex align-items-center justify-content-center gap-2 border-0">
                        <i class="bi bi-bar-chart-line-fill"></i> Xem thống kê cá nhân
                    </a>

                </div>
            </div> 

        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <script src="{{ asset('js/exam_result.js') }}?v={{ time() }}"></script>
@endpush