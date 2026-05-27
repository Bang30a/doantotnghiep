@extends('layouts.student.student_app')

@section('title', $classroom->name)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/student/student_show.css') }}?v={{ time() }}">
@endpush

@section('content')
    
    <div class="mb-4 mt-3">
        <a href="{{ route('student.classrooms') }}" class="text-decoration-none text-muted fw-medium d-inline-flex align-items-center gap-2 back-link px-3 py-2 rounded-pill bg-white shadow-sm border border-light">
            <i class="bi bi-arrow-left"></i> Quay lại danh sách lớp
        </a>
    </div>

    <div class="class-banner shadow-sm p-4 p-md-5 rounded-4 text-white mb-4 position-relative overflow-hidden">
        <div class="class-banner-bg-shape"></div>
        <div class="class-banner-icon shadow-sm text-purple">
            <i class="bi bi-mortarboard-fill"></i>
        </div>
        <div class="position-relative z-1">
            <h2 class="fw-bold mb-2">{{ $classroom->name }}</h2>
            <p class="mb-4 text-white text-opacity-75 d-flex align-items-center gap-2">
                <i class="bi bi-robot"></i> Hệ thống bài tập tự động AI
            </p>
            
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="d-flex align-items-center gap-2 bg-black bg-opacity-25 px-3 py-2 rounded-pill backdrop-blur">
                    <i class="bi bi-person-workspace"></i> 
                    {{-- Tối ưu lại bằng toán tử Null Coalescing (??) --}}
                    <span style="font-size: 0.9rem;">Giảng viên: <strong>{{ $classroom->teacher->name ?? 'Đang cập nhật' }}</strong></span>
                </div>
                <div class="bg-white bg-opacity-25 text-white px-3 py-2 rounded-pill fw-medium backdrop-blur d-flex align-items-center gap-2 shadow-sm" style="font-size: 0.9rem;">
                    <i class="bi bi-upc-scan"></i> Mã lớp: <strong class="letter-spacing-1">{{ $classroom->code }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-box shadow-sm d-flex flex-column justify-content-center">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <p class="text-muted fw-bold text-uppercase small mb-0 letter-spacing-1">Đã hoàn thành</p>
                    <div class="icon-box bg-purple-light theme-text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="bi bi-check2-all fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 theme-text-dark">{{ $completedExams }}<span class="text-muted fs-6 fw-medium ms-1">/ {{ $totalExams }} bài</span></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-box shadow-sm d-flex flex-column justify-content-center">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <p class="text-muted fw-bold text-uppercase small mb-0 letter-spacing-1">Điểm trung bình</p>
                    <div class="icon-box bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="bi bi-graph-up-arrow fs-6"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-2 text-success">{{ number_format($averageScore, 1) }}<span class="text-muted fs-6 fw-medium ms-1">/ 10</span></h3>
                <div class="progress bg-light" style="height: 6px; border-radius: 10px;">
                    <div class="progress-bar bg-success rounded-pill" style="width: {{ $averageScore * 10 }}%"></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-box shadow-sm d-flex flex-column justify-content-center">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <p class="text-muted fw-bold text-uppercase small mb-0 letter-spacing-1">Bài tập chưa nộp</p>
                    <div class="icon-box bg-danger-subtle text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="bi bi-exclamation-lg fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 text-danger">{{ $pendingExams }} <span class="text-muted fs-6 fw-medium ms-1">bài</span></h3>
            </div>
        </div>
    </div>

    <div class="d-flex align-items-center mb-4 gap-2">
        <h5 class="fw-bold theme-text-dark mb-0">Danh sách bài tập</h5>
        <span class="badge bg-purple-light text-purple-dark border border-purple-subtle rounded-pill px-3 py-2 shadow-sm">{{ $classroom->exams->count() }} bài</span>
    </div>

    <div class="task-list">
        @forelse ($classroom->exams as $exam)
            @php
                $result = $results->firstWhere('exam_id', $exam->id);

                $hasEssay = $exam->questions->where('type', 'essay')->count() > 0;

                if ($result) {
                    if ($hasEssay) {
                        // Tự luận: score đã là điểm hệ 10
                        $displayScore = floatval($result->score);
                    } else {
                        // Trắc nghiệm: score là số câu đúng
                        $displayScore = (floatval($result->score) / max(1, intval($result->total_questions))) * 10;
                    }

                    $displayScore = max(0, min(10, $displayScore));
                }
            @endphp

            <div class="task-item shadow-sm d-flex flex-column flex-md-row gap-4 align-items-md-center position-relative">
                
                @if($result)
                    <div class="task-icon bg-success-subtle text-success flex-shrink-0 shadow-sm">
                        <i class="bi bi-check-lg fs-4"></i>
                    </div>
                @else
                    <div class="task-icon bg-warning-subtle text-warning flex-shrink-0 shadow-sm">
                        <i class="bi bi-hourglass-split fs-4"></i>
                    </div>
                @endif

                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <h5 class="fw-bold mb-0 theme-text-dark text-truncate" style="max-width: 400px;" title="{{ $exam->title }}">{{ $exam->title }}</h5>
                        @if($result)
                            <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill px-3 py-1 ms-md-2 shadow-sm">Đã nộp</span>
                        @else
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning border-opacity-25 rounded-pill px-3 py-1 ms-md-2 shadow-sm">Chưa làm</span>
                        @endif
                    </div>
                    
                    <p class="text-muted mb-3 opacity-75" style="font-size: 0.9rem;">
                        {{ $hasEssay ? 'Bài kiểm tra tự luận AI' : 'Bài kiểm tra trắc nghiệm AI' }}
                        &bull; {{ $exam->questions->count() }} câu
                    </p>

                    <div class="d-flex align-items-center text-muted flex-wrap gap-4" style="font-size: 0.85rem;">
                        <span class="d-flex align-items-center gap-1 bg-light px-2 py-1 rounded-2 border"><i class="bi bi-calendar-event"></i> Ngày giao: <strong>{{ $exam->created_at->format('d/m/Y') }}</strong></span>
                        <span class="d-flex align-items-center gap-1 bg-light px-2 py-1 rounded-2 border"><i class="bi bi-stopwatch"></i> Thời gian: <strong>{{ $exam->duration }} phút</strong></span>
                        @if($result)
                            <span class="text-success fw-bold d-flex align-items-center gap-1 bg-success-subtle px-2 py-1 rounded-2 border border-success border-opacity-25">
                                <i class="bi bi-award-fill"></i> Điểm: {{ number_format($displayScore, 1) }}/10
                            </span>
                        @endif
                    </div>
                </div>

                <div class="task-action border-start-md ps-md-4 mt-3 mt-md-0 d-flex justify-content-end" style="min-width: 160px;">
                    @if($result)
                        <a href="{{ route('exams.result', $exam->id) }}" class="btn btn-outline-success fw-bold rounded-pill w-100 py-2 d-flex justify-content-center align-items-center gap-2">
                            <i class="bi bi-file-bar-graph"></i> Xem điểm
                        </a>
                    @else
                        <a href="{{ route('exams.play', $exam->id) }}" class="btn btn-theme-primary fw-bold rounded-pill w-100 py-2 d-flex justify-content-center align-items-center gap-2 shadow-sm">
                            <i class="bi bi-pencil-square"></i> Làm bài ngay
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-5 bg-white border border-purple-subtle border-dashed rounded-4 shadow-sm">
                <div class="empty-icon-circle bg-purple-light theme-text-primary d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 80px; height: 80px; border-radius: 50%;">
                    <i class="bi bi-inboxes fs-1"></i>
                </div>
                <h5 class="mt-2 theme-text-dark fw-bold">Chưa có bài tập nào</h5>
                <p class="text-muted opacity-75 mb-0">Giảng viên chưa giao bài tập nào trong lớp này. Hãy quay lại sau nhé!</p>
            </div>
        @endforelse
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('js/student/student_show.js') }}?v={{ time() }}"></script>
@endpush