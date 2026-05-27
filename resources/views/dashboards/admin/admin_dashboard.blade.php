@extends('layouts.admin.admin_app')

@section('title', 'Thống kê tổng quan')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/admin_dashboard2.css') }}?v={{ time() }}">
@endpush

@section('content')

    <!-- Tiêu đề trang -->
    <div class="d-flex align-items-center gap-2 mb-4 mt-2 border-bottom border-light-subtle pb-3">
        <h3 class="fw-800 text-dark mb-0 d-flex align-items-center gap-2">
            Thống kê tổng quan <i class="bi bi-bar-chart-line-fill theme-text-primary"></i>
        </h3>
    </div>

    <!-- Số liệu nền tảng -->
    <h5 class="fw-bold text-dark mb-3">Số liệu nền tảng</h5>
    <div class="row g-4 mb-5">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 hover-lift h-100 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-icon-box bg-purple-light theme-text-primary rounded-3 shadow-sm border border-white">
                        <i class="bi bi-person-workspace fs-4"></i>
                    </div>
                </div>
                <h2 class="fw-800 text-dark mb-1">{{ number_format($totalTeachers ?? 0) }}</h2>
                <p class="text-muted fw-bold text-uppercase letter-spacing-1 small mb-0">Giảng viên</p>
            </div>
        </div>
        
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 hover-lift h-100 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-icon-box bg-emerald-soft text-emerald rounded-3 shadow-sm border border-white">
                        <i class="bi bi-people-fill fs-4"></i>
                    </div>
                </div>
                <h2 class="fw-800 text-dark mb-1">{{ number_format($totalStudents ?? 0) }}</h2>
                <p class="text-muted fw-bold text-uppercase letter-spacing-1 small mb-0">Học viên</p>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 hover-lift h-100 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-icon-box bg-warning-soft text-warning-dark rounded-3 shadow-sm border border-white">
                        <i class="bi bi-diagram-3-fill fs-4"></i>
                    </div>
                </div>
                <h2 class="fw-800 text-dark mb-1">{{ number_format($totalClassrooms ?? 0) }}</h2>
                <p class="text-muted fw-bold text-uppercase letter-spacing-1 small mb-0">Lớp học</p>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 hover-lift h-100 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-icon-box bg-info-soft text-info rounded-3 shadow-sm border border-white">
                        <i class="bi bi-journal-text fs-4"></i>
                    </div>
                </div>
                <h2 class="fw-800 text-dark mb-1">{{ number_format($totalExams ?? 0) }}</h2>
                <p class="text-muted fw-bold text-uppercase letter-spacing-1 small mb-0">Đề thi / {{ number_format($totalDocuments ?? 0) }} Tài liệu</p>
            </div>
        </div>
    </div>

    <!-- Sử dụng AI & API -->
    <h5 class="fw-bold text-dark mb-3">Sử dụng AI & API</h5>
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 d-flex flex-row align-items-center gap-4 hover-lift bg-white">
                <div class="stat-icon-box bg-purple-light theme-text-primary rounded-circle shadow-sm border border-white flex-shrink-0" style="width: 60px; height: 60px;">
                    <i class="bi bi-currency-dollar fs-3"></i>
                </div>
                <div>
                    <p class="text-muted fw-bold text-uppercase letter-spacing-1 small mb-1">Chi phí ước tính</p>
                    <h3 class="fw-800 text-dark mb-0">${{ number_format($totalAiCost ?? 0, 4) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 d-flex flex-row align-items-center gap-4 hover-lift bg-white">
                <div class="stat-icon-box bg-warning-soft text-warning-dark rounded-circle shadow-sm border border-white flex-shrink-0" style="width: 60px; height: 60px;">
                    <i class="bi bi-lightning-charge-fill fs-3"></i>
                </div>
                <div>
                    <p class="text-muted fw-bold text-uppercase letter-spacing-1 small mb-1">Tokens tiêu thụ</p>
                    <h3 class="fw-800 text-dark mb-0">{{ number_format($totalAiTokens ?? 0) }} <span class="fs-6 text-muted fw-medium">tokens</span></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Hoạt động AI mới nhất -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold text-dark mb-0">Hoạt động AI mới nhất</h6>
            <a href="{{ route('admin.ai_history') ?? '#' }}" class="btn btn-sm bg-purple-light theme-text-primary border-purple-subtle fw-bold rounded-pill px-3 transition-all hover-pulse">Xem tất cả</a>
        </div>
        <div class="card-body p-0 mt-3">
            <div class="table-responsive">
                <table class="table custom-table align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th width="25%" class="ps-4">Người dùng</th>
                            <th width="35%">Hành động</th>
                            <th width="20%" class="text-center">Tokens</th>
                            <th width="20%" class="text-end pe-4">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentAiLogs ?? [] as $log)
                            <tr class="hover-row transition-all">
                                <td class="ps-4">
                                    <span class="fw-bold text-dark"><i class="bi bi-person-fill text-muted me-1"></i> {{ $log->user->name ?? 'User' }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark mb-1">{{ $log->action }}</div>
                                    <small class="text-muted"><i class="bi bi-cpu"></i> {{ $log->model }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-gray-soft text-dark border px-2 py-1">{{ number_format($log->tokens) }}</span>
                                </td>
                                <td class="text-end pe-4">
                                    @if($log->status == 'success')
                                        <span class="badge bg-emerald-soft text-emerald border border-emerald-subtle px-2 py-1 rounded-pill"><i class="bi bi-check-circle me-1"></i>Thành công</span>
                                    @else
                                        <span class="badge bg-danger-soft text-danger border border-danger-subtle px-2 py-1 rounded-pill"><i class="bi bi-x-circle me-1"></i>Lỗi</span>
                                    @endif
                                    <div class="text-muted small mt-1">{{ $log->created_at->diffForHumans() }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Chưa có hoạt động AI nào gần đây.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection