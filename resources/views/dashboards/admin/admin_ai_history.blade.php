@extends('layouts.admin.admin_app')

@section('title', 'Lịch sử dùng AI')

@push('styles')
    <link rel="stylesheet" href="{{ versioned_asset('css/admin/admin_ai_history.css') }}">
@endpush

@section('content')

    <div class="admin-page-heading d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-3 gap-3 mt-1 pb-2">
        <div>
            <h4 class="fw-800 text-dark mb-1 d-flex align-items-center gap-2">
                Lịch sử dùng AI (API Logs) <i class="bi bi-activity theme-text-primary"></i>
            </h4>
            <p class="text-muted fw-medium mb-0 small">Theo dõi lượng Token và chi phí gọi API từ mọi người dùng</p>
        </div>
        
        <div class="d-flex gap-2">
            <a href="{{ route('admin.ai_history.export') }}" class="btn btn-light fw-bold px-4 py-2 shadow-sm border d-flex align-items-center gap-2 transition-all btn-export" style="border-radius: 10px;">
                <i class="bi bi-cloud-arrow-down-fill theme-text-primary fs-5"></i> Xuất CSV
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-emerald-soft alert-dismissible fade show mb-4 border border-emerald-subtle shadow-sm rounded-4 d-flex align-items-center px-4 py-3 auto-close-alert" role="alert">
            <i class="bi bi-check-circle-fill fs-4 me-3 text-emerald"></i> 
            <span class="fw-bold fs-6">{{ session('success') }}</span>
            <button type="button" class="btn-close mt-2 me-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="card stat-card border-0 shadow-sm p-3 px-4 d-flex flex-row align-items-center gap-3 transition-all h-100 bg-white" style="border-radius: 16px;">
                <div class="stat-icon-box bg-purple-light theme-text-primary shadow-sm border border-purple-subtle">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div>
                    <p class="text-muted mb-0 fw-bold text-uppercase letter-spacing-1" style="font-size: 0.75rem;">Tổng chi phí API (Tháng này)</p>
                    <h3 class="fw-800 text-dark mb-0 fs-3">${{ number_format($totalCost ?? 0, 4) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card stat-card border-0 shadow-sm p-3 px-4 d-flex flex-row align-items-center gap-3 transition-all h-100 bg-white" style="border-radius: 16px;">
                <div class="stat-icon-box shadow-sm border border-warning-subtle" style="background-color: #fef3c7; color: #d97706;">
                    <i class="bi bi-lightning-charge-fill"></i>
                </div>
                <div>
                    <p class="text-muted mb-0 fw-bold text-uppercase letter-spacing-1" style="font-size: 0.75rem;">Tổng Tokens đã tiêu thụ</p>
                    <h3 class="fw-800 text-dark mb-0 fs-3">
                        {{ number_format($totalTokens ?? 0) }} <span class="fs-6 text-muted fw-bold">tokens</span>
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow rounded-4 overflow-hidden mb-4 bg-white" style="border-radius: 16px !important;">
        <div class="table-responsive">
            <table class="table custom-table align-middle mb-0">
                <thead class="bg-light border-bottom border-light-subtle">
                    <tr>
                        <th width="25%" class="ps-4 py-3 text-muted fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Người yêu cầu</th>
                        <th width="30%" class="py-3 text-muted fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Hoạt động / Prompt</th>
                        <th width="15%" class="text-center py-3 text-muted fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Tokens</th>
                        <th width="15%" class="text-center py-3 text-muted fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Chi phí</th>
                        <th width="15%" class="text-end pe-4 py-3 text-muted fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Thời gian & Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($histories as $log)
                        <tr class="hover-row transition-all border-bottom border-light-subtle">
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-light text-secondary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 border shadow-sm" style="width: 42px; height: 42px;">
                                        <i class="bi bi-person-fill fs-5"></i>
                                    </div>
                                    <span class="fw-bold text-dark user-name fs-6">{{ $log->user->name ?? 'Người dùng bị xóa' }}</span>
                                </div>
                            </td>
                            <td class="py-3">
                                <div class="fw-bold text-dark mb-1">{{ $log->action }}</div>
                                <span class="badge bg-purple-light theme-text-primary border border-purple-subtle px-2 py-1 rounded-2 small fw-bold shadow-sm">
                                    <i class="bi bi-cpu-fill me-1"></i> {{ $log->model }}
                                </span>
                            </td>
                            <td class="text-center py-3">
                                @if($log->status == 'success')
                                    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-bold fs-6 shadow-sm">
                                        {{ number_format($log->tokens) }}
                                    </span>
                                @else
                                    <span class="text-muted fw-bold">-</span>
                                @endif
                            </td>
                            <td class="text-center py-3">
                                @if($log->status == 'success')
                                    <span class="text-emerald fw-bold px-3 py-2 rounded-pill border border-emerald-subtle shadow-sm" style="background-color: #d1fae5;">
                                        + ${{ number_format(($log->tokens / 1000) * 0.002, 4) }}
                                    </span>
                                @else
                                    <span class="text-muted fw-medium">$0.0000</span>
                                @endif
                            </td>
                            <td class="text-end pe-4 py-3">
                                <div class="mb-2">
                                    @if($log->status == 'success')
                                        <span class="badge custom-badge-success d-inline-flex align-items-center gap-1 px-3 py-2 rounded-pill shadow-sm">
                                            <i class="bi bi-check-circle-fill"></i> Thành công
                                        </span>
                                    @else
                                        <span class="badge bg-danger-soft text-danger border border-danger-subtle d-inline-flex align-items-center gap-1 px-3 py-2 rounded-pill shadow-sm" style="background-color: #fee2e2; color: #b91c1c;">
                                            <i class="bi bi-exclamation-triangle-fill"></i> Lỗi API
                                        </span>
                                    @endif
                                </div>
                                <small class="text-muted fw-medium d-flex align-items-center justify-content-end gap-1">
                                    <i class="bi bi-clock opacity-50"></i> {{ $log->created_at->diffForHumans() }}
                                </small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 bg-light">
                                <div class="empty-state-icon bg-white text-muted rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 80px; height: 80px; font-size: 2rem;">
                                    <i class="bi bi-activity"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-1">Chưa có dữ liệu API</h5>
                                <p class="text-muted fw-medium mb-0">Hệ thống chưa ghi nhận hoạt động sử dụng AI nào.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(isset($histories) && method_exists($histories, 'links'))
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 mb-5">
            
            <div class="d-flex align-items-center gap-3 bg-white px-4 py-2 rounded-pill shadow-sm border border-light-subtle">
                <span class="text-muted small fw-medium">
                    Đang hiển thị <strong class="text-dark">{{ $histories->firstItem() ?? 0 }} - {{ $histories->lastItem() ?? 0 }}</strong> trên tổng <strong class="text-dark">{{ $histories->total() }}</strong>
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

            <div class="custom-pagination shadow-sm rounded-pill overflow-hidden bg-white px-2">
                {{ $histories->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
            
        </div>
    @endif

@endsection

@push('scripts')
    <script src="{{ versioned_asset('js/admin/admin_ai_history.js') }}"></script>
@endpush
