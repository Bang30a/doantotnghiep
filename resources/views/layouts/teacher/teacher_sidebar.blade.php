<aside class="modern-sidebar" id="sidebar">
    
    <div class="sidebar-header p-4 d-flex align-items-center gap-3">
        <div class="sidebar-logo bg-purple-light theme-text-primary rounded-4 d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px; min-width: 48px; font-size: 1.5rem;">
            <i class="bi bi-robot"></i>
        </div>
        <div class="sidebar-logo-text overflow-hidden">
            <h5 class="mb-0 fw-bold theme-text-dark text-truncate" style="max-width: 160px;" title="{{ $globalSettings['site_name'] ?? 'EduQuiz AI' }}">
                {{ $globalSettings['site_name'] ?? 'EduQuiz AI' }}
            </h5>
            <small class="text-danger fw-bold text-uppercase letter-spacing-1" style="font-size: 0.7rem;">Giảng viên</small>
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
                <span class="text-muted fw-bold text-uppercase letter-spacing-1 opacity-75" style="font-size: 0.7rem;">Quản lý đào tạo</span>
            </li>
            <li class="mb-1">
                <a href="{{ route('teacher.classrooms') }}" class="sidebar-link {{ request()->routeIs('teacher.classrooms*') ? 'active' : '' }}">
                    <i class="bi bi-diagram-3-fill"></i> <span class="menu-text">Quản lý lớp học</span>
                </a>
            </li>
            <li class="mb-1">
                <a href="{{ route('teacher.students.index') }}" class="sidebar-link {{ request()->routeIs('teacher.students*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i> <span class="menu-text">Quản lý học viên</span>
                </a>
            </li>
            
            <li class="mt-4 mb-2 px-3 sidebar-heading">
                <span class="text-muted fw-bold text-uppercase letter-spacing-1 opacity-75" style="font-size: 0.7rem;">Học liệu & Đề thi</span>
            </li>
            <li class="mb-1">
                <a href="{{ route('teacher.documents.index') }}" class="sidebar-link {{ request()->routeIs('teacher.documents*') ? 'active' : '' }}">
                    <i class="bi bi-folder-fill"></i> <span class="menu-text">Kho tài liệu</span>
                </a>
            </li>
            <li class="mb-1">
                <a href="{{ route('teacher.exams.create') }}" class="sidebar-link {{ request()->routeIs('teacher.exams.create*') ? 'active' : '' }}">
                    <i class="bi bi-magic text-warning"></i> <span class="menu-text">Tạo đề AI</span>
                </a>
            </li>
            <li class="mb-1">
                <a href="{{ route('teacher.exams.index') }}" class="sidebar-link {{ request()->routeIs('teacher.exams.index*') ? 'active' : '' }}">
                    <i class="bi bi-collection-play-fill"></i> <span class="menu-text">Danh sách đề thi</span>
                </a>
            </li>

            <li class="mt-4 mb-2 px-3 sidebar-heading">
                <span class="text-muted fw-bold text-uppercase letter-spacing-1 opacity-75" style="font-size: 0.7rem;">Báo cáo & Hệ thống</span>
            </li>
            <li class="mb-1">
                <a href="{{ route('teacher.reports.index') }}" class="sidebar-link {{ request()->routeIs('teacher.reports.index') ? 'active' : '' }}">
                    <i class="bi bi-clipboard-data-fill"></i> <span class="menu-text">Sổ điểm & Báo cáo</span>
                </a>
            </li>
            <li class="mb-1">
                <a href="{{ route('teacher.statistics') }}" class="sidebar-link {{ request()->routeIs('teacher.statistics*') ? 'active' : '' }}">
                    <i class="bi bi-pie-chart-fill"></i> <span class="menu-text">Thống kê</span>
                </a>
            </li>
        </ul>
    </div>
</aside>