<aside class="modern-sidebar" id="sidebar">
    
    <div class="sidebar-header p-4 d-flex align-items-center gap-3">
        <div class="sidebar-logo bg-purple-light theme-text-primary rounded-4 d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px; min-width: 48px; font-size: 1.5rem;">
            <i class="bi bi-robot"></i> 
        </div>
        <div class="sidebar-logo-text overflow-hidden">
            <h5 class="mb-0 fw-bold theme-text-dark text-truncate" style="max-width: 160px;" title="{{ $globalSettings['site_name'] ?? 'EduQuiz AI' }}">
                {{ $globalSettings['site_name'] ?? 'EduQuiz AI' }}
            </h5>
            <small class="theme-text-primary fw-bold text-uppercase letter-spacing-1" style="font-size: 0.7rem;">Học viên</small>
        </div>
    </div>

    <div class="sidebar-body overflow-auto p-3 flex-grow-1 custom-scrollbar">
        <ul class="sidebar-menu list-unstyled mb-0">
            <li class="mb-1">
                <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-house-door-fill"></i> <span class="menu-text">Trang chủ</span>
                </a>
            </li>

            <li class="mt-4 mb-2 px-3 sidebar-heading">
                <span class="text-muted fw-bold text-uppercase letter-spacing-1 opacity-75" style="font-size: 0.7rem;">Lớp học & Nhiệm vụ</span>
            </li>
            <li class="mb-1">
                <a href="{{ route('student.classrooms') }}" class="sidebar-link {{ request()->routeIs('student.classrooms*') ? 'active' : '' }}">
                    <i class="bi bi-mortarboard-fill"></i> <span class="menu-text">Lớp học của tôi</span>
                </a>
            </li>

            <li class="mt-4 mb-2 px-3 sidebar-heading">
                <span class="text-muted fw-bold text-uppercase letter-spacing-1 opacity-75" style="font-size: 0.7rem;">Góc tự học AI</span>
            </li>
            <li class="mb-1">
                <a href="{{ route('student.exams.create') }}" class="sidebar-link {{ request()->routeIs('student.exams.create') ? 'active' : '' }}">
                    <i class="bi bi-magic text-warning"></i> <span class="menu-text">Tạo đề tự luyện</span>
                </a>
            </li>
            <li class="mb-1">
                <a href="{{ route('student.documents') }}" class="sidebar-link {{ request()->routeIs('student.documents*') ? 'active' : '' }}">
                    <i class="bi bi-folder-fill"></i> <span class="menu-text">Kho tài liệu</span>
                </a>
            </li>
            <li class="mb-1">
                <a href="{{ route('student.question-banks') }}" class="sidebar-link {{ request()->routeIs('student.question-banks*') ? 'active' : '' }}">
                    <i class="bi bi-collection-play-fill"></i> <span class="menu-text">Ngân hàng đề</span>
                </a>
            </li>

            <li class="mt-4 mb-2 px-3 sidebar-heading">
                <span class="text-muted fw-bold text-uppercase letter-spacing-1 opacity-75" style="font-size: 0.7rem;">Cá nhân</span>
            </li>
            <li class="mb-1">
                <a href="{{ route('student.history') }}" class="sidebar-link {{ request()->routeIs('student.history*') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i> <span class="menu-text">Lịch sử làm bài</span>
                </a>
            </li>
            <li class="mb-1">
                <a href="{{ route('student.statistics') }}" class="sidebar-link {{ request()->routeIs('student.statistics*') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart-fill"></i> <span class="menu-text">Thống kê</span>
                </a>
            </li>
        </ul>
    </div>
</aside>