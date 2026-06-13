@extends('layouts.auth_app')

@section('title', 'Tạo mật khẩu mới')

@section('banner_content')
    <div class="auth-kicker">
        <i class="bi bi-shield-check"></i>
        Thiết lập bảo mật
    </div>
    <h1 class="banner-title">Tạo mật khẩu<br>mới</h1>
    <p class="banner-copy">
        Chỉ còn một bước nữa thôi! Hãy thiết lập một mật khẩu mới an toàn để tiếp tục hành trình học tập cùng {{ $globalSettings['site_name'] ?? 'EduQuiz AI' }}.
    </p>
    <div class="auth-signal-grid">
        <div class="auth-signal-card">
            <span><i class="bi bi-fingerprint"></i></span>
            <div>
                <strong>Xác thực an toàn</strong>
                <small>Đổi mật khẩu qua token</small>
            </div>
        </div>
        <div class="auth-signal-card">
            <span><i class="bi bi-check2-circle"></i></span>
            <div>
                <strong>Sẵn sàng tiếp tục</strong>
                <small>Quay lại học tập ngay</small>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <a href="{{ route('login') }}" class="auth-back-link">
        <i class="bi bi-arrow-left"></i> Quay lại đăng nhập
    </a>

    <div class="auth-card-heading">
        <div class="auth-form-icon"><i class="bi bi-lock-fill"></i></div>
        <div>
            <h2 class="auth-title h3">Bảo mật tài khoản</h2>
            <p class="auth-subtitle">Nhập mật khẩu mới để hoàn tất quá trình khôi phục.</p>
        </div>
    </div>

    @if(session('status'))
        <div class="alert alert-success fw-medium small rounded-3 mb-4">
            <i class="bi bi-check-circle-fill me-1"></i> {{ session('status') }}
        </div>
    @endif
    
    @if($errors->any())
        <div class="alert alert-danger fw-medium small rounded-3 mb-4">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="auth-field mb-3">
            <label class="form-label">Email đăng ký</label>
            <div class="auth-input-wrap">
                <i class="bi bi-envelope"></i>
                <input type="email" name="email" class="form-control custom-input bg-light" value="{{ request()->email }}" readonly required>
            </div>
        </div>

        <div class="auth-field mb-3">
            <label class="form-label">Mật khẩu mới</label>
            <div class="input-group auth-input-wrap auth-password-wrap">
                <i class="bi bi-lock"></i>
                <input type="password" name="password" class="form-control custom-input border-end-0" placeholder="Nhập ít nhất 8 ký tự" required autofocus>
                <span class="input-group-text bg-white border-start-0 custom-input">
                    <i class="bi bi-eye toggle-password" style="cursor: pointer;"></i>
                </span>
            </div>
        </div>

        <div class="auth-field mb-4">
            <label class="form-label">Xác nhận mật khẩu</label>
            <div class="input-group auth-input-wrap auth-password-wrap">
                <i class="bi bi-shield-check"></i>
                <input type="password" name="password_confirmation" class="form-control custom-input border-end-0" placeholder="Nhập lại mật khẩu mới" required>
                <span class="input-group-text bg-white border-start-0 custom-input">
                    <i class="bi bi-eye toggle-password" style="cursor: pointer;"></i>
                </span>
            </div>
        </div>

        <button type="submit" id="btnSubmit" class="btn btn-primary-custom w-100 d-flex align-items-center justify-content-center gap-2">
            Xác nhận đổi mật khẩu <i class="bi bi-check2-circle"></i>
        </button>
    </form>
@endsection
