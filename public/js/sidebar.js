/* ==========================================
   XỬ LÝ SỰ KIỆN THU/PHÓNG SIDEBAR
========================================== */
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('toggleSidebar');
    const body = document.body;
    
    if(toggleBtn) {
        const toggleIcon = toggleBtn.querySelector('i');

        // 1. Khôi phục trạng thái từ LocalStorage khi load trang (chống giật)
        if (localStorage.getItem('sidebar-collapsed') === 'true') {
            body.classList.add('sidebar-collapsed');
            if(toggleIcon) toggleIcon.classList.replace('bi-list', 'bi-text-indent-right');
        }

        // 2. Lắng nghe sự kiện click vào nút toggle
        toggleBtn.addEventListener('click', function() {
            body.classList.toggle('sidebar-collapsed');
            
            // 3. Đổi icon và lưu trạng thái vào LocalStorage
            if (body.classList.contains('sidebar-collapsed')) {
                if(toggleIcon) toggleIcon.classList.replace('bi-list', 'bi-text-indent-right');
                localStorage.setItem('sidebar-collapsed', 'true');
            } else {
                if(toggleIcon) toggleIcon.classList.replace('bi-text-indent-right', 'bi-list');
                localStorage.setItem('sidebar-collapsed', 'false');
            }
        });
    }
});