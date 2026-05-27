<header class="modern-tophead d-flex justify-content-between align-items-center">
    
    <div class="d-flex align-items-center gap-3 theme-text-dark">
        <button class="toggle-btn shadow-sm me-2" id="toggleSidebar">
            <i class="bi bi-list fs-5"></i>
        </button>

        <div class="bg-purple-light theme-text-primary rounded-3 d-none d-md-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
            <i class="bi bi-layout-sidebar-inset fs-5"></i>
        </div>

        <span class="fw-bold fs-5">
            {{ html_entity_decode($pageTitle ?? 'Bảng điều khiển') }}
        </span>
    </div>

    <div class="dropdown">
        <button class="btn border-0 p-0 d-flex align-items-center gap-2 bg-transparent shadow-none" type="button" id="topMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
            <div class="text-end d-none d-md-block pe-1">
                <div class="fw-bold theme-text-dark" style="font-size: 0.95rem; line-height: 1.2;">
                    {{ Auth::user()->name ?? 'Người dùng' }}
                </div>
                
                <div class="{{ isset($roleName) && $roleName == 'Quản trị viên' ? 'text-danger' : 'theme-text-primary' }} fw-bold text-uppercase letter-spacing-1" style="font-size: 0.7rem; opacity: 0.85;">
                    {{ $roleName ?? 'Thành viên' }}
                </div>
            </div>

            @php
                $isAdmin = isset($roleName) && $roleName == 'Quản trị viên';
                $bgAvatar = $isAdmin ? 'FEE2E2' : 'F5F3FF';
                $colorAvatar = $isAdmin ? 'DC2626' : '7C3AED';
                $borderColor = $isAdmin ? '#DC2626' : '#7C3AED';
                
                $settingsLink = '#';

                if (Auth::check()) {
                    $role = Auth::user()->role;

                    if ($role === 'admin') {
                        $settingsLink = route('admin.settings');
                    } elseif ($role === 'teacher') {
                        $settingsLink = route('teacher.settings.edit');
                    } elseif ($role === 'student') {
                        $settingsLink = route('student.settings.edit');
                    }
                }
            @endphp

            <div class="position-relative">
                <img src="{{ Auth::user()->avatar ? asset(Auth::user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name ?? 'User').'&background='.$bgAvatar.'&color='.$colorAvatar }}" 
                     alt="Avatar" 
                     class="rounded-circle shadow-sm bg-white" 
                     style="width: 46px; height: 46px; object-fit: cover; border: 2px solid {{ $borderColor }}; padding: 2px; transition: transform 0.2s;">
            </div>

            <i class="bi bi-chevron-down text-muted ms-1" style="font-size: 0.8rem;"></i>
        </button>
        
        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-3" style="min-width: 240px; border-radius: 16px; padding: 12px;">
            <li class="px-3 py-2 mb-2 d-md-none bg-purple-light rounded-3 border-0">
                <div class="fw-bold theme-text-dark">{{ Auth::user()->name ?? 'Người dùng' }}</div>
                <div class="theme-text-primary small fw-medium">{{ Auth::user()->email ?? 'email@example.com' }}</div>
            </li>
            
            <li>
                <a class="dropdown-item py-2 px-3 rounded-3 d-flex align-items-center gap-3 fw-medium" href="{{ $settingsLink }}" style="transition: all 0.2s;">
                    <div class="icon-box bg-purple-light {{ $isAdmin ? 'text-secondary' : 'theme-text-primary' }} rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="bi bi-gear-fill"></i>
                    </div>
                    Cài đặt cá nhân
                </a>
            </li>
            
            <li><hr class="dropdown-divider my-2 opacity-50 border-0"></li>
            
            <li>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="dropdown-item py-2 px-3 rounded-3 d-flex align-items-center gap-3 text-danger fw-bold" style="transition: all 0.2s;">
                        <div class="icon-box bg-danger-subtle text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="bi bi-box-arrow-right"></i>
                        </div>
                        Đăng xuất
                    </button>
                </form>
            </li>
        </ul>
    </div>
</header>