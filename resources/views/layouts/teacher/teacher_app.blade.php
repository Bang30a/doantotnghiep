<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="session-timeout" content="{{ $globalSettings['session_timeout'] ?? 60 }}">

    <title>@yield('title') - {{ $globalSettings['site_name'] ?? 'EduQuiz AI' }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/teacher/teacher_dashboard.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/topheader.css') }}?v={{ time() }}">

    @stack('styles')
</head>

<body class="bg-body-custom">

    @include('layouts.teacher.teacher_sidebar')

    <main class="main-wrapper" id="mainWrapper">
        @php
            $headerTitle = View::hasSection('title')
                ? html_entity_decode(View::getSection('title'))
                : ($pageTitle ?? 'Trang chủ');
        @endphp

        @include('layouts.top_header', [
            'pageTitle' => $headerTitle,
            'roleName' => 'Giảng viên'
        ])

        <div class="content-area">
            @yield('content')
        </div>
    </main>

    <form id="auto-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script src="{{ asset('js/sidebar.js') }}?v={{ time() }}"></script>

    <script>
        window.refreshCsrfUrl = "{{ route('refresh.csrf') }}";
        window.loginUrl = "{{ route('login') }}";
        window.autoLogoutUrl = "{{ url('/auto-logout') }}";
    </script>

    <script src="{{ asset('js/session_timeout.js') }}?v={{ time() }}"></script>

    @stack('scripts')
</body>
</html>