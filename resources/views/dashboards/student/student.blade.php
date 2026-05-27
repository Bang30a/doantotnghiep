@extends('layouts.student.student_app')

@section('title', 'Bảng điều khiển')

@section('content')
    <div class="mb-5 mt-4">
        <h2 class="fw-800 text-dark mb-1">
            Xin chào, {{ Auth::user()->name ?? 'Học viên' }}! <span class="wave">👋</span>
        </h2>
        <p class="text-muted fs-6 fw-medium">Hôm nay bạn muốn chinh phục kiến thức nào mới?</p>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="stat-card p-4 shadow-sm border-0 rounded-4 bg-white d-flex align-items-center justify-content-between">
                <div>
                    <p class="text-muted mb-1 fw-semibold small">Bài thi đã làm</p>
                    <h3 class="fw-bold text-dark mb-0">{{ $completedExamsCount ?? 0 }}</h3>
                </div>
                <div class="stat-icon-wrapper purple">
                    <i class="bi bi-file-earmark-check-fill"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card p-4 shadow-sm border-0 rounded-4 bg-white d-flex align-items-center justify-content-between">
                <div>
                    <p class="text-muted mb-1 fw-semibold small">Điểm trung bình</p>
                    <h3 class="fw-bold text-dark mb-0 text-gradient-purple">{{ number_format($averageScore ?? 0, 1) }}</h3>
                </div>
                <div class="stat-icon-wrapper success">
                    <i class="bi bi-stars"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card p-4 shadow-sm border-0 rounded-4 bg-white d-flex align-items-center justify-content-between">
                <div>
                    <p class="text-muted mb-1 fw-semibold small">Lớp học</p>
                    <h3 class="fw-bold text-dark mb-0">{{ $classroomsCount ?? 0 }}</h3>
                </div>
                <div class="stat-icon-wrapper info">
                    <i class="bi bi-buildings-fill"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card p-4 shadow-sm border-0 rounded-4 bg-white d-flex align-items-center justify-content-between">
                <div>
                    <p class="text-muted mb-1 fw-semibold small">Thời gian học</p>
                    <h3 class="fw-bold text-dark mb-0">{{ ($completedExamsCount ?? 0) * 2 }}h</h3>
                </div>
                <div class="stat-icon-wrapper warning">
                    <i class="bi bi-stopwatch-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-lg-6">
            <div class="section-card p-4 bg-white shadow-sm border-soft rounded-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold mb-1">Bài tập sắp đến hạn</h5>
                        <p class="text-muted small mb-0">Cần hoàn thành đúng hạn bạn nhé</p>
                    </div>
                    <i class="bi bi-calendar-event text-purple fs-4"></i>
                </div>
                
                <div class="list-container">
                    @if(isset($upcomingExams) && count($upcomingExams) > 0)
                        @foreach($upcomingExams as $exam)
                            <div class="list-item-modern d-flex align-items-center p-3 mb-3 rounded-4">
                                <div class="item-icon bg-warning-light text-warning me-3">
                                    <i class="bi bi-clock-history"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold">{{ $exam->title }}</h6>
                                    <span class="badge bg-light text-muted fw-medium rounded-pill px-2">
                                        <i class="bi bi-folder2 me-1"></i>{{ $exam->classroom ? $exam->classroom->name : 'Cá nhân' }}
                                    </span>
                                </div>
                                <a href="{{ route('exams.play', $exam->id) }}" class="btn btn-purple-sm rounded-pill fw-bold">Làm ngay</a>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <img src="https://cdn-icons-png.flaticon.com/512/4436/4436481.png" alt="No exams" style="width: 60px; opacity: 0.6;" class="mb-3">
                            <p class="text-muted fw-medium small">Tất cả bài tập đã hoàn tất!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="section-card p-4 bg-white shadow-sm border-soft rounded-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold mb-1">Bài thi gần đây</h5>
                        <p class="text-muted small mb-0">Theo dõi tiến độ học tập</p>
                    </div>
                    <i class="bi bi-bar-chart-fill text-purple fs-4"></i>
                </div>
                
                <div class="list-container">
                    @if(isset($recentResults) && count($recentResults) > 0)
                        @foreach($recentResults as $result)
                            @php
                                $result->loadMissing('exam.questions');

                                $hasEssay = $result->exam
                                    && $result->exam->questions
                                    && $result->exam->questions->where('type', 'essay')->count() > 0;

                                if ($hasEssay) {
                                    // Tự luận: score đã là điểm hệ 10
                                    $score10 = floatval($result->score);
                                } else {
                                    // Trắc nghiệm: score là số câu đúng
                                    $score10 = (floatval($result->score) / max(1, intval($result->total_questions))) * 10;
                                }

                                $score10 = max(0, min(10, $score10));
                            @endphp
                            <div class="list-item-modern d-flex align-items-center p-3 mb-3 rounded-4">
                                <div class="item-icon {{ $score10 >= 5 ? 'bg-success-light text-success' : 'bg-danger-light text-danger' }} me-3">
                                    <i class="bi {{ $score10 >= 5 ? 'bi-patch-check' : 'bi-exclamation-triangle' }}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold">{{ $result->exam->title ?? 'Bài thi' }}</h6>
                                    <small class="text-muted fw-medium">{{ $result->created_at->format('d/m/Y') }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="fs-5 fw-800 {{ $score10 >= 5 ? 'text-success' : 'text-danger' }}">{{ number_format($score10, 1) }}</span>
                                    <div class="small text-muted opacity-75">Điểm</div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-clipboard2-x fs-1 text-muted opacity-50 mb-2 d-block"></i>
                            <p class="text-muted fw-medium small">Chưa có kết quả thi nào.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="mb-5">
        <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
            <span class="title-line"></span> Khám phá tính năng AI
        </h5>
        <div class="quick-actions-wrapper d-flex gap-3 flex-wrap">
            <a href="{{ route('student.classrooms') ?? '#' }}" class="action-card rounded-4 p-3 flex-fill text-center">
                <div class="action-icon bg-primary-light text-primary"><i class="bi bi-diagram-3"></i></div>
                <span class="fw-bold small mt-2 d-block">Lớp học</span>
            </a>
            <a href="{{ route('student.documents') ?? '#' }}" class="action-card rounded-4 p-3 flex-fill text-center">
                <div class="action-icon bg-success-light text-success"><i class="bi bi-folder-symlink"></i></div>
                <span class="fw-bold small mt-2 d-block">Kho tài liệu</span>
            </a>
            <a href="{{ route('student.exams.create') ?? '#' }}" class="action-card active rounded-4 p-3 flex-fill text-center shadow-purple">
                <div class="action-icon bg-purple text-white"><i class="bi bi-magic"></i></div>
                <span class="fw-bold small mt-2 d-block">Tạo đề AI</span>
            </a>
            <a href="{{ route('student.question-banks') ?? '#' }}" class="action-card rounded-4 p-3 flex-fill text-center">
                <div class="action-icon bg-warning-light text-warning"><i class="bi bi-collection-play"></i></div>
                <span class="fw-bold small mt-2 d-block">Ngân hàng</span>
            </a>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="tip-card-modern p-4 rounded-4 border-0 h-100" style="--tip-color: #7E22CE;">
                <div class="tip-icon mb-3"><i class="bi bi-calendar-check"></i></div>
                <h6 class="fw-bold">Học đều đặn</h6>
                <p class="mb-0 text-muted small">Dành 30-60 phút mỗi ngày để ôn tập tài liệu và giải đề thi do AI tạo ra.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="tip-card-modern p-4 rounded-4 border-0 h-100" style="--tip-color: #059669;">
                <div class="tip-icon mb-3"><i class="bi bi-lightbulb"></i></div>
                <h6 class="fw-bold">Xem giải thích AI</h6>
                <p class="mb-0 text-muted small">Đừng chỉ xem điểm. Hãy đọc kỹ phần giải thích của AI cho các câu trả lời sai.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="tip-card-modern p-4 rounded-4 border-0 h-100" style="--tip-color: #E11D48;">
                <div class="tip-icon mb-3"><i class="bi bi-file-earmark-arrow-up"></i></div>
                <h6 class="fw-bold">Tải tài liệu chuẩn</h6>
                <p class="mb-0 text-muted small">Upload file PDF bài giảng để AI tạo ra bộ câu hỏi sát thực tế nhất.</p>
            </div>
        </div>
    </div>
@endsection