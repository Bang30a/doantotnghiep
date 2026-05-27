@extends('layouts.teacher.teacher_app')

@section('title', 'Quản lý Lớp học')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/teacher/teacher_classroom.css') }}?v={{ time() }}">
@endpush

@section('content')

    <!-- Tiêu đề trang -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center pb-3 mb-4 mt-2 border-bottom border-light-subtle gap-3">
        <div>
            <h3 class="fw-800 text-dark mb-1 d-flex align-items-center gap-2">
                Quản lý Lớp học <i class="bi bi-diagram-3 theme-text-primary"></i>
            </h3>
            <p class="text-muted fs-6 mb-0">Tổ chức và quản lý học viên theo từng lớp</p>
        </div>
        
        <button class="btn btn-purple-gradient fw-bold px-4 py-2.5 rounded-pill shadow-sm d-flex align-items-center gap-2 hover-lift transition-all" data-bs-toggle="modal" data-bs-target="#createClassModal">
            <i class="bi bi-plus-circle-fill"></i> Tạo lớp mới
        </button>
    </div>

    <!-- Thống kê nhanh -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="stat-card hover-lift p-4 bg-white border-0 rounded-4 shadow-sm d-flex align-items-center gap-4 h-100">
                <div class="icon-wrapper purple shadow-sm flex-shrink-0">
                    <i class="bi bi-diagram-3-fill"></i>
                </div>
                <div>
                    <p class="text-muted mb-1 small fw-bold text-uppercase letter-spacing-1">Tổng số lớp</p>
                    <h3 class="fw-800 mb-0 text-dark">{{ $totalClasses ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card hover-lift p-4 bg-white border-0 rounded-4 shadow-sm d-flex align-items-center gap-4 h-100">
                <div class="icon-wrapper emerald shadow-sm flex-shrink-0">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <p class="text-muted mb-1 small fw-bold text-uppercase letter-spacing-1">Tổng học viên</p>
                    <h3 class="fw-800 mb-0 text-dark">{{ $totalStudents ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card hover-lift p-4 bg-white border-0 rounded-4 shadow-sm d-flex align-items-center gap-4 h-100">
                <div class="icon-wrapper fuchsia shadow-sm flex-shrink-0">
                    <i class="bi bi-send-check-fill"></i>
                </div>
                <div>
                    <p class="text-muted mb-1 small fw-bold text-uppercase letter-spacing-1">Bài thi đã giao</p>
                    <h3 class="fw-800 mb-0 text-dark">{{ $totalExamsAssigned ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Thông báo Alert -->
    @if(session('success'))
        <div class="alert bg-purple-light text-purple border border-purple-subtle alert-dismissible fade show shadow-sm rounded-4 mb-4 d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill fs-4 me-3"></i> 
            <strong class="me-1">Thành công!</strong> {{ session('success') }}
            <button type="button" class="btn-close mt-1" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <h5 class="fw-bold mb-3 text-dark">Danh sách lớp học ({{ $classrooms->count() ?? 0 }})</h5>
    
    <!-- Danh sách Lớp -->
    <div class="row g-4">
        @forelse($classrooms as $room)
            <div class="col-md-4 col-xl-3">
                <div class="classroom-card card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4 d-flex flex-column">
                        
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="class-avatar text-white fw-bold fs-5 shadow-sm">
                                {{ mb_substr($room->name, 0, 1) }}
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light-hover border-0 rounded-circle" type="button" data-bs-toggle="dropdown" style="width: 32px; height: 32px;">
                                    <i class="bi bi-three-dots-vertical text-muted"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-1">
                                    <li><a class="dropdown-item py-2 fw-medium text-secondary" href="#"><i class="bi bi-pencil me-2"></i> Đổi tên lớp</a></li>
                                    <li><hr class="dropdown-divider opacity-25"></li>
                                    <li>
                                        <form action="#" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa lớp này? Mọi dữ liệu sẽ bị mất!');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item py-2 fw-medium text-danger"><i class="bi bi-trash3 me-2"></i> Xóa lớp</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <h5 class="fw-bold text-dark mb-1 text-truncate" title="{{ $room->name }}">{{ $room->name }}</h5>
                        <p class="text-muted small mb-3">Mã tham gia: <span class="code-badge ms-1">{{ $room->code }}</span></p>
                        
                        <div class="d-flex flex-wrap gap-2 mb-4 mt-auto pt-2">
                            <div class="meta-tag">
                                <i class="bi bi-person theme-text-primary"></i> <span class="fw-semibold">{{ $room->users_count ?? 0 }}</span> hv
                            </div>
                            <div class="meta-tag">
                                <i class="bi bi-file-earmark-text text-fuchsia"></i> <span class="fw-semibold">{{ $room->exams_count ?? 0 }}</span> bài
                            </div>
                        </div>
                        
                        <a href="{{ route('classrooms.show', $room->id) }}" class="btn btn-outline-theme w-100 fw-bold rounded-pill mt-auto transition-all">
                            Xem chi tiết
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="empty-state text-center py-5 bg-white border-0 rounded-4 shadow-sm hover-lift transition-all">
                    <div class="empty-icon-wrapper bg-purple-light text-purple rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center shadow-sm" style="width: 90px; height: 90px; font-size: 3rem;">
                        <i class="bi bi-buildings opacity-75"></i>
                    </div>
                    <h4 class="text-dark fw-bold mb-2">Chưa có lớp học nào</h4>
                    <p class="text-muted mb-4 max-w-md mx-auto">Tổ chức học viên vào các lớp học để dễ dàng giao bài tập và theo dõi tiến độ thi tự động.</p>
                    <button class="btn btn-purple-gradient px-4 py-2.5 rounded-pill shadow-sm fw-bold hover-lift transition-all" data-bs-toggle="modal" data-bs-target="#createClassModal">
                        <i class="bi bi-plus-lg me-1"></i> Tạo lớp đầu tiên
                    </button>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Modal Tạo Lớp -->
    <div class="modal fade" id="createClassModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                        <div class="bg-purple-light theme-text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="bi bi-diagram-3-fill fs-6"></i>
                        </div>
                        Tạo lớp học mới
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <form action="{{ route('classrooms.store') }}" method="POST">
                    @csrf
                    <div class="modal-body px-4 py-4">
                        <div class="form-group mb-3">
                            <label for="className" class="form-label fw-bold text-dark mb-2">Tên lớp học <span class="text-danger">*</span></label>
                            <input type="text" class="form-control custom-input form-control-lg rounded-3" id="className" name="name" placeholder="VD: Lịch sử Đảng K64, Toán Cao Cấp..." required>
                            <div class="form-text mt-2 text-muted small d-flex align-items-center gap-1">
                                <i class="bi bi-info-circle theme-text-primary"></i> Mã tham gia lớp (Code 6 ký tự) sẽ được hệ thống tạo tự động.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                        <button type="button" class="btn btn-light border fw-bold rounded-pill px-4 transition-all" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-purple-gradient fw-bold rounded-pill px-4 shadow-sm transition-all hover-lift">Hoàn tất tạo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    {{-- Các file JS tĩnh hoặc logic mở modal (nếu có) --}}
@endpush