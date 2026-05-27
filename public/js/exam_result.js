/* ========================================================
   FILE: public/js/exam_result.js
   HIỆU ỨNG PHÁO HOA ĂN MỪNG ĐIỂM CAO & PROGRESS RING
======================================================== */

document.addEventListener("DOMContentLoaded", () => {
    const summaryCard = document.getElementById('result-summary');
    if (!summaryCard) return; 

    const score = parseFloat(summaryCard.dataset.score) || 0;
    
    // 1. Hiệu ứng SVG Progress Ring trượt lên
    const ringFill = document.querySelector('.progress-ring-fill');
    if (ringFill) {
        // Reset về 0 rồi mới chạy lên để có hiệu ứng
        const radius = ringFill.r.baseVal.value;
        const circumference = radius * 2 * Math.PI;
        ringFill.style.strokeDasharray = `${circumference} ${circumference}`;
        ringFill.style.strokeDashoffset = circumference;
        
        setTimeout(() => {
            const offset = circumference - (score / 100) * circumference;
            ringFill.style.strokeDashoffset = offset;
        }, 300); // Đợi 300ms rồi chạy animation
    }

    // 2. Bắn pháo hoa nếu điểm >= 80%
    if (score >= 80 && typeof confetti === 'function') {
        const end = Date.now() + (3 * 1000); // Bắn liên tục trong 3 giây
        const colors = ['#10B981', '#34D399', '#ffffff']; // Tông màu xanh lá/trắng vì đây là điểm giỏi
        
        const frame = () => {
            confetti({ particleCount: 4, angle: 60, spread: 55, origin: { x: 0 }, colors: colors });
            confetti({ particleCount: 4, angle: 120, spread: 55, origin: { x: 1 }, colors: colors });
            
            if (Date.now() < end) requestAnimationFrame(frame);
        };
        frame();
    }
});