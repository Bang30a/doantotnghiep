@extends('layouts.student.student_app')

@section('title', 'Cài đặt cá nhân')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/student/student_settings.css') }}?v={{ time() }}">
@endpush

@section('content')
    <div class="mb-4 border-bottom border-purple-subtle pb-3">
        <h3 class="fw-bold theme-text-dark mb-1 d-flex align-items-center gap-2">
            <i class="bi bi-person-lines-fill theme-text-primary"></i> Cài đặt cá nhân
        </h3>
        <p class="text-muted fs-6 mb-0">Quản lý thông tin tài khoản và bảo mật cá nhân</p>
    </div>

    <div class="row g-4">
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden sticky-top" style="top: 20px;">
                <div class="card-body p-0">
                    <div class="settings-menu py-2">
                        <button class="settings-tab-btn active" data-target="tab-profile">
                            <i class="bi bi-person-circle"></i> Thông tin chung
                        </button>
                        <button class="settings-tab-btn" data-target="tab-security" id="btn-tab-security">
                            <i class="bi bi-shield-lock"></i> Bảo mật & Mật khẩu
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            
            <div id="tab-profile" class="settings-content active">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4 p-md-5">
                        <h5 class="fw-bold mb-4 pb-2 border-bottom theme-text-dark">Hồ sơ Học viên</h5>
                        
                        @if(session('success_profile'))
                            <div class="alert alert-custom-success alert-dismissible fade show rounded-3 border-0 shadow-sm" role="alert">
                                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success_profile') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('student.settings.update.profile') ?? '#' }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="d-flex flex-column flex-md-row align-items-center gap-4 mb-5 bg-purple-light p-4 rounded-4 border border-purple-subtle shadow-sm">
                                <div class="position-relative">
                                    <div class="avatar-preview-container shadow-sm border border-3 border-white">
                                        <img id="avatarPreview" src="{{ Auth::user()->avatar ? asset(Auth::user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name ?? 'HV').'&background=F5F3FF&color=7C3AED&size=150' }}" alt="Avatar">
                                    </div>
                                    <label for="avatarInput" class="avatar-upload-btn shadow-sm" title="Thay đổi ảnh đại diện">
                                        <i class="bi bi-camera-fill"></i>
                                    </label>
                                    <input type="file" id="avatarInput" name="avatar" class="d-none" accept="image/*">
                                </div>
                                <div class="text-center text-md-start">
                                    <h6 class="fw-bold mb-1 theme-text-dark">Ảnh đại diện</h6>
                                    <p class="text-muted small mb-0 opacity-75">Định dạng JPG, PNG. Dung lượng tối đa 2MB.</p>
                                    @error('avatar') <span class="text-danger small mt-2 d-block bg-danger-subtle px-2 py-1 rounded"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1">Họ và Tên <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-custom shadow-sm">
                                        <span class="input-group-text bg-white theme-text-primary border-end-0"><i class="bi bi-person"></i></span>
                                        <input type="text" name="name" class="form-control form-control-lg border-start-0 ps-0" value="{{ old('name', Auth::user()->name ?? '') }}" required>
                                    </div>
                                    @error('name') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1">Số điện thoại</label>
                                    <div class="input-group input-group-custom shadow-sm">
                                        <span class="input-group-text bg-white theme-text-primary border-end-0"><i class="bi bi-telephone"></i></span>
                                        <input type="text" name="phone" class="form-control form-control-lg border-start-0 ps-0" value="{{ old('phone', Auth::user()->phone ?? '') }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1">Email đăng nhập</label>
                                    <div class="input-group input-group-custom shadow-sm opacity-75">
                                        <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-envelope-lock"></i></span>
                                        <input type="email" class="form-control form-control-lg bg-light text-muted border-start-0 ps-0 fw-medium" value="{{ Auth::user()->email ?? '' }}" disabled readonly>
                                    </div>
                                    <small class="text-muted mt-2 d-block"><i class="bi bi-shield-check text-success me-1"></i> Email được khóa để đảm bảo an toàn cho tài khoản của bạn.</small>
                                </div>
                            </div>

                            <div class="text-end mt-4">
                                <button type="submit" class="btn btn-theme-primary px-5 py-2 border-0 rounded-pill fw-bold shadow-sm d-inline-flex align-items-center gap-2">
                                    <i class="bi bi-floppy"></i> Lưu thay đổi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div id="tab-security" class="settings-content" style="display: none;">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4 p-md-5">
                        <h5 class="fw-bold mb-4 pb-2 border-bottom theme-text-dark">Đổi mật khẩu</h5>
                        
                        @if(session('success_password'))
                            <div class="alert alert-custom-success alert-dismissible fade show rounded-3 border-0 shadow-sm" role="alert">
                                <i class="bi bi-shield-check me-2"></i>{{ session('success_password') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('student.settings.update.password') ?? '#' }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                                <div class="input-group input-group-custom shadow-sm">
                                    <span class="input-group-text bg-white text-muted border-end-0"><i class="bi bi-key"></i></span>
                                    <input type="password" name="current_password" class="form-control form-control-lg border-start-0 ps-0 @error('current_password') is-invalid @enderror" required placeholder="Nhập mật khẩu cũ...">
                                </div>
                                @error('current_password') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                            </div>

                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1">Mật khẩu mới <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-custom shadow-sm">
                                        <span class="input-group-text bg-white theme-text-primary border-end-0"><i class="bi bi-lock"></i></span>
                                        <input type="password" name="new_password" class="form-control form-control-lg border-start-0 ps-0 @error('new_password') is-invalid @enderror" required placeholder="Mật khẩu mới...">
                                    </div>
                                    @error('new_password') <span class="text-danger small mt-1 d-block">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-custom shadow-sm">
                                        <span class="input-group-text bg-white theme-text-primary border-end-0"><i class="bi bi-check2-all"></i></span>
                                        <input type="password" name="new_password_confirmation" class="form-control form-control-lg border-start-0 ps-0" required placeholder="Nhập lại mật khẩu mới...">
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 bg-indigo-light rounded-4 mb-4 border border-indigo border-opacity-25 shadow-sm">
                                <h6 class="fw-bold text-indigo mb-2 d-flex align-items-center gap-2">
                                    <i class="bi bi-shield-exclamation fs-5"></i> Yêu cầu mật khẩu an toàn
                                </h6>
                                <ul class="text-indigo opacity-75 small mb-0 ps-3" style="line-height: 1.8;">
                                    <li>Độ dài tối thiểu <strong>8 ký tự</strong>.</li>
                                    <li>Nên kết hợp <strong>chữ hoa, chữ thường và số</strong> để tăng tính bảo mật.</li>
                                    <li>Không sử dụng mật khẩu dễ đoán (ví dụ: 12345678, password, ngày sinh).</li>
                                </ul>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-theme-primary px-5 py-2 border-0 rounded-pill fw-bold shadow-sm d-inline-flex align-items-center gap-2">
                                    <i class="bi bi-shield-lock-fill"></i> Cập nhật mật khẩu
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script id="error-tab-data" type="application/json">
        { "hasPasswordError": {{ session('error_tab') == 'password' || $errors->has('current_password') || $errors->has('new_password') ? 'true' : 'false' }} }
    </script>
    <script src="{{ asset('js/student/student_settings.js') }}?v={{ time() }}"></script>
@endpush