@extends('layouts.admin.admin_app')

@section('title', 'Bảng điều khiển Quản trị')

@section('content')

    <!-- Header chào mừng -->
    <div class="admin-page-heading admin-dashboard-hero mb-4 mt-3 d-flex justify-content-between align-items-end flex-wrap gap-3">
        <div>
            @php $firstName = explode(' ', Auth::user()->name ?? 'Admin')[0]; @endphp
            <h2 class="fw-800 text-dark mb-1">
                Xin chào, {{ $firstName }}! <span class="wave-emoji"></span>
            </h2>
            <p class="text-muted fs-6 fw-medium mb-0">Quản trị toàn bộ hệ thống và dữ liệu của EduQuiz AI</p>
            <div class="admin-hero-chips d-flex flex-wrap gap-2 mt-3">
                <span><i class="bi bi-shield-check"></i> Hệ thống ổn định</span>
                <span><i class="bi bi-activity"></i> {{ $todayActivity ?? 0 }} hoạt động hôm nay</span>
                <span><i class="bi bi-people"></i> {{ $totalUsers ?? 0 }} tài khoản</span>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.backup.database') }}" class="btn admin-hero-button bg-white border shadow-sm rounded-pill fw-bold text-dark px-4 py-2 hover-lift transition-all">
                <i class="bi bi-cloud-download me-1"></i> Sao lưu Dữ liệu
            </a>
        </div>
    </div>

    <!-- Thẻ KPI Thống kê -->
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="admin-kpi-card kpi-users bg-white rounded-4 p-4 shadow-sm border-0 h-100 hover-lift">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <p class="text-muted mb-0 fw-bold text-uppercase letter-spacing-1" style="font-size: 0.75rem;">Tổng người dùng</p>
                    <div class="admin-kpi-icon bg-purple-light theme-text-primary"><i class="bi bi-people-fill"></i></div>
                </div>
                <h2 class="fw-800 mb-0 text-dark display-6">{{ $totalUsers ?? 0 }}</h2>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="admin-kpi-card kpi-students bg-white rounded-4 p-4 shadow-sm border-0 h-100 hover-lift">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <p class="text-muted mb-0 fw-bold text-uppercase letter-spacing-1" style="font-size: 0.75rem;">Học viên</p>
                    <div class="admin-kpi-icon bg-emerald-soft text-emerald"><i class="bi bi-person-badge-fill"></i></div>
                </div>
                <h2 class="fw-800 mb-0 text-dark display-6">{{ $studentsCount ?? 0 }}</h2>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="admin-kpi-card kpi-teachers bg-white rounded-4 p-4 shadow-sm border-0 h-100 hover-lift">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <p class="text-muted mb-0 fw-bold text-uppercase letter-spacing-1" style="font-size: 0.75rem;">Giảng viên</p>
                    <div class="admin-kpi-icon bg-info-soft text-info"><i class="bi bi-person-workspace"></i></div>
                </div>
                <h2 class="fw-800 mb-0 text-dark display-6">{{ $teachersCount ?? 0 }}</h2>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="admin-kpi-card kpi-ai bg-white rounded-4 p-4 shadow-sm border-0 h-100 hover-lift">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <p class="text-muted mb-0 fw-bold text-uppercase letter-spacing-1" style="font-size: 0.75rem;">Lượt dùng AI (Nay)</p>
                    <div class="admin-kpi-icon bg-warning-soft text-warning-dark"><i class="bi bi-lightning-charge-fill"></i></div>
                </div>
                <h2 class="fw-800 mb-0 text-dark display-6">{{ $todayActivity ?? 0 }}</h2>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Khối Hành động nhanh -->
        <div class="col-xl-5">
            <div class="section-card bg-white rounded-4 p-4 p-md-5 shadow-sm border-0 h-100">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <div class="admin-icon-sm bg-purple-light theme-text-primary rounded-3"><i class="bi bi-lightning-fill"></i></div>
                    <h5 class="fw-bold mb-0 text-dark">Hành động nhanh</h5>
                </div>
                <p class="text-muted fs-6 mb-4 fw-medium">Công cụ quản trị thường dùng</p>

                <div class="d-flex flex-column gap-3 mt-4">
                    <a href="{{ route('admin.students') }}" class="admin-qa-item dark shadow-sm">
                        <div class="qa-icon"><i class="bi bi-people"></i></div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 fw-bold">Quản lý Người dùng</h6>
                            <small class="opacity-75 fw-medium">Thêm, sửa, xóa hoặc khóa tài khoản</small>
                        </div>
                        <i class="bi bi-arrow-right fs-5"></i>
                    </a>
                    
                    <a href="{{ route('admin.exams') }}" class="admin-qa-item light">
                        <div class="qa-icon bg-emerald-soft text-emerald"><i class="bi bi-journal-check"></i></div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 fw-bold text-dark">Duyệt đề thi mới</h6>
                            <small class="text-muted fw-medium">Kiểm tra các đề thi vừa được tạo</small>
                        </div>
                        <i class="bi bi-arrow-right fs-5 text-muted"></i>
                    </a>

                    <a href="{{ route('admin.settings') ?? '#' }}" class="admin-qa-item light">
                        <div class="qa-icon bg-info-soft text-info"><i class="bi bi-gear-fill"></i></div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 fw-bold text-dark">Cài đặt Hệ thống</h6>
                            <small class="text-muted fw-medium">Tên miền, Logo, Cấu hình API</small>
                        </div>
                        <i class="bi bi-arrow-right fs-5 text-muted"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Khối Timeline Hoạt động -->
        <div class="col-xl-7">
            <div class="section-card bg-white rounded-4 p-4 p-md-5 shadow-sm border-0 h-100">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="d-flex align-items-center gap-2">
                        <div class="admin-icon-sm bg-gray-soft text-secondary rounded-3"><i class="bi bi-clock-history"></i></div>
                        <h5 class="fw-bold mb-0 text-dark">Hoạt động gần đây</h5>
                    </div>
                    <a href="{{ route('admin.activities') }}" class="btn btn-sm btn-light fw-bold rounded-pill px-3 transition-all hover-theme">Xem tất cả</a>
                </div>
                <p class="text-muted fs-6 mb-4 fw-medium">Các log hệ thống mới nhất</p>

                <div class="admin-timeline mt-4">
                    @if(isset($recentActivities) && count($recentActivities) > 0)
                        @foreach($recentActivities as $activity)
                            <div class="timeline-item d-flex gap-3 mb-4">
                                <div class="timeline-icon bg-{{ $activity->color_theme ?? 'primary' }}-soft text-{{ $activity->color_theme ?? 'primary' }} position-relative">
                                    @if($activity->type == 'system_warning')
                                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                                    @endif
                                    <i class="bi {{ $activity->icon_class ?? 'bi-info-circle-fill' }}"></i>
                                </div>
                                <div class="timeline-content flex-grow-1 pb-4 {{ !$loop->last ? 'border-bottom' : '' }}">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <h6 class="fw-bold {{ $activity->type == 'system_warning' ? 'text-danger' : 'text-dark' }} mb-0">{{ $activity->title }}</h6>
                                        <span class="badge bg-light text-muted fw-bold"><i class="bi bi-clock me-1"></i>{{ $activity->time_ago }}</span>
                                    </div>
                                    <p class="text-muted small mb-0 fw-medium">{!! $activity->description !!}</p>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <div class="bg-light text-muted rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-size: 1.5rem;">
                                <i class="bi bi-journal-x"></i>
                            </div>
                            <p class="text-muted fw-medium small mb-0">Chưa có hoạt động nào được ghi nhận.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    {{-- Các file JS phục vụ chart/đồ thị nếu có --}}
@endpush
