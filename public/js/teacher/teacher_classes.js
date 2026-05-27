// public/js/teacher_classes.js

document.addEventListener('DOMContentLoaded', function() {
    // Xử lý nút Copy mã lớp
    const copyBadges = document.querySelectorAll('.code-badge');
    
    copyBadges.forEach(badge => {
        badge.addEventListener('click', function() {
            const code = this.getAttribute('data-code');
            
            // API Copy text vào Clipboard
            navigator.clipboard.writeText(code).then(() => {
                // Hiệu ứng UX nhỏ khi copy thành công
                const originalHTML = this.innerHTML;
                this.innerHTML = `<i class="bi bi-check-lg text-success"></i> Đã copy`;
                
                setTimeout(() => {
                    this.innerHTML = originalHTML;
                }, 2000);
            }).catch(err => {
                alert('Không thể copy mã: ' + err);
            });
        });
    });
});