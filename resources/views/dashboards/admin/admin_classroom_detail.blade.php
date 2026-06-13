@extends('layouts.admin.admin_app')

@section('title', 'Chi tiết Lớp học: ' . ($classroom->name ?? ''))

@section('content')

    <!-- Tiêu đề trang & Nút quay lại -->
    <div class="admin-page-heading d-flex align-items-center gap-3 mb-4 mt-2 pb-3">
        <a href="{{ route('admin.classrooms') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h3 class="fw-800 text-dark mb-1">Chi tiết Lớp học</h3>
            <p class="text-muted fw-medium mb-0">Xem thông tin tổng quan, danh sách học viên và đề thi</p>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <!-- Khối Thông tin Tổng quan -->
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white hover-lift transition-all">
                <div class="row align-items-center">
                    <div class="col-md-6 d-flex align-items-center gap-4">
                        <div class="bg-purple-light theme-text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm border border-purple-subtle" style="width: 80px; height: 80px; font-size: 2.5rem;">
                            <i class="bi bi-mortarboard-fill"></i>
                        </div>
                        <div>
                            <h4 class="fw-800 text-dark mb-1">{{ $classroom->name ?? 'Tên lớp học' }}</h4>
                            <div class="text-muted fw-medium mb-3"><i class="bi bi-hash"></i> Mã lớp: <strong class="text-dark ms-1">{{ $classroom->code }}</strong></div>
                            <div>
                                <span class="badge bg-gray-soft text-dark px-3 py-2 rounded-pill border shadow-sm">
                                    <i class="bi bi-person-workspace theme-text-primary me-1"></i> Giảng viên: {{ $classroom->teacher->name ?? 'Không xác định' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mt-4 mt-md-0 d-flex justify-content-md-end gap-3 flex-wrap">
                        <div class="text-center bg-gray-soft rounded-4 p-3 border shadow-sm" style="min-width: 110px;">
                            <h3 class="fw-800 text-dark mb-0">{{ $classroom->users ? $classroom->users->count() : 0 }}</h3>
                            <span class="text-muted small fw-bold text-uppercase">Học viên</span>
                        </div>
                        <div class="text-center bg-gray-soft rounded-4 p-3 border shadow-sm" style="min-width: 110px;">
                            <h3 class="fw-800 text-dark mb-0">{{ $classroom->exams ? $classroom->exams->count() : 0 }}</h3>
                            <span class="text-muted small fw-bold text-uppercase">Đề thi</span>
                        </div>
                        <div class="text-center bg-gray-soft rounded-4 p-3 border shadow-sm" style="min-width: 110px;">
                            @if(isset($classroom->status) && $classroom->status == 1)
                                <h3 class="fw-800 text-success mb-0"><i class="bi bi-check-circle-fill"></i></h3>
                                <span class="text-muted small fw-bold text-uppercase">Đang mở</span>
                            @else
                                <h3 class="fw-800 text-danger mb-0"><i class="bi bi-lock-fill"></i></h3>
                                <span class="text-muted small fw-bold text-uppercase">Bị khóa</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Khối Danh sách Học viên -->
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white hover-lift transition-all">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-people-fill text-emerald fs-4"></i> Danh sách Học viên
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if(isset($classroom->users) && $classroom->users->count() > 0)
                        <div class="list-group list-group-flush rounded-3 border">
                            @foreach($classroom->users as $student)
                                <div class="list-group-item px-3 py-3 d-flex align-items-center gap-3 transition-all hover-row">
                                    <img src="{{ $student->avatar ? asset($student->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($student->name).'&background=D1FAE5&color=059669' }}" alt="Avatar" class="rounded-circle" width="45" height="45" style="object-fit: cover;">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0">{{ $student->name }}</h6>
                                        <small class="text-muted">{{ $student->email }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5 text-muted bg-gray-soft rounded-4 border border-dashed">
                            <i class="bi bi-person-x fs-1 d-block mb-2 opacity-50"></i>
                            <span class="fw-medium">Chưa có học viên nào tham gia lớp này.</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Khối Danh sách Đề thi -->
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white hover-lift transition-all">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-journal-text text-warning-dark fs-4"></i> Danh sách Đề thi
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if(isset($classroom->exams) && $classroom->exams->count() > 0)
                        <div class="list-group list-group-flush rounded-3 border">
                            @foreach($classroom->exams as $exam)
                                <div class="list-group-item px-3 py-3 transition-all hover-row">
                                    <h6 class="fw-bold text-dark mb-1 text-truncate" title="{{ $exam->title }}">{{ $exam->title }}</h6>
                                    <div class="d-flex gap-3 text-muted small fw-medium mt-2">
                                        <span class="badge bg-gray-soft text-dark border"><i class="bi bi-clock me-1"></i> {{ $exam->duration }} phút</span>
                                        <span class="badge bg-gray-soft text-dark border"><i class="bi bi-patch-question me-1"></i> {{ $exam->total_questions ?? $exam->questions->count() ?? 0 }} câu</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5 text-muted bg-gray-soft rounded-4 border border-dashed">
                            <i class="bi bi-journal-x fs-1 d-block mb-2 opacity-50"></i>
                            <span class="fw-medium">Chưa có đề thi nào được tạo trong lớp này.</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
