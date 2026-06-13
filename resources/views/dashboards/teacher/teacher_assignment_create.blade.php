@extends('layouts.teacher.teacher_app')

@section('title', 'Giao bài tập')

@push('styles')
    <link rel="stylesheet" href="{{ versioned_asset('css/teacher/teacher_assignment.css') }}">
@endpush

@section('content')
<div class="container-fluid py-4">

    <div class="teacher-page-heading d-flex align-items-center justify-content-between mb-4 pb-3">
        <div>
            <a href="{{ route('teacher.classrooms.show', $classroom->id) }}" class="btn-back text-muted text-decoration-none fw-bold d-inline-flex align-items-center gap-2 mb-2">
                <div class="back-icon-box bg-white shadow-sm rounded-circle d-flex align-items-center justify-content-center border">
                    <i class="bi bi-arrow-left"></i>
                </div> 
                Quay lại lớp học
            </a>
            <h3 class="fw-800 text-dark mb-0 d-flex align-items-center gap-2">
                Giao bài tập mới <i class="bi bi-send-check-fill theme-text-primary"></i>
            </h3>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger-soft alert-dismissible fade show mb-4 border-0 shadow-sm rounded-4 p-3 d-flex align-items-start" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-5 me-3 mt-1 text-danger"></i>
            <div>
                <strong class="fs-6 d-block mb-1">Vui lòng kiểm tra lại thông tin:</strong>
                <ul class="mb-0 ps-3 fw-medium small">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4 align-items-start">
        
        <div class="col-lg-7 col-xl-8">
            <div class="premium-card p-4 p-md-5 bg-white">
                <div class="mb-4 d-flex align-items-center gap-3">
                    <div class="icon-bg-purple"><i class="bi bi-sliders"></i></div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">Cấu hình thông số bài tập</h5>
                        <p class="text-muted small mb-0">Thiết lập đề thi, thời hạn và lời dặn dò gửi tới học viên.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('teacher.classrooms.assignments.store', $classroom->id) }}" id="assignmentForm">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label-custom"><i class="bi bi-collection-play me-1"></i> 1. Chọn đề thi từ thư viện <span class="text-danger">*</span></label>
                        <div class="input-group custom-input-group">
                            <span class="input-group-text"><i class="bi bi-file-earmark-text fs-5"></i></span>
                            <select name="exam_id" class="form-select" required>
                                <option value="" selected disabled>-- Bấm để duyệt danh sách đề --</option>
                                @forelse($exams as $exam)
                                    <option value="{{ $exam->id }}" {{ old('exam_id') == $exam->id ? 'selected' : '' }}>
                                        {{ $exam->title }} — [{{ $exam->questions->count() ?? $exam->total_questions }} câu hỏi]
                                    </option>
                                @empty
                                    <option value="" disabled>Kho đề trống. Vui lòng tạo đề bằng AI trước khi giao!</option>
                                @endforelse
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-custom"><i class="bi bi-shuffle me-1"></i> 2. Đảo đề & số lượng mã đề</label>
                        <div class="assignment-variant-box p-3 p-md-4 rounded-4">
                            <div class="d-flex flex-column flex-md-row gap-3 align-items-md-center justify-content-between">
                                <label class="variant-toggle d-flex align-items-start gap-3 mb-0">
                                    <input type="checkbox" name="shuffle_questions" value="1" class="form-check-input mt-1" id="shuffle_questions" {{ old('shuffle_questions') ? 'checked' : '' }}>
                                    <span>
                                        <span class="d-block fw-bold text-dark">Đảo thứ tự câu hỏi khi phát đề</span>
                                        <span class="d-block text-muted small mt-1">Mỗi mã đề sẽ dùng cùng nội dung nhưng thứ tự câu hỏi khác nhau.</span>
                                    </span>
                                </label>

                                <div class="variant-count-control">
                                    <label for="variant_count" class="form-label-custom mb-1">Số lượng mã đề</label>
                                    <input type="number" name="variant_count" id="variant_count" class="form-control text-center fw-bold" value="{{ old('variant_count', 1) }}" min="1" max="10">
                                </div>
                            </div>
                            <div class="variant-hint mt-3">
                                <i class="bi bi-info-circle-fill me-1"></i>
                                Nếu chọn từ 2 mã đề trở lên, hệ thống sẽ tự động đảo câu hỏi và phân mỗi học viên thấy một mã đề.
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label-custom"><i class="bi bi-clock-history me-1"></i> 3. Thời hạn khóa cổng nộp bài <span class="text-danger">*</span></label>
                        <div class="input-group custom-input-group">
                            <span class="input-group-text"><i class="bi bi-calendar-alarm fs-5"></i></span>
                            <input type="datetime-local" name="deadline" id="deadline_input" class="form-control" value="{{ old('deadline') }}" required>
                        </div>
                        <div class="form-text text-muted mt-2 d-flex align-items-center gap-1">
                            <i class="bi bi-info-circle-fill theme-text-primary"></i> Sau thời gian này, hệ thống sẽ đóng nút nộp bài của học viên.
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="form-label-custom"><i class="bi bi-envelope-heart me-1"></i> 4. Lời nhắn / Lời dặn dò gửi lớp</label>
                        <div class="input-group custom-input-group align-items-start">
                            <span class="input-group-text pt-3"><i class="bi bi-chat-dots fs-5"></i></span>
                            <textarea name="note" class="form-control" rows="4" placeholder="VD: Bài kiểm tra có tính vào điểm điều kiện. Các em hãy ôn tập kỹ chương 2 và làm bài nghiêm túc nhé...">{{ old('note') }}</textarea>
                        </div>
                    </div>

                    <hr class="border-light-subtle mb-4">

                    <div class="d-flex justify-content-end gap-3">
                        <a href="{{ route('teacher.classrooms.show', $classroom->id) }}" class="btn btn-cancel rounded-pill px-4 fw-bold">Hủy bỏ</a>
                        <button type="submit" class="btn btn-theme-primary px-5 py-2.5 fw-bold text-white shadow-sm" id="btnSubmitForm">
                            <i class="bi bi-send-fill me-2"></i> Kích hoạt & Phát đề
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-5 col-xl-4 position-sticky" style="top: 24px;">
            
            <div class="premium-card p-4 mb-4 info-sidebar-card">
                <div class="d-flex align-items-center gap-3">
                    <div class="classroom-avatar shadow-sm d-flex justify-content-center align-items-center text-white border">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                    <div>
                        <small class="text-uppercase text-muted fw-bold tracking-wider" style="font-size: 0.75rem;">Lớp học tiếp nhận</small>
                        <h4 class="fw-800 text-dark mb-0 mt-1 class-title-text">{{ $classroom->name }}</h4>
                    </div>
                </div>
                
                <div class="dashed-info-box p-3 mt-4">
                    <h6 class="fw-bold text-dark mb-2 small text-uppercase"><i class="bi bi-shield-check text-success me-1"></i> Cơ chế tự động hóa:</h6>
                    <ul class="list-unstyled mb-0 small text-muted lh-lg">
                        <li class="d-flex align-items-start gap-2">
                            <i class="bi bi-dot fs-4 text-purple-light mt-n1"></i>
                            <span>Bắn thông báo bài tập mới tới tất cả học viên trong lớp.</span>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="bi bi-dot fs-4 text-purple-light mt-n1"></i>
                            <span>Tự động tạo bảng điểm thành phần sau khi kết thúc.</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="premium-card p-4 bg-dark text-white border-0 widget-tips-box">
                <div class="sparkle-icon"><i class="bi bi-lightbulb-fill"></i></div>
                <h6 class="fw-bold text-warning d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-stars"></i> Gợi ý hữu ích khi giao bài tập
                </h6>
                <p class="small text-white-50 lh-base mb-0">
                    Bác nên thiết lập thời gian nộp bài kéo dài ít nhất <strong>24 giờ</strong> để bảo đảm các học viên gặp sự cố mạng hoặc thiết bị đều có đủ thời gian hoàn thành bài làm tốt nhất.
                </p>
            </div>
            
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ versioned_asset('js/teacher/teacher_assignment.js') }}"></script>
@endpush
