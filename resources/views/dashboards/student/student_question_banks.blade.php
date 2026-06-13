@extends('layouts.student.student_app')

@section('title', 'Ngân hàng đề')

@push('styles')
    <link rel="stylesheet" href="{{ versioned_asset('css/student/student_question_banks.css') }}">
@endpush

@section('content')
    @if(session('success'))
        <div class="alert alert-custom-success alert-dismissible fade show mb-4 shadow-sm border-0 rounded-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="student-page-heading d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-1 theme-text-dark d-flex align-items-center gap-2">
                <i class="bi bi-collection-play theme-text-primary"></i> Ngân hàng đề của tôi
            </h3>
            <p class="text-muted fs-6 mb-0">Lưu trữ, quản lý và ôn tập các bộ câu hỏi cá nhân</p>
        </div>
        
        <a href="{{ route('student.exams.create', ['show_back' => 1]) ?? '#' }}" class="btn btn-theme-primary px-4 py-2 fw-bold rounded-pill shadow-sm d-flex align-items-center gap-2">
            <i class="bi bi-magic"></i> Tạo đề mới bằng AI
        </a>
    </div>

    <div class="filter-card shadow-sm mb-4 border-0 rounded-4 bg-white p-3">
        <div class="row g-3">
            <div class="col-md-5">
                <div class="input-group input-group-custom h-100">
                    <span class="input-group-text bg-light border-end-0 text-muted rounded-start-3"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control bg-light border-start-0 ps-0 rounded-end-3" id="searchInput" placeholder="Tìm kiếm tên đề, môn học...">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select form-select-custom bg-light rounded-3 h-100" id="subjectFilter">
                    <option value="">Tất cả môn học</option>
                    @if(isset($subjects))
                        @foreach($subjects as $subject)
                            <option value="{{ $subject }}">{{ $subject }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select form-select-custom bg-light rounded-3 h-100" id="sortFilter">
                    <option value="newest">Mới nhất</option>
                    <option value="oldest">Cũ nhất</option>
                    <option value="highest_score">Điểm mục tiêu cao nhất</option>
                </select>
            </div>
            <div class="col-md-1 text-end">
                <button type="button" id="resetFilters" class="btn btn-light border rounded-3 w-100 h-100 text-muted hover-theme" title="Tải lại bộ lọc">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="row g-4" id="bankContainer">
        @forelse($questionBanks ?? [] as $bank)
            <div class="col-md-6 col-lg-4 bank-item" data-created="{{ optional($bank->created_at)->timestamp ?? 0 }}" data-score="{{ $bank->target_score ?? 0 }}">
                <div class="bank-card card border-0 shadow-sm h-100 d-flex flex-column rounded-4">
                    
                    <div class="bank-card-header p-4 pb-0 d-flex justify-content-between align-items-start">
                        <div class="bank-icon bg-purple-light theme-text-primary shadow-sm">
                            <i class="bi bi-stack"></i>
                        </div>
                        <div class="dropdown">
                            <button type="button" class="btn btn-sm btn-light rounded-circle text-muted p-2 border-0" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-purple-subtle rounded-3">
                                <li><button type="button" class="dropdown-item py-2 text-danger btn-delete-bank" data-id="{{ $bank->id }}" data-url="{{ route('student.question-banks.destroy', $bank->id) }}"><i class="bi bi-trash3 me-2"></i>Xóa đề</button></li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="bank-card-body flex-grow-1 p-4 pt-3">
                        <h5 class="fw-bold mb-2 theme-text-dark text-truncate title-text" title="{{ $bank->title }}">{{ $bank->title }}</h5>
                        <span class="badge border border-purple-subtle text-purple-dark bg-purple-light px-3 py-1 rounded-pill mb-3 fw-medium">
                            {{ $bank->subject ?? 'Môn chung' }}
                        </span>
                        
                        <p class="text-muted small mb-4 description-text line-clamp-2" style="line-height: 1.6;">
                            {{ $bank->description ?? 'Bộ câu hỏi tự luyện do AI tổng hợp và biên soạn.' }}
                        </p>

                        <div class="d-flex align-items-center justify-content-between text-muted small bg-light p-2 rounded-3 border">
                            <div class="d-flex flex-column align-items-center flex-fill border-end">
                                <i class="bi bi-patch-question theme-text-primary mb-1 fs-6"></i> 
                                <span class="fw-bold theme-text-dark">{{ $bank->questions_count ?? 20 }} <span class="fw-normal text-muted" style="font-size: 0.75rem;">câu</span></span>
                            </div>
                            <div class="d-flex flex-column align-items-center flex-fill border-end">
                                <i class="bi bi-hourglass-split text-indigo mb-1 fs-6"></i> 
                                <span class="fw-bold theme-text-dark">{{ $bank->duration ?? 45 }} <span class="fw-normal text-muted" style="font-size: 0.75rem;">phút</span></span>
                            </div>
                            <div class="d-flex flex-column align-items-center flex-fill">
                                <i class="bi bi-bullseye text-pink mb-1 fs-6"></i>
                                <span class="fw-bold theme-text-dark">{{ $bank->target_score ?? 80 }}<span class="fw-normal text-muted" style="font-size: 0.75rem;">%</span></span>
                            </div>
                        </div>
                    </div>

                    <div class="bank-card-footer p-3 bg-white border-top rounded-bottom-4 d-flex gap-2">
                        <a href="{{ route('student.question-banks.preview', $bank->id ?? 1) }}" class="btn btn-outline-theme w-50 py-2 fw-bold rounded-3">
                            <i class="bi bi-eye"></i> Xem đề
                        </a>
                        <a href="{{ route('exams.play', $bank->id ?? 1) }}" class="btn btn-theme-primary w-50 py-2 fw-bold d-flex align-items-center justify-content-center gap-2 rounded-3 shadow-sm">
                            <i class="bi bi-play-fill fs-5"></i> Luyện ngay
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="empty-state-card text-center py-5 bg-white shadow-sm rounded-4 border border-purple-subtle border-dashed">
                    <div class="empty-icon-circle bg-purple-light theme-text-primary d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 90px; height: 90px; border-radius: 50%;">
                        <i class="bi bi-inboxes fs-1"></i>
                    </div>
                    <h4 class="fw-bold mb-2 theme-text-dark">Ngân hàng đề trống</h4>
                    <p class="text-muted mb-4 opacity-75">Bạn chưa lưu bộ câu hỏi nào. Hãy tải tài liệu lên và để AI giúp bạn tạo đề thi nhé!</p>
                    <a href="{{ route('student.exams.create', ['show_back' => 1]) ?? '#' }}" class="btn btn-theme-primary px-5 py-3 fw-bold rounded-pill shadow-sm">
                        <i class="bi bi-magic me-2"></i> Tạo đề ôn tập đầu tiên
                    </a>
                </div>
            </div>
        @endforelse
    </div>
    
    @if(isset($questionBanks) && method_exists($questionBanks, 'links'))
        <div class="mt-4 d-flex justify-content-end pagination-custom">
            {{ $questionBanks->links('pagination::bootstrap-5') }}
        </div>
    @endif
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ versioned_asset('js/student/student_question_banks.js') }}"></script>
@endpush
