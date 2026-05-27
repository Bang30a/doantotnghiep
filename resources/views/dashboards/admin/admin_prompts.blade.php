@extends('layouts.admin.admin_app')

@section('title', 'Cấu hình AI (Prompts)')

@push('styles')
    <!-- Link file CSS riêng -->
    <link rel="stylesheet" href="{{ asset('css/admin/admin_prompts.css') }}?v={{ time() }}">
@endpush

@section('content')

    <!-- Tiêu đề trang & Thanh công cụ -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4 gap-3 mt-2 border-bottom border-light-subtle pb-3">
        <div>
            <h3 class="fw-800 text-dark mb-1 d-flex align-items-center gap-2">
                Cấu hình AI Prompts <i class="bi bi-robot theme-text-primary"></i>
            </h3>
            <p class="text-muted fw-medium mb-0">Quản lý các lệnh chỉ thị (Prompts) giúp AI sinh đề thi thông minh hơn</p>
        </div>
        
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-theme-primary text-white fw-bold px-4 shadow-sm d-flex align-items-center gap-2 hover-pulse transition-all" data-bs-toggle="modal" data-bs-target="#addPromptModal" style="border-radius: 12px;">
                <i class="bi bi-plus-lg"></i> Thêm Prompt mới
            </button>
        </div>
    </div>

    <!-- Thông báo Thành công/Lỗi -->
    @if(session('success'))
        <div class="alert alert-emerald-soft alert-dismissible fade show mb-4 border border-emerald-subtle shadow-sm rounded-4 d-flex align-items-center px-4 py-3 auto-close-alert" role="alert">
            <i class="bi bi-check-circle-fill fs-4 me-3 text-emerald"></i> 
            <span class="fw-bold fs-6">{{ session('success') }}</span>
            <button type="button" class="btn-close mt-2 me-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger-soft alert-dismissible fade show mb-4 border border-danger-subtle shadow-sm rounded-4 d-flex align-items-start px-4 py-3 auto-close-alert" role="alert" style="background-color: #fee2e2; color: #991b1b;">
            <i class="bi bi-exclamation-triangle-fill fs-4 me-3 mt-1 text-danger"></i> 
            <div>
                <ul class="mb-0 fw-medium ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="btn-close mt-2 me-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Bảng danh sách Prompts -->
    <div class="card border-0 shadow rounded-4 mb-4 bg-white" style="border-radius: 16px !important;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table custom-table align-middle mb-0">
                    <thead class="bg-light border-bottom border-light-subtle">
                        <tr>
                            <th width="35%" class="ps-4 py-3 text-muted fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Tên Prompt</th>
                            <th width="30%" class="py-3 text-muted fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Mô tả chỉ thị</th>
                            <th width="15%" class="text-center py-3 text-muted fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Model AI</th>
                            <th width="10%" class="text-center py-3 text-muted fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Trạng thái</th>
                            <th width="10%" class="text-end pe-4 py-3 text-muted fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($prompts as $prompt)
                            <tr class="hover-row transition-all border-bottom border-light-subtle">
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="prompt-icon-box bg-purple-light theme-text-primary shadow-sm border border-white">
                                            <i class="bi bi-terminal-fill fs-4"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1 fs-6 prompt-name">{{ $prompt->name }}</h6>
                                            <small class="text-muted fw-medium d-flex align-items-center gap-1">
                                                <i class="bi bi-clock-history opacity-75"></i> Cập nhật: {{ \Carbon\Carbon::parse($prompt->updated_at)->locale('vi')->diffForHumans() }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <span class="text-muted fw-medium small d-inline-block text-truncate" style="max-width: 250px;" title="{{ $prompt->description ?? 'Không có mô tả' }}">
                                        {{ $prompt->description ?? 'Không có mô tả' }}
                                    </span>
                                </td>
                                <td class="text-center py-3">
                                    <span class="badge bg-light text-dark border border-secondary-subtle px-3 py-2 rounded-pill fw-bold shadow-sm d-inline-flex align-items-center gap-2">
                                        <i class="bi bi-cpu-fill theme-text-primary"></i> {{ $prompt->model_type ?? 'gemini-pro' }}
                                    </span>
                                </td>
                                <td class="text-center py-3">
                                    @if($prompt->status == '1' || $prompt->status == 'active')
                                        <span class="badge custom-badge-success d-inline-flex align-items-center gap-1 px-3 py-2 rounded-pill shadow-sm">
                                            <i class="bi bi-check-circle-fill"></i> Đang dùng
                                        </span>
                                    @else
                                        <span class="badge bg-warning-soft text-warning-dark border border-warning-subtle d-inline-flex align-items-center gap-1 px-3 py-2 rounded-pill shadow-sm">
                                            <i class="bi bi-tools"></i> Đang Test
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end pe-4 py-3">
                                    <div class="d-flex justify-content-end gap-2">
                                        <!-- Nút Sửa -->
                                        <button type="button" class="btn btn-sm btn-light border shadow-sm rounded-circle d-inline-flex align-items-center justify-content-center transition-all btn-action-edit" title="Chỉnh sửa" data-bs-toggle="modal" data-bs-target="#editPromptModal_{{ $prompt->id }}" style="width: 38px; height: 38px;">
                                            <i class="bi bi-pencil-square fs-6 text-muted"></i>
                                        </button>

                                        <!-- Nút Xóa -->
                                        <form action="{{ route('admin.prompts.destroy', $prompt->id) }}" method="POST" class="d-inline-block m-0 p-0 form-delete-prompt">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-light border shadow-sm rounded-circle d-inline-flex align-items-center justify-content-center transition-all btn-action-delete btn-confirm-delete" title="Xóa" style="width: 38px; height: 38px;">
                                                <i class="bi bi-trash3-fill fs-6 text-danger"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal Chỉnh Sửa Prompt -->
                            <div class="modal fade text-start" id="editPromptModal_{{ $prompt->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                                        <div class="modal-header bg-light border-bottom-0 py-3 px-4" style="border-top-left-radius: 20px; border-top-right-radius: 20px;">
                                            <h5 class="modal-title fw-bold text-dark"><i class="bi bi-pencil-square theme-text-primary me-2"></i>Chỉnh sửa Prompt</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('admin.prompts.update', $prompt->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body p-4 text-start">
                                                <div class="row g-4">
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-bold text-dark mb-1">Tên Prompt <span class="text-danger">*</span></label>
                                                        <input type="text" name="name" class="form-control rounded-3 py-2 shadow-none border-light-subtle" value="{{ $prompt->name }}" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-bold text-dark mb-1">Model AI <span class="text-danger">*</span></label>
                                                        <select name="model" class="form-select bg-light rounded-3 py-2 shadow-none border-light-subtle" required>
                                                            <option value="gemini-pro" selected>Google Gemini (Mặc định)</option>
                                                        </select>
                                                        <small class="text-muted fst-italic mt-1 d-block"><i class="bi bi-info-circle me-1"></i>Tối ưu hóa cho mô hình Gemini.</small>
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label fw-bold text-dark mb-1">Mô tả ngắn</label>
                                                        <input type="text" name="description" class="form-control rounded-3 py-2 shadow-none border-light-subtle" value="{{ $prompt->description }}">
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label fw-bold text-dark mb-1">Nội dung Prompt <span class="text-danger">*</span></label>
                                                        <textarea name="content" class="form-control rounded-3 py-2 shadow-none border-light-subtle" rows="6" required>{{ $prompt->prompt_text }}</textarea>
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label fw-bold mb-2 text-dark">Trạng thái</label>
                                                        <div class="d-flex gap-4">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="status" id="statusActive_{{ $prompt->id }}" value="1" {{ ($prompt->status == '1' || $prompt->status == 'active') ? 'checked' : '' }}>
                                                                <label class="form-check-label text-success fw-bold" for="statusActive_{{ $prompt->id }}">Hoạt động</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="status" id="statusInactive_{{ $prompt->id }}" value="0" {{ ($prompt->status == '0' || $prompt->status == 'inactive') ? 'checked' : '' }}>
                                                                <label class="form-check-label text-warning-dark fw-bold" for="statusInactive_{{ $prompt->id }}">Vô hiệu hóa</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-top-0 bg-light py-3 px-4" style="border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;">
                                                <button type="button" class="btn btn-secondary rounded-pill px-4 fw-medium" data-bs-dismiss="modal">Hủy bỏ</button>
                                                <button type="submit" class="btn btn-theme-primary text-white rounded-pill px-4 fw-bold shadow-sm hover-pulse"><i class="bi bi-floppy-fill me-2"></i>Lưu thay đổi</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 bg-light">
                                    <div class="empty-state-icon bg-white text-muted rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 80px; height: 80px; font-size: 2rem;">
                                        <i class="bi bi-terminal-x"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-1">Chưa có cấu hình Prompt nào</h5>
                                    <p class="text-muted fw-medium mb-0">Hãy thêm Prompt mới để AI có thể bắt đầu sinh đề thi.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Thêm Prompt Mới -->
    <div class="modal fade text-start" id="addPromptModal" tabindex="-1" aria-labelledby="addPromptModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header bg-light border-bottom-0 py-3 px-4" style="border-top-left-radius: 20px; border-top-right-radius: 20px;">
                    <h5 class="modal-title fw-bold text-dark" id="addPromptModalLabel">
                        <i class="bi bi-robot theme-text-primary me-2"></i>Thêm cấu hình Prompt mới
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.prompts.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold mb-1">Tên Prompt <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control rounded-3 py-2 shadow-none border-light-subtle" placeholder="VD: Tạo Trắc Nghiệm Chuẩn" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark mb-1">Model AI <span class="text-danger">*</span></label>
                                <select name="model" class="form-select bg-light rounded-3 py-2 shadow-none border-light-subtle" required>
                                    <option value="gemini-pro" selected>Google Gemini (Mặc định)</option>
                                </select>
                                <small class="text-muted fst-italic mt-1 d-block"><i class="bi bi-info-circle me-1"></i>Hệ thống đang cấu hình tối ưu hóa cho Gemini.</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold mb-1">Mô tả ngắn</label>
                                <input type="text" name="description" class="form-control rounded-3 py-2 shadow-none border-light-subtle" placeholder="VD: Dùng để tạo ra các câu hỏi trắc nghiệm 4 đáp án...">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold mb-1">Nội dung Prompt (Câu chỉ thị) <span class="text-danger">*</span></label>
                                <div class="alert bg-info-soft text-info border border-info-subtle py-2 mb-3 text-dark d-flex align-items-center rounded-3 shadow-sm" style="font-size: 0.85rem;">
                                    <i class="bi bi-info-circle-fill me-2 fs-5"></i> 
                                    <span>Sử dụng các biến: <code class="fw-bold text-danger px-1 bg-white rounded">[TOTAL_QUESTIONS]</code>, <code class="fw-bold text-danger px-1 bg-white rounded">[TOPIC]</code> để hệ thống tự động điền.</span>
                                </div>
                                <textarea name="content" class="form-control rounded-3 py-2 shadow-none border-light-subtle" rows="6" placeholder="Bạn là một chuyên gia giáo dục. Hãy tạo ra [TOTAL_QUESTIONS] câu hỏi..." required></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold mb-2">Trạng thái</label>
                                <div class="d-flex gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" id="statusActive" value="1" checked>
                                        <label class="form-check-label text-success fw-bold" for="statusActive">Hoạt động</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" id="statusInactive" value="0">
                                        <label class="form-check-label text-warning-dark fw-bold" for="statusInactive">Đang Test (Vô hiệu hóa)</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 bg-light py-3 px-4" style="border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;">
                        <button type="button" class="btn btn-secondary rounded-pill px-4 fw-medium" data-bs-dismiss="modal">Hủy bỏ</button>
                        <button type="submit" class="btn btn-theme-primary text-white rounded-pill px-4 fw-bold shadow-sm hover-pulse"><i class="bi bi-floppy-fill me-2"></i>Lưu cấu hình</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <!-- Link file JS riêng -->
    <script src="{{ asset('js/admin/admin_prompts.js') }}?v={{ time() }}"></script>
@endpush