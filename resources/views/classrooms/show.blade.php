@extends('layouts.teacher.teacher_app')

@section('title', 'Quản lý Lớp: ' . $classroom->name)

@push('styles')
    <link rel="stylesheet" href="{{ versioned_asset('css/teacher/teacher_classes.css') }}">
@endpush

@section('content')

    <!-- Nút quay lại -->
    <div class="mb-4 mt-2">
        <a href="{{ route('teacher.classrooms') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    <!-- Banner Thông tin lớp -->
    <div class="class-banner shadow-sm p-4 p-md-5 rounded-4 text-white mb-4 position-relative overflow-hidden">
        <div class="banner-bg-shape"><i class="bi bi-mortarboard-fill"></i></div>
        
        <div class="position-relative z-1 d-flex justify-content-between align-items-center flex-column flex-lg-row gap-4">
            <div>
                <span class="badge bg-white theme-text-primary px-3 py-2 rounded-pill fw-bold mb-3 shadow-sm">
                    <i class="bi bi-diagram-3-fill me-1"></i> Không gian lớp học
                </span>
                <h2 class="fw-800 mb-2 display-6">{{ $classroom->name }}</h2>
                <p class="mb-0 text-white opacity-75 d-flex align-items-center gap-2 fw-medium">
                    <i class="bi bi-shield-check"></i> Quản lý Học viên và Bài tập của lớp học này
                </p>
            </div>
            
            <div class="glass-box p-3 px-4 rounded-4 shadow-sm text-center text-lg-end">
                <p class="mb-1 text-white opacity-75 small text-uppercase fw-bold letter-spacing-1">Mã tham gia lớp</p>
                <div class="d-flex align-items-center justify-content-center justify-content-lg-end gap-3">
                    <span class="fs-2 fw-800 font-monospace tracking-wide">{{ $classroom->code }}</span>
                    <button type="button" class="btn btn-light rounded-circle shadow-sm btn-copy" title="Sao chép mã" onclick="navigator.clipboard.writeText('{{ $classroom->code }}'); alert('Đã sao chép mã lớp: {{ $classroom->code }}');">
                        <i class="bi bi-copy theme-text-primary"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Thống kê nhanh -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="stat-card shadow-sm d-flex align-items-center gap-4 bg-white rounded-4 p-4 border-0 h-100 hover-lift">
                <div class="icon-wrapper purple shadow-sm flex-shrink-0">
                    <i class="bi bi-journal-check"></i>
                </div>
                <div>
                    <p class="text-muted mb-1 small fw-bold text-uppercase letter-spacing-1">Bài tập đã giao</p>
                    <h3 class="fw-800 mb-0 text-dark">{{ $classroom->exams->count() }} <span class="fs-6 fw-medium text-muted ms-1">bài</span></h3>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card shadow-sm d-flex align-items-center gap-4 bg-white rounded-4 p-4 border-0 h-100 hover-lift">
                <div class="icon-wrapper emerald shadow-sm flex-shrink-0">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <p class="text-muted mb-1 small fw-bold text-uppercase letter-spacing-1">Học viên tham gia</p>
                    <h3 class="fw-800 mb-0 text-dark">{{ $classroom->users->count() }} <span class="fs-6 fw-medium text-muted ms-1">người</span></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <ul class="nav nav-pills custom-nav-pills mb-4 gap-3" id="classTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-4 py-2.5 fw-bold shadow-sm d-flex align-items-center gap-2" id="exams-tab" data-bs-toggle="pill" data-bs-target="#exams" type="button" role="tab">
                <i class="bi bi-journal-text"></i> Bài tập trong lớp
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 py-2.5 fw-bold shadow-sm d-flex align-items-center gap-2" id="students-tab" data-bs-toggle="pill" data-bs-target="#students" type="button" role="tab">
                <i class="bi bi-people"></i> Danh sách Học viên
            </button>
        </li>
    </ul>

    <!-- Tabs Content -->
    <div class="tab-content" id="classTabsContent">
        
        <!-- Tab 1: Bài tập -->
        <div class="tab-pane fade show active" id="exams" role="tabpanel">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 gap-3">
                <h5 class="fw-bold mb-0 text-dark">Danh sách bài tập</h5>
                <a href="{{ route('teacher.classrooms.assignments.create', $classroom->id) }}" class="btn btn-theme-primary rounded-pill px-4 fw-bold text-white shadow-sm">
                    <i class="bi bi-plus-circle-fill"></i> Giao bài tập mới
                </a>
            </div>

            <div class="row g-4">
                @forelse($classroom->exams as $exam)
                    <div class="col-xl-6">
                        <div class="card shadow-sm rounded-4 border-0 p-4 h-100 d-flex flex-column hover-lift transition-all">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <div class="icon-wrapper fuchsia rounded-3 flex-shrink-0" style="width: 50px; height: 50px;">
                                    <i class="bi bi-file-earmark-text-fill fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="fw-bold mb-2 text-dark lh-base">{{ $exam->title }}</h5>
                                    <div class="d-flex flex-wrap gap-2">
                                        <span class="badge bg-purple-light theme-text-primary rounded-pill px-3 py-1 fw-bold border border-purple-subtle">
                                            <i class="bi bi-patch-question-fill me-1"></i> {{ $exam->questions->count() }} câu hỏi
                                        </span>
                                        <span class="badge bg-light text-muted border rounded-pill px-3 py-1 fw-medium">
                                            <i class="bi bi-stopwatch me-1"></i> {{ $exam->duration }} phút
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-auto pt-3 border-top d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                                <div class="text-muted small fw-medium">
                                    <i class="bi bi-calendar-event me-1"></i> Ngày tạo: {{ $exam->created_at->format('d/m/Y') }}
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('exams.show', $exam->id) }}" class="btn btn-outline-theme btn-sm fw-bold rounded-pill px-4 py-2 transition-all">
                                        <i class="bi bi-eye me-1"></i> Xem đề
                                    </a>
                                    <a href="{{ route('exams.teacher_results', $exam->id) }}" class="btn btn-theme-primary btn-sm fw-bold rounded-pill px-4 py-2 shadow-sm transition-all">
                                        <i class="bi bi-bar-chart-fill me-1"></i> Bảng điểm
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5 rounded-4 bg-white shadow-sm border border-soft hover-lift transition-all">
                        <div class="icon-wrapper purple rounded-circle mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2.5rem;">
                            <i class="bi bi-folder-x"></i>
                        </div>
                        <h5 class="text-dark fw-bold">Chưa có bài tập nào được giao</h5>
                        <p class="text-muted mb-4 opacity-75">Hãy click vào nút "Giao bài tập mới" để đánh giá học viên.</p>
                        <a href="{{ route('teacher.exams.create') }}" class="btn btn-purple-gradient rounded-pill fw-bold shadow-sm px-4 py-2 transition-all hover-lift">
                            <i class="bi bi-plus-lg me-1"></i> Tạo bài tập ngay
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Tab 2: Học viên -->
        <div class="tab-pane fade" id="students" role="tabpanel">
            <h5 class="fw-bold mb-4 text-dark">Học viên đã tham gia ({{ $classroom->users->count() }})</h5>

            <div class="row g-4">
                @forelse($classroom->users as $student)
                    <div class="col-md-6 col-lg-4">
                        <div class="card shadow-sm rounded-4 border-0 p-3 h-100 d-flex flex-row align-items-center gap-3 hover-lift transition-all">
                            <div class="avatar-sm bg-purple-gradient text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm fw-bold" style="width: 50px; height: 50px; font-size: 1.2rem;">
                                {{ mb_strtoupper(mb_substr($student->name, 0, 1)) }}
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <h6 class="fw-bold mb-1 text-dark text-truncate">{{ $student->name }}</h6>
                                <p class="text-muted mb-0 small text-truncate">{{ $student->email }}</p>
                            </div>
                            <div class="text-success ms-auto" title="Đang tham gia">
                                <i class="bi bi-check-circle-fill fs-5"></i>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5 rounded-4 bg-white shadow-sm border border-soft hover-lift transition-all">
                        <div class="icon-wrapper emerald rounded-circle mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2.5rem;">
                            <i class="bi bi-people"></i>
                        </div>
                        <h5 class="text-dark fw-bold">Chưa có học viên nào tham gia</h5>
                        <p class="text-muted mb-0 opacity-75">Hãy gửi mã lớp <strong class="theme-text-primary">{{ $classroom->code }}</strong> cho học viên nhé.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    {{-- Các logic JS nếu có --}}
@endpush
