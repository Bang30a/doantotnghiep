document.addEventListener("DOMContentLoaded", function() {
    
    // 1. Tự động ẩn thông báo thành công sau 3 giây
    const successAlert = document.getElementById('success-alert');
    if (successAlert) {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(successAlert);
            bsAlert.close();
        }, 3000);
    }

    // 2. Thêm xác nhận nhỏ trước khi Khóa / Mở khóa tài khoản
    const lockButtons = document.querySelectorAll('.btn-confirm-lock');
    lockButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('.form-toggle-lock');
            const actionText = this.innerText.trim();
            
            if(confirm(`Bạn có chắc chắn muốn ${actionText} học viên này không?`)) {
                form.submit();
            }
        });
    });

    // 3. Xử lý khi thay đổi số lượng hiển thị (per_page)
    const perPageSelect = document.getElementById('perPageSelect');
    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            // Lấy URL hiện tại
            const url = new URL(window.location.href);
            
            // Cập nhật tham số per_page
            url.searchParams.set('per_page', this.value);
            
            // Xóa tham số page (để quay về trang 1, tránh lỗi trang rỗng)
            url.searchParams.delete('page'); 
            
            // Tải lại trang với cấu hình mới
            window.location.href = url.toString();
        });
    }

});