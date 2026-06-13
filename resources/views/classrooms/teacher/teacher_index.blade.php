@extends('layouts.teacher.teacher_app')

@section('title', 'Quản lý Lớp học')

@push('styles')
    <link rel="stylesheet" href="{{ versioned_asset('css/teacher/teacher_classroom.css') }}">
@endpush

@section('content')

    <!-- Tiêu đề trang -->
    <div class="teacher-page-heading d-flex flex-column flex-md-row justify-content-between align-items-md-center pb-3 mb-4 mt-2 gap-3">
        <div>
            <h3 class="fw-800 text-dark mb-1 d-flex align-items-center gap-2">
                Quản lý Lớp học <i class="bi bi-diagram-3 theme-text-primary"></i>
            </h3>
            <p class="text-muted fs-6 mb-0">Tổ chức và quản lý học viên theo từng lớp</p>
        </div>
        
        <button type="button" class="btn btn-purple-gradient fw-bold px-4 py-2.5 rounded-pill shadow-sm d-flex align-items-center gap-2 hover-lift transition-all" data-bs-toggle="modal" data-bs-target="#createClassModal">
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

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-4 mb-4 d-flex align-items-center" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
            <strong class="me-1">Lỗi!</strong> {{ session('error') }}
            <button type="button" class="btn-close mt-1" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <h5 class="fw-bold mb-3 text-dark">Danh sách lớp học ({{ $classrooms->count() ?? 0 }})</h5>
    
    <!-- Danh sách Lớp -->
    <div class="row g-4">
        @forelse($classrooms as $room)
            <div class="col-md-6 col-lg-4">
                <div class="classroom-card teacher-class-card border-0 shadow-sm h-100">
                    <div class="teacher-class-card-header">
                        <div class="d-flex justify-content-between align-items-start gap-3">
                            <div class="min-w-0">
                                <h5 class="fw-bold mb-1 text-white text-truncate" title="{{ $room->name }}">{{ $room->name }}</h5>
                                <p class="mb-0 text-white text-opacity-75 d-flex align-items-center gap-1" style="font-size: 0.85rem;">
                                    <i class="bi bi-cpu"></i> Hệ thống {{ $globalSettings['site_name'] ?? 'EduQuiz AI' }}
                                </p>
                            </div>
                            <div class="dropdown">
                                <button class="teacher-class-card-menu btn btn-sm border-0 rounded-circle" type="button" data-bs-toggle="dropdown" aria-label="Tùy chọn lớp học">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-1">
                                    <li><button type="button" class="dropdown-item py-2 fw-medium text-secondary" data-bs-toggle="modal" data-bs-target="#editClassModal-{{ $room->id }}"><i class="bi bi-pencil me-2"></i> Đổi tên lớp</button></li>
                                    <li><hr class="dropdown-divider opacity-25"></li>
                                    <li>
                                        <form action="{{ route('classrooms.destroy', $room->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa lớp này? Mọi dữ liệu sẽ bị mất!');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item py-2 fw-medium text-danger"><i class="bi bi-trash3 me-2"></i> Xóa lớp</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="teacher-class-card-body flex-grow-1 d-flex flex-column">
                        <div class="d-flex align-items-center text-muted mb-4">
                            <div class="teacher-class-avatar bg-purple-light theme-text-primary rounded-circle d-flex align-items-center justify-content-center me-3">
                                <i class="bi bi-people-fill fs-5"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="d-block small fw-bold text-uppercase letter-spacing-1 opacity-75">Học viên</span>
                                <span class="fw-medium theme-text-dark">{{ $room->users_count ?? 0 }} học viên đang theo học</span>
                            </div>
                        </div>
                        
                        <div class="teacher-class-summary mt-auto rounded-3 p-3">
                            <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
                                <div class="teacher-card-meta text-muted fw-medium d-flex align-items-center gap-2">
                                    <span class="teacher-card-meta-icon bg-white rounded shadow-sm d-flex align-items-center justify-content-center">
                                        <i class="bi bi-file-earmark-text theme-text-primary"></i>
                                    </span>
                                    <span>{{ $room->exams_count ?? 0 }} bài thi</span>
                                </div>

                                <div class="d-flex align-items-center gap-2" title="Mã lớp học">
                                    <span class="text-muted small">Mã:</span>
                                    <span class="code-badge">{{ $room->code }}</span>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('classrooms.show', $room->id) }}" class="btn btn-outline-theme w-100 fw-bold rounded-pill mt-4 transition-all">
                            Xem chi tiết
                        </a>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="editClassModal-{{ $room->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-4">
                        <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                            <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                                <div class="bg-purple-light theme-text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                    <i class="bi bi-pencil-square fs-6"></i>
                                </div>
                                Đổi tên lớp học
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <form action="{{ route('classrooms.update', $room->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-body px-4 py-4">
                                <label for="editClassName-{{ $room->id }}" class="form-label fw-bold text-dark mb-2">Tên lớp học <span class="text-danger">*</span></label>
                                <input type="text" class="form-control custom-input form-control-lg rounded-3" id="editClassName-{{ $room->id }}" name="name" value="{{ $room->name }}" required maxlength="255">
                            </div>
                            <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                                <button type="button" class="btn btn-light border fw-bold rounded-pill px-4 transition-all" data-bs-dismiss="modal">Hủy</button>
                                <button type="submit" class="btn btn-purple-gradient fw-bold rounded-pill px-4 shadow-sm transition-all hover-lift">Lưu thay đổi</button>
                            </div>
                        </form>
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
                    <button type="button" class="btn btn-purple-gradient px-4 py-2.5 rounded-pill shadow-sm fw-bold hover-lift transition-all" data-bs-toggle="modal" data-bs-target="#createClassModal">
                        <i class="bi bi-plus-lg me-1"></i> Tạo lớp đầu tiên
                    </button>
                </div>
            </div>
        @endforelse
    </div>

    <div class="guide-section teacher-class-guide shadow-sm mt-5">
        <h6 class="fw-bold mb-4 theme-text-dark text-uppercase letter-spacing-1 d-flex align-items-center gap-2">
            <i class="bi bi-info-circle-fill theme-text-primary"></i> Hướng dẫn quản lý lớp
        </h6>
        <div class="row g-4">
            <div class="col-md-4 d-flex gap-3 align-items-start">
                <div class="guide-icon bg-indigo-light text-indigo shadow-sm">
                    <i class="bi bi-diagram-3"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1 fs-6 theme-text-dark">1. Tạo và chia sẻ mã lớp</h6>
                    <p class="text-muted mb-0 opacity-75" style="font-size: 0.85rem;">Tạo lớp học, gửi mã tham gia cho học viên và quản lý danh sách theo từng lớp.</p>
                </div>
            </div>
            <div class="col-md-4 d-flex gap-3 align-items-start">
                <div class="guide-icon bg-success-subtle text-success shadow-sm">
                    <i class="bi bi-send-check"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1 fs-6 theme-text-dark">2. Giao bài kiểm tra</h6>
                    <p class="text-muted mb-0 opacity-75" style="font-size: 0.85rem;">Chọn ngân hàng đề, cấu hình thời gian và giao bài cho lớp cần theo dõi.</p>
                </div>
            </div>
            <div class="col-md-4 d-flex gap-3 align-items-start">
                <div class="guide-icon bg-purple-light theme-text-primary shadow-sm">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1 fs-6 theme-text-dark">3. Theo dõi tiến độ</h6>
                    <p class="text-muted mb-0 opacity-75" style="font-size: 0.85rem;">Xem điểm, số bài đã làm và chấm các câu tự luận ngay trong hệ thống.</p>
                </div>
            </div>
        </div>
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
