@extends('layouts.admin.admin_app')

@section('title', 'Cài đặt hệ thống')

@push('styles')
    <link rel="stylesheet" href="{{ versioned_asset('css/admin/admin_settings.css') }}">
@endpush

@section('content')

<div class="admin-app-window d-flex flex-column">

    <div class="flex-shrink-0 mb-3">
        @if(session('success'))
            <div class="alert bg-emerald-soft text-emerald border border-emerald-subtle py-2 px-3 mb-2 shadow-sm rounded-3 d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> 
                <span class="fw-bold small">{{ session('success') }}</span>
                <button type="button" class="btn-close btn-close-sm ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="admin-page-heading d-flex justify-content-between align-items-center pb-2 mb-2">
            <div class="d-flex align-items-center gap-3">
                <h4 class="fw-900 text-dark mb-0 d-flex align-items-center gap-2">
                    Cài đặt hệ thống <i class="bi bi-gear-fill theme-text-primary fs-5"></i>
                </h4>
                <span class="text-muted small fw-medium d-none d-md-block border-start ps-3">Quản lý cấu hình EduQuiz AI</span>
            </div>
            
            <div class="d-flex gap-2">
                <button type="button" id="btn-reset-settings" class="btn btn-light fw-bold px-3 py-1.5 rounded-3 shadow-sm border small transition-all">Khôi phục</button>
                <button type="submit" form="settingsForm" class="btn btn-purple-gradient text-white fw-bold px-4 py-1.5 rounded-3 shadow-sm d-flex align-items-center gap-2 small transition-all">
                    <i class="bi bi-floppy-fill"></i> Lưu cài đặt
                </button>
            </div>
        </div>

        <ul class="nav nav-pills custom-pills gap-2" id="settingsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold d-flex align-items-center gap-2 px-3 py-1.5 rounded-3 small" id="tab-chung-btn" data-bs-toggle="pill" data-bs-target="#tab-chung" type="button" role="tab">
                    <i class="bi bi-globe"></i> Chung
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold d-flex align-items-center gap-2 px-3 py-1.5 rounded-3 small" id="tab-bao-mat-btn" data-bs-toggle="pill" data-bs-target="#tab-bao-mat" type="button" role="tab">
                    <i class="bi bi-shield-lock-fill"></i> Bảo mật
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold d-flex align-items-center gap-2 px-3 py-1.5 rounded-3 small" id="tab-thong-bao-btn" data-bs-toggle="pill" data-bs-target="#tab-thong-bao" type="button" role="tab">
                    <i class="bi bi-bell-fill"></i> Thông báo
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold d-flex align-items-center gap-2 px-3 py-1.5 rounded-3 small" id="tab-luu-tru-btn" data-bs-toggle="pill" data-bs-target="#tab-luu-tru" type="button" role="tab">
                    <i class="bi bi-database-fill"></i> Lưu trữ
                </button>
            </li>
        </ul>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST" id="settingsForm" class="flex-grow-1 overflow-hidden d-flex flex-column m-0">
        @csrf

        <div class="card border border-light-subtle shadow-sm rounded-4 flex-grow-1 overflow-hidden d-flex flex-column bg-white">
            <div class="tab-content flex-grow-1 overflow-y-auto custom-scrollbar p-4 p-md-5" id="settingsTabsContent">
                
                <div class="tab-pane fade show active" id="tab-chung" role="tabpanel">
                    <div class="d-flex align-items-center gap-2 mb-3 border-bottom pb-2">
                        <i class="bi bi-info-square-fill text-primary fs-5"></i>
                        <h6 class="fw-bold mb-0 text-dark text-uppercase letter-spacing-1 small">Thông tin cơ bản</h6>
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small text-uppercase">Tên hệ thống</label>
                            <input type="text" name="site_name" class="form-control custom-input rounded-3 py-2 fw-medium text-dark" value="{{ $settings['site_name'] ?? 'EduQuiz AI' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small text-uppercase">URL hệ thống</label>
                            <input type="url" name="site_url" class="form-control custom-input rounded-3 py-2 fw-medium text-dark" value="{{ $settings['site_url'] ?? 'http://localhost:8000' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted small text-uppercase">Email quản trị</label>
                            <input type="email" name="admin_email" class="form-control custom-input rounded-3 py-2 fw-medium text-dark" value="{{ $settings['admin_email'] ?? 'admin@eduquiz.ai' }}">
                        </div>
                        <div class="col-md-6">
                            @php $systemTimezone = $settings['system_timezone'] ?? 'Asia/Ho_Chi_Minh'; @endphp
                            <label class="form-label fw-bold text-muted small text-uppercase">Múi giờ hệ thống</label>
                            <select name="system_timezone" class="form-select custom-input rounded-3 py-2 fw-bold text-primary bg-primary bg-opacity-10 border-primary border-opacity-25">
                                <option value="Asia/Ho_Chi_Minh" {{ $systemTimezone == 'Asia/Ho_Chi_Minh' ? 'selected' : '' }}>Việt Nam - GMT+7</option>
                                <option value="UTC" {{ $systemTimezone == 'UTC' ? 'selected' : '' }}>UTC - Giờ chuẩn quốc tế</option>
                                <option value="Asia/Bangkok" {{ $systemTimezone == 'Asia/Bangkok' ? 'selected' : '' }}>Bangkok - GMT+7</option>
                                <option value="Asia/Tokyo" {{ $systemTimezone == 'Asia/Tokyo' ? 'selected' : '' }}>Tokyo - GMT+9</option>
                            </select>
                            <div class="form-text small text-muted">Áp dụng cho thời gian nộp bài, lịch sử hoạt động và báo cáo.</div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2 mb-3 border-bottom pb-2 mt-4">
                        <i class="bi bi-person-plus-fill text-success fs-5"></i>
                        <h6 class="fw-bold mb-0 text-dark text-uppercase letter-spacing-1 small">Cài đặt đăng ký</h6>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3 bg-light p-3 rounded-3 border border-light-subtle">
                        <div>
                            <div class="fw-bold text-dark small">Cho phép đăng ký mới</div>
                            <div class="text-muted small fw-medium" style="font-size: 0.8rem;">Người dùng có thể tự đăng ký tài khoản trên trang chủ</div>
                        </div>
                        <div class="form-check form-switch fs-5 mb-0">
                            <input type="hidden" name="allow_registration" value="0">
                            <input class="form-check-input custom-switch shadow-sm m-0" type="checkbox" name="allow_registration" value="1" {{ ($settings['allow_registration'] ?? '1') == '1' ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded-3 border border-light-subtle">
                        <div>
                            <div class="fw-bold text-dark small">Xác thực Email</div>
                            <div class="text-muted small fw-medium" style="font-size: 0.8rem;">Bắt buộc người dùng xác thực qua liên kết gửi vào email</div>
                        </div>
                        <div class="form-check form-switch fs-5 mb-0">
                            <input type="hidden" name="require_email_verify" value="0">
                            <input class="form-check-input custom-switch shadow-sm m-0" type="checkbox" name="require_email_verify" value="1" {{ ($settings['require_email_verify'] ?? '0') == '1' ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="tab-bao-mat" role="tabpanel">
                    <div class="d-flex align-items-center gap-2 mb-3 border-bottom pb-2">
                        <i class="bi bi-shield-lock-fill text-warning fs-5"></i>
                        <h6 class="fw-bold mb-0 text-dark text-uppercase letter-spacing-1 small">Bảo mật phiên làm việc</h6>
                    </div>
                    
                    <div class="mb-2" style="max-width: 400px;">
                        <label class="form-label fw-bold text-muted small text-uppercase">Thời gian hết phiên (Phút)</label>
                        <input type="number" name="session_timeout" class="form-control custom-input rounded-3 py-2 fw-bold text-dark" value="{{ $settings['session_timeout'] ?? '60' }}">
                        <small class="text-muted mt-2 d-block fw-medium" style="font-size: 0.8rem;"><i class="bi bi-info-circle-fill text-primary me-1"></i>Phiên làm việc sẽ tự động đăng xuất sau khoảng thời gian này nếu không hoạt động.</small>
                    </div>
                </div>

                <div class="tab-pane fade" id="tab-thong-bao" role="tabpanel">
                    <div class="d-flex align-items-center gap-2 mb-3 border-bottom pb-2">
                        <i class="bi bi-envelope-at-fill text-info fs-5"></i>
                        <h6 class="fw-bold mb-0 text-dark text-uppercase letter-spacing-1 small">Cấu hình Email (SMTP)</h6>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label class="form-label fw-bold text-muted small text-uppercase">Máy chủ SMTP (Host)</label>
                            <input type="text" name="smtp_host" class="form-control custom-input rounded-3 py-2 fw-medium text-dark" value="{{ $settings['smtp_host'] ?? 'smtp.gmail.com' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted small text-uppercase">Cổng (Port)</label>
                            <input type="text" name="smtp_port" class="form-control custom-input rounded-3 py-2 fw-medium text-dark" value="{{ $settings['smtp_port'] ?? '587' }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-muted small text-uppercase">Tài khoản (Username)</label>
                            <input type="email" name="smtp_username" class="form-control custom-input rounded-3 py-2 fw-medium text-dark" value="{{ $settings['smtp_username'] ?? '' }}" placeholder="your-email@gmail.com">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-muted small text-uppercase">Mật khẩu ứng dụng (Password)</label>
                            <input type="password" name="smtp_password" class="form-control custom-input rounded-3 py-2 fw-medium text-dark" value="{{ $settings['smtp_password'] ?? '' }}" placeholder="••••••••••••">
                        </div>
                    </div>
                    <button type="button" id="btn-test-email" data-url="{{ route('admin.settings.test-email') }}" class="btn btn-light text-primary fw-bold rounded-3 px-4 py-2 transition-all w-100 border border-primary border-opacity-25 small hover-lift">
                        <i class="bi bi-send-check-fill me-1"></i> Gửi email kiểm tra kết nối
                    </button>
                </div>

            <div class="tab-pane fade" id="tab-luu-tru" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-hdd-network-fill text-primary fs-5"></i>
                            <h6 class="fw-bold mb-0 text-dark text-uppercase letter-spacing-1 small">Sử dụng bộ nhớ</h6>
                        </div>
                        <button type="button" id="btn-clear-cache" data-url="{{ route('admin.settings.clear-cache') }}" class="btn btn-danger">
                            <i class="bi bi-trash3-fill me-1"></i> Dọn dẹp
                        </button>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-end mb-2 mt-2">
                        <span class="fw-bold text-muted small text-uppercase letter-spacing-1">Đã sử dụng</span>
                        <span class="fw-800 text-primary fs-6">
                            {{ isset($totalMB) && (float)str_replace(',', '', $totalMB) >= 1024 ? number_format((float)str_replace(',', '', $totalMB) / 1024, 2) . ' GB' : ($totalMB ?? '0.00') . ' MB' }} 
                            <span class="text-muted small fw-medium">/ {{ $totalSpaceGB ?? 50 }} GB</span>
                        </span>
                    </div>
                    @php
                        $rawUsedPercentage = (float) ($usedPercentage ?? 0);
                        $displayUsedPercentage = $rawUsedPercentage > 0 ? max($rawUsedPercentage, 0.8) : 0;
                    @endphp
                    
                    <div class="progress mb-4 bg-light border" style="height: 10px; border-radius: 10px;">
                        <div class="progress-bar bg-primary rounded-pill" role="progressbar" style="width: {{ $displayUsedPercentage }}%;" aria-valuenow="{{ number_format($rawUsedPercentage, 4, '.', '') }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>

                    <div class="row g-3 mb-2 text-center">
                        <div class="col-md-4">
                            <div class="bg-light text-primary rounded-3 p-3 border border-light-subtle">
                                <i class="bi bi-folder-symlink-fill fs-3 mb-1 d-block"></i>
                                <h4 class="fw-800 mb-0">{{ number_format($documentCount ?? 0) }}</h4>
                                <p class="mb-0 small fw-bold text-uppercase mt-1 opacity-75" style="font-size: 0.7rem;">Tài liệu</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light text-success rounded-3 p-3 border border-light-subtle">
                                <i class="bi bi-journal-text fs-3 mb-1 d-block"></i>
                                <h4 class="fw-800 mb-0">{{ number_format($examCount ?? 0) }}</h4>
                                <p class="mb-0 small fw-bold text-uppercase mt-1 opacity-75" style="font-size: 0.7rem;">Đề thi</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light text-warning rounded-3 p-3 border border-light-subtle">
                                <i class="bi bi-ui-checks-grid fs-3 mb-1 d-block"></i>
                                <h4 class="fw-800 mb-0">{{ number_format($questionCount ?? 0) }}</h4>
                                <p class="mb-0 small fw-bold text-uppercase mt-1 opacity-75" style="font-size: 0.7rem;">Câu hỏi</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div> </div>
    </form>
</div>
@endsection
@push('scripts')
    <script src="{{ versioned_asset('js/admin/admin_settings.js') }}"></script>
@endpush
