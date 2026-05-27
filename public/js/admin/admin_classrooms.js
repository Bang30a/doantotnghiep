document.addEventListener("DOMContentLoaded", function() {
    
    // 1. Tự động ẩn thông báo thành công sau 3 giây
    const successAlert = document.getElementById('success-alert');
    if (successAlert) {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(successAlert);
            bsAlert.close();
        }, 3000);
    }

    // 2. Xác nhận Khóa / Mở khóa lớp học
    const lockButtons = document.querySelectorAll('.btn-confirm-lock');
    lockButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('.form-toggle-lock');
            const actionText = this.innerText.trim();
            
            if(confirm(`Bạn có chắc chắn muốn ${actionText} không?`)) {
                form.submit();
            }
        });
    });

    // 3. Xác nhận Xóa vĩnh viễn lớp học
    const deleteButtons = document.querySelectorAll('.btn-confirm-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('.form-delete-class');
            
            if(confirm('Bạn có chắc chắn muốn xóa vĩnh viễn lớp học này? Mọi dữ liệu liên quan sẽ bị mất và KHÔNG THỂ khôi phục!')) {
                form.submit();
            }
        });
    });

    // 4. Xử lý khi thay đổi số lượng hiển thị (per_page)
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