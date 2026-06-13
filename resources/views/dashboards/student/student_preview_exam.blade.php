@extends('layouts.student.student_app')

@section('title', 'Xem trước đề: ' . ($exam->title ?? 'Chi tiết đề thi'))

@push('styles')
    <link rel="stylesheet" href="{{ versioned_asset('css/student/student_preview_exam.css') }}">
@endpush

@section('content')
    <div class="exam-header-card shadow-sm mb-4 p-4 bg-white rounded-4 border border-soft">
        <div class="d-flex justify-content-between align-items-md-center flex-column flex-md-row gap-3">
            <div>
                <span class="badge bg-purple-light text-purple-dark border border-purple-subtle mb-3 px-3 py-2 rounded-pill">
                    <i class="bi bi-tag-fill me-1"></i> {{ $exam->subject ?? 'Môn chung' }}
                </span>
                <h2 class="fw-bold mb-2 theme-text-dark">{{ $exam->title }}</h2>
                <p class="text-muted mb-0">{{ $exam->description ?? 'Không có mô tả cho đề thi này.' }}</p>
            </div>
            
            <div class="d-flex flex-wrap gap-3 preview-actions">
                <button type="button" id="btnPrintExam" class="btn btn-outline-theme fw-bold d-flex align-items-center gap-2 px-4 py-2 rounded-3 border-purple-subtle text-purple-dark bg-light">
                    <i class="bi bi-printer"></i> In đề
                </button>
                <a href="{{ route('exams.play', $exam->id ?? 1) }}" class="btn btn-theme-primary fw-bold d-flex align-items-center gap-2 px-4 py-2 rounded-3 shadow-sm">
                    <i class="bi bi-play-circle-fill"></i> Luyện ngay
                </a>
            </div>
        </div>

        <hr class="my-4 border-purple-subtle">

        <div class="d-flex flex-wrap gap-4 text-muted fw-medium small">
            <div class="d-flex align-items-center">
                <div class="icon-box me-2"><i class="bi bi-patch-question theme-text-primary"></i></div> 
                Số câu: <span class="theme-text-dark ms-1 fw-bold">{{ isset($exam->questions) ? $exam->questions->count() : 0 }}</span>
            </div>
            <div class="d-flex align-items-center">
                <div class="icon-box me-2"><i class="bi bi-clock theme-text-primary"></i></div> 
                Thời gian: <span class="theme-text-dark ms-1 fw-bold">{{ $exam->duration ?? 0 }} phút</span>
            </div>
            <div class="d-flex align-items-center">
                <div class="icon-box me-2"><i class="bi bi-calendar3 theme-text-primary"></i></div> 
                Ngày tạo: <span class="theme-text-dark ms-1 fw-bold">{{ isset($exam->created_at) ? $exam->created_at->format('d/m/Y') : date('d/m/Y') }}</span>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 px-4 rounded-4 shadow-sm border border-soft">
        <h5 class="fw-bold mb-0 theme-text-dark">Danh sách câu hỏi</h5>
        <div class="form-check form-switch d-flex align-items-center gap-2 mb-0">
            <label class="form-check-label fw-semibold text-muted mt-1 me-2" for="toggleAnswers">Hiện đáp án & giải thích AI</label>
            <input class="form-check-input custom-switch fs-4 m-0 shadow-none" type="checkbox" role="switch" id="toggleAnswers">
        </div>
    </div>

    <div class="questions-container">
        @forelse($exam->questions ?? [] as $index => $question)
            <div class="question-card shadow-sm mb-4 bg-white rounded-4 border border-soft p-4 p-md-5">
                <div class="d-flex gap-3 gap-md-4">
                    <div class="q-number fw-bold bg-purple-light theme-text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                        {{ $index + 1 }}
                    </div>
                    
                    <div class="flex-grow-1">
                        <h5 class="fw-bold mb-4 lh-base theme-text-dark" style="font-size: 1.15rem;">
                            {{ $question->content }}
                            
                            @if($question->type === 'essay')
                                <span class="badge bg-info bg-opacity-10 text-info border border-info ms-2 align-middle px-2 py-1" style="font-size: 0.75rem;">Tự luận</span>
                            @else
                                <span class="badge bg-purple-light text-purple-dark border border-purple-subtle ms-2 align-middle px-2 py-1" style="font-size: 0.75rem;">Trắc nghiệm</span>
                            @endif
                        </h5>

                        @if($question->type === 'essay')
                            @php
                                $rawText = $question->answers->first()->content ?? 'Học viên tự làm.';
                                $formatted = preg_replace('/\*\*(.*?)\*\*/s', '<strong class="theme-text-dark">$1</strong>', $rawText);
                                $formatted = preg_replace('/(\s|^)([0-9]+\.\s)/', '<br><br><span class="theme-text-primary fw-bold">$2</span>', $formatted);
                                $formatted = preg_replace('/(\s|^)([\*\-]\s+)/', '<br><i class="bi bi-arrow-return-right text-success ms-3 me-2"></i> ', $formatted);
                                $formatted = nl2br(trim($formatted));
                                $formatted = preg_replace('/^(<br\s*\/?>)+/', '', $formatted);
                            @endphp

                            <div class="p-4 rounded-4 mt-3 essay-answer-box border border-success-subtle bg-success bg-opacity-10 d-none answer-block">
                                <strong class="text-success fs-6"><i class="bi bi-check2-square me-1"></i> Gợi ý đáp án / Bareme chấm điểm:</strong>
                                <div class="mt-3 text-muted" style="line-height: 1.8; font-size: 1rem;">
                                    {!! $formatted !!}
                                </div>
                            </div>

                        @else
                            <div class="row g-3 answers-grid">
                                @foreach($question->answers as $aIndex => $answer)
                                    @php
                                        $label = chr(65 + $aIndex); // A, B, C, D
                                        $isCorrectClass = $answer->is_correct ? 'is-correct-ans' : '';
                                    @endphp
                                    <div class="col-md-6">
                                        <div class="answer-box p-3 rounded-3 border {{ $isCorrectClass }} d-flex gap-3 align-items-start h-100 transition-all">
                                            <span class="fw-bold ans-label theme-text-primary">{{ $label }}.</span>
                                            <span class="flex-grow-1 mt-1 text-muted">{{ $answer->content }}</span>
                                            <i class="bi bi-check-circle-fill text-success correct-icon d-none fs-5 mt-1"></i>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if(!empty($question->ai_explanation))
                            @php
                                $formattedExp = preg_replace('/\*\*(.*?)\*\*/s', '<strong class="theme-text-dark">$1</strong>', $question->ai_explanation);
                            @endphp
                            <div class="ai-explanation-box d-none mt-4 p-4 rounded-4 bg-purple-light border border-purple-subtle answer-block">
                                <div class="d-flex align-items-center gap-2 theme-text-primary fw-bold mb-3 fs-6">
                                    <div class="ai-icon-wrapper"><i class="bi bi-robot fs-5"></i></div> 
                                    AI Phân tích & Giải thích
                                </div>
                                <p class="mb-0 text-muted" style="font-size: 1rem; line-height: 1.7;">
                                    {!! nl2br($formattedExp) !!}
                                </p>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted bg-white rounded-4 shadow-sm border border-soft">
                <div class="empty-icon-circle bg-purple-light theme-text-primary d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 80px; height: 80px; border-radius: 50%;">
                    <i class="bi bi-journal-x fs-1"></i>
                </div>
                <h5 class="fw-bold theme-text-dark">Chưa có câu hỏi</h5>
                <p>Đề thi này hiện đang trống, vui lòng tải lại hoặc tạo đề mới.</p>
            </div>
        @endforelse
    </div>

    @php
        $printQuestions = collect($exam->questions ?? []);
        $printQuestionCount = $printQuestions->count();
        $formatPrintScore = function ($score) {
            $formatted = number_format((float) $score, 2, ',', '.');
            return rtrim(rtrim($formatted, '0'), ',');
        };
        $getQuestionScore = function () use ($printQuestionCount) {
            return $printQuestionCount > 0 ? 10 / $printQuestionCount : 0;
        };
        $buildEssayRubricRows = function ($rawText, $questionScore) use ($formatPrintScore) {
            $lines = preg_split('/\R+/', trim((string) $rawText));
            $items = collect($lines)->map(fn ($line) => trim($line))->filter()->values();
            $hasBulletRubric = $items->contains(fn ($line) => preg_match('/^\s*[-+]\s+/', $line));
            $scoreUnits = $hasBulletRubric
                ? $items->filter(fn ($line) => preg_match('/^\s*[-+]\s+/', $line))->count()
                : $items->count();
            $scoreUnits = max(1, $scoreUnits);
            $unitScore = $questionScore / $scoreUnits;

            $rows = $items->map(function ($line) use ($hasBulletRubric, $unitScore, $formatPrintScore) {
                $isMainIdea = (bool) preg_match('/^\s*-\s+/', $line);
                $isSubIdea = (bool) preg_match('/^\s*\+\s+/', $line);
                $isScored = $hasBulletRubric ? ($isMainIdea || $isSubIdea) : true;
                $cleanLine = preg_replace('/^\s*[-+]\s+/', '', $line);
                $safeLine = e($cleanLine);
                $safeLine = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', $safeLine);

                return [
                    'html' => $safeLine,
                    'score' => $isScored ? $formatPrintScore($unitScore) : null,
                    'class' => $isMainIdea ? 'print-rubric-main' : ($isSubIdea ? 'print-rubric-sub' : 'print-rubric-line'),
                    'marker' => $isSubIdea ? '+' : '-',
                ];
            });

            return [
                'rows' => $rows,
                'unit_count' => $scoreUnits,
                'unit_score' => $unitScore,
            ];
        };
        $questionScore = $getQuestionScore();
    @endphp

    <section class="print-exam-paper" aria-hidden="true">
        <div class="print-paper-header">
            <div class="print-brand">EduQuiz AI</div>
            <div class="print-meta-line">ĐỀ KIỂM TRA / ĐỀ TỰ LUYỆN</div>
            <h1>{{ $exam->title }}</h1>
            <p>{{ $exam->description ?? 'Không có mô tả cho đề thi này.' }}</p>
        </div>

        <div class="print-info-grid">
            <div><strong>Môn:</strong> {{ $exam->subject ?? 'Môn chung' }}</div>
            <div><strong>Thời gian:</strong> {{ $exam->duration ?? 0 }} phút</div>
            <div><strong>Số câu:</strong> {{ isset($exam->questions) ? $exam->questions->count() : 0 }}</div>
            <div><strong>Ngày tạo:</strong> {{ isset($exam->created_at) ? $exam->created_at->format('d/m/Y') : date('d/m/Y') }}</div>
            <div><strong>Tổng điểm:</strong> 10 điểm</div>
            <div><strong>Điểm mỗi câu:</strong> 10/{{ max(1, $printQuestionCount) }} điểm (~{{ $formatPrintScore($questionScore) }})</div>
        </div>

        <div class="print-student-info">
            <span>Họ và tên: ....................................................</span>
            <span>Lớp: ............................</span>
            <span>Điểm: ............................</span>
        </div>

        <div class="print-note">
            Học viên làm bài trực tiếp trên đề hoặc theo hướng dẫn của giảng viên. Chọn một đáp án đúng nhất với câu hỏi trắc nghiệm.
        </div>

        <div class="print-question-list">
            @forelse($exam->questions ?? [] as $index => $question)
                <article class="print-question-item">
                    <div class="print-question-title">
                        <strong>Câu {{ $index + 1 }}.</strong> {{ $question->content }}
                    </div>

                    @if($question->type === 'essay')
                        <div class="print-answer-lines">
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                        </div>
                    @else
                        <div class="print-options-grid">
                            @foreach($question->answers as $aIndex => $answer)
                                <div class="print-option">
                                    <strong>{{ chr(65 + $aIndex) }}.</strong> {{ $answer->content }}
                                </div>
                            @endforeach
                        </div>
                    @endif
                </article>
            @empty
                <div class="print-empty">Đề thi này chưa có câu hỏi.</div>
            @endforelse
        </div>
    </section>

    <section class="print-answer-key-paper" aria-hidden="true">
        <div class="print-paper-header">
            <div class="print-brand">EduQuiz AI</div>
            <div class="print-meta-line">ĐÁP ÁN VÀ HƯỚNG DẪN CHẤM</div>
            <h1>{{ $exam->title }}</h1>
            <p>Phần này chỉ được in khi bật chế độ hiển thị đáp án và giải thích AI.</p>
        </div>

        <div class="print-info-grid">
            <div><strong>Tổng điểm:</strong> 10 điểm</div>
            <div><strong>Số câu:</strong> {{ $printQuestionCount }}</div>
            <div><strong>Điểm mỗi câu:</strong> 10/{{ max(1, $printQuestionCount) }} điểm (~{{ $formatPrintScore($questionScore) }})</div>
            <div><strong>Nguyên tắc:</strong> Trắc nghiệm đúng được đủ điểm, tự luận chia theo từng ý.</div>
        </div>

        <div class="print-answer-list">
            @forelse($exam->questions ?? [] as $index => $question)
                <article class="print-answer-item">
                    <div class="print-answer-title">
                        <strong>Câu {{ $index + 1 }}.</strong> {{ $question->content }}
                        <span class="print-question-score">{{ $formatPrintScore($questionScore) }} điểm</span>
                    </div>

                    @if($question->type === 'essay')
                        @php
                            $essayAnswer = $question->answers->first()->content ?? 'Chưa có gợi ý đáp án.';
                            $essayRubric = $buildEssayRubricRows($essayAnswer, $questionScore);
                        @endphp
                        <div class="print-answer-content">
                            <div class="print-answer-label">
                                Gợi ý đáp án / bareme chấm điểm:
                                <span class="print-score-note">
                                    {{ $essayRubric['unit_count'] }} ý, mỗi ý {{ $formatPrintScore($essayRubric['unit_score']) }} điểm.
                                </span>
                            </div>
                            <ul class="print-rubric-list">
                                @foreach($essayRubric['rows'] as $rubricRow)
                                    <li class="{{ $rubricRow['class'] }}">
                                        <span class="print-rubric-marker">{{ $rubricRow['marker'] }}</span>
                                        <span class="print-rubric-text">{!! $rubricRow['html'] !!}</span>
                                        @if($rubricRow['score'] !== null)
                                            <span class="print-rubric-score">{{ $rubricRow['score'] }} điểm</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        @php
                            $correctIndex = $question->answers->search(function ($answer) {
                                return (bool) $answer->is_correct;
                            });
                            $correctAnswer = $correctIndex !== false ? $question->answers[$correctIndex] : null;
                            $correctLabel = $correctIndex !== false ? chr(65 + $correctIndex) : '?';
                        @endphp
                        <div class="print-answer-content">
                            <div class="print-answer-label">
                                Đáp án đúng: {{ $correctLabel }}@if($correctAnswer). {{ $correctAnswer->content }}@endif
                                <span class="print-score-note">({{ $formatPrintScore($questionScore) }} điểm)</span>
                            </div>
                        </div>
                    @endif

                    @if(!empty($question->ai_explanation))
                        @php
                            $safePrintExplanation = e($question->ai_explanation);
                            $safePrintExplanation = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', $safePrintExplanation);
                        @endphp
                        <div class="print-explanation-content">
                            <div class="print-answer-label">Giải thích AI:</div>
                            <div>{!! nl2br($safePrintExplanation) !!}</div>
                        </div>
                    @endif
                </article>
            @empty
                <div class="print-empty">Đề thi này chưa có câu hỏi.</div>
            @endforelse
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ versioned_asset('js/student/student_preview_exam.js') }}"></script>
@endpush
