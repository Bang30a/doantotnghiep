@extends('layouts.admin.admin_app')

@section('title', 'Nhật ký Hoạt động')

@section('content')

    <!-- Tiêu đề trang & Nút quay lại -->
    <div class="admin-page-heading d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4 gap-3 mt-2 pb-3">
        <div>
            <h3 class="fw-800 text-dark mb-1 d-flex align-items-center gap-2">
                Toàn bộ Hoạt động hệ thống <i class="bi bi-clock-history theme-text-primary"></i>
            </h3>
            <p class="text-muted fw-medium mb-0">Theo dõi chi tiết các thao tác, thông báo và cảnh báo trên toàn hệ thống.</p>
        </div>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-light fw-bold px-4 py-2.5 rounded-pill shadow-sm border d-flex align-items-center gap-2 transition-all hover-lift">
                <i class="bi bi-arrow-left"></i> Quay lại Dashboard
            </a>
        </div>
    </div>

    <!-- Timeline Hoạt động -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white hover-lift transition-all">
        <div class="card-body p-4 p-md-5">
            <div class="admin-timeline">
                @if(isset($activities) && count($activities) > 0)
                    @foreach($activities as $activity)
                        <div class="timeline-item d-flex gap-4 mb-4 pb-4 {{ !$loop->last ? 'border-bottom border-light-subtle' : '' }}">
                            <div class="timeline-icon bg-{{ $activity->color_theme ?? 'primary' }}-soft text-{{ $activity->color_theme ?? 'primary' }} position-relative d-flex align-items-center justify-content-center rounded-circle fs-4 flex-shrink-0 shadow-sm" style="width: 50px; height: 50px;">
                                @if($activity->type == 'system_warning')
                                    <span class="position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle"></span>
                                @endif
                                <i class="bi {{ $activity->icon_class ?? 'bi-info-circle-fill' }}"></i>
                            </div>
                            <div class="timeline-content flex-grow-1">
                                <div class="d-flex flex-wrap justify-content-between align-items-center mb-2 gap-2">
                                    <h5 class="fw-bold {{ $activity->type == 'system_warning' ? 'text-danger' : 'text-dark' }} mb-0">{{ $activity->title }}</h5>
                                    <span class="badge bg-light text-muted fw-bold border px-3 py-2 rounded-pill shadow-sm"><i class="bi bi-clock me-1"></i>{{ $activity->time_ago }}</span>
                                </div>
                                <p class="text-muted mb-0 fw-medium fs-6">{!! $activity->description !!}</p>
                                <small class="text-black-50 fw-medium mt-2 d-block"><i class="bi bi-calendar-event me-1 opacity-50"></i> {{ \Carbon\Carbon::parse($activity->created_at)->format('d/m/Y - H:i:s') }}</small>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-5">
                        <div class="icon-wrapper-md bg-gray-soft text-muted rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center shadow-sm border border-light-subtle" style="width: 80px; height: 80px; font-size: 2.5rem;">
                            <i class="bi bi-journal-x"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-1">Trống rỗng</h4>
                        <p class="text-muted fw-medium mb-0">Hệ thống chưa ghi nhận bất kỳ hoạt động nào.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Phân trang -->
    @if(isset($activities) && method_exists($activities, 'links'))
        <div class="d-flex justify-content-center custom-pagination mt-4 mb-5 pb-3">
            {{ $activities->links('pagination::bootstrap-5') }}
        </div>
    @endif

@endsection
