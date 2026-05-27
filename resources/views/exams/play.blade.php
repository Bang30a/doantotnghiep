@extends('layouts.student.student_app')

@section('title', 'Đang làm bài: ' . ($exam->title ?? 'Bài thi'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/exam_play.css') }}?v={{ time() }}">
@endpush

@section('content')
    <div class="bg-white rounded-3 p-3 mb-4 header-shadow d-flex flex-column flex-md-row justify-content-between align-items-md-center">
        <div class="mb-3 mb-md-0">
            <h4 class="fw-bold mb-1 text-dark">
                {{ $exam->title ?? 'Bài thi' }}
            </h4>

            <div class="text-muted small">
                {{ $exam->description ?? $exam->classroom->name ?? 'Không có mô tả' }}
            </div>
        </div>
        
        <div class="d-flex align-items-center gap-3">
            <div class="header-info-box bg-blue-light text-primary d-flex align-items-center gap-2 px-3 py-2 rounded-3">
                <i class="bi bi-check-circle fs-5"></i>
                <div class="lh-sm">
                    <div class="fw-bold text-sm" id="answered-count">
                        0/{{ isset($exam->questions) ? $exam->questions->count() : 0 }} câu
                    </div>
                    <div class="text-xs text-primary-muted">Đã trả lời</div>
                </div>
            </div>

            <div class="header-info-box bg-green-light text-success d-flex align-items-center gap-2 px-3 py-2 rounded-3" id="timer-display">
                <i class="bi bi-clock fs-5"></i>
                <div class="lh-sm">
                    <div class="fw-bold text-sm font-monospace" id="time-left">
                        {{ $exam->duration ?? 0 }}:00
                    </div>
                    <div class="text-xs text-success-muted">Thời gian còn lại</div>
                </div>
            </div>

            <button type="button" class="btn btn-primary-gradient px-4 py-2 fw-bold rounded-3 shadow-sm border-0" onclick="showSubmitModal()">
                Nộp Bài
            </button>
        </div>
    </div>

    <form id="examForm" action="{{ route('exams.submit', $exam->id ?? 1) ?? '#' }}" method="POST">
        @csrf
        <div class="row g-4">
            
            <div class="col-xl-3 col-lg-4">
                <div class="card border-0 rounded-4 custom-shadow sticky-top" style="top: 20px;">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-4 text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-file-earmark-text text-primary fs-5"></i> Danh Sách Câu Hỏi
                        </h6>
                        <hr class="text-muted opacity-25 mb-4">
                        
                        <div id="overview-grid" class="d-grid grid-cols-5 gap-2 mb-4">
                            @if(isset($exam->questions))
                                @for($i = 1; $i <= $exam->questions->count(); $i++)
                                    <button type="button" class="btn overview-btn fw-semibold" id="overview-btn-{{ $i }}" onclick="goToStep({{ $i }})">
                                        {{ $i }}
                                    </button>
                                @endfor
                            @endif
                        </div>

                        <hr class="text-muted opacity-25 mb-3">
                        
                        <div class="d-flex flex-column gap-2 small text-muted">
                            <div class="d-flex align-items-center gap-2">
                                <span class="legend-color bg-primary-gradient"></span> Đang làm
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="legend-color bg-success-light"></span> Đã trả lời
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="legend-color bg-gray-light"></span> Chưa trả lời
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-9 col-lg-8">
                <div class="card border-0 rounded-4 custom-shadow overflow-hidden position-relative">
                    
                    <div class="questions-wrapper">
                        @forelse($exam->questions ?? [] as $index => $question)
                            @php $step = $index + 1; @endphp
                            <div class="step-item {{ $step == 1 ? 'active' : 'd-none' }}" id="question-step-{{ $step }}" data-step="{{ $step }}">
                                
                                <div class="question-header bg-primary-gradient p-4 text-white">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="d-flex gap-2">
                                            <span class="badge bg-white bg-opacity-25 text-white rounded-pill px-3 py-2 fw-normal">Câu {{ $step }}/{{ isset($exam->questions) ? $exam->questions->count() : 0 }}</span>
                                            <span class="badge bg-white bg-opacity-25 text-white rounded-pill px-3 py-2 fw-normal">Trắc Nghiệm</span>
                                        </div>
                                        <span class="badge bg-white bg-opacity-25 text-white rounded-pill px-3 py-2 fw-normal d-flex align-items-center gap-1">
                                            <i class="bi bi-award"></i> 10đ
                                        </span>
                                    </div>
                                    <h4 class="fw-bold mb-0 lh-base text-white" style="font-size: 1.3rem;">
                                        {{ $question->content }}
                                    </h4>
                                </div>

                                <div class="card-body p-4 p-md-5 bg-white">
                                    <div class="answers-list d-flex flex-column gap-3">
                                        @if($question->type === 'essay')
                                            <textarea class="form-control rounded-3 p-3 bg-light border-light" name="question_{{ $question->id }}" rows="8" placeholder="Nhập câu trả lời của bạn..." oninput="markAnswered({{ $step }})"></textarea>
                                        @else
                                            @foreach($question->answers as $ansIndex => $answer)
                                                @php $letter = chr(64 + $loop->iteration); /* A, B, C, D */ @endphp
                                                <div class="option-wrapper">
                                                    <input type="radio" class="d-none custom-radio-input" name="question_{{ $question->id }}" id="ans_{{ $answer->id }}" value="{{ $answer->id }}" onchange="markAnswered({{ $step }})">
                                                    <label class="option-label cursor-pointer w-100 m-0 border rounded-3 p-3 d-flex align-items-center gap-3 transition-all" for="ans_{{ $answer->id }}">
                                                        <div class="letter-circle rounded-circle bg-light text-secondary d-flex align-items-center justify-content-center flex-shrink-0 fw-bold">
                                                            {{ $letter }}
                                                        </div>
                                                        <span class="answer-text text-dark">{{ $answer->content }}</span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-5 text-center text-muted">Đề thi chưa có câu hỏi.</div>
                        @endforelse
                    </div>

                    <div class="card-footer bg-white border-top p-4 d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-light text-muted fw-semibold px-4 py-2 rounded-3 shadow-none d-flex align-items-center gap-2 transition-all" id="btn-prev" onclick="navigate(-1)">
                            <i class="bi bi-chevron-left"></i> Câu Trước
                        </button>
                        
                        <div id="status-badge" class="badge bg-warning-light text-warning px-3 py-2 rounded-pill fw-semibold d-flex align-items-center gap-1">
                            <i class="bi bi-exclamation-circle-fill"></i> Chưa trả lời
                        </div>

                        <button type="button" class="btn btn-primary-gradient text-white fw-semibold px-4 py-2 rounded-3 shadow-none d-flex align-items-center gap-2 transition-all" id="btn-next" onclick="navigate(1)">
                            Câu Tiếp <i class="bi bi-chevron-right"></i>
                        </button>
                        
                        <button type="button" class="btn btn-primary-gradient text-white fw-semibold px-4 py-2 rounded-3 shadow-none d-flex align-items-center gap-2 transition-all d-none" id="btn-next-submit" onclick="showSubmitModal()">
                            Nộp Bài <i class="bi bi-check2-circle"></i>
                        </button>
                    </div>
                </div>
            </div>
            
        </div>
    </form>
    <div class="modal fade" id="submitConfirmModal" tabindex="-1" aria-labelledby="submitConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);">
                
                <div class="modal-header border-0 flex-column align-items-start p-4" style="background-color: #FF8A00; border-radius: 16px 16px 0 0;">
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="d-flex align-items-center gap-2 mb-1 text-white">
                        <i class="bi bi-exclamation-triangle fs-4"></i>
                        <h5 class="modal-title fw-bold" id="submitConfirmModalLabel">Xác Nhận Nộp Bài</h5>
                    </div>
                    <p class="mb-0 small" style="color: rgba(255, 255, 255, 0.8);">Vui lòng kiểm tra kỹ trước khi nộp</p>
                </div>
                
                <div class="modal-body p-4">
                    <div class="d-flex flex-column gap-3 mb-4">
                        <div class="d-flex justify-content-between align-items-center p-3 rounded-3" style="background-color: #F0FDF4; border: 1px solid #DCFCE7;">
                            <div class="text-success fw-semibold d-flex align-items-center gap-2">
                                <i class="bi bi-check-circle"></i> Đã trả lời
                            </div>
                            <div class="fw-bold text-success" id="modal-answered-count">0 câu</div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center p-3 rounded-3" style="background-color: #FEF2F2; border: 1px solid #FEE2E2;">
                            <div class="text-danger fw-semibold d-flex align-items-center gap-2">
                                <i class="bi bi-x-circle"></i> Chưa trả lời
                            </div>
                            <div class="fw-bold text-danger" id="modal-unanswered-count">0 câu</div>
                        </div>
                    </div>

                    <div class="alert mb-0 d-flex gap-2 align-items-start" id="modal-warning-box" style="background-color: #FFFBEB; border: 1px solid #FEF3C7; color: #D97706; border-radius: 8px; display: none;">
                        <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                        <div class="small fw-medium">
                            Bạn còn <span id="modal-warning-count" class="fw-bold">0</span> câu chưa trả lời. Các câu này sẽ không được tính điểm.
                        </div>
                    </div>

                    <div class="text-center text-muted small mt-4 mb-2" style="line-height: 1.6;">
                        Sau khi nộp bài, bạn sẽ không thể chỉnh sửa câu trả lời.<br>Bạn có chắc chắn muốn nộp bài?
                    </div>
                </div>
                
                <div class="modal-footer border-0 p-4 pt-0 d-flex gap-3">
                    <button type="button" class="btn btn-light flex-fill fw-bold py-2 text-muted rounded-3" data-bs-dismiss="modal" style="background-color: #F3F4F6;">Kiểm Tra Lại</button>
                    <button type="button" class="btn btn-primary-gradient flex-fill fw-bold py-2 rounded-3 text-white border-0" id="confirm-submit-btn">Xác Nhận Nộp Bài</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        window.examConfig = {
            durationMinutes: {{ $exam->duration ?? 0 }},
            totalQuestions: {{ isset($exam->questions) ? $exam->questions->count() : 0 }}
        };
    </script>
    <script src="{{ asset('js/exam_play.js') }}?v={{ time() }}"></script>
@endpush