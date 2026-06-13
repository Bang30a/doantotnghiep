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
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="{{ versioned_asset('css/auth.css') }}">
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
                    <div class="auth-kicker">
                        <i class="bi bi-stars"></i>
                        Không gian học tập AI
                    </div>
                    <h1 class="banner-title">Nền tảng giáo dục<br>thông minh toàn diện</h1>
                    <p class="banner-copy">
                        Tạo đề, luyện tập và theo dõi tiến độ trong một hệ thống gọn, nhanh và dễ dùng.
                    </p>

                    <div class="auth-signal-grid">
                        <div class="auth-signal-card">
                            <span><i class="bi bi-magic"></i></span>
                            <div>
                                <strong>Tạo đề AI</strong>
                                <small>Từ tài liệu học tập</small>
                            </div>
                        </div>
                        <div class="auth-signal-card">
                            <span><i class="bi bi-patch-check"></i></span>
                            <div>
                                <strong>Chấm điểm rõ ràng</strong>
                                <small>Trắc nghiệm và tự luận</small>
                            </div>
                        </div>
                        <div class="auth-signal-card">
                            <span><i class="bi bi-graph-up-arrow"></i></span>
                            <div>
                                <strong>Theo dõi tiến độ</strong>
                                <small>Lớp học, đề thi, kết quả</small>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            
            <div class="banner-footer">
                &copy; {{ date('Y') }} {{ $globalSettings['site_name'] ?? 'EduQuiz AI' }}. Nền tảng học tập và đánh giá bằng AI.
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

    <script src="{{ versioned_asset('js/auth.js') }}"></script>
</body>
</html>
