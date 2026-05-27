@extends('layouts.admin.admin_app')

@section('title', 'Quản lý Giảng viên')

@push('styles')
    <!-- Link file CSS riêng -->
    <link rel="stylesheet" href="{{ asset('css/admin/admin_teacher.css') }}?v={{ time() }}">
@endpush

@section('content')
    <!-- Tiêu đề trang & Thanh công cụ -->
    <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-end mb-4 gap-3 mt-2 border-bottom border-light-subtle pb-3">
        <div>
            <h3 class="fw-800 text-dark mb-1 d-flex align-items-center gap-2">
                Quản lý Giảng viên <i class="bi bi-person-workspace theme-text-primary"></i>
            </h3>
            <p class="text-muted fw-medium mb-0">Danh sách các giảng viên đang hoạt động trên hệ thống</p>
        </div>
        
        <div class="d-flex flex-wrap align-items-center gap-3">
            <form action="{{ route('admin.teachers') }}" method="GET" class="d-flex gap-2">
                <div class="input-group search-focus shadow-sm rounded-4 border overflow-hidden transition-all bg-white" style="border-radius: 12px;">
                    <span class="input-group-text bg-white border-0 text-muted pe-1 ps-3"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-0 fw-medium shadow-none py-2" placeholder="Tìm tên, email..." value="{{ request('search') }}">
                </div>
                <button type="submit" class="btn btn-theme-primary fw-bold px-4 rounded-4 shadow-sm hover-pulse text-white" style="border-radius: 12px;">
                    <i class="bi bi-funnel-fill me-1"></i> Lọc
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.teachers') }}" class="btn btn-light fw-bold text-danger border rounded-4 px-3 transition-all" title="Xóa tìm kiếm" style="border-radius: 12px;">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Thông báo Thành công -->
    @if(session('success'))
        <div class="alert alert-emerald-soft alert-dismissible fade show mb-4 border border-emerald-subtle shadow-sm rounded-4 d-flex align-items-center px-4 py-3 auto-close-alert" role="alert" id="success-alert">
            <i class="bi bi-check-circle-fill fs-4 me-3 text-emerald"></i> 
            <span class="fw-bold fs-6">{{ session('success') }}</span>
            <button type="button" class="btn-close mt-2 me-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Bảng dữ liệu bọc trong Card sang trọng -->
    <div class="card border-0 shadow rounded-4 mb-4 bg-white" style="border-radius: 16px !important;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table custom-table align-middle mb-0">
                    <thead class="bg-light border-bottom border-light-subtle">
                        <tr>
                            <th width="35%" class="ps-4 py-3 text-muted fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Thông tin Giảng viên</th>
                            <th width="20%" class="py-3 text-muted fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Số điện thoại</th>
                            <th width="20%" class="py-3 text-muted fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Ngày tham gia</th>
                            <th width="15%" class="text-center py-3 text-muted fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Trạng thái</th>
                            <th width="10%" class="text-end pe-4 py-3 text-muted fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teachers as $teacher)
                            @php
                                // Đã sửa lại logic kiểm tra trạng thái khớp với database
                                $isLocked = (isset($teacher->status) && $teacher->status === 'locked');
                            @endphp
                            <tr class="hover-row transition-all border-bottom border-light-subtle">
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-container shadow-sm border border-2 border-white" style="width: 45px; height: 45px;">
                                            <img src="{{ $teacher->avatar ? asset($teacher->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($teacher->name).'&background=E0E7FF&color=4F46E5' }}" alt="Avatar" class="w-100 h-100 rounded-circle" style="object-fit: cover;">
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1 fs-6">{{ $teacher->name }}</h6>
                                            <p class="text-muted small mb-0 fw-medium d-flex align-items-center"><i class="bi bi-envelope me-2 opacity-75"></i>{{ $teacher->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <span class="text-dark fw-medium d-flex align-items-center">
                                        <i class="bi bi-telephone text-muted me-2 opacity-75"></i> {{ $teacher->phone ?? 'Chưa cập nhật' }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    <span class="text-muted fw-medium d-flex align-items-center gap-2">
                                        <div class="bg-light rounded p-1"><i class="bi bi-calendar-event text-secondary"></i></div> 
                                        {{ $teacher->created_at->format('d/m/Y') }}
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
                                        <button class="btn btn-light btn-action-custom border shadow-sm rounded-circle d-inline-flex align-items-center justify-content-center transition-all" type="button" data-bs-toggle="dropdown" style="width: 38px; height: 38px;">
                                            <i class="bi bi-three-dots-vertical text-muted fw-bold icon-action transition-all"></i>
                                        </button>          
                                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2" style="border-radius: 12px; min-width: 200px;">
                                            
                                            <!-- ĐÃ GỘP: Nút Xem / Sửa hồ sơ -->
                                            <li>
                                                <a class="dropdown-item py-2 px-3 fw-medium text-dark d-flex align-items-center gap-2 hover-bg-light" href="{{ route('admin.teachers.show', $teacher->id) }}">
                                                    <i class="bi bi-pencil-square text-primary fs-5"></i> Xem / Sửa hồ sơ
                                                </a>
                                            </li>
                                            
                                            <li><hr class="dropdown-divider opacity-10 mx-3 my-1"></li>
                                            
                                            <!-- Nút Khóa / Mở khóa tài khoản -->
                                            <li>
                                                <form action="{{ route('admin.teachers.toggle_lock', $teacher->id) }}" method="POST" class="d-inline m-0 p-0 form-toggle-lock">
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
                                <td colspan="5" class="text-center py-5 bg-light">
                                    <div class="empty-icon-wrapper bg-white text-muted rounded-circle mx-auto mb-3 shadow-sm d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                                        <i class="bi bi-person-x"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-2">Trống trơn!</h5>
                                    <p class="text-muted fw-medium mb-0">Hệ thống chưa ghi nhận tài khoản giảng viên hoặc không tìm thấy kết quả.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Phân trang -->
    @if(isset($teachers) && method_exists($teachers, 'links'))
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 mb-5">
            
            <!-- Khối thông tin và Chọn số lượng hiển thị -->
            <div class="d-flex align-items-center gap-3 bg-white px-4 py-2 rounded-pill shadow-sm border border-light-subtle">
                <span class="text-muted small fw-medium">
                    Đang hiển thị <strong class="text-dark">{{ $teachers->firstItem() ?? 0 }} - {{ $teachers->lastItem() ?? 0 }}</strong> trên tổng <strong class="text-dark">{{ $teachers->total() }}</strong>
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

            <!-- Khối nút phân trang -->
            <div class="custom-pagination shadow-sm rounded-pill overflow-hidden bg-white px-2">
                {{ $teachers->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
            
        </div>
    @endif
@endsection

@push('scripts')
    <!-- Link file JS riêng -->
    <script src="{{ asset('js/admin/admin_teacher.js') }}?v={{ time() }}"></script>
@endpush