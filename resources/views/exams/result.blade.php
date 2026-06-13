@extends('layouts.student.student_app')

@section('title', 'Kết quả thi: ' . ($exam->title ?? 'Chi tiết bài làm'))

@push('styles')
    <link rel="stylesheet" href="{{ versioned_asset('css/exam_result.css') }}">
@endpush

@section('content')
    @php
        $questions = collect($exam->questions ?? []);
        $totalQ = max(1, intval($result->total_questions ?: $questions->count() ?: 1));
        $essayCount = $questions->where('type', 'essay')->count();
        $hasEssay = $essayCount > 0;
        $isPureEssay = $questions->count() > 0 ? ($essayCount === $questions->count()) : false;
        $rawScore = floatval($result->score ?? 0);
        $score10 = $hasEssay ? $rawScore : (($rawScore / $totalQ) * 10);
        $score10 = max(0, min(10, $score10));
        $percentage = max(0, min(100, ($score10 / 10) * 100));
        $studentAnswers = \App\Models\StudentAnswer::where('exam_result_id', $result->id ?? 0)->get()->keyBy('question_id');
        $answeredCount = $studentAnswers->count();

        $mcqTotal = 0;
        $mcqCorrect = 0;
        $mcqWrong = 0;
        $mcqBlank = 0;
        $essayScoredCount = 0;
        $essayScoreTotal = 0;
        $questionSummaries = [];

        foreach ($questions as $index => $question) {
            $userAnswer = $studentAnswers->get($question->id);
            $userAnswerId = $userAnswer ? $userAnswer->answer_id : null;
            $isEssay = $question->type === 'essay';
            $status = 'blank';
            $statusText = 'Bỏ trống';
            $statusIcon = 'bi-dash-circle';

            if ($isEssay) {
                $essayScore = ($userAnswer && $userAnswer->score !== null) ? floatval($userAnswer->score) : null;
                $status = $essayScore === null ? 'pending' : 'essay';
                $statusText = $essayScore === null ? 'Chờ chấm' : number_format($essayScore, 1) . ' điểm';
                $statusIcon = $essayScore === null ? 'bi-hourglass-split' : 'bi-pencil-square';

                if ($essayScore !== null) {
                    $essayScoredCount++;
                    $essayScoreTotal += $essayScore;
                }
            } else {
                $mcqTotal++;

                if (!$userAnswerId) {
                    $mcqBlank++;
                } else {
                    $pickedAnswer = $question->answers->where('id', $userAnswerId)->first();

                    if ($pickedAnswer && $pickedAnswer->is_correct) {
                        $mcqCorrect++;
                        $status = 'correct';
                        $statusText = 'Đúng';
                        $statusIcon = 'bi-check-circle';
                    } else {
                        $mcqWrong++;
                        $status = 'wrong';
                        $statusText = 'Sai';
                        $statusIcon = 'bi-x-circle';
                    }
                }
            }

            $questionSummaries[$question->id] = [
                'number' => $index + 1,
                'is_essay' => $isEssay,
                'status' => $status,
                'text' => $statusText,
                'icon' => $statusIcon,
            ];
        }

        $accuracyRate = $mcqTotal > 0 ? round(($mcqCorrect / max(1, $mcqTotal)) * 100) : round($percentage);
        $submittedAt = $result->created_at ? $result->created_at->format('d/m/Y H:i') : '--/--/---- --:--';
        $durationText = $exam->duration ? $exam->duration . ' phút' : 'Không rõ';
        $subject = $exam->subject ?? ($exam->classroom->name ?? 'Môn chung');

        if ($answeredCount === 0) {
            $statusColor = 'danger';
            $msg = 'Bài bỏ trống';
            $msgIcon = 'bi-exclamation-circle';
        } elseif ($isPureEssay && $rawScore == 0 && $essayScoredCount === 0) {
            $statusColor = 'warning';
            $msg = 'Chờ giảng viên chấm';
            $msgIcon = 'bi-hourglass-split';
        } elseif ($score10 >= 8) {
            $statusColor = 'success';
            $msg = 'Kết quả rất tốt';
            $msgIcon = 'bi-stars';
        } elseif ($score10 >= 5) {
            $statusColor = 'info';
            $msg = 'Đã hoàn thành';
            $msgIcon = 'bi-check2-circle';
        } else {
            $statusColor = 'danger';
            $msg = 'Cần ôn tập thêm';
            $msgIcon = 'bi-arrow-repeat';
        }
    @endphp

    <div class="online-result-page result-redesign-page" id="result-summary" data-score="{{ $percentage }}">
        <div class="result-topbar mb-4">
            <div class="result-topbar-main">
                <a href="javascript:history.back()" class="result-back-link" aria-label="Quay lại">
                    <i class="bi bi-arrow-left"></i>
                </a>

                <div class="result-title-icon">
                    <i class="bi bi-journal-bookmark"></i>
                </div>

                <div>
                    <h2>{{ $exam->title ?? 'Bài kiểm tra' }}</h2>
                    <div class="result-topbar-meta">
                        <span>{{ Auth::user()->name ?? 'Học viên' }}</span>
                        <span>{{ $subject }}</span>
                        <span>{{ $submittedAt }}</span>
                    </div>
                </div>
            </div>

            <div class="result-total-score result-score-{{ $statusColor }}">
                <strong>{{ number_format($score10, 1) }}</strong><span>/10</span>
                <small>Tổng điểm</small>
            </div>
        </div>

        <div class="result-metric-grid mb-4">
            <div class="result-metric-card">
                <span class="metric-icon metric-icon-primary"><i class="bi bi-check2-square"></i></span>
                <div>
                    <p>Câu trắc nghiệm đúng</p>
                    <strong>{{ $mcqTotal > 0 ? $mcqCorrect . '/' . $mcqTotal : 'Không có' }}</strong>
                </div>
            </div>

            <div class="result-metric-card">
                <span class="metric-icon metric-icon-success"><i class="bi bi-bullseye"></i></span>
                <div>
                    <p>Độ chính xác</p>
                    <strong>{{ $accuracyRate }}%</strong>
                </div>
            </div>

            <div class="result-metric-card">
                <span class="metric-icon metric-icon-amber"><i class="bi bi-clock-history"></i></span>
                <div>
                    <p>Thời lượng đề</p>
                    <strong>{{ $durationText }}</strong>
                </div>
            </div>

            <div class="result-metric-card">
                <span class="metric-icon metric-icon-blue"><i class="bi {{ $msgIcon }}"></i></span>
                <div>
                    <p>Trạng thái</p>
                    <strong>{{ $msg }}</strong>
                </div>
            </div>
        </div>

        <div class="row g-4 align-items-start">
            <div class="col-xl-3 col-lg-4">
                <aside class="result-outline-card position-sticky" style="top: 84px;">
                    <h5>Mục lục câu hỏi</h5>

                    @if($mcqTotal > 0)
                        <div class="result-outline-section">
                            <div class="outline-section-title">
                                <span class="outline-section-icon"><i class="bi bi-list-check"></i></span>
                                <div>
                                    <strong>Trắc nghiệm</strong>
                                    <small>{{ $mcqCorrect }}/{{ $mcqTotal }} câu đúng</small>
                                </div>
                            </div>

                            <div class="result-outline-grid">
                                @foreach($questionSummaries as $questionId => $summary)
                                    @if(!$summary['is_essay'])
                                        <a href="#question-{{ $questionId }}" class="outline-question-btn is-{{ $summary['status'] }}">
                                            {{ $summary['number'] }}
                                        </a>
                                    @endif
                                @endforeach
                            </div>

                            <div class="outline-legend">
                                <span><i class="legend-dot correct"></i>Đúng: {{ $mcqCorrect }}</span>
                                <span><i class="legend-dot wrong"></i>Sai: {{ $mcqWrong }}</span>
                                @if($mcqBlank > 0)
                                    <span><i class="legend-dot blank"></i>Bỏ trống: {{ $mcqBlank }}</span>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($essayCount > 0)
                        <div class="result-outline-section">
                            <div class="outline-section-title">
                                <span class="outline-section-icon essay"><i class="bi bi-file-text"></i></span>
                                <div>
                                    <strong>Tự luận</strong>
                                    <small>{{ $essayScoredCount }}/{{ $essayCount }} câu đã chấm</small>
                                </div>
                            </div>

                            <div class="essay-score-summary">
                                <span>Điểm tự luận đã chấm</span>
                                <strong>{{ number_format($essayScoreTotal, 1) }}</strong>
                            </div>

                            <div class="result-outline-list">
                                @foreach($questionSummaries as $questionId => $summary)
                                    @if($summary['is_essay'])
                                        <a href="#question-{{ $questionId }}" class="essay-outline-item is-{{ $summary['status'] }}">
                                            <span>Câu {{ $summary['number'] }}</span>
                                            <strong>{{ $summary['text'] }}</strong>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </aside>
            </div>

            <div class="col-xl-9 col-lg-8">
                @if($mcqTotal > 0)
                    <section class="result-section-panel mb-4">
                        <div class="result-section-header">
                            <div class="result-section-title">
                                <span><i class="bi bi-list-check"></i></span>
                                <div>
                                    <h4>Phần I: Trắc nghiệm</h4>
                                    <p>{{ $mcqTotal }} câu</p>
                                </div>
                            </div>
                            <div class="section-score">{{ $mcqCorrect }}/{{ $mcqTotal }} câu đúng</div>
                        </div>

                        <div class="result-question-stack">
                            @foreach($questions as $index => $question)
                                @continue($question->type === 'essay')

                                @php
                                    $userAnswer = $studentAnswers->get($question->id);
                                    $userAnswerId = $userAnswer ? $userAnswer->answer_id : null;
                                    $summary = $questionSummaries[$question->id];
                                    $labels = ['A', 'B', 'C', 'D', 'E', 'F'];
                                @endphp

                                <article class="premium-question-card result-question-card is-{{ $summary['status'] }}" id="question-{{ $question->id }}">
                                    <div class="result-question-head">
                                        <div class="question-status-icon">
                                            <i class="bi {{ $summary['icon'] }}"></i>
                                        </div>

                                        <div>
                                            <div class="question-kicker">
                                                Câu {{ $summary['number'] }} <span>Trắc nghiệm</span>
                                            </div>
                                            <h5>{{ $question->content }}</h5>
                                        </div>

                                        <span class="question-status-pill">{{ $summary['text'] }}</span>
                                    </div>

                                    <div class="result-answer-list">
                                        @foreach($question->answers as $answerIndex => $answer)
                                            @php
                                                $isCorrectAnswer = (bool) $answer->is_correct;
                                                $isUserPicked = intval($userAnswerId) === intval($answer->id);
                                                $answerClass = 'is-neutral';

                                                if ($isUserPicked && $isCorrectAnswer) {
                                                    $answerClass = 'is-correct-picked';
                                                } elseif ($isUserPicked && !$isCorrectAnswer) {
                                                    $answerClass = 'is-wrong-picked';
                                                } elseif (!$isUserPicked && $isCorrectAnswer) {
                                                    $answerClass = 'is-correct-answer';
                                                }
                                            @endphp

                                            <div class="result-answer-option {{ $answerClass }}">
                                                <span class="answer-letter">{{ $labels[$answerIndex] ?? '•' }}</span>
                                                <span class="answer-content">{{ $answer->content }}</span>

                                                <span class="answer-tags">
                                                    @if($isCorrectAnswer)
                                                        <small><i class="bi bi-check2"></i>Đáp án đúng</small>
                                                    @endif

                                                    @if($isUserPicked)
                                                        <small><i class="bi bi-person-check"></i>Bạn chọn</small>
                                                    @endif
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>

                                    @if(!empty($question->ai_explanation))
                                        <div class="ai-explain-box result-explain-box">
                                            <div class="ai-avatar">AI</div>
                                            <div>
                                                <h6><i class="bi bi-stars"></i> Trợ lý AI giải thích</h6>
                                                <p>{!! nl2br(e($question->ai_explanation)) !!}</p>
                                            </div>
                                        </div>
                                    @endif
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif

                @if($essayCount > 0)
                    <section class="result-section-panel mb-4">
                        <div class="result-section-header">
                            <div class="result-section-title">
                                <span class="essay"><i class="bi bi-file-text"></i></span>
                                <div>
                                    <h4>{{ $mcqTotal > 0 ? 'Phần II: Tự luận' : 'Phần I: Tự luận' }}</h4>
                                    <p>{{ $essayCount }} câu</p>
                                </div>
                            </div>
                            <div class="section-score essay-score">{{ $essayScoredCount }}/{{ $essayCount }} câu đã chấm</div>
                        </div>

                        <div class="result-question-stack">
                            @foreach($questions as $index => $question)
                                @continue($question->type !== 'essay')

                                @php
                                    $userAnswer = $studentAnswers->get($question->id);
                                    $summary = $questionSummaries[$question->id];
                                    $essayScore = ($userAnswer && $userAnswer->score !== null) ? floatval($userAnswer->score) : null;
                                    $essayFeedback = $userAnswer ? $userAnswer->feedback : null;
                                    $baremeAnswer = $question->answers->first();
                                    $bareme = $baremeAnswer ? $baremeAnswer->content : null;
                                    $essayProgress = $essayScore !== null ? min(100, max(0, $essayScore * 10)) : 0;
                                @endphp

                                <article class="premium-question-card result-question-card result-essay-card is-{{ $summary['status'] }}" id="question-{{ $question->id }}">
                                    <div class="result-question-head">
                                        <div class="question-status-icon">
                                            <i class="bi {{ $summary['icon'] }}"></i>
                                        </div>

                                        <div>
                                            <div class="question-kicker">
                                                Câu {{ $summary['number'] }} <span>Tự luận</span>
                                            </div>
                                            <h5>{{ $question->content }}</h5>
                                        </div>

                                        <span class="question-status-pill">{{ $summary['text'] }}</span>
                                    </div>

                                    <div class="essay-score-line">
                                        <div>
                                            <span>Điểm đạt được</span>
                                            <strong>{{ $essayScore !== null ? number_format($essayScore, 1) . '/10' : 'Chưa chấm' }}</strong>
                                        </div>
                                        <div class="essay-progress">
                                            <span style="width: {{ $essayProgress }}%"></span>
                                        </div>
                                    </div>

                                    <div class="result-essay-answer">
                                        <h6><i class="bi bi-chat-left-text"></i>Bài làm của bạn</h6>
                                        <div class="rich-text-content">
                                            {!! \App\Support\RichTextSanitizer::render($userAnswer->content ?? null, 'Bạn đã bỏ trống.') !!}
                                        </div>
                                    </div>

                                    @if(!empty($essayFeedback))
                                        <div class="teacher-feedback-box">
                                            <h6>Nhận xét của giáo viên</h6>
                                            <div class="rich-text-content">
                                                {!! \App\Support\RichTextSanitizer::render($essayFeedback) !!}
                                            </div>
                                        </div>
                                    @endif

                                    @if(!empty($bareme))
                                        <details class="bareme-details result-bareme-details">
                                            <summary class="bareme-summary">
                                                <i class="bi bi-check2-all"></i>
                                                Xem chi tiết đáp án / bareme
                                            </summary>
                                            <div class="bareme-scroll">
                                                {!! nl2br(e($bareme)) !!}
                                            </div>
                                        </details>
                                    @endif
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif

                @if($questions->isEmpty())
                    <div class="result-empty-card">
                        <i class="bi bi-inbox"></i>
                        <p>Không tìm thấy chi tiết bài làm.</p>
                    </div>
                @endif

                <div class="result-actions">
                    <a href="{{ route('dashboard') }}" class="btn btn-light fw-bold">
                        <i class="bi bi-house-door-fill me-2"></i>Trang chủ
                    </a>
                    <a href="{{ route('student.question-banks') }}" class="btn btn-theme-primary fw-bold">
                        <i class="bi bi-arrow-repeat me-2"></i>Làm đề khác
                    </a>
                    <a href="{{ route('student.statistics') }}" class="btn btn-outline-theme fw-bold">
                        <i class="bi bi-bar-chart-line-fill me-2"></i>Thống kê cá nhân
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <script src="{{ versioned_asset('js/exam_result.js') }}"></script>
@endpush
