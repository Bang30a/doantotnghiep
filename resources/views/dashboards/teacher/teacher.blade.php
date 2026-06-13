@extends('layouts.teacher.teacher_app')

@section('title', 'Bảng điều khiển')

@push('styles')
    <link rel="stylesheet" href="{{ versioned_asset('css/teacher/teacher_dashboard.css') }}">
@endpush

@section('content')
    <div class="teacher-page-heading teacher-dashboard-hero mb-4 mt-2 d-flex align-items-center justify-content-between">
        <div>
            @php
                $firstName = explode(' ', Auth::user()->name)[0];
            @endphp
            <h2 class="fw-bold theme-text-dark mb-1 d-flex align-items-center gap-2">
                Xin chào, {{ $firstName }}! <span class="wave-emoji"></span>
            </h2>
            <p class="text-muted fs-6 mb-0 opacity-75">Cùng xem qua tổng quan các lớp học và bài kiểm tra hôm nay nhé.</p>
        </div>
        
        <a href="{{ route('teacher.exams.create') }}" class="btn btn-theme-primary px-4 py-2 rounded-pill fw-bold shadow-sm d-flex align-items-center gap-2">
            <i class="bi bi-magic text-warning"></i> Tạo đề AI mới
        </a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="stat-card-top shadow-sm border-0">
                <div class="d-flex flex-column h-100 justify-content-between">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <p class="text-muted mb-0 fw-bold text-uppercase letter-spacing-1" style="font-size: 0.75rem;">Lớp học</p>
                        <div class="icon-box-rounded bg-blue-subtle text-blue shadow-sm border-0"><i class="bi bi-diagram-3-fill"></i></div>
                    </div>
                    <h2 class="fw-bold mb-0 theme-text-dark display-6">{{ $classCount ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="stat-card-top shadow-sm border-0">
                <div class="d-flex flex-column h-100 justify-content-between">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <p class="text-muted mb-0 fw-bold text-uppercase letter-spacing-1" style="font-size: 0.75rem;">Học viên</p>
                        <div class="icon-box-rounded bg-green-subtle text-green shadow-sm border-0"><i class="bi bi-people-fill"></i></div>
                    </div>
                    <h2 class="fw-bold mb-0 theme-text-dark display-6">{{ $studentCount ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="stat-card-top shadow-sm border-0">
                <div class="d-flex flex-column h-100 justify-content-between">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <p class="text-muted mb-0 fw-bold text-uppercase letter-spacing-1" style="font-size: 0.75rem;">Ngân hàng đề</p>
                        <div class="icon-box-rounded bg-purple-light theme-text-primary shadow-sm border-0"><i class="bi bi-collection-play-fill"></i></div>
                    </div>
                    <h2 class="fw-bold mb-0 theme-text-dark display-6">{{ $examCount ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="stat-card-top shadow-sm border-0">
                <div class="d-flex flex-column h-100 justify-content-between">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <p class="text-muted mb-0 fw-bold text-uppercase letter-spacing-1" style="font-size: 0.75rem;">Bài thi đã giao</p>
                        <div class="icon-box-rounded bg-orange-subtle text-orange shadow-sm border-0"><i class="bi bi-send-check-fill"></i></div>
                    </div>
                    <h2 class="fw-bold mb-0 theme-text-dark display-6">{{ $assignedExamCount ?? 0 }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        
        <div class="col-xl-8 col-lg-7">
            <div class="section-card shadow-sm border-0">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3">
                    <div>
                        <h5 class="fw-bold mb-1 theme-text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-mortarboard-fill theme-text-primary"></i> Lớp học của bạn
                        </h5>
                        <p class="text-muted fs-6 mb-0 opacity-75">Quản lý các lớp học đang giảng dạy</p>
                    </div>
                    <a href="{{ route('teacher.classrooms') }}" class="btn btn-sm btn-outline-theme rounded-pill fw-medium px-3 border-0">
                        Xem tất cả <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                
                <div class="class-list">
                    @forelse($recentClasses ?? [] as $room)
                        <div class="list-item-teacher shadow-sm border-0">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-placeholder shadow-sm border-0">
                                    <i class="bi bi-journal-bookmark-fill"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 theme-text-dark text-truncate" style="max-width: 200px;">{{ $room->name }}</h6>
                                    <div class="d-flex align-items-center gap-3 text-muted small fw-medium">
                                        <span class="d-flex align-items-center gap-1"><i class="bi bi-people-fill"></i> {{ $room->users_count ?? 0 }} học viên</span>
                                        <span class="d-flex align-items-center gap-1"><i class="bi bi-file-earmark-text-fill"></i> {{ $room->exams_count ?? 0 }} bài thi</span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge bg-light text-dark border-0 px-2 py-1 rounded-3 font-monospace">{{ $room->code }}</span>
                                <a href="{{ route('classrooms.show', $room->id) }}" class="btn btn-light rounded-circle p-2 d-flex align-items-center justify-content-center shadow-sm hover-theme border-0" style="width: 36px; height: 36px;" title="Vào lớp">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 bg-light rounded-4 border-0 mb-3">
                            <div class="icon-box bg-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm mb-3 text-muted border-0" style="width: 60px; height: 60px;">
                                <i class="bi bi-inboxes fs-3"></i>
                            </div>
                            <h6 class="fw-bold theme-text-dark">Chưa có lớp học nào</h6>
                            <p class="text-muted mb-0 opacity-75 small">Hãy tạo lớp học đầu tiên để thêm học viên vào.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="section-card shadow-sm bg-theme-primary text-white position-relative overflow-hidden border-0">
                <div class="position-absolute top-0 end-0 h-100 opacity-20 pointer-events-none" style="width: 200px; background: radial-gradient(circle at top right, white, transparent 70%); border-0"></div>
                
                <div class="position-relative z-1">
                    <h5 class="fw-bold mb-1 d-flex align-items-center gap-2">
                        <i class="bi bi-lightning-charge-fill text-warning"></i> Hành động nhanh
                    </h5>
                    <p class="fs-6 mb-4 text-white text-opacity-75">Công cụ thường dùng cho giảng viên</p>

                    <div class="d-flex flex-column gap-3">
                        <a href="{{ route('teacher.exams.create') }}" class="quick-action-item dark shadow-sm border-0">
                            <div class="qa-icon bg-white text-dark border-0"><i class="bi bi-magic text-warning"></i></div>
                            <div class="qa-text">
                                <span class="d-block fw-bold text-white fs-6">Tạo đề thi tự động bằng AI</span>
                                <span class="d-block small text-white text-opacity-75 fw-normal">Biến tài liệu thành câu hỏi trong 1 phút</span>
                            </div>
                            <i class="bi bi-arrow-right-short fs-4 ms-auto text-white opacity-50"></i>
                        </a>
                        
                        <a href="{{ route('teacher.documents.index') }}" class="quick-action-item light shadow-sm border-0">
                            <div class="qa-icon bg-purple-light theme-text-primary border-0"><i class="bi bi-cloud-arrow-up-fill"></i></div>
                            <div class="qa-text">
                                <span class="d-block fw-bold theme-text-dark fs-6">Kho tài liệu & Bài giảng</span>
                                <span class="d-block small text-muted fw-normal">Upload PDF, DOCX lên hệ thống</span>
                            </div>
                        </a>
                        
                        <a href="{{ route('teacher.reports.index') }}" class="quick-action-item light shadow-sm border-0">
                            <div class="qa-icon bg-success-subtle text-success border-0"><i class="bi bi-clipboard-data-fill"></i></div>
                            <div class="qa-text">
                                <span class="d-block fw-bold theme-text-dark fs-6">Sổ điểm & Báo cáo</span>
                                <span class="d-block small text-muted fw-normal">Chấm điểm và xem kết quả</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div>
        <h5 class="fw-bold mb-3 theme-text-dark d-flex align-items-center gap-2">
            <i class="bi bi-bar-chart-line-fill theme-text-primary"></i> Tổng quan thống kê
        </h5>
        <div class="row g-4">
            
            <div class="col-md-4">
                <div class="bottom-stat bg-blue-light shadow-sm d-flex flex-column justify-content-center position-relative overflow-hidden border-0">
                    <i class="bi bi-graph-up-arrow position-absolute text-blue opacity-10" style="font-size: 5rem; right: -10px; bottom: -15px;"></i>
                    <div class="position-relative z-1">
                        <div class="d-flex align-items-center gap-2 mb-2 text-blue fw-bold text-uppercase letter-spacing-1" style="font-size: 0.8rem;">
                            <i class="bi bi-award-fill fs-5"></i> Điểm trung bình
                        </div>
                        <h2 class="fw-bold text-blue mb-1 display-6">{{ number_format($averageScore ?? 0, 1) }}<span class="fs-5 fw-medium opacity-75">/10</span></h2>
                        <small class="text-blue opacity-75 fw-medium">Toàn hệ thống ({{ isset($results) ? $results->count() : 0 }} lượt nộp)</small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="bottom-stat bg-green-light shadow-sm d-flex flex-column justify-content-center position-relative overflow-hidden border-0">
                    <i class="bi bi-check-circle position-absolute text-green opacity-10" style="font-size: 5rem; right: -10px; bottom: -15px;"></i>
                    <div class="position-relative z-1">
                        <div class="d-flex align-items-center gap-2 mb-2 text-green fw-bold text-uppercase letter-spacing-1" style="font-size: 0.8rem;">
                            <i class="bi bi-ui-checks fs-5"></i> Tỷ lệ hoàn thành
                        </div>
                        <h2 class="fw-bold text-green mb-1 display-6">{{ round($completionRate ?? 0) }}%</h2>
                        <small class="text-green opacity-75 fw-medium">Số bài tập được giao đã nộp</small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="bottom-stat bg-purple-light shadow-sm d-flex flex-column justify-content-center position-relative overflow-hidden border-0">
                    <i class="bi bi-journal-plus position-absolute theme-text-primary opacity-10" style="font-size: 5rem; right: -10px; bottom: -15px;"></i>
                    <div class="position-relative z-1">
                        <div class="d-flex align-items-center gap-2 mb-2 theme-text-primary fw-bold text-uppercase letter-spacing-1" style="font-size: 0.8rem;">
                            <i class="bi bi-file-earmark-plus-fill fs-5"></i> Đề thi tuần này
                        </div>
                        <h2 class="fw-bold theme-text-primary mb-1 display-6">{{ $examsThisWeek ?? 0 }} <span class="fs-5 fw-medium opacity-75">đề</span></h2>
                        <small class="theme-text-primary opacity-75 fw-medium">Đề thi mới tạo trong 7 ngày qua</small>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
@endsection
