@extends('layouts.teacher.teacher_app')

@section('title', 'Bảng điểm: ' . $exam->title)

@push('styles')
    <link rel="stylesheet" href="{{ versioned_asset('css/teacher/teacher_results.css') }}">
@endpush

@section('content')

    <!-- Nút quay lại -->
    <div class="mb-4 mt-2">
        <a href="javascript:history.back()" class="btn-back">
            <i class="bi bi-arrow-left"></i> Quay lại lớp học
        </a>
    </div>

    <!-- Header Bảng điểm -->
    <div class="score-header p-4 p-md-5 mb-4 shadow-sm hover-lift">
        <div class="bg-icon"><i class="bi bi-bar-chart-line"></i></div>
        <div class="row align-items-center position-relative z-1">
            <div class="col-md-8">
                <span class="badge bg-white theme-text-primary mb-3 px-3 py-2 rounded-pill fw-bold shadow-sm text-uppercase">
                    <i class="bi bi-diagram-3-fill me-1"></i> {{ $exam->classroom->name ?? 'Bài kiểm tra chung' }}
                </span>
                <h2 class="fw-800 mb-2 display-6 text-white text-shadow-sm">{{ $exam->title }}</h2>
                <p class="text-white opacity-75 fw-medium mb-0"><i class="bi bi-calendar-event me-1"></i> Kì thi ngày: {{ $exam->created_at->format('d/m/Y') }}</p>
            </div>
            <div class="col-md-4 text-md-end mt-4 mt-md-0">
                <button type="button" class="btn bg-white theme-text-primary fw-bold rounded-pill px-4 py-2 shadow-sm transition-all hover-pulse" id="btn-export-excel" data-export-table="#resultsTable" data-export-name="bang-diem-{{ $exam->id }}">
                    <i class="bi bi-file-earmark-excel-fill me-1"></i> Xuất file Excel
                </button>
            </div>
        </div>
    </div>

    <!-- Thống kê nhanh -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card-mini shadow-sm hover-lift">
                <div class="icon-wrapper-md bg-purple-light theme-text-primary rounded-3 flex-shrink-0"><i class="bi bi-people-fill"></i></div>
                <div>
                    <p class="text-muted fw-bold text-uppercase small mb-0 letter-spacing-1">Lượt nộp bài</p>
                    <h3 class="fw-900 mb-0 text-dark">{{ $results->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card-mini shadow-sm hover-lift">
                <div class="icon-wrapper-md bg-emerald-soft text-emerald rounded-3 flex-shrink-0"><i class="bi bi-star-fill"></i></div>
                <div>
                    <p class="text-muted fw-bold text-uppercase small mb-0 letter-spacing-1">Điểm trung bình</p>
                    <h3 class="fw-900 mb-0 text-emerald">{{ number_format($averageScore, 1) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card-mini shadow-sm hover-lift">
                <div class="icon-wrapper-md bg-info-soft text-info rounded-3 flex-shrink-0"><i class="bi bi-list-task"></i></div>
                <div>
                    <p class="text-muted fw-bold text-uppercase small mb-0 letter-spacing-1">Tổng số câu</p>
                    <h3 class="fw-900 mb-0 text-dark">{{ $exam->total_questions ?: $exam->questions()->count() ?: 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách Học viên -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-5">
        <div class="p-4 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <h5 class="fw-bold mb-0 text-dark">Danh sách Học viên</h5>
            <div class="position-relative" style="min-width: 280px;">
                <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 theme-text-primary"></i>
                <input type="text" id="searchStudent" class="form-control bg-light border-0 rounded-pill ps-5 fw-medium" placeholder="Tìm tên hoặc email...">
            </div>
        </div>
        <div class="table-responsive">
            <table class="table custom-table mb-0 align-middle" id="resultsTable">
                <thead class="bg-light">
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
                            $totalQ = max(1, $result->total_questions ?: $exam->total_questions ?: 1);

                            $hasEssay = $exam->questions
                                && $exam->questions->where('type', 'essay')->count() > 0;

                            if ($hasEssay) {
                                // Tự luận: score đã là điểm hệ 10
                                $score10 = floatval($result->score);
                            } else {
                                // Trắc nghiệm: score là số câu đúng
                                $score10 = (floatval($result->score) / $totalQ) * 10;
                            }

                            $score10 = max(0, min(10, $score10));

                            $scoreColor = $score10 >= 8
                                ? 'text-emerald bg-emerald-soft border-emerald-subtle'
                                : ($score10 >= 5
                                    ? 'text-warning-dark bg-warning-soft border-warning-subtle'
                                    : 'text-danger bg-danger-soft border-danger-subtle');
                        @endphp
                        <tr class="student-row transition-all hover-row">
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-sm shadow-sm">
                                        {{ mb_strtoupper(mb_substr($result->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark mb-0 student-name">{{ $result->user->name ?? 'Người dùng Ẩn' }}</div>
                                        <div class="text-muted small student-email">{{ $result->user->email ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-muted fw-medium small d-flex align-items-center gap-1">
                                    <i class="bi bi-clock theme-text-primary opacity-50"></i> {{ $result->created_at->format('H:i - d/m/Y') }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge-score {{ $scoreColor }} border px-3 py-2 fw-800 rounded-3 shadow-sm">
                                    {{ number_format($score10, 1) }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($hasEssay)
                                    <span class="badge bg-info-subtle text-info border border-info border-opacity-25 rounded-pill px-3 py-2">
                                        Tự luận
                                    </span>
                                @else
                                    <span class="fw-bold text-dark">
                                        {{ intval($result->score) }}<span class="text-muted fw-normal">/{{ $totalQ }}</span>
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('teacher.exams.grading', $result->id) }}" target="_blank" class="btn btn-sm btn-purple-soft rounded-pill px-3 py-1.5 fw-bold transition-all text-decoration-none hover-lift">
                                    Chi tiết <i class="bi bi-arrow-right-short fs-5 lh-1 align-middle"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="empty-icon-wrapper bg-light text-muted rounded-circle mx-auto mb-3 shadow-sm">
                                    <i class="bi bi-clipboard-x theme-text-primary"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">Chưa có bài nộp nào</h6>
                                <p class="text-muted small mb-0">Học viên của lớp này chưa hoàn thành bài thi.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection

@push('scripts')
    {{-- Tạm thời giữ lại jQuery nếu file JS bên dưới cần dùng để xử lý chức năng Tìm kiếm (searchStudent) hoặc Xuất Excel --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ versioned_asset('js/teacher/teacher_results.js') }}"></script>
@endpush
