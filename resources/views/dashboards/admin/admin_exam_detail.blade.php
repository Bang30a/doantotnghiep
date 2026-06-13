@extends('layouts.admin.admin_app')

@section('title', 'Chi tiết Đề thi: ' . ($exam->title ?? ''))

@push('styles')
    <link rel="stylesheet" href="{{ versioned_asset('css/admin/admin_exam_detail.css') }}">
@endpush

@section('content')

    <!-- Tiêu đề trang & Nút quay lại -->
    <div class="admin-page-heading d-flex align-items-center justify-content-between mb-4 mt-2 pb-3">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.exams') ?? url()->previous() }}" class="btn-back">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h3 class="fw-800 text-dark mb-1">Nội dung chi tiết Đề thi</h3>
                <p class="text-muted fw-medium mb-0">Chế độ xem trước dành cho Quản trị viên</p>
            </div>
        </div>
        <span class="badge bg-danger-soft text-danger border border-danger-subtle px-4 py-2 rounded-pill fw-bold shadow-sm fs-6">
            <i class="bi bi-shield-lock-fill me-2"></i> Quyền Admin
        </span>
    </div>

    <!-- Banner Thông tin đề thi -->
    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 mb-5 exam-banner">
        <div class="row align-items-center g-4">
            <div class="col-xl-8">
                <span class="badge bg-white text-dark mb-3 rounded-pill px-3 py-2 fw-bold shadow-sm">
                    <i class="bi bi-clock-history theme-text-primary me-1"></i> Thời gian: {{ $exam->duration ?? 0 }} phút
                </span>
                <h2 class="fw-800 mb-3 text-white lh-base">{{ $exam->title ?? 'Chưa có tiêu đề' }}</h2>
                <div class="d-flex flex-wrap align-items-center gap-4 text-white-50 small fw-medium mt-2">
                    <span><i class="bi bi-person-circle fs-6 me-1"></i> Người tạo: <strong class="text-white">{{ $exam->teacher->name ?? 'Hệ thống' }}</strong></span>
                    <span><i class="bi bi-diagram-3 fs-6 me-1"></i> Lớp: <strong class="text-white">{{ $exam->classroom->name ?? 'Ngân hàng đề chung' }}</strong></span>
                    <span><i class="bi bi-calendar-event fs-6 me-1"></i> Ngày tạo: <strong class="text-white">{{ isset($exam->created_at) ? $exam->created_at->format('d/m/Y') : '' }}</strong></span>
                </div>
            </div>
            <div class="col-xl-4 d-flex justify-content-xl-end gap-3">
                <div class="glass-stat p-3 text-center" style="min-width: 110px;">
                    <h2 class="fw-900 text-white mb-0">{{ $exam->total_questions ?? ($exam->questions ? $exam->questions->count() : 0) }}</h2>
                    <span class="small fw-bold text-white-50 text-uppercase letter-spacing-1">Câu hỏi</span>
                </div>
                <div class="glass-stat p-3 text-center" style="min-width: 110px;">
                    <h2 class="fw-900 text-white mb-0">{{ $exam->results ? $exam->results()->count() : 0 }}</h2>
                    <span class="small fw-bold text-white-50 text-uppercase letter-spacing-1">Lượt nộp</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tiêu đề Danh sách câu hỏi -->
    <div class="d-flex align-items-center gap-2 mb-4">
        <div class="icon-wrapper-md bg-purple-light theme-text-primary rounded-3"><i class="bi bi-card-list"></i></div>
        <h5 class="fw-bold text-dark mb-0">Danh sách câu hỏi và đáp án</h5>
    </div>
    
    <!-- Render danh sách câu hỏi -->
    @if(isset($exam->questions) && $exam->questions->count() > 0)
        @foreach($exam->questions as $index => $question)
            <div class="card border-0 shadow-sm rounded-4 mb-4 question-card bg-white">
                <div class="card-body p-4 p-md-5">
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="badge px-4 py-2 rounded-pill fw-bold fs-6 shadow-sm letter-spacing-1 text-white" style="background-color: #7E22CE;">
                            CÂU {{ $index + 1 }}
                        </span>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn-tool copy-question-btn" data-content="{{ $question->content }}">
                                <i class="bi bi-copy me-1"></i> Sao chép
                            </button>
                            <span class="badge bg-info-soft text-info border border-info-subtle px-3 py-2 rounded-pill fw-bold text-uppercase letter-spacing-1">
                                <i class="bi {{ $question->type == 'essay' ? 'bi-pen-fill' : 'bi-ui-radios' }} me-1"></i> 
                                {{ $question->type == 'essay' ? 'Tự luận' : 'Trắc nghiệm' }}
                            </span>
                        </div>
                    </div>
                    
                    <h5 class="fw-bold text-dark mb-4 lh-base" style="font-size: 1.15rem;">{{ $question->content }}</h5>

                    <div class="d-flex justify-content-end mb-3">
                        <button type="button" class="btn-tool toggle-answer-btn theme-text-primary border-purple-subtle bg-purple-light" data-target="answer-content-{{ $question->id }}">
                            <i class="bi bi-eye-slash me-1"></i> Ẩn đáp án
                        </button>
                    </div>

                    <div id="answer-content-{{ $question->id }}" class="answer-box">
                        
                        @if($question->type == 'essay')
                            <h6 class="fw-bold theme-text-primary mb-3 text-uppercase letter-spacing-1">
                                <i class="bi bi-stars text-warning me-2 fs-5 align-middle"></i> BAREME CHẤM ĐIỂM / GỢI Ý AI
                            </h6>
                            <div class="text-dark fw-medium lh-lg pt-2 border-top border-purple border-opacity-25">
                                @php
                                    $hint = $question->ai_explanation ?? $question->explanation ?? '';
                                    if(empty($hint) && $question->answers->count() > 0) {
                                        $hint = $question->answers->first()->content;
                                    }
                                @endphp
                                
                                @if(!empty($hint))
                                    {!! nl2br(e($hint)) !!}
                                @else
                                    <span class="text-muted fst-italic opacity-75"><i class="bi bi-info-circle me-1"></i> Giảng viên chưa cung cấp đáp án gợi ý cho câu hỏi này.</span>
                                @endif
                            </div>

                        @else
                            @if($question->answers && $question->answers->count() > 0)
                                <div class="row g-3">
                                    @php
                                        $alphabet = ['A', 'B', 'C', 'D', 'E', 'F']; 
                                    @endphp

                                    @foreach($question->answers as $ansIndex => $answer)
                                        @php
                                            $isCorrect = ($answer->is_correct == 1);
                                            $letter = $alphabet[$ansIndex] ?? ($ansIndex + 1);
                                        @endphp
                                        <div class="col-md-6">
                                            <div class="p-3 rounded-4 option-item border transition-all {{ $isCorrect ? 'correct-option fw-bold shadow-sm' : 'bg-white text-dark' }}">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span><strong class="me-2 fs-5">{{ $letter }}.</strong> {{ $answer->content }}</span>
                                                    @if($isCorrect)
                                                        <i class="bi bi-check-circle-fill fs-4 theme-text-primary"></i>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-3 text-warning-emphasis bg-warning-subtle rounded-3 fst-italic border border-warning-subtle">
                                    <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i> Câu hỏi này chưa có dữ liệu ở bảng Answers.
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="text-center py-5 bg-white rounded-4 shadow-sm border-0 mt-4">
            <div class="bg-gray-soft rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                <i class="bi bi-inbox fs-1 text-muted opacity-50"></i>
            </div>
            <h5 class="fw-bold text-dark">Đề thi này chưa có câu hỏi nào</h5>
        </div>
    @endif

@endsection

@push('scripts')
    <script src="{{ versioned_asset('js/admin/admin_exam_detail.js') }}"></script>
@endpush
