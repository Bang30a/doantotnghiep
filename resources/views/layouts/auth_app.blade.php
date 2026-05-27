<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    
    <title>@yield('title') - {{ $globalSettings['site_name'] ?? 'EduQuiz AI' }}</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}?v={{ time() }}">
</head>
<body>

    <div class="auth-split-layout">
        
        <div class="auth-left-banner">
            <div class="auth-logo">
                <div class="auth-logo-icon">
                    <i class="bi bi-mortarboard-fill text-white fs-5"></i>
                </div>
                {{ $globalSettings['site_name'] ?? 'EduQuiz AI' }}
            </div>
            
            <div class="banner-content">
                @hasSection('banner_content')
                    @yield('banner_content')
                @else
                    <h1 class="banner-title">Nền tảng giáo dục<br>thông minh toàn diện</h1>
                    <ul class="feature-list">
                        <li class="feature-item">
                            <span class="feature-icon"><i class="bi bi-check"></i></span>
                            Hệ thống AI chấm bài & tự động tạo đề
                        </li>
                        <li class="feature-item">
                            <span class="feature-icon"><i class="bi bi-check"></i></span>
                            Trải nghiệm UI tối giản, tốc độ mượt mà
                        </li>
                        <li class="feature-item">
                            <span class="feature-icon"><i class="bi bi-check"></i></span>
                            Quản lý Học viên & Giảng viên dễ dàng
                        </li>
                    </ul>
                @endif
            </div>
            
            <div class="banner-footer">
                &copy; {{ date('Y') }} {{ $globalSettings['site_name'] ?? 'EduQuiz AI' }}. Prototype for Laravel Integration.
            </div>
        </div>

        <div class="auth-right-form">
            <div class="auth-form-card">
                @yield('content')
            </div>
        </div>

    </div>

    <script>
        window.refreshCsrfUrl = "{{ route('refresh.csrf') }}";
        window.loginUrl = "{{ route('login') }}";
    </script>

    <script src="{{ asset('js/auth.js') }}?v={{ time() }}"></script>
</body>
</html>