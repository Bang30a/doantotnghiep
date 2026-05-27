@extends('layouts.auth_app')

@section('title', 'Quên mật khẩu')

<!-- TỰ ĐỘNG THAY ĐỔI BANNER TRÁI CHO TRANG NÀY -->
@section('banner_content')
    <h1 class="banner-title">Phục hồi quyền truy cập<br>của bạn</h1>
    <p class="fs-5 opacity-90 fw-medium mb-5">
        Đừng lo lắng! Chỉ vài bước đơn giản để lấy lại mật khẩu và tiếp tục hành trình học tập cùng {{ $globalSettings['site_name'] ?? 'EduQuiz AI' }}.
    </p>
@endsection

@section('content')
    <a href="{{ route('login') }}" class="text-muted text-decoration-none fw-bold small d-inline-flex align-items-center gap-2 mb-4 text-link" style="color: #64748B;">
        <i class="bi bi-arrow-left"></i> Quay lại đăng nhập
    </a>

    <h2 class="auth-title h3">Quên mật khẩu?</h2>
    <p class="auth-subtitle mb-4">Nhập email liên kết với tài khoản của bạn, chúng tôi sẽ gửi liên kết để đặt lại mật khẩu.</p>

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

    <form method="POST" action="{{ route('password.email') }}" onsubmit="document.getElementById('btnSubmit').disabled = true; document.getElementById('btnSubmit').innerHTML = '<i class=\'bi bi-hourglass-split\'></i> Đang gửi email...';">
    @csrf
        
        <div class="mb-4">
            <label class="form-label">Email đăng ký</label>
            <div class="input-icon-group">
                <i class="bi bi-envelope position-absolute text-muted" style="left: 15px; top: 50%; transform: translateY(-50%); z-index: 10;"></i>
                <input type="email" name="email" class="form-control custom-input" placeholder="name@example.com" style="padding-left: 2.5rem;" required autofocus>
            </div>
        </div>

        <button type="submit" id="btnSubmit" class="btn w-100" style="background-color: #4c1d95; color: white;">
        Gửi liên kết đặt lại
    </button>
</form>
@endsection