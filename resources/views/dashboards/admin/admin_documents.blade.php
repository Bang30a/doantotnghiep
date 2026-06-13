@extends('layouts.admin.admin_app')

@section('title', 'Tài liệu hệ thống')

@push('styles')
    <!-- Link file CSS riêng -->
    <link rel="stylesheet" href="{{ versioned_asset('css/admin/admin_documents.css') }}">
@endpush

@section('content')

    <!-- Tiêu đề trang & Thanh công cụ -->
    <div class="admin-page-heading d-flex flex-column flex-xl-row justify-content-between align-items-xl-end mb-4 gap-3 mt-2 pb-3">
        <div>
            <h3 class="fw-800 text-dark mb-1 d-flex align-items-center gap-2">
                Tài liệu hệ thống <i class="bi bi-folder-symlink-fill theme-text-primary"></i>
            </h3>
            <p class="text-muted fw-medium mb-0">Quản lý các tệp tin (PDF, Word...) được tải lên máy chủ</p>
        </div>
        
        <div class="d-flex flex-wrap align-items-center gap-3">
            <form action="{{ route('admin.documents') }}" method="GET" class="d-flex gap-2">
                <div class="input-group search-focus shadow-sm rounded-4 border overflow-hidden transition-all bg-white" style="border-radius: 12px;">
                    <span class="input-group-text bg-white border-0 text-muted pe-1 ps-3"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-0 fw-medium shadow-none py-2" placeholder="Tìm tên tài liệu..." value="{{ request('search') }}">
                </div>
                <button type="submit" class="btn btn-theme-primary fw-bold px-4 rounded-4 shadow-sm hover-pulse text-white" style="border-radius: 12px;">
                    <i class="bi bi-funnel-fill me-1"></i> Lọc
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.documents') }}" class="btn btn-light fw-bold text-danger border rounded-4 px-3 transition-all" title="Xóa tìm kiếm" style="border-radius: 12px;">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Thông báo Thành công / Lỗi -->
    @if(session('success'))
        <div class="alert alert-emerald-soft alert-dismissible fade show mb-4 border border-emerald-subtle shadow-sm rounded-4 d-flex align-items-center px-4 py-3 auto-close-alert" role="alert">
            <i class="bi bi-check-circle-fill fs-4 me-3 text-emerald"></i> 
            <span class="fw-bold fs-6">{{ session('success') }}</span>
            <button type="button" class="btn-close mt-2 me-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger-soft alert-dismissible fade show mb-4 border border-danger-subtle shadow-sm rounded-4 d-flex align-items-center px-4 py-3 auto-close-alert" role="alert" style="background-color: #fee2e2; color: #991b1b;">
            <i class="bi bi-exclamation-triangle-fill fs-4 me-3 text-danger"></i> 
            <span class="fw-bold fs-6">{{ session('error') }}</span>
            <button type="button" class="btn-close mt-2 me-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Bảng danh sách Tài liệu bọc trong Card sang trọng -->
    <div class="card border-0 shadow rounded-4 mb-4 bg-white" style="border-radius: 16px !important;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table custom-table align-middle mb-0">
                    <thead class="bg-light border-bottom border-light-subtle">
                        <tr>
                            <th width="35%" class="ps-4 py-3 text-muted fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Tên tài liệu</th>
                            <th width="20%" class="py-3 text-muted fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Người tải lên</th>
                            <th width="15%" class="text-center py-3 text-muted fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Định dạng</th>
                            <th width="15%" class="text-center py-3 text-muted fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Ngày tải lên</th>
                            <th width="15%" class="text-end pe-4 py-3 text-muted fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documents as $doc)
                            <tr class="hover-row transition-all border-bottom border-light-subtle">
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        @if(strtolower($doc->file_type) == 'pdf')
                                            <div class="doc-icon-box shadow-sm border border-danger-subtle" style="background-color: #fee2e2; color: #dc2626;">
                                                <i class="bi bi-filetype-pdf fs-4"></i>
                                            </div>
                                        @else
                                            <div class="doc-icon-box shadow-sm border border-primary-subtle" style="background-color: #dbeafe; color: #2563eb;">
                                                <i class="bi bi-filetype-docx fs-4"></i>
                                            </div>
                                        @endif
                                        
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1 user-name text-truncate" style="max-width: 250px;" title="{{ $doc->title ?? 'Tài liệu chưa đặt tên' }}">
                                                {{ $doc->title ?? 'Tài liệu chưa đặt tên' }}
                                            </h6>
                                            <small class="text-muted fw-medium d-flex align-items-center gap-1">
                                                <i class="bi bi-hdd opacity-75"></i> {{ number_format(($doc->file_size ?? 0) / 1024, 1) }} KB
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="py-3">
                                    <span class="fw-bold text-dark d-flex align-items-center gap-2">
                                        <div class="bg-light rounded p-1"><i class="bi bi-person-circle theme-text-primary"></i></div>
                                        {{ $doc->user->name ?? 'Không xác định' }}
                                    </span>
                                </td>
                                
                                <td class="text-center py-3">
                                    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-bold text-uppercase shadow-sm">
                                        {{ $doc->file_type ?? 'Không rõ' }}
                                    </span>
                                </td>
                                
                                <td class="text-center py-3">
                                    <span class="text-muted fw-medium small d-flex align-items-center justify-content-center gap-2">
                                        <div class="bg-light rounded p-1"><i class="bi bi-calendar-event text-secondary"></i></div> 
                                        {{ $doc->created_at->format('d/m/Y H:i') }}
                                    </span>
                                </td>
                                
                                <td class="text-end pe-4 py-3">
                                    <div class="d-flex justify-content-end gap-2">
                                        @php
                                            // Các đuôi file trình duyệt có thể tự đọc được
                                            $viewableTypes = ['pdf', 'jpg', 'jpeg', 'png', 'txt'];
                                            $fileExt = strtolower($doc->file_type ?? '');
                                            $canPreview = in_array($fileExt, $viewableTypes);
                                        @endphp

                                        @if($canPreview)
                                            <a href="{{ route('admin.documents.preview', $doc->id) }}" target="_blank" class="btn btn-sm btn-light border shadow-sm rounded-circle d-inline-flex align-items-center justify-content-center transition-all btn-action-view" title="Xem trước" style="width: 38px; height: 38px;">
                                                <i class="bi bi-eye-fill fs-6 text-primary"></i>
                                            </a>
                                        @else
                                            <!-- Nếu là Word/Excel: Đổi icon thành mắt gạch chéo và báo lỗi -->
                                            <button type="button" class="btn btn-sm btn-light border shadow-sm rounded-circle d-inline-flex align-items-center justify-content-center transition-all" title="Trình duyệt không hỗ trợ xem trước" style="width: 38px; height: 38px; opacity: 0.6;" onclick="alert('Trình duyệt web không hỗ trợ xem trực tiếp file {{ strtoupper($fileExt) }}. Vui lòng bấm nút Tải xuống bên cạnh để xem nhé!')">
                                                <i class="bi bi-eye-slash-fill fs-6 text-muted"></i>
                                            </button>
                                        @endif

                                        <!-- Nút Tải (Giữ nguyên) -->
                                        <a href="{{ route('admin.documents.download', $doc->id) }}" class="btn btn-sm btn-light border shadow-sm rounded-circle d-inline-flex align-items-center justify-content-center transition-all btn-action-download" title="Tải xuống" style="width: 38px; height: 38px;">
                                            <i class="bi bi-cloud-arrow-down-fill fs-6 theme-text-primary"></i>
                                        </a>

                                        <!-- Nút Xóa (Giữ nguyên) -->
                                        <form action="{{ route('admin.documents.destroy', $doc->id) }}" method="POST" class="d-inline m-0 p-0 form-delete-doc">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-light border shadow-sm rounded-circle d-inline-flex align-items-center justify-content-center transition-all btn-action-delete btn-confirm-delete" title="Xóa file" style="width: 38px; height: 38px;">
                                                <i class="bi bi-trash3-fill fs-6 text-danger"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 bg-light">
                                    <div class="empty-state-icon bg-white text-muted rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 80px; height: 80px; font-size: 2rem;">
                                        <i class="bi bi-folder-x"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-2">Chưa có tài liệu nào</h5>
                                    <p class="text-muted fw-medium mb-0">Hệ thống hiện tại chưa có tệp tin nào được tải lên.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Phân trang -->
    @if(isset($documents) && method_exists($documents, 'links'))
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 mb-5">
            
            <!-- Khối thông tin và Chọn số lượng hiển thị -->
            <div class="d-flex align-items-center gap-3 bg-white px-4 py-2 rounded-pill shadow-sm border border-light-subtle">
                <span class="text-muted small fw-medium">
                    Đang hiển thị <strong class="text-dark">{{ $documents->firstItem() ?? 0 }} - {{ $documents->lastItem() ?? 0 }}</strong> trên tổng <strong class="text-dark">{{ $documents->total() }}</strong>
                </span>
                
                <div class="vr text-muted opacity-25" style="width: 1px; min-height: 20px;"></div>
                
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted small fw-medium">Hiển thị:</span>
                    <select class="form-select form-select-sm border-light-subtle shadow-none fw-bold text-dark" id="perPageSelect" style="width: 65px; border-radius: 8px; cursor: pointer; background-color: #f8f9fa;">
                        <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>
            </div>

            <!-- Khối nút phân trang -->
            <div class="custom-pagination shadow-sm rounded-pill overflow-hidden bg-white px-2">
                {{ $documents->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
            
        </div>
    @endif

@endsection

@push('scripts')
    <!-- Link file JS riêng -->
    <script src="{{ versioned_asset('js/admin/admin_documents.js') }}"></script>
@endpush
