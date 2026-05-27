document.addEventListener("DOMContentLoaded", function() {
    
    // Tự động ẩn thông báo thành công sau 4 giây
    const alerts = document.querySelectorAll('.auto-close-alert');
    alerts.forEach(alertEl => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alertEl);
            bsAlert.close();
        }, 4000);
    });

    // Thêm xác nhận nhỏ trước khi Khóa / Mở khóa tài khoản
    const lockButtons = document.querySelectorAll('.btn-confirm-lock');
    lockButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('.form-toggle-lock');
            const actionText = this.innerText.trim();
            
            if(confirm(`Bạn có chắc chắn muốn ${actionText} giảng viên này không?`)) {
                form.submit();
            }
        });
    });

    // Xử lý khi thay đổi số lượng hiển thị (per_page)
    const perPageSelect = document.getElementById('perPageSelect');
    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', this.value);
            url.searchParams.delete('page'); // Reset về trang 1
            window.location.href = url.toString();
        });
    }

});