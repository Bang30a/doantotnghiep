<aside class="modern-sidebar" id="sidebar">
    
    <div class="sidebar-header p-4 d-flex align-items-center gap-3">
        <!-- Đổi màu icon Admin sang tone đỏ cho ngầu -->
        <div class="sidebar-logo bg-danger-subtle text-danger rounded-4 d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px; min-width: 48px; font-size: 1.5rem;">
            <i class="bi bi-shield-lock-fill"></i>
        </div>
        <div class="sidebar-logo-text overflow-hidden">
            <h5 class="mb-0 fw-bold theme-text-dark text-truncate" style="max-width: 160px;" title="{{ $globalSettings['site_name'] ?? 'EduQuiz AI' }}">
                {{ $globalSettings['site_name'] ?? 'EduQuiz AI' }}
            </h5>
            <small class="text-danger fw-bold text-uppercase letter-spacing-1" style="font-size: 0.7rem;">Super Admin</small>
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
                <span class="text-muted fw-bold text-uppercase letter-spacing-1 opacity-75" style="font-size: 0.7rem;">Quản lý tài khoản</span>
            </li>
            <li class="mb-1">
                <a href="{{ route('admin.teachers') }}" class="sidebar-link {{ request()->routeIs('admin.teachers*') ? 'active' : '' }}">
                    <i class="bi bi-person-workspace"></i> <span class="menu-text">Quản lý Giảng viên</span>
                </a>
            </li>
            <li class="mb-1">
                <a href="{{ route('admin.students') }}" class="sidebar-link {{ request()->routeIs('admin.students*') ? 'active' : '' }}">
                    <i class="bi bi-person-badge"></i> <span class="menu-text">Quản lý Học viên</span>
                </a>
            </li>

            <li class="mt-4 mb-2 px-3 sidebar-heading">
                <span class="text-muted fw-bold text-uppercase letter-spacing-1 opacity-75" style="font-size: 0.7rem;">Kiểm duyệt nội dung</span>
            </li>
            <li class="mb-1">
                <a href="{{ route('admin.classrooms') }}" class="sidebar-link {{ request()->routeIs('admin.classrooms*') ? 'active' : '' }}">
                    <i class="bi bi-diagram-3"></i> <span class="menu-text">Tất cả lớp học</span>
                </a>
            </li>
            <li class="mb-1">
                <a href="{{ route('admin.exams') }}" class="sidebar-link {{ request()->routeIs('admin.exams*') ? 'active' : '' }}">
                    <i class="bi bi-journal-text"></i> <span class="menu-text">Kho đề thi chung</span>
                </a>
            </li>
            <li class="mb-1">
                <a href="{{ route('admin.documents') }}" class="sidebar-link {{ request()->routeIs('admin.documents*') ? 'active' : '' }}">
                    <i class="bi bi-folder-symlink"></i> <span class="menu-text">Tài liệu hệ thống</span>
                </a>
            </li>

            <li class="mt-4 mb-2 px-3 sidebar-heading">
                <span class="text-muted fw-bold text-uppercase letter-spacing-1 opacity-75" style="font-size: 0.7rem;">Hệ thống AI & API</span>
            </li>
            <li class="mb-1">
                <a href="{{ route('admin.prompts') }}" class="sidebar-link {{ request()->routeIs('admin.prompts*') ? 'active' : '' }}">
                    <i class="bi bi-robot"></i> <span class="menu-text">Cấu hình AI (Prompts)</span>
                </a>
            </li>
            <li class="mb-1">
                <a href="{{ route('admin.ai_history') }}" class="sidebar-link {{ request()->routeIs('admin.ai_history*') ? 'active' : '' }}">
                    <i class="bi bi-activity"></i> <span class="menu-text">Lịch sử dùng AI</span>
                </a>
            </li>

            <li class="mt-4 mb-2 px-3 sidebar-heading">
                <span class="text-muted fw-bold text-uppercase letter-spacing-1 opacity-75" style="font-size: 0.7rem;">Hệ thống</span>
            </li>
            <li class="mb-1">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart-line"></i> <span class="menu-text">Thống kê tổng quan</span>
                </a>
            </li>
            <li class="mb-1">
                <a href="{{ route('admin.settings') ?? '#' }}" class="sidebar-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                    <i class="bi bi-gear"></i> <span class="menu-text">Cài đặt chung</span>
                </a>
            </li>
        </ul>
    </div>
</aside>