@extends('layouts.student.student_app')

@section('title', 'Khởi tạo đề tự luyện')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/student/student_create_exam.css') }}?v={{ time() }}">
@endpush

@section('content')
<div class="fixed-wrapper-teacher">
    
    {{-- Header & Nút Thao tác --}}
    <div class="flex-shrink-0 mb-3 pb-2 border-bottom border-light-subtle">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <a href="{{ route('student.question-banks') }}" class="btn-back text-muted text-decoration-none fw-bold hover-text-primary transition-all d-inline-flex align-items-center gap-2 mb-2" style="font-size: 0.85rem;">
                    <div class="bg-white shadow-sm rounded-circle d-flex align-items-center justify-content-center border border-light-subtle" style="width: 28px; height: 28px;">
                        <i class="bi bi-arrow-left small"></i>
                    </div> 
                    Quay lại góc học tập
                </a>
                <h4 class="fw-900 mb-0 theme-text-dark d-flex align-items-center gap-2 fs-4">
                    Khởi tạo Đề tự luyện <i class="bi bi-robot text-purple ms-1"></i>
                </h4>
                <p class="text-muted small mb-0 mt-1">Thiết lập thông số và tạo câu hỏi luyện tập tự động bằng AI</p>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" form="studentExamForm" class="btn btn-theme-primary px-4 py-2 fw-bold rounded-pill d-flex align-items-center gap-2 transition-all shadow-sm" id="btn-save-exam">
                    <i class="bi bi-floppy-fill"></i> Lưu đề & Hoàn tất
                </button>
            </div>
        </div>
    </div>

    {{-- Thông báo Lỗi System --}}
    @if ($errors->any())
        <div class="alert alert-danger-soft alert-dismissible fade show border-0 shadow-sm rounded-4 mb-3 p-3 d-flex align-items-start flex-shrink-0">
            <i class="bi bi-exclamation-triangle-fill fs-5 me-3 mt-1 text-danger"></i>
            <div>
                <strong class="fw-bold fs-6 d-block mb-1 text-danger">Đã xảy ra lỗi hệ thống:</strong>
                <ul class="mb-0 ps-3 fw-medium small text-danger">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="btn-close mt-1 me-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- BẮT ĐẦU FORM CHÍNH --}}
    <form action="{{ route('student.exams.store') ?? '#' }}" 
          method="POST" 
          id="studentExamForm" 
          class="flex-grow-1 overflow-hidden d-flex flex-column"
          data-ajax-url="{{ route('student.exams.generate-ai') ?? '#' }}"
          data-existing-questions="[]">
        
        @csrf
        <input type="hidden" name="ai_questions_data" id="ai_questions_data" value="">
        <input type="hidden" name="classroom_id" value=""> 

        <div class="row g-4 flex-grow-1 overflow-hidden align-items-start">
            
            {{-- CỘT TRÁI: CẤU HÌNH & DANH SÁCH BÀI TẬP --}}
            <div class="col-lg-7 col-xl-8 d-flex flex-column h-100 overflow-hidden">
                
                {{-- KHỐI 1: CẤU HÌNH BÀI THI --}}
                <div class="premium-card config-card mb-3 flex-shrink-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 px-4 py-3 cursor-pointer d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#configCollapse" id="configHeader">
                        <h6 class="fw-bold mb-0 theme-text-dark d-flex align-items-center gap-3">
                            <div class="icon-bg-purple"><i class="bi bi-gear-fill"></i></div> 
                            <span style="font-size: 1.05rem;">CẤU HÌNH BÀI TẬP</span>
                        </h6>
                        <div class="d-flex align-items-center gap-2 text-muted small fw-bold">
                            <span id="configStatusText">Thu gọn</span>
                            <div class="border rounded-circle d-flex align-items-center justify-content-center toggle-icon shadow-sm" style="width: 28px; height: 28px; background-color: #fff;">
                                <i class="bi bi-chevron-up"></i>
                            </div>
                        </div>
                    </div>

                    <div class="collapse show border-top" id="configCollapse">
                        <div class="card-body p-4 bg-white">
                            <div class="row g-3">
                                
                                <div class="col-12">
                                    <label class="form-label-custom">TÊN BÀI LUYỆN TẬP <span class="text-danger">*</span></label>
                                    <div class="input-group custom-input-group">
                                        <input type="text" name="title" class="form-control" placeholder="VD: Luyện thi cuối kỳ môn Lịch Sử..." required>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label-custom">LOẠI CÂU HỎI</label>
                                    <div class="input-group custom-input-group">
                                        <select id="exam_type" name="exam_type" class="form-select">
                                            <option value="multiple_choice">Trắc nghiệm</option>
                                            <option value="essay">Tự luận</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label-custom"><i class="bi bi-hourglass-split me-1"></i> MỤC TIÊU THỜI GIAN</label>
                                    <div class="input-group custom-input-group">
                                        <input type="number" name="duration" class="form-control border-end-0" value="45" min="1" required>
                                        <span class="input-group-text bg-transparent text-muted fw-bold">Phút</span>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KHỐI 2: VÙNG DANH SÁCH CÂU HỎI --}}
                <div class="premium-card question-list-card flex-grow-1 d-flex flex-column overflow-hidden shadow-sm">
                    <div class="card-header bg-light border-bottom border-light-subtle py-3 px-4 d-flex justify-content-between align-items-center flex-shrink-0">
                        <h6 class="fw-bold mb-0 theme-text-dark d-flex align-items-center gap-3" id="lbl-question-count">
                            <div class="icon-bg-green"><i class="bi bi-card-checklist"></i></div> 
                            <span style="font-size: 1.05rem;">DANH SÁCH CÂU HỎI</span>
                        </h6>
                        <div class="badge bg-white text-purple border px-3 py-2 rounded-pill fw-bold shadow-sm" id="badge-question-stats">0 câu</div>
                    </div>

                    <div class="card-body bg-white p-4 custom-scrollbar question-scroll-area" id="preview-questions-area">
                        <div class="empty-questions py-5 text-center rounded-4 bg-light border-dashed transition-all mt-2">
                            <div class="icon-wrapper-sm bg-white text-purple mx-auto rounded-circle mb-3 shadow-sm border" style="width: 56px; height: 56px; font-size: 1.6rem;">
                                <i class="bi bi-inboxes"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-1">Chưa có câu hỏi nào</h5>
                            <p class="text-muted fw-medium mb-0">Sử dụng công cụ AI sinh tự động hoặc nhập thủ công ở cột bên phải</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CỘT PHẢI: THANH CÔNG CỤ TẠO CÂU HỎI --}}
            <div class="col-lg-5 col-xl-4 h-100 overflow-hidden">
                <div class="premium-card p-4 d-flex flex-column h-100 shadow-sm bg-white border-0" style="border: 1px solid #E2E8F0 !important;">
                    
                    {{-- Nav Tabs --}}
                    <div class="custom-tabs-container mb-4 flex-shrink-0">
                        <ul class="nav nav-pills w-100 custom-pills p-1 rounded-pill bg-light border" id="toolTabs">
                            <li class="nav-item w-50">
                                <button type="button" class="nav-link w-100 active fw-bold py-2 d-flex align-items-center justify-content-center gap-2" data-bs-toggle="pill" data-bs-target="#ai-pane">
                                    <i class="bi bi-magic text-warning"></i> Dùng AI
                                </button>
                            </li>
                            <li class="nav-item w-50">
                                <button type="button" class="nav-link w-100 fw-bold py-2 d-flex align-items-center justify-content-center gap-2" data-bs-toggle="pill" data-bs-target="#manual-pane">
                                    <i class="bi bi-pencil-square text-secondary"></i> Nhập tay
                                </button>
                            </li>
                        </ul>
                    </div>

                    {{-- Nội Dung Tabs --}}
                    <div class="tab-content flex-grow-1 overflow-y-auto custom-scrollbar pe-2" id="questionTabsContent">
                        
                        {{-- Tab AI --}}
                        <div class="tab-pane fade show active" id="ai-pane">
                            <div class="mb-4">
                                <label class="form-label-custom">1. CHỌN TÀI LIỆU GỐC <span class="text-danger">*</span></label>
                                <div class="input-group custom-input-group">
                                    <span class="input-group-text"><i class="bi bi-file-earmark-pdf fs-5"></i></span>
                                    <select id="document_id" name="document_id" class="form-select text-dark fw-medium">
                                        <option value="" selected disabled>Chọn tài liệu bài giảng...</option>
                                        @isset($documents)
                                            @foreach($documents as $doc)
                                                <option value="{{ $doc->id }}">{{ $doc->title }} ({{ strtoupper($doc->file_type) }})</option>
                                            @endforeach
                                        @endisset
                                    </select>
                                </div>
                                <div class="text-end mt-2">
                                    <a href="{{ route('student.documents') ?? '#' }}" class="text-purple text-decoration-none fw-bold small hover-text-primary">
                                        <i class="bi bi-cloud-arrow-up-fill me-1"></i> Tải tài liệu mới lên
                                    </a>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label-custom">2. SỐ LƯỢNG CÂU HỎI</label>
                                <div class="quantity-input-box d-flex justify-content-between align-items-center px-2 py-2 rounded-4 bg-light border">
                                    <button class="btn btn-light rounded-circle fw-bold text-purple fs-5 py-0 px-2 shadow-sm border ms-1" type="button" onclick="document.getElementById('question_count').stepDown()">-</button>
                                    <input type="number" id="question_count" class="form-control text-center border-0 fw-bold bg-transparent fs-4" value="10" min="1" max="50">
                                    <button class="btn btn-light rounded-circle fw-bold text-purple fs-5 py-0 px-2 shadow-sm border me-1" type="button" onclick="document.getElementById('question_count').stepUp()">+</button>
                                </div>
                            </div>

                            <button type="button" id="btn-generate-ai" class="btn btn-magic-gradient w-100 py-3 rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2 mt-3 fs-6 hover-lift">
                                <i class="bi bi-lightning-charge-fill text-warning fs-5"></i> Bắt đầu sinh câu hỏi
                            </button>
                        </div>

                        {{-- Tab Nhập Tay --}}
                        <div class="tab-pane fade" id="manual-pane">
                            <div class="mb-3">
                                <label class="form-label-custom">NỘI DUNG CÂU HỎI <span class="text-danger">*</span></label>
                                <div class="input-group custom-input-group align-items-start">
                                    <span class="input-group-text pt-2"><i class="bi bi-question-circle fs-5"></i></span>
                                    <textarea id="manual_q_content" class="form-control" rows="3" placeholder="Nhập nội dung câu hỏi..."></textarea>
                                </div>
                            </div>
                            
                            <div id="manual_mcq_area">
                                <label class="form-label-custom d-flex justify-content-between align-items-end mb-2">
                                    <span>ĐÁP ÁN TRẮC NGHIỆM <span class="text-danger">*</span></span>
                                    <span class="text-muted fw-normal text-lowercase" style="font-size: 0.7rem;">(Chọn radio cho đáp án đúng)</span>
                                </label>
                                <div class="d-flex flex-column gap-2 mb-4">
                                    @foreach(['A', 'B', 'C', 'D'] as $index => $label)
                                        <div class="input-group custom-input-group align-items-center pe-2">
                                            <div class="input-group-text bg-transparent border-0 pe-1 ps-2">
                                                <input class="form-check-input mt-0 custom-radio-edit" type="radio" name="manual_correct" value="{{ $index }}" {{ $index === 0 ? 'checked' : '' }} id="manual_rad_{{$label}}">
                                            </div>
                                            <label class="input-group-text bg-transparent border-0 fw-bold theme-text-primary px-1" for="manual_rad_{{$label}}">{{ $label }}.</label>
                                            <input type="text" class="form-control manual-ans-input shadow-none px-2" placeholder="Nhập đáp án...">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <div id="manual_essay_area" class="d-none mb-4">
                                <label class="form-label-custom text-purple"><i class="bi bi-card-checklist me-1"></i> GỢI Ý ĐÁP ÁN (TỰ LUẬN)</label>
                                <div class="input-group custom-input-group">
                                    <textarea id="manual_e_answer" class="form-control bg-purple-light" rows="3" placeholder="Nhập các ý chính để AI chấm điểm..."></textarea>
                                </div>
                            </div>

                            <button type="button" id="btn-add-manual" class="btn btn-light w-100 py-2.5 rounded-pill border-dashed theme-text-primary fw-bold hover-lift shadow-sm">
                                <i class="bi bi-plus-circle-fill me-1"></i> Thêm vào danh sách
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

