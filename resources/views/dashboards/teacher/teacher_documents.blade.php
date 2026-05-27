@extends('layouts.teacher.teacher_app')

@section('title', 'Kho tài liệu')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/teacher/teacher_documents.css') }}?v={{ time() }}">
@endpush

@section('content')

    <!-- Tiêu đề & Nút Upload -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 border-bottom border-light-subtle pb-3 mt-2 gap-3">
        <div>
            <h3 class="fw-800 text-dark mb-1 d-flex align-items-center gap-2">
                Kho Tài Liệu <i class="bi bi-folder2-open theme-text-primary"></i>
            </h3>
            <p class="text-muted fw-medium mb-0">Lưu trữ bài giảng, tài liệu PDF/Word để làm nguồn cho AI tạo đề thi</p>
        </div>
        
        <button class="btn btn-purple-gradient text-white fw-bold rounded-pill px-4 py-2.5 d-flex align-items-center gap-2 hover-lift transition-all" data-bs-toggle="modal" data-bs-target="#uploadModal">
            <i class="bi bi-cloud-arrow-up-fill fs-5"></i> Tải tài liệu lên
        </button>
    </div>

    <!-- Thông báo Thành công/Lỗi -->
    @if(session('success'))
        <div class="alert alert-emerald-soft alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill fs-4 me-3 text-emerald"></i> 
            <div><strong class="d-block mb-1">Thành công!</strong>{{ session('success') }}</div>
            <button type="button" class="btn-close m-auto me-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if($errors->any())
        <div class="alert alert-danger-soft border-0 shadow-sm rounded-4 mb-4 d-flex">
            <i class="bi bi-exclamation-triangle-fill fs-4 text-danger me-3 mt-1"></i>
            <ul class="mb-0 ps-3 text-danger fw-medium">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Thống kê nhanh -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="stat-card bg-white p-4 rounded-4 shadow-sm border-0 d-flex align-items-center gap-4 hover-lift h-100">
                <div class="icon-wrapper fuchsia shadow-sm flex-shrink-0"><i class="bi bi-files"></i></div>
                <div>
                    <p class="text-muted small fw-bold text-uppercase letter-spacing-1 mb-1">Tổng tài liệu</p>
                    <h2 class="fw-800 text-dark mb-0">{{ $documents->total() ?? 0 }} <span class="fs-6 text-muted fw-normal">file</span></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card bg-white p-4 rounded-4 shadow-sm border-0 d-flex align-items-center gap-4 hover-lift h-100">
                <div class="icon-wrapper emerald shadow-sm flex-shrink-0"><i class="bi bi-hdd-fill"></i></div>
                <div>
                    <p class="text-muted small fw-bold text-uppercase letter-spacing-1 mb-1">Dung lượng đã dùng</p>
                    <h2 class="fw-800 text-dark mb-0">{{ $totalSizeMB ?? 0 }} <span class="fs-6 text-muted fw-normal">MB</span></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card p-4 rounded-4 shadow-sm d-flex align-items-center gap-4 hover-lift h-100" style="background: linear-gradient(135deg, var(--theme-light) 0%, #ffffff 100%); border: 1px solid var(--theme-subtle) !important;">
                <div class="icon-wrapper purple shadow-sm flex-shrink-0"><i class="bi bi-magic"></i></div>
                <div>
                    <p class="theme-text-primary small fw-bold text-uppercase letter-spacing-1 mb-1">Sẵn sàng cho AI</p>
                    <h6 class="fw-bold text-dark mb-0">Tạo đề thi tự động từ tài liệu chỉ với 1 click!</h6>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách Tài liệu -->
    <div class="row g-4">
        @forelse($documents as $doc)
            @php
                $ext = strtolower($doc->file_type);
                $iconClass = 'bi-file-earmark-text icon-txt';
                $bgClass = 'bg-gray-gradient';
                
                if($ext == 'pdf') { $iconClass = 'bi-file-earmark-pdf-fill icon-pdf'; $bgClass = 'bg-red-gradient'; }
                if(in_array($ext, ['doc', 'docx'])) { $iconClass = 'bi-file-earmark-word-fill icon-doc'; $bgClass = 'bg-blue-gradient'; }
            @endphp

            <div class="col-md-4 col-xl-3">
                <div class="doc-card bg-white shadow-sm rounded-4 h-100 d-flex flex-column hover-lift">
                    <div class="doc-icon-wrapper {{ $bgClass }} position-relative overflow-hidden">
                        <i class="{{ $iconClass }} text-white opacity-25 position-absolute" style="font-size: 8rem; right: -20px; bottom: -20px;"></i>
                        
                        <div class="d-flex align-items-center justify-content-center h-100 position-relative z-1">
                            <i class="{{ $iconClass }} text-white" style="font-size: 3.5rem; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.2));"></i>
                        </div>
                    </div>
                    
                    <div class="p-4 flex-grow-1 d-flex flex-column">
                        <h6 class="fw-bold text-dark mb-2 text-truncate" title="{{ $doc->title }}">{{ $doc->title }}</h6>
                        <p class="text-muted small fw-medium mb-4 d-flex align-items-center gap-1"><i class="bi bi-calendar3"></i> Đã tải lên: {{ $doc->created_at->format('d/m/Y') }}</p>
                        
                        <div class="mt-auto d-flex justify-content-between align-items-center">
                            <span class="badge bg-soft text-muted border border-soft rounded-pill px-3 py-1.5 fw-bold shadow-sm">
                                {{ strtoupper($ext) }} <span class="mx-1">•</span> {{ number_format($doc->file_size / 1024, 1) }} KB
                            </span>
                            
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light rounded-circle shadow-sm transition-all hover-purple" type="button" data-bs-toggle="dropdown" style="width: 36px; height: 36px;">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4 mt-2">
                                    <li>
                                        <a class="dropdown-item py-2 fw-bold theme-text-primary" href="{{ route('teacher.exams.create', ['document_id' => $doc->id]) }}">
                                            <i class="bi bi-magic me-2"></i> Tạo đề AI từ file này
                                        </a>
                                    </li>
                                    <li><a class="dropdown-item py-2 fw-medium text-dark" href="{{ route('teacher.documents.preview', $doc->id) }}" target="_blank"><i class="bi bi-eye-fill me-2 text-muted"></i> Xem tài liệu</a></li>
                                    <li><hr class="dropdown-divider opacity-25"></li>
                                    <li>
                                        <form action="{{ route('teacher.documents.destroy', $doc->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài liệu này? Không thể khôi phục lại được!');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item py-2 text-danger fw-bold"><i class="bi bi-trash3-fill me-2"></i> Xóa tài liệu</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5 bg-white border-0 rounded-4 shadow-sm hover-lift transition-all">
                    <div class="icon-wrapper purple rounded-circle mx-auto mb-4" style="width: 90px; height: 90px; font-size: 3rem;">
                        <i class="bi bi-cloud-arrow-up-fill"></i>
                    </div>
                    <h4 class="text-dark fw-bold mb-2">Kho tài liệu trống</h4>
                    <p class="text-muted mb-4 max-w-md mx-auto fw-medium">Tải lên các file PDF, Word bài giảng của bạn. Hệ thống AI của EduQuiz sẽ phân tích và tạo ra hàng trăm câu hỏi tự động chỉ trong vài giây!</p>
                    <button class="btn btn-purple-gradient rounded-pill px-4 py-2.5 fw-bold transition-all" data-bs-toggle="modal" data-bs-target="#uploadModal">
                        <i class="bi bi-plus-lg me-1"></i> Tải tài liệu lên ngay
                    </button>
                </div>
            </div>
        @endforelse
    </div>
    
    <div class="mt-5 d-flex justify-content-center custom-pagination">
        {{ $documents->links('pagination::bootstrap-5') }}
    </div>

    <!-- Modal Upload Tài Liệu -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-bottom-0 pb-0 bg-white pt-4 px-4 position-relative z-1">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                        <div class="bg-purple-light theme-text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bi bi-cloud-upload-fill"></i>
                        </div>
                        Tải tài liệu lên hệ thống
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <form action="{{ route('teacher.documents.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body px-4 py-4 bg-white position-relative">
                        <i class="bi bi-folder-plus position-absolute theme-text-primary" style="font-size: 15rem; opacity: 0.03; top: -10%; right: -5%; pointer-events: none;"></i>
                        
                        <div class="form-group mb-4 position-relative z-1">
                            <label class="form-label fw-bold text-dark small text-uppercase letter-spacing-1">Tên tài liệu (Tùy chọn)</label>
                            <input type="text" class="form-control custom-input form-control-lg rounded-3" name="title" placeholder="VD: Bài giảng Lịch sử Chương 1 (Để trống sẽ lấy tên file)">
                        </div>

                        <div class="form-group mb-2 position-relative z-1">
                            <label class="form-label fw-bold text-dark small text-uppercase letter-spacing-1 d-flex justify-content-between">
                                Chọn File Upload <span class="text-danger">*</span>
                                <span class="text-muted fw-normal text-lowercase"><i class="bi bi-info-circle theme-text-primary"></i> Tối đa 10MB</span>
                            </label>
                            
                            <label for="fileInput" class="upload-zone d-block w-100 m-0 shadow-sm transition-all">
                                <div class="upload-icon-pulse mb-3">
                                    <div class="icon-wrapper purple mx-auto shadow-sm" style="width: 80px; height: 80px; font-size: 2.5rem;">
                                        <i class="bi bi-cloud-arrow-up-fill"></i>
                                    </div>
                                </div>
                                <span class="fw-800 text-dark fs-5 d-block mb-1">Kéo thả file hoặc nhấn để chọn</span>
                                <p class="text-muted small fw-medium mb-0">Hỗ trợ định dạng: PDF, DOC, DOCX, TXT</p>
                            </label>
                            
                            <input type="file" id="fileInput" name="document" style="opacity: 0; position: absolute; z-index: -1;" accept=".pdf,.doc,.docx,.txt" required>
                            
                            <div id="fileNameDisplay" class="text-center mt-3"></div>
                        </div>

                    </div>
                    <div class="modal-footer border-top-0 bg-soft py-3 px-4 rounded-bottom-4">
                        <button type="button" class="btn btn-white fw-bold rounded-pill px-4 border shadow-sm transition-all" data-bs-dismiss="modal">Hủy bỏ</button>
                        <button type="submit" class="btn btn-purple-gradient fw-bold rounded-pill px-4 py-2 transition-all d-flex align-items-center gap-2">
                            <i class="bi bi-upload"></i> Bắt đầu tải lên
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    {{-- Giữ nguyên script xử lý giao diện Upload Modal (Tên file, kéo thả) --}}
    <script src="{{ asset('js/teacher/teacher_documents.js') }}?v={{ time() }}"></script>
@endpush