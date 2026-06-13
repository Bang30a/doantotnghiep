@extends('layouts.teacher.teacher_app')

@section('title', 'Chấm bài: ' . $result->user->name)

@push('styles')
    <link rel="stylesheet" href="{{ versioned_asset('css/teacher/teacher_grading.css') }}">
@endpush

@section('content')

    <!-- Nút quay lại -->
    <div class="mb-4 mt-2">
        <button type="button" id="btn-back" class="btn-back border-0 bg-white">
            <i class="bi bi-arrow-left"></i> Quay lại danh sách
        </button>
    </div>

    <!-- Thông báo Toast -->
    @if(session('success'))
        <div class="toast-container position-fixed top-0 end-0 p-4" style="z-index: 1055; margin-top: 60px;">
            <div id="successToast" class="toast align-items-center text-bg-success border-0 show shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex p-2">
                    <div class="toast-body fw-bold fs-6">
                        <i class="bi bi-check-circle-fill me-2 fs-5"></i> {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

    @php
        // FIX LOGIC ĐIỂM CHUẨN XÁC
        $totalQ = max(1, $result->total_questions);
        $essayCount = $result->exam->questions->where('type', 'essay')->count();
        $hasEssay = ($essayCount > 0);
        
        // Nếu có câu Tự luận -> Điểm $result->score lưu trong DB đã là điểm tổng chuẩn xác.
        // Nếu 100% trắc nghiệm -> Cần nhân tỷ lệ để ra hệ 10.
        if ($hasEssay) {
            $displayScore = $result->score;
        } else {
            $displayScore = ($result->score / $totalQ) * 10;
        }
    @endphp

    <!-- Header Chấm bài -->
    <div class="grading-header p-4 p-md-5 mb-5 shadow-sm hover-lift">
        <div class="bg-icon"><i class="bi bi-file-earmark-check-fill"></i></div>
        
        <div class="row align-items-center position-relative z-1">
            <div class="col-md-8">
                <span class="badge bg-white theme-text-primary mb-3 rounded-pill px-3 py-1.5 fw-bold shadow-sm letter-spacing-1 text-uppercase">
                    <i class="bi bi-clipboard-data-fill me-1"></i> Phiếu chấm điểm
                </span>
                <h2 class="fw-800 mb-3">{{ $result->exam->title }}</h2>
                <div class="d-flex align-items-center gap-3 opacity-90 fw-medium">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-person-circle fs-5"></i>
                        <span>{{ $result->user->name }}</span>
                    </div>
                    <span>•</span>
                    <span><i class="bi bi-clock me-1"></i> Nộp lúc: {{ $result->created_at->format('H:i - d/m/Y') }}</span>
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-4 mt-md-0">
                <div class="bg-white text-dark d-inline-block p-4 rounded-4 shadow-sm text-center" style="min-width: 160px;">
                    <p class="text-muted small fw-bold mb-1 text-uppercase letter-spacing-1">Tổng điểm</p>
                    <div class="badge-score mb-1">
                        {{ number_format($displayScore, 1) }}
                    </div>
                    <p class="mb-0 fw-bold small text-success"><i class="bi bi-check-circle-fill me-1"></i> Hệ thống đã lưu</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách Câu hỏi & Bài làm -->
    <div class="row">
        <div class="col-lg-12">
            @php $labels = ['A', 'B', 'C', 'D', 'E', 'F']; @endphp

            @foreach($result->exam->questions as $index => $q)
                @php
                    $userAnswerRecord = \App\Models\StudentAnswer::where('exam_result_id', $result->id)
                        ->where('question_id', $q->id)
                        ->first();

                    $userAnswerId = $userAnswerRecord ? $userAnswerRecord->answer_id : null;
                    $isEssay = $q->type === 'essay';

                    $aiSuggestion = $aiSuggestions[$q->id] ?? [
                        'score' => null,
                        'feedback' => 'AI chua phan tich bai lam nay.'
                    ];
                    
                    $isCorrectQuestion = false;
                    if ($userAnswerId && !$isEssay) {
                        $pickedAns = $q->answers->where('id', $userAnswerId)->first();
                        $isCorrectQuestion = $pickedAns ? $pickedAns->is_correct : false;
                    }
                @endphp

                <div class="card border-0 shadow-sm rounded-4 mb-4 question-box">
                    <div class="card-body p-4 p-md-5">
                        
                        <!-- Header Câu hỏi -->
                        <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom border-light-subtle">
                            <span class="badge bg-purple-custom text-white rounded-pill px-4 py-2 fw-bold fs-6 shadow-sm">Câu {{ $index + 1 }}</span>
                            
                            @if(!$isEssay)
                                @if(!$userAnswerId)
                                    <span class="badge bg-secondary text-white rounded-pill px-3 py-1.5"><i class="bi bi-dash-circle me-1"></i> Đã bỏ trống</span>
                                @elseif($isCorrectQuestion)
                                    <span class="badge bg-success text-white rounded-pill px-3 py-1.5"><i class="bi bi-check-circle-fill me-1"></i> Học viên trả lời ĐÚNG</span>
                                @else
                                    <span class="badge bg-danger text-white rounded-pill px-3 py-1.5"><i class="bi bi-x-circle-fill me-1"></i> Học viên trả lời SAI</span>
                                @endif
                                <span class="badge bg-light text-muted border rounded-pill px-3 py-1.5 fw-bold ms-auto"><i class="bi bi-ui-radios me-1"></i> Trắc nghiệm</span>
                            @else
                                <span class="badge bg-info-soft text-info border border-info border-opacity-25 rounded-pill px-3 py-1.5 fw-bold"><i class="bi bi-pencil-square me-1"></i> Tự luận</span>
                            @endif
                        </div>

                        <!-- Nội dung & Đáp án -->
                        <div class="row g-4">
                            
                            <!-- Nội dung câu hỏi -->
                            <div class="{{ $q->type == 'essay' ? 'col-lg-7' : 'col-12' }}">
                                <h5 class="fw-bold text-dark mb-4 lh-base fs-5">{{ $q->content }}</h5>

                                @if($q->type == 'essay')
                                    <div class="student-answer-box p-4 rounded-4 position-relative mb-3">
                                        <h6 class="fw-bold text-muted mb-3 text-uppercase small d-flex align-items-center gap-2">
                                            <i class="bi bi-person-lines-fill text-primary fs-5"></i> Bài làm của học viên:
                                        </h6>
                                        <div class="text-dark lh-lg fw-medium rich-text-content grading-rich-display">
                                            @php $studentAnswer = $userAnswerRecord->content ?? null; @endphp
                                            {!! \App\Support\RichTextSanitizer::render($studentAnswer, 'Học viên đã bỏ trống câu này.') !!}
                                        </div>
                                    </div>
                                    
                                    @if($userAnswerRecord && $userAnswerRecord->feedback)
                                        <div class="alert alert-success border-success border-opacity-25 bg-success bg-opacity-10 rounded-4 p-3">
                                            <div class="fw-bold text-success small text-uppercase mb-1"><i class="bi bi-check-all"></i> Đã chấm điểm</div>
                                            <p class="mb-0 fw-medium"><strong>Điểm:</strong> {{ $userAnswerRecord->score }}</p>
                                            <p class="mb-0 fw-medium"><strong>Nhận xét:</strong> {{ $userAnswerRecord->feedback }}</p>
                                        </div>
                                    @endif
                                @else
                                    <div class="row g-3">
                                        @foreach($q->answers as $aIndex => $ans)
                                            @php
                                                $isCorrectAns = $ans->is_correct; 
                                                $isUserPicked = ($userAnswerId == $ans->id); 
                                                
                                                $boxClass = 'border-light-subtle bg-white';
                                                $textClass = 'text-dark';
                                                $badgeHtml = '';
                                                $boxStyle = 'border: 1px solid;';

                                                if ($isUserPicked && $isCorrectAns) {
                                                    $boxClass = 'border-success bg-success bg-opacity-10';
                                                    $textClass = 'text-success fw-bold';
                                                    $boxStyle = 'border: 2px solid;';
                                                    $badgeHtml = '<div class="mt-2"><span class="badge bg-success px-3 py-2 shadow-sm"><i class="bi bi-check2-all me-1"></i> Học viên chọn (ĐÚNG)</span></div>';
                                                } elseif ($isUserPicked && !$isCorrectAns) {
                                                    $boxClass = 'border-danger bg-danger bg-opacity-10';
                                                    $textClass = 'text-danger fw-bold text-decoration-line-through opacity-75';
                                                    $boxStyle = 'border: 2px solid;';
                                                    $badgeHtml = '<div class="mt-2"><span class="badge bg-danger px-3 py-2 shadow-sm"><i class="bi bi-x-lg me-1"></i> Học viên chọn (SAI)</span></div>';
                                                } elseif (!$isUserPicked && $isCorrectAns) {
                                                    $boxClass = 'border-success bg-light';
                                                    $textClass = 'text-success fw-bold';
                                                    $boxStyle = 'border: 2px dashed !important;';
                                                    $badgeHtml = '<div class="mt-2"><span class="badge bg-white text-success border border-success px-3 py-2"><i class="bi bi-key-fill me-1"></i> Đáp án chính xác</span></div>';
                                                }
                                            @endphp
                                            
                                            <div class="col-md-6">
                                                <div class="p-3 p-md-4 rounded-4 d-flex flex-column justify-content-center h-100 {{ $boxClass }}" style="{{ $boxStyle }}">
                                                    <div class="d-flex align-items-start">
                                                        <span class="fw-900 me-3 {{ $textClass }} fs-5">{{ $labels[$aIndex] }}.</span>
                                                        <span class="{{ $textClass }} lh-base fs-6">{{ $ans->content }}</span>
                                                    </div>
                                                    {!! $badgeHtml !!}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <!-- Khối chấm điểm tự luận (Bên phải) -->
                            @if($q->type == 'essay')
                                <div class="col-lg-5">
                                    
                                    <div class="ai-hint-box p-4 rounded-4 mb-4 position-relative shadow-sm border-purple-subtle">
                                        <div class="position-absolute top-0 end-0 mt-3 me-3">
                                            <span class="badge bg-purple-custom fw-bold px-3 py-2 fs-6 shadow-sm border border-white">
                                                AI Đề xuất:
                                                <span class="text-warning">
                                                    {{ $aiSuggestion['score'] !== null ? number_format($aiSuggestion['score'], 1) : 'Chưa có' }}
                                                </span> / 10
                                            </span>
                                        </div>

                                        <h6 class="fw-bold theme-text-primary mb-3 text-uppercase small d-flex align-items-center gap-2">
                                            <i class="bi bi-robot fs-5"></i> Trợ giảng AI phân tích:
                                        </h6>
                                        
                                        <div class="text-dark small lh-lg fw-medium mb-3 border-bottom border-purple-subtle pb-3">
                                            <strong class="text-success"><i class="bi bi-check2-square"></i> Bareme / Gợi ý đáp án:</strong><br>
                                            <span class="opacity-75">
                                                {!! nl2br(e($q->answers->first()->content ?? 'Chưa có bareme gợi ý.')) !!}
                                            </span>
                                        </div>

                                        <div class="text-dark small fw-medium">
                                            <strong class="text-primary"><i class="bi bi-chat-left-text"></i> AI Nhận xét bài làm:</strong><br>
                                            <span class="opacity-75 ai-feedback-text" id="ai-feedback-{{ $q->id }}">
                                                {{ $aiSuggestion['feedback'] ?? 'AI chua co nhan xet.' }}
                                            </span>
                                        </div>
                                    </div>

                                    <form action="{{ route('teacher.exams.save_grade', $result->id) }}" method="POST" class="grading-input-wrapper p-4 rounded-4 shadow-sm bg-white d-block">
                                        @csrf
                                        <input type="hidden" name="question_id" value="{{ $q->id }}">

                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="fw-bold text-dark mb-0 text-uppercase small letter-spacing-1 d-flex align-items-center gap-2">
                                                <i class="bi bi-pen-fill theme-text-primary"></i> Giảng viên chốt điểm
                                            </h6>
                                            <button type="button" class="btn btn-sm btn-outline-purple rounded-pill fw-bold btn-use-ai-feedback" data-target="feedback-{{ $q->id }}" data-source="ai-feedback-{{ $q->id }}">
                                                <i class="bi bi-magic"></i> Dùng nhận xét AI
                                            </button>
                                        </div>
                                        
                                        <div class="d-flex justify-content-center align-items-center mb-3 gap-2 bg-light p-3 rounded-3 border border-light-subtle">
                                            <label class="fw-bold text-muted mb-0 me-2">Điểm đạt:</label>
                                            <input type="number"
                                                    name="score_{{ $q->id }}"
                                                    class="form-control score-input text-center fw-900 rounded-3 shadow-sm"
                                                    style="width: 100px; height: 55px;"
                                                    placeholder="0.0"
                                                    step="0.5"
                                                    min="0"
                                                    max="10"
                                                    value="{{ $userAnswerRecord && $userAnswerRecord->score !== null ? $userAnswerRecord->score : ($aiSuggestion['score'] ?? '') }}"
                                                    required>
                                        </div>
                                        
                                        <div class="mb-4">
                                            <div class="edu-rich-editor grading-feedback-editor">
                                                <div class="edu-rich-toolbar" role="toolbar" aria-label="Công cụ định dạng nhận xét">
                                                    <button type="button" title="In đậm" data-rich-command="bold"><i class="bi bi-type-bold"></i></button>
                                                    <button type="button" title="In nghiêng" data-rich-command="italic"><i class="bi bi-type-italic"></i></button>
                                                    <button type="button" title="Gạch chân" data-rich-command="underline"><i class="bi bi-type-underline"></i></button>
                                                    <span class="toolbar-divider"></span>
                                                    <select title="Cỡ chữ" data-rich-command="fontSize">
                                                        <option value="">Cỡ chữ</option>
                                                        <option value="2">Nhỏ</option>
                                                        <option value="3">Vừa</option>
                                                        <option value="4">Lớn</option>
                                                        <option value="5">Rất lớn</option>
                                                    </select>
                                                    <span class="toolbar-divider"></span>
                                                    <button type="button" title="Căn trái" data-rich-command="justifyLeft"><i class="bi bi-text-left"></i></button>
                                                    <button type="button" title="Căn giữa" data-rich-command="justifyCenter"><i class="bi bi-text-center"></i></button>
                                                    <button type="button" title="Căn phải" data-rich-command="justifyRight"><i class="bi bi-text-right"></i></button>
                                                    <button type="button" title="Căn đều" data-rich-command="justifyFull"><i class="bi bi-justify"></i></button>
                                                    <span class="toolbar-divider"></span>
                                                    <button type="button" title="Danh sách chấm" data-rich-command="insertUnorderedList"><i class="bi bi-list-ul"></i></button>
                                                    <button type="button" title="Danh sách số" data-rich-command="insertOrderedList"><i class="bi bi-list-ol"></i></button>
                                                    <button type="button" title="Xóa định dạng" data-rich-command="removeFormat"><i class="bi bi-eraser"></i></button>
                                                </div>

                                                <div class="edu-rich-editor-surface feedback-input"
                                                     contenteditable="true"
                                                     data-placeholder="Giảng viên gõ lời phê hoặc nhận xét cho học viên tại đây...">{!! \App\Support\RichTextSanitizer::render($userAnswerRecord->feedback ?? null) !!}</div>

                                                <textarea id="feedback-{{ $q->id }}" name="feedback_{{ $q->id }}" class="d-none edu-rich-editor-input">{{ \App\Support\RichTextSanitizer::sanitize($userAnswerRecord->feedback ?? '') }}</textarea>
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-purple-gradient w-100 py-3 rounded-pill fw-bold shadow-sm hover-lift d-flex align-items-center justify-content-center gap-2">
                                            <i class="bi bi-check2-circle fs-5 lh-1"></i> Lưu điểm & Nhận xét
                                        </button>
                                    </form>

                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

@endsection

@push('scripts')
    {{-- Chuyển đoạn script tắt Toast xuống dưới cùng để load mượt hơn --}}
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
                    var toastEl = document.getElementById('successToast');
                    if (toastEl) {
                        var toast = new bootstrap.Toast(toastEl);
                        toast.hide();
                    }
                }, 4000);
            });
        </script>
    @endif

    {{-- Tạm thời giữ lại jQuery nếu file JS bên dưới cần dùng --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ versioned_asset('js/rich_text_editor.js') }}"></script>
    <script src="{{ versioned_asset('js/teacher/teacher_grading.js') }}"></script>
@endpush
