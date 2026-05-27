@extends('layouts.student.student_app')

@section('title', 'Lớp học của tôi')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/student/student_classrooms.css') }}?v={{ time() }}">
@endpush

@section('content')

    @if(session('success'))
        <div class="alert alert-custom-success alert-dismissible fade show mb-4 shadow-sm border-0 rounded-3 mt-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm border-0 rounded-3 mt-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4 gap-3 mt-3">
        <div>
            <h3 class="fw-bold mb-2 theme-text-dark d-flex align-items-center gap-2">
                <i class="bi bi-mortarboard-fill theme-text-primary"></i> Lớp học của tôi
            </h3>
            <p class="text-muted fs-6 mb-0">Xem và quản lý các lớp học bạn đã tham gia</p>
        </div>
        <button class="btn btn-theme-primary px-4 py-2 fw-bold rounded-pill shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#joinClassModal">
            <i class="bi bi-plus-lg"></i> Tham gia lớp
        </button>
    </div>

    @if($classrooms->isEmpty())
        <div class="empty-state-card mt-4 shadow-sm">
            <div class="empty-icon-circle bg-purple-light theme-text-primary shadow-sm">
                <i class="bi bi-people-fill"></i>
            </div>
            <h4 class="fw-bold mb-2 theme-text-dark">Chưa tham gia lớp học nào</h4>
            <p class="text-muted mb-4 opacity-75">Nhập mã lớp học từ giảng viên để tham gia và nhận bài tập ngay!</p>
            <button class="btn btn-theme-primary px-5 py-3 fw-bold rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#joinClassModal">
                <i class="bi bi-door-open-fill me-2"></i> Bắt đầu tham gia lớp học
            </button>
        </div>
    @else
        <div class="row g-4 mt-2">
            @foreach($classrooms as $room)
                <div class="col-md-6 col-lg-4">
                    <a href="{{ route('classrooms.show', $room->id) }}" class="class-card shadow-sm h-100 d-flex flex-column">
                        <div class="class-card-header">
                            <h5 class="fw-bold mb-1 text-truncate" title="{{ $room->name }}">{{ $room->name }}</h5>
                            <p class="mb-0 text-white text-opacity-75 d-flex align-items-center gap-1" style="font-size: 0.85rem;">
                                <i class="bi bi-cpu"></i> Hệ thống {{ $globalSettings['site_name'] ?? 'EduQuiz AI' }}
                            </p>
                        </div>
                        <div class="class-card-body flex-grow-1 d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-center text-muted mb-4">
                                <div class="teacher-avatar bg-purple-light theme-text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    <i class="bi bi-person-workspace fs-5"></i>
                                </div>
                                <div>
                                    <span class="d-block small fw-bold text-uppercase letter-spacing-1 opacity-75">Giảng viên</span>
                                    <span class="fw-medium theme-text-dark">{{ $room->teacher ? $room->teacher->name : 'Đang cập nhật' }}</span>
                                </div>
                            </div>
                           <div class="mt-auto bg-light rounded-3 p-3 d-flex justify-content-between align-items-center">
                                <div class="text-muted fw-medium d-flex align-items-center gap-2" style="font-size: 0.9rem;">
                                    <div class="bg-white rounded p-1 shadow-sm d-flex align-items-center justify-content-center">
                                        <i class="bi bi-file-earmark-text theme-text-primary"></i>
                                    </div>
                                    <span>{{ $room->exams_count }} bài tập</span>
                                </div>
                                
                                <div class="d-flex align-items-center gap-2" title="Mã lớp học">
                                    <span class="text-muted small">Mã:</span>
                                    <span class="fw-bold theme-text-primary bg-white px-2 py-1 rounded shadow-sm" style="letter-spacing: 1px;">
                                        {{ $room->code }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <div class="guide-section shadow-sm mt-5">
            <h6 class="fw-bold mb-4 theme-text-dark text-uppercase letter-spacing-1 d-flex align-items-center gap-2">
                <i class="bi bi-info-circle-fill theme-text-primary"></i> Hướng dẫn học tập
            </h6>
            <div class="row g-4">
                <div class="col-md-4 d-flex gap-3 align-items-start">
                    <div class="guide-icon bg-indigo-light text-indigo shadow-sm">
                        <i class="bi bi-journal-text"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 fs-6 theme-text-dark">1. Xem bài tập</h6>
                        <p class="text-muted mb-0 opacity-75" style="font-size: 0.85rem;">Nhấp vào lớp học tương ứng để xem danh sách các bài tập được giao.</p>
                    </div>
                </div>
                <div class="col-md-4 d-flex gap-3 align-items-start">
                    <div class="guide-icon bg-success-subtle text-success shadow-sm">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 fs-6 theme-text-dark">2. Làm bài & Nộp</h6>
                        <p class="text-muted mb-0 opacity-75" style="font-size: 0.85rem;">Hoàn thành trắc nghiệm/tự luận trước thời hạn (deadline) của lớp.</p>
                    </div>
                </div>
                <div class="col-md-4 d-flex gap-3 align-items-start">
                    <div class="guide-icon bg-purple-light theme-text-primary shadow-sm">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1 fs-6 theme-text-dark">3. Theo dõi tiến độ</h6>
                        <p class="text-muted mb-0 opacity-75" style="font-size: 0.85rem;">Xem ngay điểm số và đọc nhận xét chi tiết từ giảng viên.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="modal fade" id="joinClassModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold theme-text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-door-open theme-text-primary"></i> Tham gia lớp học
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="{{ route('classrooms.join') }}" method="POST">
                        @csrf
                        <div class="mb-4 mt-2">
                            <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1">Nhập mã lớp học <span class="text-danger">*</span></label>
                            <div class="input-group input-group-custom shadow-sm">
                                <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-upc-scan"></i></span>
                                <input type="text" name="code" class="form-control form-control-lg bg-light border-start-0 ps-0 fw-bold" placeholder="VD: MATH10A" required>
                            </div>
                            <small class="text-muted mt-2 d-block opacity-75"><i class="bi bi-lightbulb text-warning me-1"></i>Hãy hỏi giảng viên của bạn mã lớp học (gồm chữ và số) để tham gia nhé.</small>
                        </div>
                        <div class="d-flex justify-content-end gap-2 pt-2">
                            <button type="button" class="btn btn-light fw-bold px-4 rounded-pill" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-theme-primary px-5 fw-bold rounded-pill shadow-sm">Tham gia ngay</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/student/student_classrooms.js') }}?v={{ time() }}"></script>
@endpush