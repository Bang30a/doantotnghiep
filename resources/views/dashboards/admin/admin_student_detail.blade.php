@extends('layouts.admin.admin_app')

@section('title', 'Chi tiết Học viên: ' . ($student->name ?? ''))

@push('styles')
    <link rel="stylesheet" href="{{ versioned_asset('css/admin/admin_student_detail.css') }}">
@endpush

@section('content')

    <!-- Tiêu đề trang & Nút quay lại -->
    <div class="admin-page-heading d-flex align-items-center gap-3 mb-4 mt-2 pb-3">
        <a href="{{ route('admin.students') ?? '#' }}" class="btn-back">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h3 class="fw-800 text-dark mb-1">Hồ sơ Học viên</h3>
            <p class="text-muted fw-medium mb-0">Xem tiến độ học tập và chỉnh sửa thông tin tài khoản</p>
        </div>
    </div>

    <!-- Thông báo Thành công -->
    @if(session('success'))
        <div class="alert bg-emerald-soft text-emerald border border-emerald-subtle alert-dismissible fade show mb-4 shadow-sm rounded-4 d-flex align-items-center" role="alert" id="success-alert">
            <i class="bi bi-check-circle-fill fs-5 me-2"></i> 
            <span class="fw-bold">{{ session('success') }}</span>
            <button type="button" class="btn-close mt-1" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4 mb-5">
        <!-- Cột trái: Thông tin Card Profile -->
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white hover-lift transition-all">
                <div class="profile-cover" style="height: 120px;"></div>
                
                <div class="card-body px-4 pb-4 pt-0 text-center position-relative">
                    <div class="profile-avatar-wrapper position-relative d-inline-block shadow-sm">
                        <img src="{{ $student->avatar ? asset($student->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($student->name ?? 'Student').'&background=D1FAE5&color=059669&size=150' }}" alt="Avatar" class="profile-avatar bg-white">
                    </div>
                    
                    <h4 class="fw-800 text-dark mt-3 mb-1">{{ $student->name ?? 'Học viên Nguyễn B' }}</h4>
                    <p class="text-muted fw-medium mb-3">{{ $student->email ?? 'student@eduquiz.com' }}</p>
                    
                    <div class="d-flex justify-content-center gap-2 mb-4">
                        <span class="badge bg-emerald-light text-emerald border border-emerald-subtle px-3 py-1.5 rounded-pill fw-bold">
                            <i class="bi bi-person-badge-fill me-1"></i> Học viên
                        </span>
                        @if(isset($student->status) && ($student->status === 'locked'))
                            <span class="badge bg-danger-soft text-danger border border-danger-subtle px-3 py-1.5 rounded-pill"><i class="bi bi-lock-fill"></i> Đã khóa</span>
                        @else
                            <span class="badge bg-emerald-soft text-emerald border border-emerald-subtle px-3 py-1.5 rounded-pill"><i class="bi bi-check-circle-fill"></i> Hoạt động</span>
                        @endif
                    </div>

                    <div class="row g-3 border-top border-light-subtle pt-4">
                        <div class="col-6 border-end border-light-subtle">
                            <h3 class="fw-800 text-dark mb-0">{{ $student->mock_exams_done ?? 0 }}</h3>
                            <p class="text-muted small fw-bold text-uppercase letter-spacing-1 mb-0">Bài đã làm</p>
                        </div>
                        <div class="col-6">
                            <h3 class="fw-800 text-dark mb-0">{{ number_format($student->mock_avg_score ?? 0, 1) }}</h3>
                            <p class="text-muted small fw-bold text-uppercase letter-spacing-1 mb-0">Điểm TB</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Box Hành động nguy hiểm -->
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mt-4">
                <h6 class="fw-bold text-danger mb-3 d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill"></i> Khu vực nguy hiểm
                </h6>
                <form action="{{ route('admin.students.destroy', $student->id ?? 0) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn tài khoản học viên này? Toàn bộ dữ liệu liên quan sẽ bị xóa và không thể khôi phục.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn w-100 fw-bold rounded-3 py-2.5 d-flex align-items-center justify-content-center gap-2 transition-all hover-lift" style="background-color: #fee2e2; color: #dc3545; border: 1px solid #f8d7da;">
                        <i class="bi bi-trash3-fill"></i> Xóa tài khoản vĩnh viễn
                    </button>
                </form>
                <p class="text-muted small mt-3 mb-0 text-center">Toàn bộ dữ liệu của học viên này sẽ bị xóa sạch khỏi hệ thống.</p>
            </div>
        </div>

        <!-- Cột phải: Form Chỉnh sửa -->
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white hover-lift transition-all h-100">
                <div class="d-flex align-items-center gap-2 mb-4 border-bottom pb-3">
                    <div class="icon-wrapper-md bg-purple-light theme-text-primary rounded-3"><i class="bi bi-pencil-square"></i></div>
                    <h5 class="fw-bold mb-0 text-dark">Chỉnh sửa thông tin</h5>
                </div>

                <form action="{{ route('admin.students.update', $student->id ?? 0) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1">Họ và Tên <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control custom-input rounded-3 py-2.5 fw-medium text-dark" value="{{ $student->name ?? '' }}" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control custom-input rounded-3 py-2.5 fw-medium text-dark" value="{{ $student->phone ?? '' }}" placeholder="Nhập số điện thoại...">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1">Email đăng nhập</label>
                            <div class="input-group">
                                <span class="input-group-text bg-gray-soft border-end-0 text-muted"><i class="bi bi-envelope-fill"></i></span>
                                <input type="email" name="email" class="form-control custom-input bg-gray-soft border-start-0 py-2.5 fw-bold text-muted" value="{{ $student->email ?? '' }}" readonly>
                            </div>
                            <small class="text-danger mt-1 d-block fw-medium"><i class="bi bi-info-circle-fill me-1"></i> Email không thể thay đổi.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1">Trạng thái tài khoản</label>
                            <select name="status" class="form-select custom-input rounded-3 py-2.5 fw-bold text-dark">
                                <!-- Đã sửa value thành 'active' và 'locked' -->
                                <option value="active" {{ (!isset($student->status) || $student->status === 'active') ? 'selected' : '' }}>Hoạt động bình thường</option>
                                <option value="locked" {{ (isset($student->status) && $student->status === 'locked') ? 'selected' : '' }}>Khóa tài khoản</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1">Đặt lại mật khẩu</label>
                            <input type="password" name="password" class="form-control custom-input rounded-3 py-2.5 fw-medium text-dark" placeholder="Để trống nếu không đổi">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-4 mt-auto">
                        <a href="{{ route('admin.students') ?? '#' }}" class="btn btn-light fw-bold px-4 py-2.5 rounded-pill shadow-sm border transition-all hover-lift">Hủy</a>
                        <button type="submit" class="btn btn-purple-gradient text-white fw-bold px-5 py-2.5 rounded-pill shadow-sm d-flex align-items-center gap-2 transition-all hover-pulse">
                            <i class="bi bi-floppy-fill"></i> Lưu cập nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ versioned_asset('js/admin/admin_student_detail.js') }}"></script>
@endpush
