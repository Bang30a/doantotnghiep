@extends('layouts.auth_app')

@section('title', 'Tạo mật khẩu mới')

@section('banner_content')
    <h1 class="banner-title">Tạo mật khẩu<br>mới</h1>
    <p class="fs-5 opacity-90 fw-medium mb-5">
        Chỉ còn một bước nữa thôi! Hãy thiết lập một mật khẩu mới an toàn để tiếp tục hành trình học tập cùng {{ $globalSettings['site_name'] ?? 'EduQuiz AI' }}.
    </p>
@endsection

@section('content')
    <a href="{{ route('login') }}" class="text-muted text-decoration-none fw-bold small d-inline-flex align-items-center gap-2 mb-4 text-link" style="color: #64748B;">
        <i class="bi bi-arrow-left"></i> Quay lại đăng nhập
    </a>

    <h2 class="auth-title h3">Bảo mật tài khoản</h2>
    <p class="auth-subtitle mb-4">Vui lòng nhập mật khẩu mới cho tài khoản của bạn.</p>

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

    <form method="POST" action="{{ route('password.update') }}" onsubmit="document.getElementById('btnSubmit').disabled = true; document.getElementById('btnSubmit').innerHTML = '<i class=\'bi bi-hourglass-split\'></i> Đang cập nhật...';">
        @csrf
        
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="mb-3 position-relative">
            <label class="form-label">Email đăng ký</label>
            <div class="input-icon-group position-relative">
                <i class="bi bi-envelope position-absolute text-muted" style="left: 15px; top: 50%; transform: translateY(-50%); z-index: 10;"></i>
                <input type="email" name="email" class="form-control custom-input bg-light" value="{{ request()->email }}" style="padding-left: 2.5rem;" readonly required>
            </div>
        </div>

        <div class="mb-3 position-relative">
            <label class="form-label">Mật khẩu mới</label>
            <div class="input-icon-group position-relative">
                <i class="bi bi-lock position-absolute text-muted" style="left: 15px; top: 50%; transform: translateY(-50%); z-index: 10;"></i>
                <input type="password" name="password" class="form-control custom-input" placeholder="Nhập ít nhất 8 ký tự" style="padding-left: 2.5rem;" required autofocus>
            </div>
        </div>

        <div class="mb-4 position-relative">
            <label class="form-label">Xác nhận mật khẩu</label>
            <div class="input-icon-group position-relative">
                <i class="bi bi-shield-check position-absolute text-muted" style="left: 15px; top: 50%; transform: translateY(-50%); z-index: 10;"></i>
                <input type="password" name="password_confirmation" class="form-control custom-input" placeholder="Nhập lại mật khẩu mới" style="padding-left: 2.5rem;" required>
            </div>
        </div>

        <button type="submit" id="btnSubmit" class="btn w-100 fw-bold py-2" style="background-color: #4c1d95; color: white; border-radius: 8px;">
            Xác nhận đổi mật khẩu
        </button>
    </form>
@endsection