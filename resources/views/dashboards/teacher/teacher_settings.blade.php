@extends('layouts.teacher.teacher_app')

@section('title', 'Cài đặt cá nhân')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/teacher/teacher_settings.css') }}?v={{ time() }}">
@endpush

@section('content')

    <div class="row g-4 mt-3">
        <div class="col-lg-4 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden sticky-top hover-lift" style="top: 24px;">
                <div class="card-body p-2">
                    <div class="settings-menu">
                        <button class="settings-tab-btn active rounded-3" data-target="tab-profile">
                            <i class="bi bi-person-badge-fill"></i> Thông tin chung
                        </button>
                        <button class="settings-tab-btn rounded-3" data-target="tab-security" id="btn-tab-security">
                            <i class="bi bi-shield-lock-fill"></i> Bảo mật & Mật khẩu
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8 col-xl-9">
            
            <div id="tab-profile" class="settings-content active">
                <div class="card border-0 shadow-sm rounded-4 mb-4 hover-lift">
                    <div class="card-body p-4 p-md-5">
                        <h5 class="fw-bold mb-4 pb-3 border-bottom text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-person-circle text-purple"></i> Hồ sơ Giảng viên
                        </h5>
                        
                        @if(session('success_profile'))
                            <div class="alert alert-emerald-soft alert-dismissible fade show rounded-3 border-0 shadow-sm d-flex align-items-center" role="alert">
                                <i class="bi bi-check-circle-fill fs-5 me-2 text-emerald"></i>
                                <span class="fw-medium">{{ session('success_profile') }}</span>
                                <button type="button" class="btn-close mt-1" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ url('teacher/settings/profile') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="d-flex flex-column flex-md-row align-items-center gap-4 mb-5 bg-light p-4 rounded-4 border border-light">
                                <div class="position-relative">
                                    <div class="avatar-preview-container shadow-sm border border-4 border-white">
                                        <img id="avatarPreview" src="{{ $user->avatar ? asset($user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=F3E8FF&color=7E22CE&size=150' }}" alt="Avatar">
                                    </div>
                                    <label for="avatarInput" class="avatar-upload-btn shadow-sm">
                                        <i class="bi bi-camera-fill"></i>
                                    </label>
                                    <input type="file" id="avatarInput" name="avatar" class="d-none" accept="image/*">
                                </div>
                                <div class="text-center text-md-start">
                                    <h6 class="fw-bold text-dark mb-1 fs-5">Ảnh đại diện</h6>
                                    <p class="text-muted small fw-medium mb-0">Định dạng JPG, PNG hoặc GIF.<br>Dung lượng tối đa 2MB.</p>
                                    @error('avatar') <span class="text-danger small fw-bold mt-2 d-block"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1">Họ và Tên <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-custom">
                                        <span class="input-group-text bg-white text-purple border-end-0"><i class="bi bi-person-fill"></i></span>
                                        <input type="text" name="name" class="form-control border-start-0 fw-medium ps-0" value="{{ old('name', $user->name) }}" required>
                                    </div>
                                    @error('name') <span class="text-danger small fw-bold mt-1 d-block">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1">Số điện thoại</label>
                                    <div class="input-group input-group-custom">
                                        <span class="input-group-text bg-white text-purple border-end-0"><i class="bi bi-telephone-fill"></i></span>
                                        <input type="text" name="phone" class="form-control border-start-0 fw-medium ps-0" value="{{ old('phone', $user->phone ?? '') }}" placeholder="Nhập số điện thoại...">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1">Email đăng nhập</label>
                                    <div class="input-group input-group-custom opacity-75" style="background-color: #f8fafc;">
                                        <span class="input-group-text border-end-0 bg-transparent text-muted"><i class="bi bi-envelope-fill"></i></span>
                                        <input type="email" class="form-control border-start-0 bg-transparent text-muted fw-bold ps-0" value="{{ $user->email }}" disabled readonly>
                                    </div>
                                    <small class="text-danger mt-2 d-block fw-medium"><i class="bi bi-info-circle-fill me-1"></i> Email không thể thay đổi để đảm bảo an toàn tài khoản.</small>
                                </div>
                            </div>

                            <div class="text-end mt-4 pt-3 border-top">
                                <button type="submit" class="btn btn-purple-gradient text-white px-5 py-2.5 rounded-pill fw-bold shadow-purple transition-all d-inline-flex align-items-center gap-2 hover-pulse">
                                    <i class="bi bi-floppy-fill"></i> Lưu thay đổi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div id="tab-security" class="settings-content" style="display: none;">
                <div class="card border-0 shadow-sm rounded-4 mb-4 hover-lift">
                    <div class="card-body p-4 p-md-5">
                        <h5 class="fw-bold mb-4 pb-3 border-bottom text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-shield-lock-fill text-purple"></i> Đổi mật khẩu
                        </h5>
                        
                        @if(session('success_password'))
                            <div class="alert alert-emerald-soft alert-dismissible fade show rounded-3 border-0 shadow-sm d-flex align-items-center" role="alert">
                                <i class="bi bi-check-circle-fill fs-5 me-2 text-emerald"></i>
                                <span class="fw-medium">{{ session('success_password') }}</span>
                                <button type="button" class="btn-close mt-1" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ url('teacher/settings/password') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                                <div class="input-group input-group-custom">
                                    <span class="input-group-text bg-white text-muted border-end-0"><i class="bi bi-key-fill"></i></span>
                                    <input type="password" name="current_password" class="form-control border-start-0 ps-0 fw-medium @error('current_password') is-invalid @enderror" placeholder="••••••••" required>
                                </div>
                                @error('current_password') <span class="text-danger small fw-bold mt-1 d-block">{{ $message }}</span> @enderror
                            </div>

                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1">Mật khẩu mới <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-custom">
                                        <span class="input-group-text bg-white text-purple border-end-0"><i class="bi bi-lock-fill"></i></span>
                                        <input type="password" name="new_password" class="form-control border-start-0 ps-0 fw-medium @error('new_password') is-invalid @enderror" placeholder="••••••••" required>
                                    </div>
                                    @error('new_password') <span class="text-danger small fw-bold mt-1 d-block">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase letter-spacing-1">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-custom">
                                        <span class="input-group-text bg-white text-purple border-end-0"><i class="bi bi-lock-fill"></i></span>
                                        <input type="password" name="new_password_confirmation" class="form-control border-start-0 ps-0 fw-medium" placeholder="••••••••" required>
                                    </div>
                                </div>
                            </div>

                            <div class="p-3 bg-warning-light rounded-3 mb-4 border border-warning-subtle d-flex align-items-start gap-3">
                                <i class="bi bi-shield-check text-warning fs-3 mt-1"></i>
                                <div>
                                    <h6 class="fw-bold text-warning-emphasis mb-1">Yêu cầu mật khẩu an toàn:</h6>
                                    <ul class="text-warning-emphasis small mb-0 ps-3 fw-medium">
                                        <li>Tối thiểu 8 ký tự.</li>
                                        <li>Nên bao gồm cả chữ hoa, chữ thường và số.</li>
                                        <li>Không sử dụng mật khẩu cũ đã từng đặt.</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="text-end pt-3 border-top">
                                <button type="submit" class="btn btn-purple-main px-5 py-2.5 rounded-pill fw-bold shadow-sm transition-all hover-pulse">
                                    Cập nhật mật khẩu
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

    <script src="{{ asset('js/teacher/teacher_settings.js') }}?v={{ time() }}"></script>
@endpush