{{-- MODAL SỬA CÂU HỎI --}}
<div class="modal fade" id="editQuestionModal" tabindex="-1" aria-labelledby="editQuestionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold theme-text-dark" id="editQuestionModalLabel">
                    <i class="bi bi-pencil-square text-purple me-2"></i> Sửa câu hỏi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>

            <div class="modal-body p-4">
                <input type="hidden" id="edit_q_index">

                <div class="mb-3">
                    <label class="form-label-custom">NỘI DUNG CÂU HỎI <span class="text-danger">*</span></label>
                    <textarea id="edit_q_content" class="form-control custom-input" rows="3" placeholder="Nhập nội dung câu hỏi..."></textarea>
                </div>

                <div id="edit_mcq_area">
                    <label class="form-label-custom d-flex justify-content-between align-items-end mb-2">
                        <span>ĐÁP ÁN TRẮC NGHIỆM <span class="text-danger">*</span></span>
                        <span class="text-muted fw-normal text-lowercase" style="font-size: 0.7rem;">Chọn radio cho đáp án đúng</span>
                    </label>

                    <div class="d-flex flex-column gap-2">
                        @foreach(['A', 'B', 'C', 'D'] as $index => $label)
                            <div class="input-group custom-input-group align-items-center pe-2">
                                <div class="input-group-text bg-transparent border-0 pe-1 ps-2">
                                    <input class="form-check-input mt-0" type="radio" name="edit_correct" value="{{ $index }}" {{ $index === 0 ? 'checked' : '' }} id="edit_rad_{{$label}}">
                                </div>
                                <label class="input-group-text bg-transparent border-0 fw-bold theme-text-primary px-1" for="edit_rad_{{$label}}">{{ $label }}.</label>
                                <input type="text" id="edit_ans_{{ $index }}" class="form-control shadow-none px-2" placeholder="Nhập đáp án...">
                            </div>
                        @endforeach
                    </div>
                </div>

                <div id="edit_essay_area" class="d-none">
                    <label class="form-label-custom text-purple">
                        <i class="bi bi-card-checklist me-1"></i> GỢI Ý ĐÁP ÁN TỰ LUẬN
                    </label>
                    <textarea id="edit_essay_answer" class="form-control custom-input bg-purple-light" rows="4" placeholder="Nhập gợi ý đáp án..."></textarea>
                </div>
            </div>

            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Hủy</button>
                <button type="button" id="btn-save-edit-question" class="btn btn-theme-primary rounded-pill px-4 fw-bold">
                    <i class="bi bi-check-circle-fill me-1"></i> Lưu chỉnh sửa
                </button>
            </div>
        </div>
    </div>
</div>
{{-- MODAL LUU THANH CONG --}}
@if(session('success_save_exam'))
<div class="modal fade" id="successSaveModal" tabindex="-1" aria-hidden="true" data-show-on-load="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-body text-center p-5">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                     style="width: 72px; height: 72px; background: #dcfce7; color: #16a34a; font-size: 2.2rem;">
                    <i class="bi bi-check-circle-fill"></i>
                </div>

                <h4 class="fw-bold theme-text-dark mb-2">Luu de thanh cong!</h4>

                <p class="text-muted mb-4">
                    De tu luyen da duoc luu vao <strong>Ngan hang de</strong>.
                </p>

                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold border" data-bs-dismiss="modal">
                        Dong
                    </button>

                    <a href="{{ route('student.question-banks') }}"
                       class="btn btn-theme-primary rounded-pill px-4 fw-bold">
                        <i class="bi bi-folder-fill me-1"></i>
                        Den ngan hang de
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('js/student/student_create_exam.js') }}?v={{ time() }}"></script>
@endpush