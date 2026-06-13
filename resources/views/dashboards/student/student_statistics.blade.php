@extends('layouts.student.student_app')

@section('title', 'Thống kê học tập')

@push('styles')
    <link rel="stylesheet" href="{{ versioned_asset('css/student/student_statistics.css') }}">
@endpush

@section('content')
    <div class="student-page-heading mb-4">
        <h3 class="fw-bold mb-2 theme-text-dark">Phân tích học tập</h3>
        <p class="text-muted fs-6 mb-0">Theo dõi tiến độ, điểm số và điểm mạnh yếu của bạn</p>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="stat-overview-card bg-white shadow-sm p-4 rounded-4 border-0 h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="icon-box bg-purple-light theme-text-primary rounded-4 d-flex align-items-center justify-content-center shadow-sm" style="width: 52px; height: 52px; font-size: 1.5rem;">
                        <i class="bi bi-check2-square"></i>
                    </div>
                </div>
                <p class="text-muted mb-1 fw-medium" style="font-size: 0.9rem;">Bài thi đã hoàn thành</p>
                <h2 class="fw-bold mb-0 theme-text-dark">{{ $totalCompleted ?? 0 }} <small class="text-muted fs-6 fw-normal">bài</small></h2>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-3">
            <div class="stat-overview-card bg-white shadow-sm p-4 rounded-4 border-0 h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="icon-box bg-indigo-light text-indigo rounded-4 d-flex align-items-center justify-content-center shadow-sm" style="width: 52px; height: 52px; font-size: 1.5rem;">
                        <i class="bi bi-bullseye"></i>
                    </div>
                </div>
                <p class="text-muted mb-1 fw-medium" style="font-size: 0.9rem;">Điểm trung bình</p>
                <h2 class="fw-bold mb-0 theme-text-dark">{{ number_format($averageScore ?? 0, 1) }} <small class="text-muted fs-6 fw-normal">/10</small></h2>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="stat-overview-card bg-white shadow-sm p-4 rounded-4 border-0 h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="icon-box bg-fuchsia-light text-fuchsia rounded-4 d-flex align-items-center justify-content-center shadow-sm" style="width: 52px; height: 52px; font-size: 1.5rem;">
                        <i class="bi bi-stopwatch"></i>
                    </div>
                </div>
                <p class="text-muted mb-1 fw-medium" style="font-size: 0.9rem;">Thời gian luyện tập</p>
                <h2 class="fw-bold mb-0 theme-text-dark">{{ $hours ?? 0 }} <small class="text-muted fs-6 fw-normal">giờ</small> {{ $minutes ?? 0 }} <small class="text-muted fs-6 fw-normal">phút</small></h2>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="stat-overview-card bg-white shadow-sm p-4 rounded-4 border-0 h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="icon-box bg-pink-light text-pink rounded-4 d-flex align-items-center justify-content-center shadow-sm" style="width: 52px; height: 52px; font-size: 1.5rem;">
                        <i class="bi bi-lightning-charge"></i>
                    </div>
                </div>
                <p class="text-muted mb-1 fw-medium" style="font-size: 0.9rem;">Tỷ lệ chính xác</p>
                <h2 class="fw-bold mb-0 theme-text-dark">{{ number_format($accuracyRate ?? 0, 1) }}%</h2>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="chart-card bg-white shadow-sm p-4 rounded-4 border-0 h-100">
                <h6 class="fw-bold mb-4 theme-text-dark"><i class="bi bi-graph-up-arrow me-2 theme-text-primary"></i>Tiến độ điểm số (6 tháng gần nhất)</h6>
                <div class="chart-container" style="position: relative; height:300px; width:100%">
                    <canvas id="scoreProgressChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="chart-card bg-white shadow-sm p-4 rounded-4 border-0 h-100">
                <h6 class="fw-bold mb-4 theme-text-dark"><i class="bi bi-pie-chart me-2 theme-text-primary"></i>Phân bổ môn học</h6>
                <div class="chart-container" style="position: relative; height:250px; width:100%">
                    <canvas id="subjectDistributionChart"></canvas>
                </div>
                <div class="mt-4 text-center text-muted small bg-purple-light py-2 rounded-3 border-purple-subtle">
                    Dựa trên <strong class="theme-text-primary">{{ $totalCompleted ?? 0 }}</strong> bài tập bạn đã hoàn thành.
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-sm p-4 rounded-4 border-0">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h6 class="fw-bold mb-0 theme-text-dark"><i class="bi bi-clock-history me-2 theme-text-primary"></i>Lịch sử làm bài gần đây</h6>
        </div>
        
        <div class="table-responsive pe-2">
            <table class="table align-middle custom-table mb-0">
                <thead>
                    <tr class="bg-purple-light">
                        <th class="border-0 theme-text-dark fw-bold rounded-start-3 ps-3">Tên bài thi</th>
                        <th class="border-0 theme-text-dark fw-bold">Môn học</th>
                        <th class="border-0 theme-text-dark fw-bold">Ngày làm bài</th>
                        <th class="border-0 theme-text-dark fw-bold">Thời gian</th>
                        <th class="border-0 theme-text-dark fw-bold rounded-end-3 text-end pe-3">Điểm số</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentResults ?? [] as $r)
                        @php 
                            $score10 = ($r->score / max(1, $r->total_questions)) * 10; 
                            $colorClass = $score10 >= 8 ? 'text-success' : ($score10 >= 5 ? 'text-warning' : 'text-danger');
                            $bgClass = $score10 >= 8 ? 'bg-success-subtle' : ($score10 >= 5 ? 'bg-warning-subtle' : 'bg-danger-subtle');
                        @endphp
                        <tr class="table-row-hover">
                            <td class="fw-bold theme-text-dark border-bottom-0 ps-3 py-3">
                                {{ $r->exam ? $r->exam->title : 'Bài thi đã xóa' }}
                            </td>
                            <td class="border-bottom-0 py-3">
                                <span class="badge border border-purple-subtle text-purple-dark bg-purple-light px-2 py-1 rounded-pill">
                                    {{ ($r->exam && $r->exam->subject) ? $r->exam->subject : 'Môn chung' }}
                                </span>
                            </td>
                            <td class="text-muted border-bottom-0 py-3">{{ $r->created_at->format('d/m/Y') }}</td>
                            <td class="text-muted border-bottom-0 py-3">
                                <i class="bi bi-hourglass-split small me-1"></i>{{ $r->exam ? $r->exam->duration : 0 }} phút
                            </td>
                            <td class="text-end border-bottom-0 py-3 pe-3">
                                <div class="d-inline-flex align-items-center justify-content-center {{ $bgClass }} {{ $colorClass }} fw-bold rounded-3 px-3 py-1">
                                    {{ number_format($score10, 1) }} <span class="small ms-1 opacity-75">/10</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="empty-state-icon mb-3 mx-auto d-flex align-items-center justify-content-center bg-purple-light rounded-circle" style="width: 60px; height: 60px;">
                                    <i class="bi bi-journal-x fs-2 theme-text-primary"></i>
                                </div>
                                <p class="text-muted mb-0">Bạn chưa làm bài kiểm tra nào.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Khởi tạo dữ liệu từ Controller để truyền vào Chart.js
        window.chartDataFromDB = @json($chartData ?? []);
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script src="{{ versioned_asset('js/student/student_statistics.js') }}"></script>
@endpush
