document.addEventListener("DOMContentLoaded", function() {
    // Tự động ẩn thông báo thành công sau 3 giây
    const successAlert = document.getElementById('success-alert');
    if (successAlert) {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(successAlert);
            bsAlert.close();
        }, 3000);
    }
});