/* ========================================================
   JS CHI TIẾT ĐỀ THI - DÀNH CHO ADMIN
   ======================================================== */
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý nút Ẩn/Hiện đáp án
    document.querySelectorAll('.toggle-answer-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const answerContent = document.getElementById(targetId);
            
            if (answerContent.style.display === 'none') {
                answerContent.style.display = 'block';
                this.innerHTML = '<i class="bi bi-eye-slash me-1"></i> Ẩn đáp án';
                // Đổi sang màu tím khi đang hiện
                this.classList.remove('text-secondary', 'bg-light', 'border-secondary-subtle');
                this.classList.add('text-purple', 'bg-purple-soft', 'border-purple-subtle');
            } else {
                answerContent.style.display = 'none';
                this.innerHTML = '<i class="bi bi-eye me-1"></i> Hiện đáp án';
                // Trả về xám khi ẩn
                this.classList.remove('text-purple', 'bg-purple-soft', 'border-purple-subtle');
                this.classList.add('text-secondary', 'bg-light', 'border-secondary-subtle');
            }
        });
    });

    // Xử lý nút Sao chép
    document.querySelectorAll('.copy-question-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const questionText = this.getAttribute('data-content');
            navigator.clipboard.writeText(questionText).then(() => {
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="bi bi-check2-all me-1"></i> Đã chép';
                this.classList.add('text-success', 'bg-success', 'bg-opacity-10', 'border-success-subtle');
                
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.classList.remove('text-success', 'bg-success', 'bg-opacity-10', 'border-success-subtle');
                }, 2000);
            });
        });
    });
});