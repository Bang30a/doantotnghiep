@extends('layouts.teacher.teacher_app')

@section('title', 'Danh sách Đề thi')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/teacher/teacher_list_exams.css') }}?v={{ time() }}">
@endpush

@section('content')

    <!-- Tiêu đề trang -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-3 mt-2 border-bottom border-light-subtle gap-3">
        <div>
            <h3 class="fw-800 text-dark mb-1 d-flex align-items-center gap-2">
                Ngân hàng Đề thi <i class="bi bi-journal-text theme-text-primary"></i>
            </h3>
            <p class="text-muted mb-0 fw-medium">Quản lý toàn bộ đề thi trắc nghiệm và tự luận của bạn</p>
        </div>
        
        <a href="{{ route('teacher.exams.create') }}" class="btn btn-purple-gradient fw-bold rounded-pill px-4 py-2.5 d-inline-flex align-items-center gap-2 transition-all shadow-sm">
            <i class="bi bi-plus-circle-fill"></i> Tạo đề thi mới
        </a>
    </div>

    <!-- Thông báo Thành công -->
    @if(session('success'))
        <div class="alert alert-emerald-soft alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill fs-4 me-3 text-emerald"></i> 
            <div>
                <strong class="d-block mb-1">Thành công!</strong>
                {{ session('success') }}
            </div>
            <button type="button" class="btn-close m-auto me-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Thống kê nhanh -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card hover-lift d-flex align-items-center gap-4 h-100">
                <div class="icon-wrapper purple shadow-sm flex-shrink-0"><i class="bi bi-files"></i></div>
                <div>
                    <p class="text-muted small fw-bold text-uppercase letter-spacing-1 mb-1">Tổng số đề</p>
                    <h2 class="fw-800 text-dark mb-0">{{ $totalExams ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card hover-lift d-flex align-items-center gap-4 h-100">
                <div class="icon-wrapper emerald shadow-sm flex-shrink-0"><i class="bi bi-send-check-fill"></i></div>
                <div>
                    <p class="text-muted small fw-bold text-uppercase letter-spacing-1 mb-1">Đã giao cho lớp</p>
                    <h2 class="fw-800 text-dark mb-0">{{ $assignedExams ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card hover-lift d-flex align-items-center gap-4 h-100">
                <div class="icon-wrapper warning shadow-sm flex-shrink-0"><i class="bi bi-archive-fill"></i></div>
                <div>
                    <p class="text-muted small fw-bold text-uppercase letter-spacing-1 mb-1">Lưu trong Ngân hàng</p>
                    <h2 class="fw-800 text-dark mb-0">{{ $bankExams ?? 0 }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Thanh tìm kiếm -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white search-card">
        <div class="card-body p-3 px-4">
            <form action="{{ route('teacher.exams.index') }}" method="GET" class="d-flex flex-column flex-md-row gap-3">
                <div class="position-relative flex-grow-1">
                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 theme-text-primary fs-5"></i>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-lg custom-search-input rounded-pill ps-5 fw-medium" placeholder="Tìm kiếm theo tên đề thi...">
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-theme-primary px-4 rounded-pill fw-bold shadow-sm">Tìm kiếm</button>
                    @if(request('search'))
                        <a href="{{ route('teacher.exams.index') }}" class="btn btn-light border px-4 rounded-pill fw-bold text-muted transition-all btn-clear">Xóa lọc</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Danh sách Đề thi -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
        <div class="table-responsive">
            <table class="table custom-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th width="35%">Tên bài kiểm tra</th>
                        <th width="15%" class="text-center">Số câu hỏi</th>
                        <th width="15%" class="text-center">Thời gian</th>
                        <th width="20%">Trạng thái / Giao cho</th>
                        <th width="15%" class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $exam)
                        <tr class="transition-all hover-row">
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="icon-box-small bg-purple-light theme-text-primary rounded-3 d-flex align-items-center justify-content-center flex-shrink-0">
                                        <i class="bi bi-file-earmark-text-fill"></i>
                                    </div>
                                    <div>
                                        <a href="{{ route('exams.show', $exam->id) }}" class="exam-title text-dark fw-bold text-decoration-none d-block mb-1">{{ $exam->title }}</a>
                                        <small class="text-muted fw-medium"><i class="bi bi-calendar3 me-1"></i> Tạo ngày: {{ $exam->created_at->format('d/m/Y') }}</small>
                                    </div>
                                </div>
                            </td>
                            
                            <td class="text-center">
                                <span class="badge bg-purple-light theme-text-primary border border-purple-subtle rounded-pill px-3 py-1.5 fw-bold">
                                    {{ $exam->questions_count ?? 0 }} câu
                                </span>
                            </td>
                            
                            <td class="text-center">
                                <span class="text-dark fw-bold"><i class="bi bi-stopwatch-fill text-muted me-1"></i> {{ $exam->duration }} <span class="fw-medium small">phút</span></span>
                            </td>
                            
                            <td>
                                @if($exam->classroom_id)
                                    <span class="badge badge-emerald d-inline-flex align-items-center gap-1 px-3 py-1.5 rounded-pill mb-1">
                                        <i class="bi bi-check-circle-fill"></i> Đã giao lớp
                                    </span>
                                    <div class="small fw-medium text-dark text-truncate" style="max-width: 180px;" title="{{ $exam->classroom->name ?? '' }}">
                                        Lớp: {{ $exam->classroom->name ?? 'Không rõ' }}
                                    </div>
                                @else
                                    <span class="badge badge-warning d-inline-flex align-items-center gap-1 px-3 py-1.5 rounded-pill mb-1">
                                        <i class="bi bi-archive-fill"></i> Ngân hàng đề
                                    </span>
                                    <div class="small fw-medium text-muted">Chưa giao cho lớp nào</div>
                                @endif
                            </td>
                            
                            <td class="text-end">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light border shadow-sm rounded-pill px-3 py-1.5 fw-bold text-muted transition-all hover-purple" type="button" data-bs-toggle="dropdown">
                                        Tùy chọn <i class="bi bi-chevron-down ms-1" style="font-size: 0.7rem;"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4 mt-2">
                                        <li><a class="dropdown-item py-2 fw-medium theme-text-primary" href="{{ route('exams.show', $exam->id) }}"><i class="bi bi-eye-fill me-2"></i> Xem chi tiết</a></li>
                                        <li><a class="dropdown-item py-2 fw-medium text-dark" href="{{ route('teacher.exams.edit', $exam->id) }}"><i class="bi bi-pencil-square me-2 text-muted"></i> Chỉnh sửa đề</a></li>
                                        <li><hr class="dropdown-divider opacity-25"></li>
                                        <li>
                                            <form action="{{ route('teacher.exams.destroy', $exam->id) }}" method="POST" class="form-delete-exam" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đề thi này không? Mọi kết quả bài làm (nếu có) cũng sẽ bị xóa theo!');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item py-2 text-danger fw-bold"><i class="bi bi-trash3-fill me-2"></i> Xóa đề thi</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="empty-icon-wrapper mx-auto mb-3 shadow-sm">
                                    <i class="bi bi-journal-x theme-text-primary"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-2">Chưa có đề thi nào</h5>
                                <p class="text-muted mb-4 opacity-75">Bạn chưa tạo đề thi nào. Hãy dùng sức mạnh của AI để tạo ngay một bộ câu hỏi nhé!</p>
                                <a href="{{ route('teacher.exams.create') }}" class="btn btn-purple-gradient rounded-pill px-4 py-2.5 fw-bold shadow-sm">
                                    <i class="bi bi-magic me-1"></i> Bắt đầu tạo đề AI
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Phân trang -->
    <div class="d-flex justify-content-center custom-pagination mt-4">
        {{ $exams->links('pagination::bootstrap-5') }}
    </div>

@endsection

@push('scripts')
    {{-- Giữ lại thư viện jQuery và file JS gốc để xử lý các hiệu ứng Dropdown hoặc Validate nếu có --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('js/teacher/teacher_exams.js') }}?v={{ time() }}"></script>
@endpush