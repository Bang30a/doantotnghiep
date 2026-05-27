@extends('layouts.admin.admin_app')

@section('title', 'Chi tiết Giảng viên: ' . ($teacher->name ?? ''))

@push('styles')
    <!-- Link file CSS riêng -->
    <link rel="stylesheet" href="{{ asset('css/admin/admin_teacher_detail.css') }}?v={{ time() }}">
@endpush

@section('content')

    <!-- Tiêu đề trang & Nút quay lại -->
    <div class="d-flex align-items-center gap-3 mb-4 mt-2 border-bottom border-light-subtle pb-3">
        <a href="{{ route('admin.teachers') ?? '#' }}" class="btn-back">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h3 class="fw-800 text-dark mb-1">Hồ sơ Giảng viên</h3>
            <p class="text-muted fw-medium mb-0">Xem chi tiết và chỉnh sửa thông tin tài khoản</p>
        </div>
    </div>

    <!-- Thông báo Thành công -->
    @if(session('success'))
        <div class="alert alert-emerald-soft alert-dismissible fade show mb-4 border-0 shadow-sm rounded-4 d-flex align-items-center" role="alert" id="success-alert">
            <i class="bi bi-check-circle-fill fs-5 me-2 text-emerald"></i> 
            <span class="fw-bold">{{ session('success') }}</span>
            <button type="button" class="btn-close mt-1" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4 mb-5">
        
        <!-- ============================================== -->
        <!-- BẮT ĐẦU CỘT TRÁI (COL-XL-4)                    -->
        <!-- ============================================== -->
        <div class="col-xl-4">
            
            <!-- Card 1: Thông tin Profile -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white hover-lift transition-all">
                <div class="profile-cover" style="height: 120px;"></div>
                
                <div class="card-body px-4 pb-4 pt-0 text-center position-relative">
                    <div class="profile-avatar-wrapper position-relative d-inline-block shadow-sm">
                        <img src="{{ isset($teacher->avatar) ? asset($teacher->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($teacher->name ?? 'User').'&background=E0E7FF&color=7E22CE&size=150' }}" alt="Avatar" class="profile-avatar bg-white">
                    </div>
                    
                    <h4 class="fw-800 text-dark mt-3 mb-1">{{ $teacher->name ?? 'Giảng viên' }}</h4>
                    <p class="text-muted fw-medium mb-3">{{ $teacher->email ?? 'teacher@eduquiz.com' }}</p>
                    
                    <div class="d-flex justify-content-center gap-2 mb-4">
                        <span class="badge bg-purple-light theme-text-primary border border-purple-subtle px-3 py-1.5 rounded-pill fw-bold">
                            <i class="bi bi-person-workspace me-1"></i> Giảng viên
                        </span>
                        @if(isset($teacher->status) && $teacher->status === 'locked')
                            <span class="badge bg-danger-soft text-danger border border-danger-subtle px-3 py-1.5 rounded-pill"><i class="bi bi-lock-fill"></i> Đã khóa</span>
                        @else
                            <span class="badge bg-emerald-soft text-emerald border border-emerald-subtle px-3 py-1.5 rounded-pill"><i class="bi bi-check-circle-fill"></i> Hoạt động</span>
                        @endif
                    </div>

                    <div class="row g-3 border-top border-light-subtle pt-4">
                        <div class="col-6 border-end border-light-subtle">
                            <h3 class="fw-800 text-dark mb-0">{{ $teacher->classrooms_count ?? 0 }}</h3>
                            <p class="text-muted small fw-bold text-uppercase letter-spacing-1 mb-0">Lớp học</p>
                        </div>
                        <div class="col-6">
                            <h3 class="fw-800 text-dark mb-0">{{ $teacher->exams_count ?? 0 }}</h3>
                            <p class="text-muted small fw-bold text-uppercase letter-spacing-1 mb-0">Đề thi</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Box Hành động nguy hiểm -->
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white hover-lift transition-all mt-4">
                <h6 class="fw-bold text-danger mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill"></i> Khu vực nguy hiểm
                </h6>
                <button type="button" class="btn w-100 fw-bold rounded-3 py-2.5 d-flex align-items-center justify-content-center gap-2 transition-all hover-lift" style="background-color: #fee2e2; color: #dc3545; border: 1px solid #f8d7da;">
                    <i class="bi bi-trash3-fill"></i> Xóa tài khoản vĩnh viễn
                </button>
                <p class="text-muted small mt-3 mb-0 text-center">Toàn bộ dữ liệu của giảng viên này sẽ bị xóa sạch khỏi hệ thống.</p>
            </div>

        </div> <!-- KẾT THÚC CỘT TRÁI (Thẻ div bị thiếu nãy giờ nằm đây!) -->


        <!-- ============================================== -->
        <!-- BẮT ĐẦU CỘT PHẢI (COL-XL-8)                    -->
        <!-- ============================================== -->
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white hover-lift transition-all h-100">
                <div class="d-flex align-items-center gap-2 mb-4 border-bottom pb-3">
                    <div class="icon-wrapper-md bg-purple-light theme-text-primary rounded-3"><i class="bi bi-pencil-square"></i></div>
                    <h5 class="fw-bold mb-0 text-dark">Chỉnh sửa thông tin</h5>
                </div>

                <form action="{{ route('admin.teachers.update', $teacher->id ?? 0) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1">Họ và Tên <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control custom-input rounded-3 py-2.5 fw-medium text-dark" value="{{ $teacher->name ?? '' }}" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control custom-input rounded-3 py-2.5 fw-medium text-dark" value="{{ $teacher->phone ?? '' }}" placeholder="Nhập số điện thoại...">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1">Email đăng nhập</label>
                            <div class="input-group">
                                <span class="input-group-text bg-gray-soft border-end-0 text-muted"><i class="bi bi-envelope-fill"></i></span>
                                <input type="email" name="email" class="form-control custom-input bg-gray-soft border-start-0 py-2.5 fw-bold text-muted" value="{{ $teacher->email ?? '' }}" readonly>
                            </div>
                            <small class="text-danger mt-1 d-block fw-medium"><i class="bi bi-info-circle-fill me-1"></i> Email đăng nhập không thể thay đổi.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1">Trạng thái tài khoản</label>
                            <select name="status" class="form-select custom-input rounded-3 py-2.5 fw-bold text-dark">
                                <option value="active" {{ (!isset($teacher->status) || $teacher->status === 'active') ? 'selected' : '' }}>Hoạt động bình thường</option>
                                <option value="locked" {{ (isset($teacher->status) && $teacher->status === 'locked') ? 'selected' : '' }}>Khóa tài khoản</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1">Đặt lại mật khẩu</label>
                            <input type="password" name="password" class="form-control custom-input rounded-3 py-2.5 fw-medium text-dark" placeholder="Để trống nếu không đổi">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-4 mt-auto">
                        <a href="{{ route('admin.teachers') ?? '#' }}" class="btn btn-light fw-bold px-4 py-2.5 rounded-pill shadow-sm border transition-all hover-lift">Hủy</a>
                        <button type="submit" class="btn btn-purple-gradient text-white fw-bold px-5 py-2.5 rounded-pill shadow-sm d-flex align-items-center gap-2 transition-all hover-pulse">
                            <i class="bi bi-floppy-fill"></i> Lưu cập nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <!-- KẾT THÚC CỘT PHẢI -->

    </div>

@endsection

@push('scripts')
    <!-- Link file JS riêng -->
    <script src="{{ asset('js/admin/admin_teacher_detail.js') }}?v={{ time() }}"></script>
@endpush