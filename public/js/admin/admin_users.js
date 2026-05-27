document.addEventListener('DOMContentLoaded', function() {
    
    // ==========================================
    // 1. TÌM KIẾM NGƯỜI DÙNG (REAL-TIME)
    // ==========================================
    const searchInput = document.getElementById('searchInput');
    const userRows = document.querySelectorAll('.user-row');

    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            
            userRows.forEach(row => {
                const nameNode = row.querySelector('.user-name');
                const emailNode = row.querySelector('.user-email');
                
                if(nameNode && emailNode) {
                    const name = nameNode.textContent.toLowerCase();
                    const email = emailNode.textContent.toLowerCase();
                    
                    if (name.includes(searchTerm) || email.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        });
    }

    // ==========================================
    // 2. GỌI API KHÓA / MỞ KHÓA TÀI KHOẢN
    // ==========================================
    const lockBtns = document.querySelectorAll('.btn-toggle-lock');

    lockBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault(); 
            
            // 1. Lấy mã bảo mật CSRF (Bắt buộc trong Laravel)
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            if (!csrfMeta) {
                alert('LỖI: Chưa có thẻ <meta name="csrf-token"> trong HTML!');
                return;
            }
            const csrfToken = csrfMeta.getAttribute('content');

            // 2. Lấy ID và trạng thái hiện tại
            const userId = this.getAttribute('data-user-id');
            const isCurrentlyLocked = this.getAttribute('data-locked') === 'true';
            
            // 3. Hiển thị thông báo xác nhận
            const confirmMsg = isCurrentlyLocked ? 'Bạn có muốn MỞ KHÓA tài khoản này?' : 'Bạn có chắc chắn muốn KHÓA tài khoản này?';
            if (!confirm(confirmMsg)) return;

            // 4. Tìm các phần tử giao diện cần thay đổi
            const row = this.closest('.user-row');
            const statusBadge = row.querySelector('.status-badge');
            const icon = this.querySelector('i');
            const statActive = document.getElementById('stat-active');
            const statLocked = document.getElementById('stat-locked');

            // 5. Gửi yêu cầu AJAX xuống Controller
            fetch(`/admin/users/${userId}/toggle-lock`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Lỗi máy chủ: ' + response.status);
                return response.json();
            })
            .then(data => {
                if(data.success) {
                    // Thành công! Bắt đầu cập nhật giao diện
                    const isNowLocked = data.is_locked;
                    this.setAttribute('data-locked', isNowLocked ? 'true' : 'false');

                    if (isNowLocked) {
                        // Trạng thái: BỊ KHÓA
                        statusBadge.textContent = 'Bị khóa';
                        statusBadge.className = 'status-badge status-locked';
                        icon.className = 'bi bi-unlock fs-5';
                        
                        if(statLocked) statLocked.textContent = parseInt(statLocked.textContent) + 1;
                        if(statActive) statActive.textContent = parseInt(statActive.textContent) - 1;
                    } else {
                        // Trạng thái: HOẠT ĐỘNG
                        statusBadge.textContent = 'Hoạt động';
                        statusBadge.className = 'status-badge status-active';
                        icon.className = 'bi bi-lock fs-5';
                        
                        if(statLocked) statLocked.textContent = parseInt(statLocked.textContent) - 1;
                        if(statActive) statActive.textContent = parseInt(statActive.textContent) + 1;
                    }
                } else {
                    alert('Lỗi: Controller trả về không thành công!');
                }
            })
            .catch(error => {
                console.error('Chi tiết lỗi:', error);
                alert('Lỗi mất kết nối! Hãy mở F12 (tab Console) để xem chi tiết.');
            });
        });
    });
});