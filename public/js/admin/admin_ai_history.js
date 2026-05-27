document.addEventListener("DOMContentLoaded", function() {
    
    // 1. Tự động ẩn thông báo thành công (nếu có lúc xuất file) sau 4 giây
    const alerts = document.querySelectorAll('.auto-close-alert');
    alerts.forEach(alertEl => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alertEl);
            bsAlert.close();
        }, 4000);
    });

    // 2. Xử lý khi thay đổi số lượng hiển thị (per_page)
    const perPageSelect = document.getElementById('perPageSelect');
    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            // Lấy URL hiện tại
            const url = new URL(window.location.href);
            
            // Cập nhật tham số per_page
            url.searchParams.set('per_page', this.value);
            
            // Xóa tham số page (reset về trang 1 khi đổi số lượng hiển thị để không bị lỗi trang trống)
            url.searchParams.delete('page'); 
            
            // Tải lại trang với cấu hình mới
            window.location.href = url.toString();
        });
    }

});