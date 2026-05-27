document.addEventListener("DOMContentLoaded", function() {
    
    // 1. Tự động ẩn CẢ thông báo lỗi và thành công sau 3.5 giây
    const alerts = document.querySelectorAll('.auto-close-alert');
    alerts.forEach(alertEl => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alertEl);
            bsAlert.close();
        }, 3500);
    });

    // 2. Xác nhận Xóa vĩnh viễn tài liệu
    const deleteButtons = document.querySelectorAll('.btn-confirm-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('.form-delete-doc');
            
            if(confirm('Bạn có chắc chắn muốn xóa tài liệu này vĩnh viễn? File trên ổ cứng máy chủ cũng sẽ bị xóa sạch và không thể khôi phục.')) {
                form.submit();
            }
        });
    });

    // 3. Xử lý khi thay đổi số lượng hiển thị (per_page)
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