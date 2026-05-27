document.addEventListener('DOMContentLoaded', function() {
    
    // 1. CHUYỂN TAB CÀI ĐẶT
    const tabBtns = document.querySelectorAll('.settings-tab-btn');
    const tabContents = document.querySelectorAll('.settings-content');

    function switchTab(targetId) {
        tabBtns.forEach(b => b.classList.remove('active'));
        tabContents.forEach(c => c.style.display = 'none');
        
        const btn = document.querySelector(`.settings-tab-btn[data-target="${targetId}"]`);
        const content = document.getElementById(targetId);
        
        if(btn && content) {
            btn.classList.add('active');
            content.style.display = 'block';
        }
    }

    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            switchTab(this.getAttribute('data-target'));
        });
    });

    // Tự động chuyển qua Tab Bảo mật nếu có lỗi nhập sai mật khẩu lúc submit
    try {
        const errorData = JSON.parse(document.getElementById('error-tab-data').textContent);
        if (errorData.hasPasswordError) {
            switchTab('tab-security');
        }
    } catch(e) {}

    // 2. TÍNH NĂNG XEM TRƯỚC AVATAR KHI CHỌN FILE
    const avatarInput = document.getElementById('avatarInput');
    const avatarPreview = document.getElementById('avatarPreview');

    if (avatarInput && avatarPreview) {
        avatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Kiểm tra nếu không phải là ảnh
                if (!file.type.match('image.*')) {
                    alert('Vui lòng chỉ chọn file hình ảnh (JPG, PNG).');
                    this.value = ''; // Reset input
                    return;
                }
                
                // Đọc file và hiển thị lên thẻ img
                const reader = new FileReader();
                reader.onload = function(e) {
                    avatarPreview.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    }
});