document.addEventListener('DOMContentLoaded', function() {
    
    // ========================================================
    // PHẦN 1: LOGIC GIỮ TAB KHI F5 (Code cũ của bác nếu có)
    // ========================================================
    const savedTabTarget = localStorage.getItem('activeAdminSettingsTab');
    if (savedTabTarget) {
        const tabToActivate = document.querySelector(`[data-bs-target="${savedTabTarget}"]`);
        if (tabToActivate) {
            let bsTab = new bootstrap.Tab(tabToActivate);
            bsTab.show();
        }
    }

    const tabNavButtons = document.querySelectorAll('.custom-pills .nav-link');
    tabNavButtons.forEach(btn => {
        btn.addEventListener('shown.bs.tab', function(event) {
            const targetId = event.target.getAttribute('data-bs-target');
            localStorage.setItem('activeAdminSettingsTab', targetId);
        });
    });

    // ========================================================
    // PHẦN 2: LOGIC GỬI EMAIL TEST (AJAX)
    // ========================================================
    const btnTestEmail = document.getElementById('btn-test-email');
    
    if (btnTestEmail) {
        btnTestEmail.addEventListener('click', function() {
            // 1. Lấy dữ liệu đang gõ trên form
            const host = document.querySelector('input[name="smtp_host"]').value;
            const port = document.querySelector('input[name="smtp_port"]').value;
            const username = document.querySelector('input[name="smtp_username"]').value;
            const password = document.querySelector('input[name="smtp_password"]').value;

            if(!host || !port || !username || !password) {
                alert('Vui lòng điền đầy đủ Host, Port, Email và Mật khẩu để test!');
                return;
            }

            // 2. Lấy đường dẫn API từ thuộc tính data-url của nút bấm
            const apiUrl = this.getAttribute('data-url');

            // 3. Hiệu ứng Loading
            const originalHtml = this.innerHTML;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Đang kết nối...';
            this.disabled = true;

            // 4. Bắn AJAX lên Backend
            fetch(apiUrl, { 
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    // Lấy CSRF token từ thẻ meta trên head của layout
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ host, port, username, password })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    alert('THÀNH CÔNG: Email test đã được gửi! Bác check hộp thư đi!');
                } else {
                    alert('LỖI RỒI: \n' + data.message);
                }
            })
            .catch(err => {
                alert('❌ Lỗi kết nối mạng hoặc lỗi Server (500)!');
                console.error(err);
            })
            .finally(() => {
                // Phục hồi lại nút bấm
                this.innerHTML = originalHtml;
                this.disabled = false;
            });
        });
    }
    // XỬ LÝ NÚT DỌN DẸP CACHE
    const btnClearCache = document.getElementById('btn-clear-cache');
    
    if (btnClearCache) {
        btnClearCache.addEventListener('click', function() {
            // Hỏi xác nhận trước khi xóa
            if(!confirm('Bác có chắc chắn muốn xóa toàn bộ bộ nhớ đệm (Cache) của hệ thống không? Điều này sẽ giúp web nhận cấu hình mới nhất.')) {
                return;
            }

            const apiUrl = this.getAttribute('data-url');
            const originalHtml = this.innerHTML;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Đang quét rác...';
            this.disabled = true;

            fetch(apiUrl, { 
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    alert('' + data.message);
                } else {
                    alert('' + data.message);
                }
            })
            .catch(err => {
                alert('❌ Lỗi kết nối tới máy chủ!');
                console.error(err);
            })
            .finally(() => {
                this.innerHTML = originalHtml;
                this.disabled = false;
            });
        });
    }
});
