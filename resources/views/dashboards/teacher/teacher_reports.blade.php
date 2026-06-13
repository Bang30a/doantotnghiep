@extends('layouts.teacher.teacher_app')

@section('title', 'Sổ điểm & Báo cáo')

@push('styles')
    <link rel="stylesheet" href="{{ versioned_asset('css/teacher/teacher_reports.css') }}">
@endpush

@section('content')

    <!-- Tiêu đề trang & Nút hành động -->
    <div class="teacher-page-heading d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4 pb-3 mt-2">
        <div class="mb-3 mb-md-0">
            <h3 class="fw-800 text-dark mb-1 d-flex align-items-center gap-2">
                Sổ điểm & Báo cáo <i class="bi bi-journal-bookmark-fill theme-text-primary"></i>
            </h3>
            <p class="text-muted mb-0 fw-medium">Theo dõi điểm số và tiến độ làm bài của tất cả các lớp</p>
        </div>
        
        <div>
            <a href="{{ route('teacher.reports.export', request()->query()) }}" class="btn btn-emerald-gradient fw-bold rounded-pill px-4 py-2.5 shadow-sm d-flex align-items-center gap-2 hover-lift transition-all text-decoration-none">
                <i class="bi bi-file-earmark-excel-fill fs-5"></i> Xuất Báo Cáo Excel
            </a>
        </div>
    </div>

    <!-- Bộ lọc -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 filter-card bg-white">
        <div class="card-body p-3 px-4">
            <div class="row g-3 align-items-center">
                <div class="col-md-6 col-lg-5">
                    <div class="dropdown custom-dropdown-select w-100">
                        <button class="btn w-100 d-flex justify-content-between align-items-center rounded-3 bg-light border border-light-subtle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="text-truncate">
                                <i class="bi bi-funnel-fill theme-text-primary me-2"></i>
                                @php
                                    $selectedClass = $classrooms->firstWhere('id', request('classroom_id'));
                                @endphp
                                <span class="fw-bold {{ $selectedClass ? 'theme-text-primary' : 'text-muted' }}">
                                    {{ $selectedClass ? 'Lớp: ' . $selectedClass->name : '-- Hiển thị tất cả các lớp học --' }}
                                </span>
                            </span>
                            <i class="bi bi-chevron-down text-muted small"></i>
                        </button>
                        
                        <ul class="dropdown-menu w-100 shadow-lg border-0 rounded-4 mt-2 p-2">
                            <li>
                                <a class="dropdown-item rounded-3 py-2.5 mb-1 {{ !request('classroom_id') ? 'active bg-purple-light theme-text-primary fw-bold' : '' }}" href="{{ route('teacher.reports.index') }}">
                                    <i class="bi bi-grid-fill me-2 opacity-50"></i> -- Hiển thị tất cả --
                                </a>
                            </li>
                            <li><hr class="dropdown-divider opacity-25"></li>
                            @foreach($classrooms as $class)
                                <li>
                                    <a class="dropdown-item rounded-3 py-2.5 mb-1 {{ request('classroom_id') == $class->id ? 'active bg-purple-light theme-text-primary fw-bold' : '' }}" href="{{ route('teacher.reports.index', ['classroom_id' => $class->id]) }}">
                                        <i class="bi bi-diagram-3-fill me-2 opacity-50"></i> Lớp: {{ $class->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-7 text-md-end">
                    @if(request('classroom_id'))
                        <a href="{{ route('teacher.reports.index') }}" class="btn btn-danger-soft fw-bold rounded-pill px-4 py-2 small shadow-sm d-inline-flex align-items-center gap-2 transition-all hover-pulse">
                            <i class="bi bi-x-circle-fill"></i> Bỏ lọc
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Bảng danh sách Báo cáo -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
        <div class="table-responsive">
            <table class="table align-middle mb-0 custom-table">
                <thead class="bg-light">
                    <tr>
                        <th width="35%" class="ps-4">Bài kiểm tra</th>
                        <th width="20%">Phân loại Lớp</th>
                        <th width="15%" class="text-center">Số bài đã nộp</th>
                        <th width="15%" class="text-center">Điểm TB (Hệ 10)</th>
                        <th width="15%" class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $exam)
                        <tr class="transition-all hover-row">
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="icon-box-small bg-purple-light theme-text-primary rounded-3 d-flex align-items-center justify-content-center flex-shrink-0">
                                        <i class="bi bi-file-text-fill fs-5"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1">{{ $exam->title }}</h6>
                                        <small class="text-muted fw-medium"><i class="bi bi-calendar-event me-1"></i> {{ $exam->created_at->format('d/m/Y') }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($exam->classroom)
                                    <span class="badge bg-purple-light theme-text-primary border border-purple-subtle px-3 py-2 rounded-pill fw-bold shadow-sm">
                                        <i class="bi bi-diagram-3-fill me-1"></i> {{ $exam->classroom->name }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-secondary border border-secondary border-opacity-25 px-3 py-2 rounded-pill fw-bold shadow-sm">
                                        <i class="bi bi-collection me-1"></i> Ngân hàng đề
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-pill px-4 py-1.5 border border-light-subtle shadow-sm">
                                    <span class="fw-900 text-dark fs-6">{{ $exam->results_count }}</span> <span class="text-muted small ms-1 fw-medium">bài</span>
                                </div>
                            </td>
                            <td class="text-center">
                                @php
                                    $avgClass = $exam->avg_score >= 8 ? 'text-emerald' : ($exam->avg_score >= 5 ? 'text-warning-dark' : 'text-danger');
                                    $bgClass = $exam->avg_score >= 8 ? 'bg-emerald-soft border-emerald-subtle' : ($exam->avg_score >= 5 ? 'bg-warning-soft border-warning-subtle' : 'bg-danger-soft border-danger-subtle');
                                @endphp
                                <div class="d-inline-flex align-items-center justify-content-center {{ $bgClass }} {{ $avgClass }} border rounded-4 px-3 py-1.5 fw-900 fs-5 shadow-sm" style="min-width: 60px;">
                                    {{ number_format($exam->avg_score, 1) }}
                                </div>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('exams.show', $exam->id) }}#tab-results" class="btn btn-outline-theme fw-bold rounded-pill px-3 py-1.5 transition-all hover-lift">
                                    Chi tiết <i class="bi bi-arrow-right-short fs-5 lh-1 align-middle"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="empty-icon-wrapper mx-auto mb-3 shadow-sm">
                                    <i class="bi bi-inboxes theme-text-primary"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-1">Chưa có dữ liệu báo cáo</h5>
                                <p class="text-muted mb-0">Học viên chưa hoàn thành bài thi nào trong mục này.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Phân trang -->
    <div class="d-flex justify-content-center custom-pagination mt-4">
        {{ $exams->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>

@endsection
