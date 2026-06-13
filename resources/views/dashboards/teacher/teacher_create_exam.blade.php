@extends('layouts.teacher.teacher_app')

@section('title', isset($exam) ? 'Chỉnh sửa Đề thi' : 'Tạo đề thi mới')

@push('styles')
    <link rel="stylesheet" href="{{ versioned_asset('css/teacher/teacher_exam.css') }}">
@endpush

@section('content')
<div class="fixed-wrapper-teacher">
    {{-- Header & Nút Quay lại --}}
    <div class="teacher-page-heading flex-shrink-0 mb-3 pb-2">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <a href="{{ route('teacher.exams.index') }}" class="btn-back text-muted text-decoration-none fw-bold hover-text-primary transition-all d-inline-flex align-items-center gap-2 mb-2" style="font-size: 0.85rem;">
                    <div class="bg-white shadow-sm rounded-circle d-flex align-items-center justify-content-center border border-light-subtle" style="width: 28px; height: 28px;">
                        <i class="bi bi-arrow-left small"></i>
                    </div> 
                    Quay lại danh sách
                </a>
                <h4 class="fw-900 mb-0 theme-text-dark d-flex align-items-center gap-2 fs-4">
                    {{ isset($exam) ? 'Chỉnh sửa Đề thi' : 'Khởi tạo Đề thi' }} 
                    <i class="bi bi-stars text-warning ms-1"></i>
                </h4>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" form="studentExamForm" id="btn-save-exam" class="btn btn-theme-primary px-4 py-2 fw-bold rounded-pill d-flex align-items-center gap-2 transition-all shadow-sm">
                    <i class="bi bi-floppy-fill"></i> {{ isset($exam) ? 'Cập nhật đề thi' : 'Lưu & Hoàn tất' }}
                </button>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger-soft alert-dismissible fade show border-0 shadow-sm rounded-4 mb-3 p-3 d-flex align-items-start flex-shrink-0">
            <i class="bi bi-exclamation-triangle-fill fs-5 me-3 mt-1 text-danger"></i>
            <div>
                <strong class="fw-bold fs-6 d-block mb-1 text-danger">Vui lòng kiểm tra lại thông tin:</strong>
                <ul class="mb-0 ps-3 fw-medium small text-danger">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="btn-close mt-1 me-2" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    @endif

    <form action="{{ isset($exam) ? url('teacher/exams/'.$exam->id) : url('teacher/exams') }}" 
          method="POST" 
          id="studentExamForm" 
          class="flex-grow-1 overflow-hidden d-flex flex-column"
          data-ajax-url="{{ route('exams.generate-ai') }}"
          data-existing-questions="{{ isset($exam) ? json_encode($exam->questions->map(function($q) { $q->ai_explanation = $q->explanation; return $q; })) : '[]' }}">
        
        @csrf
        @if(isset($exam)) @method('PUT') @endif
        <input type="hidden" name="ai_questions_data" id="ai_questions_data" value="">

        <div class="row g-4 flex-grow-1 overflow-hidden align-items-start">
            
            {{-- CỘT TRÁI: Cấu hình & Danh sách Câu hỏi (Chiếm 8 phần) --}}
            <div class="col-lg-7 col-xl-8 d-flex flex-column h-100 overflow-hidden">
                
                {{-- CARD 1: CẤU HÌNH BÀI THI --}}
                <div class="premium-card config-card mb-4 flex-shrink-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 px-4 py-3 cursor-pointer d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#configCollapse" id="configHeader">
                        <h6 class="fw-bold mb-0 theme-text-dark d-flex align-items-center gap-3">
                            <div class="icon-bg-purple"><i class="bi bi-gear-fill"></i></div> 
                            <span style="font-size: 1.05rem;">CẤU HÌNH BÀI THI</span>
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
                                    <label class="form-label-custom">TÊN BÀI KIỂM TRA <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control custom-input" placeholder="VD: Kiểm tra giữa kỳ..." value="{{ old('title', $exam->title ?? '') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom text-purple"><i class="bi bi-diagram-3-fill me-1"></i> GIAO CHO LỚP HỌC</label>
                                    <select name="classroom_id" class="form-select custom-input text-purple bg-purple-light border-purple-subtle fw-bold">
                                        <option value="">-- Chỉ lưu vào Ngân hàng đề --</option>
                                        @isset($classrooms)
                                            @foreach($classrooms as $class)
                                                <option value="{{ $class->id }}" {{ old('classroom_id', $exam->classroom_id ?? '') == $class->id ? 'selected' : '' }}>
                                                    {{ $class->name }}
                                                </option>
                                            @endforeach
                                        @endisset
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">LOẠI CÂU HỎI</label>
                                    <select id="exam_type" name="type" class="form-select custom-input">
                                        <option value="multiple_choice" {{ old('type', $exam->type ?? 'multiple_choice') == 'multiple_choice' ? 'selected' : '' }}>Trắc nghiệm</option>
                                        <option value="essay" {{ old('type', $exam->type ?? '') == 'essay' ? 'selected' : '' }}>Tự luận</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom"><i class="bi bi-hourglass-split me-1"></i> THỜI GIAN LÀM BÀI</label>
                                    <div class="input-group custom-input-group">
                                        <input type="number" name="duration" class="form-control border-end-0" value="{{ old('duration', $exam->duration ?? 45) }}" min="1" required>
                                        <span class="input-group-text bg-transparent text-muted fw-bold">Phút</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom"><i class="bi bi-calendar-event me-1"></i> HẠN NỘP BÀI</label>
                                    <div class="input-group custom-input-group">
                                        <input type="datetime-local" name="deadline" class="form-control" value="{{ old('deadline', (isset($exam) && $exam->deadline) ? date('Y-m-d\TH:i', strtotime($exam->deadline)) : '') }}">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label-custom">GHI CHÚ / LỜI DẶN DÒ</label>
                                    <div class="input-group custom-input-group">
                                        <textarea name="description" class="form-control" rows="1" placeholder="VD: Sinh viên không được sử dụng tài liệu...">{{ old('description', $exam->description ?? '') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD 2: VÙNG DANH SÁCH CÂU HỎI --}}
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
                                    <select id="document_id" name="document_id" class="form-select text-dark fw-medium document-select">
                                        <option value="" selected disabled>Chọn tài liệu bài giảng...</option>
                                        @isset($documents)
                                            @foreach($documents as $doc)
                                                <option value="{{ $doc->id }}" title="{{ $doc->title }} ({{ strtoupper($doc->file_type) }})">
                                                    {{ \Illuminate\Support\Str::limit($doc->title, 42) }} ({{ strtoupper($doc->file_type) }})
                                                </option>
                                            @endforeach
                                        @endisset
                                    </select>
                                </div>
                                <div class="text-end mt-2">
                                    <a href="{{ route('teacher.documents.index') }}" class="text-purple text-decoration-none fw-bold small hover-text-primary">
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
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-bold text-uppercase small mb-0">
                                        Đáp án trắc nghiệm <span class="text-danger">*</span>
                                    </label>
                                    <small class="text-muted">(chọn radio cho đáp án đúng)</small>
                                </div>

                                <div class="manual-answer-list">
                                    <div class="input-group mb-2">
                                        <span class="input-group-text">
                                            <input class="form-check-input m-0" type="radio" name="manual_correct" value="0" checked>
                                        </span>
                                        <span class="input-group-text fw-bold text-purple">A.</span>
                                        <input type="text" class="form-control manual-ans-input" placeholder="Nhập đáp án...">
                                    </div>

                                    <div class="input-group mb-2">
                                        <span class="input-group-text">
                                            <input class="form-check-input m-0" type="radio" name="manual_correct" value="1">
                                        </span>
                                        <span class="input-group-text fw-bold text-purple">B.</span>
                                        <input type="text" class="form-control manual-ans-input" placeholder="Nhập đáp án...">
                                    </div>

                                    <div class="input-group mb-2">
                                        <span class="input-group-text">
                                            <input class="form-check-input m-0" type="radio" name="manual_correct" value="2">
                                        </span>
                                        <span class="input-group-text fw-bold text-purple">C.</span>
                                        <input type="text" class="form-control manual-ans-input" placeholder="Nhập đáp án...">
                                    </div>

                                    <div class="input-group mb-2">
                                        <span class="input-group-text">
                                            <input class="form-check-input m-0" type="radio" name="manual_correct" value="3">
                                        </span>
                                        <span class="input-group-text fw-bold text-purple">D.</span>
                                        <input type="text" class="form-control manual-ans-input" placeholder="Nhập đáp án...">
                                    </div>
                                </div>
                            </div>
                           <div id="manual_essay_area" style="display: none;">
                                <label class="form-label fw-bold text-uppercase small">
                                    Gợi ý đáp án / Bareme tự luận <span class="text-danger">*</span>
                                </label>
                            <textarea 
                                    id="manual_e_answer"
                                    class="form-control rounded-4"
                                    rows="7"
                                    placeholder="Nhập gợi ý đáp án hoặc bareme chấm điểm..."></textarea>
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

{{-- MODAL CHỈNH SỬA CÂU HỎI --}}
<div class="modal fade" id="editQuestionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden teacher-edit-modal">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <div>
                    <h5 class="modal-title fw-bold theme-text-dark mb-1">
                        <i class="bi bi-pencil-square text-purple me-2"></i>
                        Chỉnh sửa câu hỏi
                    </h5>
                    <p class="text-muted small mb-0">Cập nhật nội dung câu hỏi, đáp án và giải thích</p>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>

            <div class="modal-body p-4">
                <input type="hidden" id="edit_q_index">
                <input type="hidden" id="edit_q_type">

                <div class="mb-3">
                    <label class="form-label-custom">NỘI DUNG CÂU HỎI <span class="text-danger">*</span></label>
                    <div class="input-group custom-input-group align-items-start">
                        <span class="input-group-text pt-2">
                            <i class="bi bi-question-circle fs-5"></i>
                        </span>
                        <textarea id="edit_q_content"
                                  class="form-control"
                                  rows="3"
                                  placeholder="Nhập nội dung câu hỏi..."></textarea>
                    </div>
                </div>

                <div id="edit_mcq_area" class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label-custom mb-0">
                            ĐÁP ÁN TRẮC NGHIỆM <span class="text-danger">*</span>
                        </label>
                        <small class="text-muted">(chọn radio cho đáp án đúng)</small>
                    </div>

                    <div class="manual-answer-list" id="edit_answers_container">
                        {{-- JS render 4 đáp án vào đây --}}
                    </div>
                </div>

                <div class="mb-0">
                    <label class="form-label-custom">
                        GIẢI THÍCH AI / GỢI Ý ĐÁP ÁN
                    </label>
                    <div class="input-group custom-input-group align-items-start">
                        <span class="input-group-text pt-2">
                            <i class="bi bi-stars fs-5"></i>
                        </span>
                        <textarea id="edit_q_explanation"
                                  class="form-control"
                                  rows="4"
                                  placeholder="Nhập giải thích ngắn gọn hoặc gợi ý đáp án..."></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold border" data-bs-dismiss="modal">
                    Hủy bỏ
                </button>
                <button type="button" id="btn-update-question" class="btn btn-theme-primary rounded-pill px-4 fw-bold shadow-sm">
                    <i class="bi bi-check-circle-fill me-1"></i>
                    Lưu thay đổi
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL LƯU THÀNH CÔNG --}}
@if(session('exam_saved'))
<div class="modal fade" id="successSaveModal" tabindex="-1" aria-hidden="true" data-show-on-load="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-body text-center p-5">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                     style="width: 72px; height: 72px; background: #dcfce7; color: #16a34a; font-size: 2.2rem;">
                    <i class="bi bi-check-circle-fill"></i>
                </div>

                <h4 class="fw-bold theme-text-dark mb-2">Lưu đề thành công!</h4>

                <p class="text-muted mb-4">
                    Đề thi đã được lưu vào <strong>Ngân hàng đề</strong>.
                </p>

                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold border" data-bs-dismiss="modal">
                        Đóng
                    </button>

                    <a href="{{ route('teacher.exams.index') }}"
                       class="btn btn-theme-primary rounded-pill px-4 fw-bold">
                        <i class="bi bi-folder-fill me-1"></i>
                        Đến danh sách đề
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
    <script src="{{ versioned_asset('js/teacher/teacher_create_exam.js') }}"></script>
@endpush
