// public/js/exam_play.js

document.addEventListener("DOMContentLoaded", () => {
    const config = window.examConfig || { durationMinutes: 60, totalQuestions: 1 };
    let timeInSeconds = config.durationMinutes * 60;
    let currentStep = 1;
    const totalSteps = config.totalQuestions;

    // DOM Elements
    const timerElement = document.getElementById('time-left'); 
    const timerContainer = document.getElementById('timer-display'); 
    const formElement = document.getElementById('examForm');
    const btnPrev = document.getElementById('btn-prev');
    const btnNext = document.getElementById('btn-next');
    const btnNextSubmit = document.getElementById('btn-next-submit');
    const answeredCountEl = document.getElementById('answered-count');
    const statusBadge = document.getElementById('status-badge');
    
    // Lưu trạng thái câu nào đã làm (dùng mảng)
    let answeredQuestions = new Set();

    // ==========================================
    // 1. LOGIC ĐỒNG HỒ ĐẾM NGƯỢC
    // ==========================================
    if (timerElement) {
        const timer = setInterval(() => {
            const minutes = String(Math.floor(timeInSeconds / 60)).padStart(2, '0');
            const seconds = String(timeInSeconds % 60).padStart(2, '0');
            timerElement.textContent = `${minutes}:${seconds}`;

            if (timeInSeconds <= 300 && timerContainer) {
                timerContainer.classList.remove('bg-green-light', 'text-success');
                timerContainer.classList.add('bg-danger', 'text-white');
            }

            if (timeInSeconds <= 0) {
                clearInterval(timer);
                alert('Đã hết thời gian làm bài! Hệ thống tự động nộp bài.');
                if (formElement) formElement.submit();
            }
            timeInSeconds--;
        }, 1000);
    }

    // ==========================================
    // 2. LOGIC CHUYỂN CÂU HỎI & TIẾN ĐỘ
    // ==========================================
    window.updateUI = () => {
        // Ẩn tất cả, hiện câu hiện tại
        document.querySelectorAll('.step-item').forEach(card => card.classList.add('d-none'));
        const activeCard = document.getElementById(`question-step-${currentStep}`);
        if(activeCard) activeCard.classList.remove('d-none');

        // Nút Câu Trước
        if (btnPrev) btnPrev.disabled = (currentStep === 1);
        
        // Nút Câu Tiếp / Nộp Bài
        if (currentStep === totalSteps) {
            btnNext.classList.add('d-none');
            btnNextSubmit.classList.remove('d-none');
        } else {
            btnNext.classList.remove('d-none');
            btnNextSubmit.classList.add('d-none');
        }

        // Cập nhật trạng thái hiển thị trên Grid (Cột trái)
        document.querySelectorAll('.overview-btn').forEach(btn => btn.classList.remove('active-view'));
        const currentOverviewBtn = document.getElementById(`overview-btn-${currentStep}`);
        if(currentOverviewBtn) currentOverviewBtn.classList.add('active-view');

        // Cập nhật Badge "Chưa trả lời" ở dưới footer
        checkCurrentQuestionStatus();
    };

    // Kiểm tra câu hiện tại đã làm chưa để đổi màu badge dưới footer
    const checkCurrentQuestionStatus = () => {
        if (!statusBadge) return;
        
        if (answeredQuestions.has(currentStep)) {
            statusBadge.className = 'badge bg-success-light text-success px-3 py-2 rounded-pill fw-semibold d-flex align-items-center gap-1';
            statusBadge.innerHTML = '<i class="bi bi-check-circle-fill"></i> Đã trả lời';
        } else {
            statusBadge.className = 'badge bg-warning-light text-warning px-3 py-2 rounded-pill fw-semibold d-flex align-items-center gap-1';
            statusBadge.innerHTML = '<i class="bi bi-exclamation-circle-fill"></i> Chưa trả lời';
        }
    };

    // Cập nhật biến số lượng câu đã trả lời trên Header
    const updateAnsweredCount = () => {
        if (answeredCountEl) {
            answeredCountEl.textContent = `${answeredQuestions.size}/${totalSteps} câu`;
        }
    };

    // ==========================================
    // 3. CÁC HÀM HỖ TRỢ GỌI TỪ HTML
    // ==========================================
    window.navigate = (direction) => {
        if (currentStep + direction >= 1 && currentStep + direction <= totalSteps) {
            currentStep += direction;
            window.updateUI();
        }
    };

    window.goToStep = (step) => {
        currentStep = step;
        window.updateUI();
    };

    // Hàm gọi khi click chọn đáp án hoặc gõ textarea
    window.markAnswered = (stepNum) => {
        // Thêm vào lưới bên trái
        const overviewBtn = document.getElementById(`overview-btn-${stepNum}`);
        if (overviewBtn) {
            overviewBtn.classList.add('answered'); 
        }
        
        // Cập nhật logic mảng
        answeredQuestions.add(stepNum);
        
        // Cập nhật UI
        checkCurrentQuestionStatus();
        updateAnsweredCount();
    };

    // Khôi phục trạng thái đã làm (Nếu trang bị load lại giữ form cũ)
    const initAnsweredState = () => {
        for (let i = 1; i <= totalSteps; i++) {
            const stepEl = document.getElementById(`question-step-${i}`);
            if(stepEl) {
                const inputs = stepEl.querySelectorAll('input[type="radio"]:checked, textarea');
                inputs.forEach(input => {
                    if ((input.type === 'radio' && input.checked) || (input.tagName.toLowerCase() === 'textarea' && input.value.trim() !== '')) {
                        answeredQuestions.add(i);
                        const overviewBtn = document.getElementById(`overview-btn-${i}`);
                        if (overviewBtn) overviewBtn.classList.add('answered');
                    }
                });
            }
        }
        updateAnsweredCount();
    };

    initAnsweredState();
    // ==========================================
    // 4. LOGIC MODAL XÁC NHẬN NỘP BÀI
    // ==========================================
    let submitModalInstance = null;

    window.showSubmitModal = () => {
        const answered = answeredQuestions.size;
        const unanswered = totalSteps - answered;

        // Cập nhật text trong modal
        document.getElementById('modal-answered-count').textContent = `${answered} câu`;
        document.getElementById('modal-unanswered-count').textContent = `${unanswered} câu`;
        document.getElementById('modal-warning-count').textContent = unanswered;

        // Ẩn/hiện box cảnh báo vàng
        const warningBox = document.getElementById('modal-warning-box');
        if (unanswered > 0) {
            warningBox.style.display = 'flex'; // Hiện cảnh báo
        } else {
            warningBox.style.display = 'none'; // Ẩn cảnh báo nếu đã làm hết
        }

        // Khởi tạo và hiển thị Bootstrap Modal
        if (!submitModalInstance) {
            submitModalInstance = new bootstrap.Modal(document.getElementById('submitConfirmModal'));
        }
        submitModalInstance.show();
    };

    // Bắt sự kiện khi click nút "Xác Nhận Nộp Bài" bên trong Modal
    document.getElementById('confirm-submit-btn').addEventListener('click', () => {
        // Vô hiệu hóa nút để tránh click 2 lần
        document.getElementById('confirm-submit-btn').disabled = true;
        document.getElementById('confirm-submit-btn').innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Đang nộp...';
        
        // Nộp form
        if (formElement) formElement.submit();
    });
    window.updateUI();
});