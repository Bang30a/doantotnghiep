@extends('layouts.student.student_app')

@section('title', 'Lịch sử làm bài')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/student/student_history.css') }}?v={{ time() }}">
@endpush

@section('content')

    <!-- 1. Tiêu đề chuẩn form -->
    <div class="mb-4 mt-3">
        <h3 class="fw-bold mb-2 theme-text-dark d-flex align-items-center gap-2">
            <i class="bi bi-clock-history theme-text-primary"></i> Lịch sử làm bài
        </h3>
        <p class="text-muted fs-6 mb-0">Xem lại chi tiết các bài kiểm tra bạn đã hoàn thành</p>
    </div>

    <!-- TÍNH TOÁN LẠI BẰNG FRONTEND: Phân biệt Tự luận và Trắc nghiệm -->
    @php
    $realHighestScore = 0;
    $realTotalScore = 0;
    $realCount = count($results ?? []);

    if ($realCount > 0) {
        foreach ($results as $res) {
            // Nhận diện tự luận theo bảng questions, không dùng exam_type
            $hasEssay = $res->exam 
                && $res->exam->questions 
                && $res->exam->questions->where('type', 'essay')->count() > 0;

            if ($hasEssay) {
                // Tự luận: score đã là điểm hệ 10
                $sc10 = floatval($res->score);
            } else {
                // Trắc nghiệm: score là số câu đúng, cần quy đổi hệ 10
                $tQ = max(1, intval($res->total_questions));
                $sc10 = (floatval($res->score) / $tQ) * 10;
            }

            // Chặn điểm ngoài khoảng 0 - 10
            $sc10 = max(0, min(10, $sc10));

            if ($sc10 > $realHighestScore) {
                $realHighestScore = $sc10;
            }

            $realTotalScore += $sc10;
        }

        $realAverageScore = $realTotalScore / $realCount;
    } else {
        $realAverageScore = 0;
    }
