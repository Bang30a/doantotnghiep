@extends('layouts.teacher.teacher_app')

@section('title', 'Hồ sơ: ' . $student->name)

@push('styles')
    {{-- Chèn CSS riêng của trang này --}}
    <link rel="stylesheet" href="{{ versioned_asset('css/teacher/teacher_student_details.css') }}">
@endpush

@section('content')

    <!-- Nút Quay lại -->
    <div class="mb-4 mt-3">
        <a href="{{ route('teacher.students.index') }}" class="btn btn-light border rounded-pill px-4 py-2 fw-bold text-muted shadow-sm hover-lift transition-all d-inline-flex align-items-center">
            <i class="bi bi-arrow-left me-2"></i> Quay lại danh sách
        </a>
    </div>

    <!-- Header Hồ sơ -->
    <div class="profile-header p-4 p-md-5 mb-5 shadow-purple">
        <div class="bg-icon"><i class="bi bi-person-vcard"></i></div>
        
        <div class="row align-items-center position-relative z-1">
            <div class="col-xl-7 col-lg-6 d-flex align-items-center gap-4 mb-4 mb-lg-0">
                <div class="avatar-xl flex-shrink-0 shadow">
                    {{ mb_strtoupper(mb_substr($student->name, 0, 1)) }}
                </div>
                <div>
                    <span class="badge bg-white mb-2 px-3 py-1.5 rounded-pill fw-bold shadow-sm text-uppercase letter-spacing-1 text-purple">
                        <i class="bi bi-mortarboard-fill me-1"></i> Học viên
                    </span>
                    <h2 class="fw-800 mb-1 display-6 text-white text-shadow">{{ $student->name }}</h2>
                    <p class="text-white opacity-75 fw-medium mb-0 fs-6">
                        <i class="bi bi-envelope-at-fill me-2"></i>{{ $student->email }}
                    </p>
                </div>
            </div>
            
            <div class="col-xl-5 col-lg-6">
                <div class="d-flex gap-3 justify-content-lg-end">
                    <div class="glass-card rounded-4 p-3 px-4 text-center flex-grow-1" style="max-width: 140px;">
                        <h2 class="fw-900 mb-0 text-white">{{ $completedCount }}</h2>
                        <small class="text-white opacity-75 fw-bold text-uppercase letter-spacing-1">Bài đã làm</small>
                    </div>
                    <div class="bg-white rounded-4 p-3 px-4 text-center shadow flex-grow-1 hover-lift transition-all" style="max-width: 140px;">
                        <h2 class="fw-900 mb-0 text-purple">{{ number_format($avg, 1) }}</h2>
                        <small class="text-muted fw-bold text-uppercase letter-spacing-1">Điểm TB</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tiêu đề Danh sách -->
    <div class="d-flex align-items-center gap-2 mb-3 px-1">
        <div class="bg-purple-soft text-purple rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
            <i class="bi bi-journal-text fs-5"></i>
        </div>
        <h5 class="fw-bold text-dark mb-0">Lịch sử làm bài của học viên</h5>
    </div>
    
    <!-- Bảng Lịch sử -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-5">
        <div class="table-responsive">
            <table class="table custom-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th width="35%">Bài kiểm tra</th>
                        <th width="20%">Thời gian nộp</th>
                        <th width="15%" class="text-center">Số câu đúng</th>
                        <th width="15%" class="text-center">Điểm (Hệ 10)</th>
                        <th width="15%" class="text-end pe-4">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($results as $result)
                        @php
                            $totalQ = max(1, $result->total_questions);
                            
                            // KIỂM TRA LOẠI BÀI THI
                            $hasEssay = $result->exam->questions->where('type', 'essay')->count() > 0;
                            $isPureEssay = $result->exam->questions->where('type', 'essay')->count() == $totalQ;

                            // TÍNH ĐIỂM CHUẨN XÁC
                            if ($hasEssay) {
                                // Bài có tự luận: lấy thẳng điểm thực tế trong DB
                                $score10 = $result->score;
                            } else {
                                // 100% Trắc nghiệm: quy đổi hệ 10
                                $score10 = ($result->score / $totalQ) * 10;
                            }

                            $scoreClass = $score10 >= 8 ? 'bg-emerald-soft border-emerald-subtle text-emerald' : ($score10 >= 5 ? 'bg-warning-soft border-warning-subtle text-warning-dark' : 'bg-danger-soft border-danger-subtle text-danger');
                        @endphp
                        <tr class="hover-row transition-all">
                            <td class="ps-4">
                                <div class="fw-bold text-dark mb-1 fs-6">{{ $result->exam->title ?? 'Đề thi đã bị xóa' }}</div>
                                <div class="text-muted small fw-medium">
                                    <i class="bi bi-diagram-3-fill me-1 text-purple opacity-50"></i> 
                                    {{ $result->exam->classroom->name ?? 'Luyện tập tự do' }}
                                </div>
                            </td>
                            <td>
                                <span class="text-muted fw-medium small d-flex align-items-center gap-2">
                                    <i class="bi bi-clock text-purple opacity-50"></i> {{ $result->created_at->format('H:i - d/m/Y') }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($isPureEssay)
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-3 py-1.5 fw-medium"><i class="bi bi-pencil-square me-1"></i>Tự luận</span>
                                @else
                                    <span class="fw-bold text-dark fs-6">{{ $hasEssay ? '-' : $result->score }}<span class="text-muted fw-normal fs-6">/{{ $result->total_questions }}</span></span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $scoreClass }} border px-3 py-2 fs-6 fw-800 shadow-sm">
                                    {{ number_format($score10, 1) }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('teacher.exams.grading', $result->id) }}" class="btn btn-sm btn-outline-purple rounded-pill px-3 py-2 fw-bold transition-all text-decoration-none hover-lift" data-bs-toggle="tooltip" data-bs-placement="top" title="Xem & chấm bài chi tiết">
                                    Chấm bài <i class="bi bi-arrow-right-short fs-5 lh-1 align-middle"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="bg-purple-soft text-purple rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 80px; height: 80px; font-size: 2.5rem;">
                                    <i class="bi bi-inbox"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-1">Chưa có lịch sử làm bài</h5>
                                <p class="text-muted mb-0">Học viên này chưa nộp bài kiểm tra nào trong hệ thống.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection

@push('scripts')
    {{-- Tạm thời giữ lại jQuery nếu file JS bên dưới cần dùng --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ versioned_asset('js/teacher/teacher_student_details.js') }}"></script>
@endpush