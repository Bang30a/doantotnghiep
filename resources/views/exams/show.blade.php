@extends('layouts.app') @section('title', 'Chi tiết đề thi: ' . $exam->title)

@push('styles')
    <link rel="stylesheet" href="{{ versioned_asset('css/show_exam.css') }}">
@endpush

@section('content')
<div class="exam-show-page">
<div class="container py-4" style="max-width: 1120px;">
    
    <div class="exam-detail-header shadow-sm mb-5 position-relative overflow-hidden bg-white rounded-4">
        <div class="position-absolute top-0 end-0 h-100 opacity-10 pointer-events-none" style="width: 300px; background: radial-gradient(circle at top right, var(--theme-primary), transparent 70%);"></div>
        
        <div class="position-relative z-1 p-4 p-md-5">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-4">
                
                <div class="flex-grow-1">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                        <span class="badge bg-purple-light text-purple-dark px-3 py-2 rounded-pill border border-purple-subtle fw-medium shadow-sm">
                            <i class="bi bi-tag-fill me-1"></i> {{ $exam->subject ?? 'Môn chung' }}
                        </span>
                        @if(isset($exam->classroom))
                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill border border-primary border-opacity-25 fw-medium shadow-sm">
                                <i class="bi bi-people-fill me-1"></i> Lớp: {{ $exam->classroom->name }}
                            </span>
                        @else
                            <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill border border-secondary border-opacity-25 fw-medium shadow-sm">
                                <i class="bi bi-dash-circle me-1"></i> Chưa gán lớp
                            </span>
                        @endif
                    </div>
                    
                    <h2 class="fw-bold theme-text-dark mb-3 lh-base">{{ $exam->title }}</h2>
                    
                    <div class="d-flex flex-wrap gap-4 text-muted small fw-bold text-uppercase letter-spacing-1 opacity-75">
                        <div class="d-flex align-items-center gap-2">
                            <div class="icon-box bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;"><i class="bi bi-patch-question-fill theme-text-primary"></i></div>
                            <span>{{ isset($exam->questions) ? $exam->questions->count() : 0 }} câu hỏi</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="icon-box bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;"><i class="bi bi-stopwatch-fill theme-text-primary"></i></div>
                            <span>{{ $exam->duration ?? 0 }} phút</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="icon-box bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;"><i class="bi bi-calendar3 theme-text-primary"></i></div>
                            <span>{{ isset($exam->created_at) ? $exam->created_at->format('d/m/Y') : date('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex flex-row flex-md-column gap-2 min-w-200 mt-3 mt-md-0">
                    <a href="{{ route('exams.teacher_results', $exam->id ?? 1) ?? '#' }}" class="btn btn-theme-primary fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2 py-2 px-4 rounded-pill">
                        <i class="bi bi-bar-chart-line-fill"></i> Xem Bảng Điểm
                    </a>
                    <a href="{{ route('dashboard') ?? '#' }}" class="btn btn-outline-theme fw-bold d-flex align-items-center justify-content-center gap-2 py-2 px-4 rounded-pill">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom border-purple-subtle">
        <div class="icon-box bg-purple-light theme-text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 42px; height: 42px;">
            <i class="bi bi-list-ol fs-5"></i>
        </div>
        <h4 class="fw-bold mb-0 theme-text-dark">Nội dung chi tiết đề thi</h4>
    </div>

    <div class="questions-wrapper">
        @php 
            $labels = ['A', 'B', 'C', 'D', 'E', 'F']; 
            
            // Helper đổi màu Badge độ khó sang tone pastel hiện đại
            function getDifficultyStyle($level) {
                $level = strtolower($level ?? '');
                if (in_array($level, ['dễ', 'easy'])) return 'bg-success-subtle text-success border border-success border-opacity-25';
                if (in_array($level, ['trung bình', 'medium'])) return 'bg-warning-subtle text-warning-emphasis border border-warning border-opacity-25';
                if (in_array($level, ['khó', 'hard'])) return 'bg-danger-subtle text-danger border border-danger border-opacity-25';
                return 'bg-light text-secondary border border-secondary border-opacity-25';
            }
        @endphp

        @forelse($exam->questions ?? [] as $index => $question)
            <div class="question-detail-card bg-white shadow-sm rounded-4 border-0 mb-4 overflow-hidden">
                
                <div class="q-header d-flex justify-content-between align-items-start gap-3 p-4 p-md-5 pb-4 bg-light border-bottom border-soft">
                    <div class="d-flex gap-3 align-items-start">
                        <div class="q-number bg-white theme-text-primary fw-bold rounded-4 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm border border-purple-subtle" style="width: 45px; height: 45px; font-size: 1.15rem;">
                            {{ $index + 1 }}
                        </div>
                        <h5 class="fw-bold theme-text-dark lh-base mt-1 mb-0" style="font-size: 1.15rem;">
                            {{ $question->content }}
                        </h5>
                    </div>
                    <span class="badge {{ getDifficultyStyle($question->difficulty ?? 'Dễ') }} px-3 py-2 rounded-pill flex-shrink-0 fw-medium shadow-sm">
                        {{ ucfirst($question->difficulty ?? 'Dễ') }}
                    </span>
                </div>

                <div class="q-body p-4 p-md-5 pt-4">
                    <div class="row g-3">
                        @foreach($question->answers as $aIndex => $answer)
                            <div class="col-md-6">
                                <div class="answer-box p-3 p-md-4 rounded-4 border d-flex align-items-center gap-3 {{ $answer->is_correct ? 'is-correct-box shadow-sm border-success bg-success bg-opacity-10' : 'bg-white text-muted border-secondary border-opacity-25' }}">
                                    <div class="check-circle rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 {{ $answer->is_correct ? 'bg-success text-white' : 'bg-light border' }}" style="width: 28px; height: 28px;">
                                        @if($answer->is_correct)
                                            <i class="bi bi-check-lg"></i>
                                        @else
                                            <span class="fw-bold small" style="color: #94A3B8;">{{ $labels[$aIndex] ?? '•' }}</span>
                                        @endif
                                    </div>
                                    
                                    <span class="flex-grow-1 {{ $answer->is_correct ? 'fw-bold text-success-dark' : 'fw-medium text-dark' }}" style="line-height: 1.5; font-size: 0.95rem;">
                                        {{ $answer->content }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @if(!empty($question->ai_explanation))
                        @php
                            // Format markdown text in đậm từ AI
                            $formattedExp = preg_replace('/\*\*(.*?)\*\*/s', '<strong class="theme-text-dark">$1</strong>', $question->ai_explanation);
                        @endphp
                        <div class="ai-explanation-box mt-4 p-4 p-md-4 rounded-4 position-relative shadow-sm bg-purple-light border border-purple-subtle">
                            <div class="d-flex align-items-start gap-3">
                                <div class="ai-avatar bg-white theme-text-primary rounded-circle shadow-sm d-flex align-items-center justify-content-center flex-shrink-0 border border-purple-subtle" style="width: 42px; height: 42px;">
                                    <i class="bi bi-robot fs-5"></i>
                                </div>
                                <div>
                                    <div class="theme-text-primary fw-bold mb-2 fs-6 text-uppercase letter-spacing-1" style="font-size: 0.85rem;">AI Phân tích & Giải thích</div>
                                    <p class="mb-0 text-dark opacity-75 lh-lg" style="font-size: 0.95rem;">
                                        {!! nl2br($formattedExp) !!}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

            </div>
        @empty
            <div class="text-center py-5">
                <i class="bi bi-journal-x fs-1 text-muted mb-3 d-block opacity-50"></i>
                <h5 class="fw-bold theme-text-dark">Chưa có câu hỏi nào trong đề thi này.</h5>
            </div>
        @endforelse
    </div>

</div>
</div>
@endsection
