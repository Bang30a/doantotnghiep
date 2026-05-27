document.addEventListener("DOMContentLoaded", function() {
    
    // 1. Tự động ẩn thông báo lỗi/thành công sau 4 giây cho mượt
    const alerts = document.querySelectorAll('.auto-close-alert');
    alerts.forEach(alertEl => {
        setTimeout(() => {
            // Dùng bootstrap API để ẩn mượt mà
            const bsAlert = new bootstrap.Alert(alertEl);
            bsAlert.close();
        }, 4000);
    });

    // 2. Thêm xác nhận nhỏ trước khi Xóa Prompt
    const deleteButtons = document.querySelectorAll('.btn-confirm-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('.form-delete-prompt');
            
            if(confirm('Bác có chắc chắn muốn xóa Prompt này không? Xóa xong là AI sẽ không dùng lệnh này để sinh đề được nữa đâu nhé!')) {
                form.submit();
            }
        });
    });

});