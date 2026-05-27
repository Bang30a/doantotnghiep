@extends('layouts.auth_app')

@section('title', $mode == 'login' ? 'Đăng nhập' : 'Đăng ký')

@section('content')
    <div class="text-center d-lg-none mb-4">
        <i class="bi bi-mortarboard-fill fs-1 text-purple"></i>
        <h2 class="fw-900 mt-2 text-dark">{{ $globalSettings['site_name'] ?? 'EduQuiz AI' }}</h2>
    </div>

    @if ($errors->any())
    
        <div class="alert alert-danger rounded-3 fw-medium small mb-4">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger rounded-3 fw-medium small mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success rounded-3 fw-medium small mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div id="login-section" class="form-section {{ $mode == 'login' ? 'form-active' : '' }}">
        <h2 class="auth-title h3">Chào mừng trở lại!</h2>
        <p class="auth-subtitle">Đăng nhập để tiếp tục quá trình học tập của bạn</p>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="role-toggle btn-group w-100" role="group">
                <input type="radio" class="btn-check" name="role" id="role_student_login" value="student" autocomplete="off" checked>
                <label class="btn" for="role_student_login">Học viên</label>
                <input type="radio" class="btn-check" name="role" id="role_teacher_login" value="teacher" autocomplete="off">
                <label class="btn" for="role_teacher_login">Giảng viên</label>
            </div>

            <div class="mb-4">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control custom-input" placeholder="name@example.com" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="form-label mb-0">Mật khẩu</label>
                    <a href="{{ route('password.request') }}" class="text-link small fw-bold">Quên mật khẩu?</a>
                </div>
                <div class="input-group">
                    <input type="password" name="password" class="form-control custom-input border-end-0" placeholder="••••••••" required>
                    <span class="input-group-text bg-white border-start-0 custom-input">
                        <i class="bi bi-eye toggle-password" style="cursor: pointer;"></i>
                    </span>
                </div>
            </div>

            <div class="form-check mb-3 d-flex align-items-center">
                <input class="form-check-input me-2" type="checkbox" name="remember" id="rememberMe">
                <label class="form-check-label text-muted" for="rememberMe" style="cursor: pointer;">
                    Ghi nhớ đăng nhập
                </label>
            </div>

            <button type="submit" class="btn btn-primary-custom mb-4 d-flex justify-content-center align-items-center gap-2">
                Đăng nhập <i class="bi bi-arrow-right"></i>
            </button>
        </form>

        <div class="divider">Hoặc tiếp tục với</div>
        
        <div class="mb-4">
            <a href="{{ route('social.redirect', ['provider' => 'google']) }}" class="btn-social text-decoration-none text-dark w-100 justify-content-center">
                <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="Google"> 
                Tiếp tục với Google
            </a>
        </div>

        <p class="text-center text-muted fw-medium mb-0">
            Chưa có tài khoản? <a href="{{ route('register') }}" data-target="register" class="text-link switch-form-btn">Đăng ký ngay</a>
        </p>
    </div>


    <div id="register-section" class="form-section {{ $mode == 'register' ? 'form-active' : '' }}">
        <h2 class="auth-title h3">Tạo tài khoản mới</h2>
        <p class="auth-subtitle">Đăng ký để trải nghiệm các tính năng tuyệt vời</p>

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="role-toggle btn-group w-100" role="group">
                <input type="radio" class="btn-check" name="role" id="role_student_reg" value="student" autocomplete="off" checked>
                <label class="btn" for="role_student_reg">Học viên</label>
                <input type="radio" class="btn-check" name="role" id="role_teacher_reg" value="teacher" autocomplete="off">
                <label class="btn" for="role_teacher_reg">Giảng viên</label>
            </div>

            <div class="mb-3">
                <label class="form-label">Họ và tên</label>
                <input type="text" name="name" class="form-control custom-input" placeholder="Nguyễn Văn A" value="{{ old('name') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control custom-input" placeholder="name@example.com" value="{{ old('email') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Mật khẩu</label>
                <div class="input-group">
                    <input type="password" name="password" class="form-control custom-input border-end-0" placeholder="••••••••" required>
                    <span class="input-group-text bg-white border-start-0 custom-input">
                        <i class="bi bi-eye toggle-password" style="cursor: pointer;"></i>
                    </span>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Xác nhận mật khẩu</label>
                <div class="input-group">
                    <input type="password" name="password_confirmation" class="form-control custom-input border-end-0" placeholder="••••••••" required>
                    <span class="input-group-text bg-white border-start-0 custom-input">
                        <i class="bi bi-eye toggle-password" style="cursor: pointer;"></i>
                    </span>
                </div>
            </div>

            <button type="submit" class="btn btn-primary-custom mb-4 d-flex justify-content-center align-items-center gap-2">
                Đăng ký ngay <i class="bi bi-arrow-right"></i>
            </button>
        </form>

        <div class="divider">Hoặc tiếp tục với</div>
        
        <div class="mb-4">
            <a href="{{ route('social.redirect', ['provider' => 'google']) }}" class="btn-social text-decoration-none text-dark w-100 justify-content-center">
                <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="Google"> 
                Tiếp tục với Google
            </a>
        </div>

        <p class="text-center text-muted fw-medium mb-0">
            Đã có tài khoản? <a href="{{ route('login') }}" data-target="login" class="text-link switch-form-btn">Đăng nhập</a>
        </p>
    </div>
@endsection