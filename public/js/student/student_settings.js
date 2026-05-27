// public/js/student_settings.js

document.addEventListener('DOMContentLoaded', () => {
    
    // ==========================================
    // 1. CHUYỂN TAB CÀI ĐẶT
    // ==========================================
    const tabBtns = document.querySelectorAll('.settings-tab-btn');
    const tabContents = document.querySelectorAll('.settings-content');

    const switchTab = (targetId) => {
        // Reset toàn bộ trạng thái
        tabBtns.forEach(btn => btn.classList.remove('active'));
        tabContents.forEach(content => content.style.display = 'none');
        
        // Kích hoạt tab được chọn
        const activeBtn = document.querySelector(`.settings-tab-btn[data-target="${targetId}"]`);
        const activeContent = document.getElementById(targetId);
        
        if (activeBtn && activeContent) {
            activeBtn.classList.add('active');
            activeContent.style.display = 'block';
        }
    };

    // Gán sự kiện click cho các nút
    tabBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            // Dùng dataset thay cho getAttribute cho chuẩn HTML5
            switchTab(e.currentTarget.dataset.target); 
        });
    });

    // Tự động chuyển qua Tab Bảo mật nếu có lỗi nhập sai mật khẩu lúc submit (Từ Laravel gửi ra)
    try {
        const errorElement = document.getElementById('error-tab-data');
        if (errorElement) {
            const errorData = JSON.parse(errorElement.textContent);
            // Dùng Optional Chaining (?.) để tránh lỗi nếu object không có key đó
            if (errorData?.hasPasswordError) {
                switchTab('tab-security');
            }
        }
    } catch(e) {
        console.error("Lỗi parse data tab:", e); // Bắt lỗi an toàn hơn
    }

    // ==========================================
    // 2. TÍNH NĂNG XEM TRƯỚC AVATAR KHI CHỌN FILE
    // ==========================================
    const avatarInput = document.getElementById('avatarInput');
    const avatarPreview = document.getElementById('avatarPreview');

    if (avatarInput && avatarPreview) {
        avatarInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (!file) return; // Thoát sớm nếu người dùng ấn Cancel

            // Kiểm tra MIME type chuẩn xác hơn thay vì dùng Regex
            if (!file.type.startsWith('image/')) {
                alert('Vui lòng chỉ chọn file hình ảnh (JPG, PNG, GIF...).');
                e.target.value = ''; // Dùng e.target thay cho this khi xài arrow function
                return;
            }
            
            // Đọc file và hiển thị lên thẻ img
            const reader = new FileReader();
            reader.onload = (event) => {
                avatarPreview.src = event.target.result;
            };
            reader.readAsDataURL(file);
        });
    }
});