@endphp

    <!-- 2. Khối Thống kê -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="stat-box bg-white shadow-sm rounded-4 p-4 d-flex flex-column justify-content-center h-100 border border-light">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <p class="text-muted fw-bold text-uppercase small mb-0 letter-spacing-1">Tổng số bài</p>
                    <div class="icon-box bg-purple-light theme-text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="bi bi-journal-check fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 theme-text-dark display-6">{{ $realCount }}</h3>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="stat-box bg-white shadow-sm rounded-4 p-4 d-flex flex-column justify-content-center h-100 border border-light">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <p class="text-muted fw-bold text-uppercase small mb-0 letter-spacing-1">Điểm trung bình</p>
                    <div class="icon-box bg-indigo-light text-indigo rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="bi bi-bullseye fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 text-indigo display-6">{{ number_format($realAverageScore, 1) }}<span class="text-muted fs-5 fw-medium ms-1">/ 10</span></h3>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="stat-box bg-white shadow-sm rounded-4 p-4 d-flex flex-column justify-content-center h-100 border border-light">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <p class="text-muted fw-bold text-uppercase small mb-0 letter-spacing-1">Điểm cao nhất</p>
                    <div class="icon-box bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="bi bi-trophy fs-5"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-0 text-success display-6">{{ number_format($realHighestScore, 1) }}<span class="text-muted fs-5 fw-medium ms-1">/ 10</span></h3>
            </div>
        </div>
    </div>

    <!-- 3. Tiêu đề danh sách -->
    <div class="d-flex align-items-center mb-4 gap-2">
        <h5 class="fw-bold theme-text-dark mb-0">Danh sách chi tiết</h5>
        <span class="badge bg-purple-light text-purple-dark border border-purple-subtle rounded-pill px-3 py-2 shadow-sm">{{ $realCount }} lượt làm</span>
    </div>

    <!-- 4. Danh sách làm bài -->
    <div class="task-list d-flex flex-column gap-3">
        @forelse($results ?? [] as $result)
           @php
                // Nhận diện tự luận theo câu hỏi trong đề
                $hasEssay = $result->exam
                    && $result->exam->questions
                    && $result->exam->questions->where('type', 'essay')->count() > 0;

                if ($hasEssay) {
                    // Tự luận: score đã là điểm hệ 10
                    $score10 = floatval($result->score);
                    $correctText = 'Tự luận';
                } else {
                    // Trắc nghiệm: score là số câu đúng
                    $totalQ = max(1, intval($result->total_questions));
                    $score10 = (floatval($result->score) / $totalQ) * 10;
                    $correctText = intval($result->score) . '/' . $totalQ . ' câu';
                }

                // Chặn điểm ngoài khoảng 0 - 10
                $score10 = max(0, min(10, $score10));

                // Phân loại màu theo Theme
                if ($score10 >= 8) {
                    $badgeClass = 'bg-success-subtle text-success border-success';
                    $iconClass = 'bi-check-circle-fill text-success';
                } elseif ($score10 >= 5) {
                    $badgeClass = 'bg-warning-subtle text-warning-emphasis border-warning';
                    $iconClass = 'bi-exclamation-circle-fill text-warning';
                } else {
                    $badgeClass = 'bg-danger-subtle text-danger border-danger';
                    $iconClass = 'bi-x-circle-fill text-danger';
                }
            @endphp

            <div class="task-item bg-white shadow-sm rounded-4 p-4 d-flex flex-column flex-md-row gap-4 align-items-md-center position-relative border border-light">
                
                <!-- Icon trạng thái -->
                <div class="task-icon flex-shrink-0 shadow-sm rounded-circle d-flex align-items-center justify-content-center {{ explode(' ', $badgeClass)[0] }} {{ explode(' ', $badgeClass)[1] }}" style="width: 50px; height: 50px;">
                    <i class="bi {{ $iconClass }} fs-4"></i>
                </div>

                <!-- Thông tin bài làm -->
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <h5 class="fw-bold mb-0 theme-text-dark text-truncate" style="max-width: 400px;" title="{{ $result->exam->title ?? 'Bài thi đã xóa' }}">
                            {{ $result->exam->title ?? 'Bài thi đã xóa' }}
                        </h5>
                        <span class="badge {{ $badgeClass }} border border-opacity-25 rounded-pill px-3 py-1 ms-md-2 shadow-sm">
                            Điểm: {{ number_format($score10, 1) }}/10
                        </span>
                    </div>
                    
                    <div class="d-flex align-items-center text-muted flex-wrap gap-3 mt-3 fw-medium" style="font-size: 0.85rem;">
                        <span class="d-flex align-items-center gap-1 bg-light px-2 py-1 rounded-2 border">
                            <i class="bi bi-calendar-check theme-text-primary"></i> Nộp lúc: <strong>{{ $result->created_at->format('H:i - d/m/Y') }}</strong>
                        </span>
                        <span class="d-flex align-items-center gap-1 bg-light px-2 py-1 rounded-2 border">
                            @if($hasEssay)
                                <i class="bi bi-pencil-square text-primary"></i> Loại bài: <strong>{{ $correctText }}</strong>
                            @else
                                <i class="bi bi-bullseye text-success"></i> Đúng: <strong>{{ $correctText }}</strong>
                            @endif
                        </span>
                    </div>
                </div>
                
                <!-- Nút hành động -->
                <div class="task-action border-start-md ps-md-4 mt-3 mt-md-0 d-flex justify-content-end" style="min-width: 150px;">
                    <a href="{{ route('exams.result', $result->exam->id ?? 1) }}" class="btn bg-purple-light theme-text-primary border border-purple-subtle fw-bold rounded-pill w-100 py-2 d-flex justify-content-center align-items-center gap-2 transition-all hover-shadow">
                        <i class="bi bi-eye"></i> Xem chi tiết
                    </a>
                </div>
            </div>
        @empty
            <div class="text-center py-5 bg-white border border-purple-subtle border-dashed rounded-4 shadow-sm">
                <div class="empty-icon-circle bg-purple-light theme-text-primary d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 80px; height: 80px; border-radius: 50%;">
                    <i class="bi bi-inboxes fs-1"></i>
                </div>
                <h5 class="mt-2 theme-text-dark fw-bold">Chưa có lịch sử làm bài</h5>
                <p class="text-muted opacity-75 mb-4">Bạn chưa hoàn thành bài kiểm tra nào. Hãy luyện tập ngay nhé!</p>
                <a href="{{ route('student.exams.create') ?? '#' }}" class="btn btn-theme-primary px-5 py-3 fw-bold rounded-pill shadow-sm">
                    <i class="bi bi-magic me-2"></i> Bắt đầu tự luyện
                </a>
            </div>
        @endforelse
    </div>

@endsection