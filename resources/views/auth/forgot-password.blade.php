@extends('layouts.auth_app')

@section('title', 'Quên mật khẩu')

<!-- TỰ ĐỘNG THAY ĐỔI BANNER TRÁI CHO TRANG NÀY -->
@section('banner_content')
    <div class="auth-kicker">
        <i class="bi bi-shield-lock"></i>
        Khôi phục tài khoản
    </div>
    <h1 class="banner-title">Phục hồi quyền truy cập<br>của bạn</h1>
    <p class="banner-copy">
        Đừng lo lắng! Chỉ vài bước đơn giản để lấy lại mật khẩu và tiếp tục hành trình học tập cùng {{ $globalSettings['site_name'] ?? 'EduQuiz AI' }}.
    </p>
    <div class="auth-signal-grid">
        <div class="auth-signal-card">
            <span><i class="bi bi-envelope-check"></i></span>
            <div>
                <strong>Nhận liên kết</strong>
                <small>Gửi qua email đăng ký</small>
            </div>
        </div>
        <div class="auth-signal-card">
            <span><i class="bi bi-lock"></i></span>
            <div>
                <strong>Bảo mật phiên</strong>
                <small>Liên kết đặt lại riêng tư</small>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <a href="{{ route('login') }}" class="auth-back-link">
        <i class="bi bi-arrow-left"></i> Quay lại đăng nhập
    </a>

    <div class="auth-card-heading">
        <div class="auth-form-icon"><i class="bi bi-key"></i></div>
        <div>
            <h2 class="auth-title h3">Quên mật khẩu?</h2>
            <p class="auth-subtitle">Nhập email liên kết với tài khoản để nhận liên kết đặt lại mật khẩu.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success fw-medium small rounded-3 mb-4">
            <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
        </div>
    @endif
    
    @if($errors->any())
        <div class="alert alert-danger fw-medium small rounded-3 mb-4">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        
        <div class="auth-field mb-4">
            <label class="form-label">Email đăng ký</label>
            <div class="auth-input-wrap">
                <i class="bi bi-envelope"></i>
                <input type="email" name="email" class="form-control custom-input" placeholder="name@example.com" value="{{ old('email') }}" required autofocus>
            </div>
        </div>

        <button type="submit" id="btnSubmit" class="btn btn-primary-custom w-100 d-flex align-items-center justify-content-center gap-2">
            Gửi liên kết đặt lại <i class="bi bi-send"></i>
        </button>
    </form>
@endsection
