@extends('layouts.teacher.teacher_app')

@section('title', 'Quản lý Học viên')

@push('styles')
    <link rel="stylesheet" href="{{ versioned_asset('css/teacher/teacher_students.css') }}">
@endpush

@section('content')

    <!-- Tiêu đề trang & Nút hành động -->
    <div class="teacher-page-heading d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-3 mt-2 gap-3">
        <div>
            <h3 class="fw-800 text-dark mb-1 d-flex align-items-center gap-2">
                Quản lý Học viên <i class="bi bi-people-fill theme-text-primary"></i>
            </h3>
            <p class="text-muted fw-medium mb-0">Theo dõi tiến độ và điểm số của tất cả sinh viên</p>
        </div>
        
        <a href="{{ route('teacher.students.export') }}" class="btn btn-emerald-gradient fw-bold rounded-pill px-4 py-2.5 shadow-sm d-flex align-items-center gap-2 hover-lift transition-all text-decoration-none">
            <i class="bi bi-file-earmark-excel-fill fs-5"></i> Xuất danh sách Excel
        </a>
    </div>

    <!-- Thống kê nhanh -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card hover-lift d-flex align-items-center gap-4 h-100">
                <div class="icon-wrapper purple shadow-sm flex-shrink-0"><i class="bi bi-people-fill"></i></div>
                <div>
                    <p class="text-muted small fw-bold text-uppercase letter-spacing-1 mb-1">Tổng học viên</p>
                    <h2 class="fw-800 text-dark mb-0">{{ $totalStudents ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card hover-lift d-flex align-items-center gap-4 h-100">
                <div class="icon-wrapper emerald shadow-sm flex-shrink-0"><i class="bi bi-person-plus-fill"></i></div>
                <div>
                    <p class="text-muted small fw-bold text-uppercase letter-spacing-1 mb-1">Mới (7 ngày qua)</p>
                    <h2 class="fw-800 text-dark mb-0">{{ $newStudentsThisWeek ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card hover-lift d-flex align-items-center gap-4 h-100">
                <div class="icon-wrapper fuchsia shadow-sm flex-shrink-0"><i class="bi bi-activity"></i></div>
                <div>
                    <p class="text-muted small fw-bold text-uppercase letter-spacing-1 mb-1">Trạng thái chung</p>
                    <h5 class="fw-bold text-emerald mb-0 mt-1 d-flex align-items-center gap-1"><i class="bi bi-check-circle-fill"></i> Ổn định</h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Bảng danh sách Học viên -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
        <div class="table-responsive">
            <table class="table custom-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th width="5%" class="text-center">#</th>
                        <th width="25%">Thông tin Học viên</th>
                        <th width="25%">Đang học lớp</th>
                        <th width="20%" class="text-center">Tiến độ bài thi</th>
                        <th width="15%" class="text-center">Điểm TB</th>
                        <th width="10%" class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $index => $student)
                        @php
                            $completedCount = $student->completed_count ?? ($student->results->count() ?? 0);
                            $avg = $student->average_score10 ?? 0;
                            $avgClass = $avg >= 8 ? 'text-emerald bg-emerald-soft border-emerald-subtle' : ($avg >= 5 ? 'text-warning bg-warning-light border-warning-subtle' : 'text-danger bg-danger-soft border-danger-subtle');
                        @endphp
                        <tr class="transition-all hover-row">
                            <td class="text-center text-muted fw-bold">{{ $students->firstItem() + $index }}</td>
                            
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="student-avatar bg-purple-gradient text-white shadow-sm">
                                        {{ mb_strtoupper(mb_substr($student->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1">{{ $student->name }}</h6>
                                        <p class="text-muted small mb-0">{{ $student->email }}</p>
                                    </div>
                                </div>
                            </td>
                            
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    @forelse($student->classrooms as $class)
                                        <span class="class-tag shadow-sm" title="{{ $class->name }}">
                                            <i class="bi bi-diagram-3-fill me-1 opacity-75"></i>{{ Str::limit($class->name, 18) }}
                                        </span>
                                    @empty
                                        <span class="text-muted small fw-medium fst-italic">Chưa xếp lớp</span>
                                    @endforelse
                                </div>
                            </td>
                            
                            <td>
                                <div class="d-flex flex-column gap-1 px-3">
                                    <div class="d-flex justify-content-between align-items-center small">
                                        <span class="fw-bold text-dark">{{ $completedCount }} bài</span>
                                        <span class="text-muted fw-medium">{{ min(100, $completedCount * 10) }}%</span>
                                    </div>
                                    <div class="progress bg-gray-soft rounded-pill" style="height: 6px;">
                                        <div class="progress-bar bg-purple-gradient rounded-pill" style="width: {{ min(100, $completedCount * 10) }}%"></div>
                                    </div>
                                </div>
                            </td>

                            <td class="text-center">
                                @if($completedCount > 0)
                                    <span class="badge {{ $avgClass }} rounded-3 px-3 py-1.5 fs-6 fw-800 border">
                                        {{ number_format($avg, 1) }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border border-light-subtle rounded-pill px-3 py-1 fw-medium">Chưa có</span>
                                @endif
                            </td>
                            
                            <td class="text-end pe-4">
                                <a href="{{ route('teacher.students.show', $student->id) }}" class="btn btn-sm btn-purple-soft rounded-pill px-3 py-1.5 fw-bold transition-all text-decoration-none hover-lift">
                                    Chi tiết <i class="bi bi-arrow-right-short"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-icon-wrapper mx-auto mb-3 shadow-sm">
                                    <i class="bi bi-person-x theme-text-primary"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-2">Chưa có học viên nào</h5>
                                <p class="text-muted mb-0 fw-medium">Khi học viên dùng mã tham gia vào các lớp của bạn, họ sẽ xuất hiện tại đây.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($students->hasPages())
        <div class="d-flex justify-content-center custom-pagination mt-4">
            {{ $students->links('pagination::bootstrap-5') }}
        </div>
    @endif

@endsection

@push('scripts')
    {{-- Đặt thêm script tìm kiếm / filter ở đây nếu có --}}
@endpush
