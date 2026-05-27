@extends('layouts.admin.admin_app')

@section('title', 'Quản lý Học viên')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/admin_student.css') }}?v={{ time() }}">
@endpush

@section('content')

    <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-end mb-4 gap-3 mt-2 border-bottom border-light-subtle pb-3">
        <div>
            <h3 class="fw-800 text-dark mb-1 d-flex align-items-center gap-2">
                Quản lý Học viên <i class="bi bi-person-badge-fill theme-text-primary"></i>
            </h3>
            <p class="text-muted fw-medium mb-0">Theo dõi tiến độ học tập và quản lý tài khoản học viên</p>
        </div>
        
        <div class="d-flex flex-wrap align-items-center gap-3">
            <form action="{{ route('admin.students') }}" method="GET" class="d-flex gap-2">
                <div class="input-group search-focus shadow-sm rounded-4 border overflow-hidden transition-all bg-white" style="border-radius: 12px;">
                    <span class="input-group-text bg-white border-0 text-muted pe-1 ps-3"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-0 fw-medium shadow-none py-2" placeholder="Tìm tên, email..." value="{{ request('search') }}">
                </div>
                <button type="submit" class="btn btn-theme-primary fw-bold px-4 rounded-4 shadow-sm hover-pulse text-white" style="border-radius: 12px;">
                    <i class="bi bi-funnel-fill me-1"></i> Lọc
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.students') }}" class="btn btn-light fw-bold text-danger border rounded-4 px-3 transition-all" title="Xóa tìm kiếm" style="border-radius: 12px;">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-emerald-soft alert-dismissible fade show mb-4 border border-emerald-subtle shadow-sm rounded-4 d-flex align-items-center px-4 py-3" role="alert" id="success-alert">
            <i class="bi bi-check-circle-fill fs-4 me-3 text-emerald"></i> 
            <span class="fw-bold fs-6">{{ session('success') }}</span>
            <button type="button" class="btn-close mt-2 me-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow rounded-4 mb-4 bg-white" style="border-radius: 16px !important;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table custom-table align-middle mb-0">
                    <thead class="bg-light border-bottom border-light-subtle">
                        <tr>
                            <th width="35%" class="ps-4 py-3 text-muted fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Thông tin Học viên</th>
                            <th width="15%" class="text-center py-3 text-muted fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Bài đã làm</th>
                            <th width="15%" class="text-center py-3 text-muted fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Điểm TB</th>
                            <th width="15%" class="text-center py-3 text-muted fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Ngày tham gia</th>
                            <th width="10%" class="text-center py-3 text-muted fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Trạng thái</th>
                            <th width="10%" class="text-end pe-4 py-3 text-muted fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            @php
                                // Đã chuẩn hóa lại: Chỉ kiểm tra chữ 'locked'
                                $isLocked = ($student->status === 'locked');
                            @endphp
                            <tr class="hover-row transition-all border-bottom border-light-subtle">
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-container shadow-sm border border-2 border-white" style="width: 45px; height: 45px;">
                                            <img src="{{ $student->avatar ? asset($student->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($student->name).'&background=F3E8FF&color=7E22CE&size=150' }}" alt="Avatar" class="w-100 h-100 rounded-circle" style="object-fit: cover;">
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1 fs-6 user-name">{{ $student->name }}</h6>
                                            <p class="text-muted small mb-0 fw-medium d-flex align-items-center"><i class="bi bi-envelope me-2 opacity-75"></i>{{ $student->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="text-center py-3">
                                    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-bold shadow-sm">{{ $student->mock_exams_done ?? 0 }} bài</span>
                                </td>
                                
                                <td class="text-center py-3">
                                    @php
                                        $avgScore = $student->mock_avg_score ?? 0;
                                        $scoreClass = $avgScore >= 8 ? 'text-emerald bg-emerald-soft border-emerald-subtle' : ($avgScore >= 5 ? 'text-warning-dark bg-warning-soft border-warning-subtle' : 'text-danger bg-danger-soft border-danger-subtle');
                                    @endphp
                                    
                                    @if($avgScore > 0)
                                        <span class="badge {{ $scoreClass }} rounded-3 px-3 py-2 fs-6 fw-bold border shadow-sm">
                                            {{ number_format($avgScore, 1) }}
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted rounded-3 px-3 py-2 fw-bold border shadow-sm">
                                            Chưa có
                                        </span>
                                    @endif
                                </td>
                                
                                <td class="text-center py-3">
                                    <span class="text-muted fw-medium d-flex align-items-center justify-content-center gap-2">
                                        <div class="bg-light rounded p-1"><i class="bi bi-calendar-event text-secondary"></i></div> 
                                        {{ $student->created_at->format('d/m/Y') }}
                                    </span>
                                </td>
                                
                                <td class="text-center py-3">
                                    @if($isLocked)
                                        <span class="badge bg-danger-soft text-danger border border-danger-subtle d-inline-flex align-items-center gap-1 px-3 py-2 rounded-pill shadow-sm">
                                            <i class="bi bi-lock-fill"></i> Đã khóa
                                        </span>
                                    @else
                                        <span class="badge custom-badge-success d-inline-flex align-items-center gap-1 px-3 py-2 rounded-pill shadow-sm">
                                            <i class="bi bi-check-circle-fill"></i> Hoạt động
                                        </span>
                                    @endif
                                </td>
                                
                                <td class="text-end pe-4 py-3">
                                    <div class="dropdown">
                                        <button class="btn btn-action-hover btn-light border shadow-sm rounded-circle d-inline-flex align-items-center justify-content-center transition-all" type="button" data-bs-toggle="dropdown" style="width: 38px; height: 38px;">
                                            <i class="bi bi-three-dots-vertical text-muted fw-bold"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2" style="border-radius: 12px; min-width: 200px;">
                                            <li><a class="dropdown-item py-2 px-3 fw-medium text-dark d-flex align-items-center gap-2 hover-bg-light" href="{{ route('admin.students.show', $student->id) }}"><i class="bi bi-eye text-primary fs-5"></i> Xem / Sửa hồ sơ</a></li>
                                            <li><hr class="dropdown-divider opacity-10 mx-3 my-1"></li>
                                            <li>
                                                <form action="{{ route('admin.students.toggle_lock', $student->id) }}" method="POST" class="d-inline m-0 p-0 form-toggle-lock">
                                                    @csrf
                                                    <button type="button" class="dropdown-item py-2 px-3 fw-bold d-flex align-items-center gap-2 btn-confirm-lock {{ $isLocked ? 'text-success' : 'text-danger' }}">
                                                        @if($isLocked)
                                                            <i class="bi bi-unlock-fill fs-5"></i> Mở khóa tài khoản
                                                        @else
                                                            <i class="bi bi-lock-fill fs-5"></i> Khóa tài khoản
                                                        @endif
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 bg-light">
                                    <div class="empty-icon-wrapper bg-white text-muted rounded-circle mx-auto mb-3 shadow-sm d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                                        <i class="bi bi-person-x"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-2">Trống trơn!</h5>
                                    <p class="text-muted fw-medium mb-0">Hệ thống chưa ghi nhận tài khoản học viên hoặc không tìm thấy kết quả.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if(isset($students) && method_exists($students, 'links'))
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 mb-5">
            
            <div class="d-flex align-items-center gap-3 bg-white px-4 py-2 rounded-pill shadow-sm border border-light-subtle">
                <span class="text-muted small fw-medium">
                    Đang hiển thị <strong class="text-dark">{{ $students->firstItem() ?? 0 }} - {{ $students->lastItem() ?? 0 }}</strong> trên tổng <strong class="text-dark">{{ $students->total() }}</strong>
                </span>
                
                <div class="vr text-muted opacity-25" style="width: 1px; min-height: 20px;"></div>
                
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted small fw-medium">Hiển thị:</span>
                    <select class="form-select form-select-sm border-light-subtle shadow-none fw-bold text-dark" id="perPageSelect" style="width: 65px; border-radius: 8px; cursor: pointer; background-color: #f8f9fa;">
                        <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>
            </div>

            <div class="custom-pagination shadow-sm rounded-pill overflow-hidden bg-white px-2">
                {{ $students->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
            
        </div>
    @endif

@endsection

@push('scripts')
    <script src="{{ asset('js/admin/admin_student.js') }}?v={{ time() }}"></script>
@endpush