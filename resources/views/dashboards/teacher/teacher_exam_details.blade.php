@extends('layouts.teacher.teacher_app')

@section('title', 'Chi tiết đề thi: ' . $exam->title)

@push('styles')
    <link rel="stylesheet" href="{{ versioned_asset('css/teacher/teacher_exam_details.css') }}">
@endpush

@section('content')

    <!-- Nút quay lại -->
    <div class="mb-4 mt-2">
        <a href="{{ route('teacher.exams.index') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Quay lại Danh sách đề
        </a>
    </div>

    <!-- Banner Thông tin đề thi -->
    <div class="exam-header mb-4 shadow-sm position-relative overflow-hidden">
        <div class="banner-bg-shape"><i class="bi bi-journal-text"></i></div>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center position-relative z-1 gap-4">
            <div>
                <span class="badge bg-white theme-text-primary mb-3 px-3 py-2 rounded-pill fw-bold shadow-sm">
                    <i class="bi bi-stopwatch-fill me-1"></i> {{ $exam->duration }} phút
                </span>
                <h2 class="fw-800 mb-2 display-6 text-white">{{ $exam->title }}</h2>
                <div class="d-flex flex-wrap align-items-center gap-3 text-white opacity-75 fw-medium">
                    <span class="d-flex align-items-center gap-1"><i class="bi bi-diagram-3"></i> Lớp: <strong class="text-white ms-1">{{ $exam->classroom->name ?? 'Ngân hàng đề' }}</strong></span>
                    <span class="d-none d-md-inline">•</span>
                    <span class="d-flex align-items-center gap-1"><i class="bi bi-calendar-event"></i> Ngày tạo: {{ $exam->created_at->format('d/m/Y') }}</span>
                </div>
            </div>
            <div class="d-flex gap-2 w-100 w-md-auto">
                <button type="button" class="btn btn-outline-white fw-bold rounded-pill px-4 py-2 flex-grow-1 flex-md-grow-0 shadow-sm" id="btn-export-excel" data-export-table="#tableResults" data-export-name="bang-diem-{{ $exam->id }}">
                    <i class="bi bi-download me-1"></i> Xuất điểm
                </button>
                <a href="{{ route('teacher.exams.edit', $exam->id) }}" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm d-inline-flex align-items-center">
                    <i class="bi bi-pencil-square me-2 theme-text-primary"></i> Sửa đề
                </a>
            </div>
        </div>
    </div>

    <!-- Thống kê nhanh -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card-mini hover-lift">
                <div class="icon-box purple shadow-sm"><i class="bi bi-inbox-fill"></i></div>
                <div><p class="label">Lượt nộp bài</p><h4 class="value">{{ $results->count() ?? 0 }}</h4></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card-mini hover-lift">
                <div class="icon-box emerald shadow-sm"><i class="bi bi-star-fill"></i></div>
                <div><p class="label">Điểm trung bình</p><h4 class="value text-emerald">{{ number_format($averageScore ?? 0, 1) }}</h4></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card-mini hover-lift">
                <div class="icon-box fuchsia shadow-sm"><i class="bi bi-ui-checks-grid"></i></div>
                <div><p class="label">Tổng số câu</p><h4 class="value">{{ $exam->questions->count() ?? 0 }}</h4></div>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <ul class="nav nav-pills custom-nav-pills mb-4 gap-3" id="examTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-4 py-2.5 fw-bold shadow-sm d-flex align-items-center gap-2" data-bs-toggle="pill" data-bs-target="#tab-questions" type="button" role="tab">
                <i class="bi bi-card-list"></i> Nội dung đề
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 py-2.5 fw-bold shadow-sm d-flex align-items-center gap-2" data-bs-toggle="pill" data-bs-target="#tab-results" type="button" role="tab">
                <i class="bi bi-bar-chart-fill"></i> Bảng điểm & Chấm thi
            </button>
        </li>
    </ul>

    <!-- Tabs Content -->
    <div class="tab-content">
        
        <!-- Tab 1: Nội dung đề thi -->
        <div class="tab-pane fade show active" id="tab-questions" role="tabpanel">
            @foreach($exam->questions as $index => $q)
                <div class="question-detail-card mb-4 shadow-sm hover-lift border-0 bg-white p-4 rounded-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="q-number shadow-sm bg-purple-custom text-white px-3 py-1.5 rounded-pill fw-bold small">Câu {{ $index + 1 }}</span>
                        @if($q->type == 'essay') 
                            <span class="badge bg-info-soft text-info border border-info border-opacity-25 px-3 py-1.5 rounded-pill fw-bold">Tự luận</span> 
                        @endif
                    </div>
                    <h6 class="q-text mb-4 text-dark fs-5 lh-base">{{ $q->content }}</h6>
                    
                    @if($q->type == 'multiple_choice')
                        <div class="row g-3">
                            @foreach($q->answers as $aIndex => $ans)
                                <div class="col-md-6">
                                    <div class="answer-item p-3 rounded-3 border d-flex align-items-center gap-3 {{ $ans->is_correct ? 'is-correct border-success bg-success bg-opacity-10 shadow-sm' : 'border-light-subtle bg-light' }}">
                                        <span class="ans-label fw-bold {{ $ans->is_correct ? 'text-success' : 'text-muted' }}">{{ chr(65 + $aIndex) }}</span>
                                        <span class="ans-content flex-grow-1 fw-medium {{ $ans->is_correct ? 'text-success' : 'text-dark' }}">{{ $ans->content }}</span>
                                        @if($ans->is_correct) <i class="bi bi-check-circle-fill text-success fs-5"></i> @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="essay-hint-box p-4 bg-light rounded-4 mt-2 border border-light-subtle">
                            <p class="fw-bold mb-3 text-success small text-uppercase d-flex align-items-center gap-2 border-bottom border-success border-opacity-25 pb-2">
                                <i class="bi bi-key-fill fs-5"></i> Bareme Chấm Điểm / Đáp án gợi ý
                            </p>
                            <div class="text-dark" style="font-size: 0.95rem; line-height: 1.8;">
                                @php
                                    $content = $q->answers->first()->content ?? 'Chưa có dữ liệu';
                                    $content = preg_replace('/(\s|^)\+\s/', '<br><span class="theme-text-primary fw-bold ms-3 me-2">•</span>', $content);
                                    $content = preg_replace('/(\s|^)-\s/', '<br><strong class="text-success me-2 mt-2 d-inline-block">♦</strong>', $content);
                                    $content = preg_replace('/^(<br>)+/', '', $content);
                                @endphp
                                {!! $content !!}
                            </div>
                        </div>
                    @endif

                    @if($q->explanation)
                        <div class="ai-explanation-box mt-4 p-4 bg-warning-soft rounded-4 position-relative overflow-hidden border border-warning border-opacity-25">
                            <div class="position-absolute top-0 end-0 opacity-10 p-2 text-warning"><i class="bi bi-robot fs-1"></i></div>
                            <p class="fw-bold mb-2 text-warning-dark small text-uppercase d-flex align-items-center gap-2"><i class="bi bi-lightbulb-fill fs-5"></i> Giải thích từ AI</p>
                            <div class="text-dark opacity-75 fw-medium" style="font-size: 0.95rem; line-height: 1.6;">{{ $q->explanation }}</div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Tab 2: Bảng điểm & Chấm thi -->
        <div class="tab-pane fade" id="tab-results" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                <div class="p-4 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <h6 class="fw-bold mb-0 text-dark fs-5">Danh sách bài nộp</h6>
                    <div class="search-box position-relative" style="min-width: 250px;">
                        <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 theme-text-primary"></i>
                        <input type="text" id="searchStudent" class="form-control bg-light border-0 rounded-pill ps-5 fw-medium" placeholder="Tìm tên học viên...">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table custom-table mb-0 align-middle" id="tableResults">
                        <thead>
                            <tr>
                                <th width="35%" class="ps-4">Học viên</th>
                                <th width="20%">Thời gian nộp</th>
                                <th width="15%" class="text-center">Điểm (Hệ 10)</th>
                                <th width="15%" class="text-center">Số câu đúng</th>
                                <th width="15%" class="text-end pe-4">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($results as $result)
                                @php
                                    $score10 = ($result->score / max(1, $result->total_questions)) * 10;
                                    $scoreClass = $score10 >= 8 ? 'text-emerald bg-emerald-soft border-emerald-subtle' : ($score10 >= 5 ? 'text-warning text-warning-dark bg-warning-soft border-warning-subtle' : 'text-danger bg-danger-soft border-danger-subtle');
                                @endphp
                                <tr class="student-row hover-row transition-all">
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-sm shadow-sm">
                                                {{ mb_strtoupper(mb_substr($result->user->name ?? 'U', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold student-name text-dark mb-1">{{ $result->user->name ?? 'Người dùng Ẩn' }}</div>
                                                <div class="text-muted small">{{ $result->user->email ?? 'Không có email' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted fw-medium small d-flex align-items-center gap-1">
                                            <i class="bi bi-clock theme-text-primary opacity-50"></i> {{ $result->created_at->format('H:i - d/m/Y') }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $scoreClass }} rounded-3 px-3 py-1.5 fs-6 fw-800 border">
                                            {{ number_format($score10, 1) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold text-dark">{{ $result->score }}<span class="text-muted fw-normal">/{{ $result->total_questions }}</span></span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('teacher.exams.grading', $result->id) }}" class="btn btn-sm btn-purple-soft rounded-pill px-3 py-1.5 fw-bold transition-all text-decoration-none">
                                            Chi tiết <i class="bi bi-arrow-right-short"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="empty-icon-wrapper mx-auto mb-3 shadow-sm">
                                            <i class="bi bi-clipboard-x theme-text-primary"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">Chưa có bài nộp nào</h6>
                                        <p class="text-muted small mb-0">Hiện tại chưa có học viên nào hoàn thành bài thi này.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    {{-- Giữ lại file JS gốc để xử lý logic Tìm kiếm Học viên và Xuất Excel --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ versioned_asset('js/teacher/teacher_exam_details.js') }}"></script>
@endpush
