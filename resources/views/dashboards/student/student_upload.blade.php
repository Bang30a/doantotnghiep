@extends('layouts.student.student_app')

@section('title', 'Quản lý tài liệu')

@push('styles')
    <link rel="stylesheet" href="{{ versioned_asset('css/admin/admin_settings.css') }}">
    <link rel="stylesheet" href="{{ versioned_asset('css/student/student_upload.css') }}">
@endpush

@section('content')
    @if(session('success'))
        <div class="alert alert-custom-success alert-dismissible fade show mb-4 shadow-sm border-0" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm border-0" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm border-0" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="student-page-heading mb-4">
        <h2 class="fw-bold mb-1 theme-text-dark">Quản lý tài liệu</h2>
        <p class="text-muted fs-6 mb-0">Upload và lưu trữ tài liệu học tập cá nhân</p>
    </div>

    <div class="row g-4">
        <div class="col-xl-5 col-lg-6">
            <div class="settings-card card border-0 shadow-sm h-100 mb-0 rounded-4 p-4">
                <h6 class="fw-bold mb-1 theme-text-dark">Upload tài liệu mới</h6>
                <p class="text-muted mb-4" style="font-size: 0.85rem;">Tải lên tài liệu PDF, DOCX, DOC</p>

                <form action="{{ route('student.documents.store') ?? '#' }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="upload-drop-zone mb-4" id="dropZone">
                        <div class="upload-icon-wrapper mb-3">
                            <i class="bi bi-cloud-arrow-up fs-1 theme-text-primary"></i>
                        </div>
                        <h6 class="fw-bold mb-1 theme-text-dark">Nhấp để chọn tệp</h6>
                        <p class="text-muted small mb-0">hoặc kéo thả tệp vào đây</p>
                        <input type="file" name="document" id="fileInput" class="d-none" accept=".pdf,.doc,.docx,.txt" required>
                        <div id="fileNameDisplay" class="mt-3 py-2 px-3 bg-purple-light text-purple-dark rounded-3 fw-medium small d-none border border-purple-subtle">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size: 0.9rem;">Môn học <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-book"></i></span>
                            <input type="text" name="subject" class="form-control form-control-custom bg-light border-start-0 ps-0" placeholder="VD: Toán học, Lịch sử..." required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="font-size: 0.9rem;">Mô tả (tuỳ chọn)</label>
                        <textarea name="description" class="form-control form-control-custom bg-light" rows="3" placeholder="Ghi chú thêm về tài liệu này..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-theme-primary text-white w-100 py-2 fw-medium d-flex justify-content-center align-items-center gap-2 shadow-sm">
                        <i class="bi bi-upload"></i> Tải lên tài liệu
                    </button>
                </form>
            </div>
        </div>

        <div class="col-xl-7 col-lg-6">
            <div class="settings-card card border-0 shadow-sm h-100 mb-0 rounded-4 p-4">
                <h6 class="fw-bold mb-1 theme-text-dark">Tài liệu đã lưu ({{ $documents->count() ?? 0 }})</h6>
                <p class="text-muted mb-4" style="font-size: 0.85rem;">Danh sách tài liệu đã tải lên hệ thống</p>

                <div class="document-list pe-2" style="max-height: 550px; overflow-y: auto;">
                    @forelse($documents ?? [] as $doc)
                        <div class="document-item d-flex align-items-center justify-content-between p-3 mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="doc-icon rounded-3">
                                    @if(in_array(strtolower($doc->file_type), ['pdf']))
                                        <i class="bi bi-filetype-pdf fs-3 text-danger"></i>
                                    @elseif(in_array(strtolower($doc->file_type), ['doc', 'docx']))
                                        <i class="bi bi-filetype-docx fs-3 text-primary"></i>
                                    @else
                                        <i class="bi bi-file-earmark-text fs-3 theme-text-primary"></i>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 theme-text-dark text-truncate" style="max-width: 250px;" title="{{ $doc->title }}">{{ $doc->title }}</h6>
                                    <div class="text-muted d-flex align-items-center gap-2" style="font-size: 0.8rem;">
                                        <span class="badge bg-purple-light text-purple-dark fw-normal">{{ $doc->subject }}</span>
                                        <span>&bull;</span>
                                        <span>{{ strtoupper($doc->file_type) }}</span>
                                        <span>&bull;</span>
                                        <span>{{ number_format($doc->file_size / 1048576, 2) }} MB</span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex gap-1">
                                <a href="{{ asset($doc->file_path) }}" target="_blank" class="btn-action-icon" title="Xem"><i class="bi bi-eye"></i></a>
                                <a href="{{ asset($doc->file_path) }}" download class="btn-action-icon" title="Tải xuống"><i class="bi bi-download"></i></a>
                                <form action="{{ route('student.documents.destroy', $doc->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bác có chắc chắn muốn xóa tài liệu này không?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action-icon text-danger delete-btn" title="Xóa"><i class="bi bi-trash3"></i></button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <div class="empty-state-icon mb-3 mx-auto d-flex align-items-center justify-content-center bg-purple-light rounded-circle" style="width: 80px; height: 80px;">
                                <i class="bi bi-folder-x fs-1 theme-text-primary"></i>
                            </div>
                            <h6 class="fw-bold theme-text-dark">Chưa có tài liệu</h6>
                            <p class="text-muted mt-1 mb-0">Bạn chưa tải lên tài liệu nào. Hãy dùng form bên trái nhé!</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="settings-card card border-0 shadow-sm mt-4 rounded-4 p-4 bg-purple-gradient text-white">
        <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-2"></i>Hướng dẫn sử dụng</h6>
        <ul class="mb-0 opacity-75" style="font-size: 0.9rem; line-height: 1.8;">
            <li>Chỉ hỗ trợ các định dạng: <strong>PDF, DOCX, DOC, TXT</strong></li>
            <li>Kích thước tệp tối đa: <strong>{{ $globalSettings['max_upload_size'] ?? '10' }} MB</strong></li>
            <li>Tài liệu sẽ được lưu trữ an toàn và AI có thể sử dụng để tạo đề thi tự động.</li>
        </ul>
    </div>
@endsection

@push('scripts')
    <script src="{{ versioned_asset('js/student/student_upload.js') }}"></script>
@endpush
