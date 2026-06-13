/* ========================================================
   FILE: public/js/teacher_grading.js
   Mô tả: JS xử lý giao diện cho trang Giảng viên chấm bài
======================================================== */

document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Xử lý nút quay lại
    const backBtn = document.getElementById('btn-back');
    if (backBtn) {
        backBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (document.referrer) {
                window.history.back();
            } else {
                window.location.href = '/teacher/exams'; 
            }
        });
    }

    // 2. Chức năng: Nút "Dùng nhận xét AI"
    const useAiBtns = document.querySelectorAll('.btn-use-ai-feedback');
    useAiBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Lấy ID của ô text area mục tiêu và ô text AI nguồn
            const targetId = this.getAttribute('data-target');
            const sourceId = this.getAttribute('data-source');
            
            const targetElement = document.getElementById(targetId);
            const sourceElement = document.getElementById(sourceId);
            
            if(targetElement && sourceElement) {
                // Copy text từ AI dán xuống ô của Giảng viên
                const sourceText = sourceElement.innerText;
                const richApplied = window.EduQuizRichText
                    && typeof window.EduQuizRichText.setText === 'function'
                    && window.EduQuizRichText.setText(targetId, sourceText);

                if (!richApplied) {
                    targetElement.value = sourceText;
                }
                
                // Đổi nút thành "Đã áp dụng" màu xanh trong 2 giây để báo hiệu
                const originalHtml = this.innerHTML;
                this.innerHTML = '<i class="bi bi-check-lg"></i> Đã dùng';
                this.classList.remove('btn-outline-purple');
                this.classList.add('btn-success', 'text-white');
                
                setTimeout(() => {
                    this.innerHTML = originalHtml;
                    this.classList.remove('btn-success', 'text-white');
                    this.classList.add('btn-outline-purple');
                }, 2000);
            }
        });
    });

    // 3. Hiệu ứng hover đổi màu nền cho đáp án trắc nghiệm
    const answerItems = document.querySelectorAll('.ans-item');
    answerItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            if(!this.classList.contains('is-correct') && !this.classList.contains('is-wrong')) {
                this.style.borderColor = '#cbd5e1';
                this.style.backgroundColor = '#f1f5f9';
            }
        });
        
        item.addEventListener('mouseleave', function() {
            if(!this.classList.contains('is-correct') && !this.classList.contains('is-wrong')) {
                this.style.borderColor = '#e2e8f0';
                this.style.backgroundColor = '#f8fafc';
            }
        });
    });
});
