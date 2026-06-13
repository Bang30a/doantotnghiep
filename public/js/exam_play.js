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
    const autoSubmitStorageKey = `eduquiz:auto-submitted:${config.examId || window.location.pathname}`;
    const unloadWarningMessage = 'Bài đang làm. Nếu bạn refresh hoặc rời khỏi trang, hệ thống sẽ tự động nộp bài.';

    let isSubmitting = false;
    let autoSubmitQueued = false;
    let leaveModalInstance = null;
    
    // Lưu trạng thái câu nào đã làm (dùng mảng)
    let answeredQuestions = new Set();

    const syncRichTextEditors = () => {
        if (window.EduQuizRichText && typeof window.EduQuizRichText.syncAll === 'function') {
            window.EduQuizRichText.syncAll();
        }
    };

    const updateLeaveModalCounts = () => {
        const answered = answeredQuestions.size;
        const unanswered = totalSteps - answered;
        const answeredEl = document.getElementById('leave-modal-answered-count');
        const unansweredEl = document.getElementById('leave-modal-unanswered-count');

        if (answeredEl) answeredEl.textContent = `${answered} câu`;
        if (unansweredEl) unansweredEl.textContent = `${unanswered} câu`;
    };

    const showLeaveExamModal = () => {
        if (!shouldGuardExamExit()) return;

        updateLeaveModalCounts();

        const modalEl = document.getElementById('leaveExamModal');
        if (!modalEl || typeof bootstrap === 'undefined') return;

        if (!leaveModalInstance) {
            leaveModalInstance = new bootstrap.Modal(modalEl);
        }

        leaveModalInstance.show();
    };

    const submitExamFromLeaveModal = () => {
        if (!formElement || isSubmitting) return;

        const leaveSubmitBtn = document.getElementById('leave-submit-btn');
        if (leaveSubmitBtn) {
            leaveSubmitBtn.disabled = true;
            leaveSubmitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Đang nộp...';
        }

        isSubmitting = true;
        localStorage.removeItem(autoSubmitStorageKey);
        syncRichTextEditors();
        formElement.submit();
    };

    const shouldGuardExamExit = () => {
        return Boolean(formElement && totalSteps > 0 && !isSubmitting && !autoSubmitQueued);
    };

    const queueAutoSubmit = () => {
        if (!shouldGuardExamExit()) return;

        autoSubmitQueued = true;
        isSubmitting = true;

        try {
            syncRichTextEditors();
            const formData = new FormData(formElement);
            formData.set('auto_submitted', '1');
            localStorage.setItem(autoSubmitStorageKey, String(Date.now()));

            if (navigator.sendBeacon && navigator.sendBeacon(formElement.action, formData)) {
                return;
            }

            fetch(formElement.action, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin',
                keepalive: true
            }).catch(() => {});
        } catch (error) {
            // Khi trình duyệt đang unload, không thể hiển thị lỗi ổn định.
        }
    };

    const redirectAfterAutoSubmit = () => {
        if (!config.resultUrl) return false;

        const marker = localStorage.getItem(autoSubmitStorageKey);
        if (!marker) return false;

        const submittedAt = Number(marker);
        if (!Number.isFinite(submittedAt) || Date.now() - submittedAt > 15000) {
            localStorage.removeItem(autoSubmitStorageKey);
            return false;
        }

        localStorage.removeItem(autoSubmitStorageKey);
        isSubmitting = true;

        document.body.innerHTML = `
            <div style="min-height:100vh;display:flex;align-items:center;justify-content:center;background:#f8fafc;font-family:system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;color:#1f2937;">
                <div style="text-align:center;background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:32px;box-shadow:0 20px 45px rgba(15,23,42,.08);max-width:420px;">
                    <div style="width:42px;height:42px;border:4px solid #e9d5ff;border-top-color:#7e22ce;border-radius:50%;margin:0 auto 18px;animation:eduquizSpin .8s linear infinite;"></div>
                    <h3 style="margin:0 0 8px;font-size:20px;">Bài đã được tự động nộp</h3>
                    <p style="margin:0;color:#6b7280;line-height:1.5;">Đang chuyển bạn đến trang kết quả...</p>
                </div>
            </div>
            <style>@keyframes eduquizSpin{to{transform:rotate(360deg)}}</style>
        `;

        setTimeout(() => {
            window.location.replace(config.resultUrl);
        }, 1200);

        return true;
    };

    if (redirectAfterAutoSubmit()) {
        return;
    }

    window.addEventListener('beforeunload', (event) => {
        if (!shouldGuardExamExit()) return;

        event.preventDefault();
        event.returnValue = unloadWarningMessage;
        return unloadWarningMessage;
    });

    window.addEventListener('pagehide', () => {
        queueAutoSubmit();
    });

    window.addEventListener('unload', () => {
        queueAutoSubmit();
    });

    if (formElement) {
        formElement.addEventListener('submit', () => {
            syncRichTextEditors();
            isSubmitting = true;
            localStorage.removeItem(autoSubmitStorageKey);
        });
    }

    document.addEventListener('click', (event) => {
        if (!shouldGuardExamExit()) return;

        const link = event.target.closest('a[href]');
        if (!link) return;
        if (link.target && link.target !== '_self') return;
        if (link.hasAttribute('download')) return;

        const href = link.getAttribute('href') || '';
        if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;

        const targetUrl = new URL(link.href, window.location.href);
        const currentUrl = new URL(window.location.href);
        const isSamePageAnchor = targetUrl.pathname === currentUrl.pathname
            && targetUrl.search === currentUrl.search
            && targetUrl.hash;

        if (isSamePageAnchor) return;

        event.preventDefault();
        showLeaveExamModal();
    }, true);

    window.addEventListener('keydown', (event) => {
        if (!shouldGuardExamExit()) return;

        const key = event.key.toLowerCase();
        const isKeyboardRefresh = event.key === 'F5' || ((event.ctrlKey || event.metaKey) && key === 'r');

        if (!isKeyboardRefresh) return;

        event.preventDefault();
        showLeaveExamModal();
    });

    if (window.history && window.history.pushState) {
        window.history.replaceState({ examGuard: true }, '', window.location.href);
        window.history.pushState({ examGuard: true }, '', window.location.href);

        window.addEventListener('popstate', () => {
            if (!shouldGuardExamExit()) return;

            window.history.pushState({ examGuard: true }, '', window.location.href);
            showLeaveExamModal();
        });
    }

    const leaveSubmitBtn = document.getElementById('leave-submit-btn');
    if (leaveSubmitBtn) {
        leaveSubmitBtn.addEventListener('click', submitExamFromLeaveModal);
    }

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
                if (formElement) {
                    isSubmitting = true;
                    syncRichTextEditors();
                    formElement.submit();
                }
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

    const setQuestionAnswered = (stepNum, isAnswered) => {
        const overviewBtn = document.getElementById(`overview-btn-${stepNum}`);
        if (overviewBtn) {
            overviewBtn.classList.toggle('answered', isAnswered);
        }

        if (isAnswered) {
            answeredQuestions.add(stepNum);
        } else {
            answeredQuestions.delete(stepNum);
        }

        checkCurrentQuestionStatus();
        updateAnsweredCount();
    };

    // Hàm gọi khi click chọn đáp án hoặc gõ nội dung tự luận
    window.markAnswered = (stepNum) => {
        setQuestionAnswered(stepNum, true);
    };

    window.unmarkAnswered = (stepNum) => {
        setQuestionAnswered(stepNum, false);
    };

    document.querySelectorAll('.edu-rich-editor').forEach(editor => {
        editor.addEventListener('richtext:change', (event) => {
            const step = Number(editor.dataset.step);
            if (!step) return;

            setQuestionAnswered(step, !event.detail.isBlank);
        });
    });

    // Khôi phục trạng thái đã làm (Nếu trang bị load lại giữ form cũ)
    const initAnsweredState = () => {
        for (let i = 1; i <= totalSteps; i++) {
            const stepEl = document.getElementById(`question-step-${i}`);
            if(stepEl) {
                const inputs = stepEl.querySelectorAll('input[type="radio"]:checked, textarea, .edu-rich-editor-surface');
                inputs.forEach(input => {
                    const isRichEditor = input.classList && input.classList.contains('edu-rich-editor-surface');
                    const richText = isRichEditor ? (input.textContent || '').replace(/\u00a0/g, ' ').trim() : '';

                    if ((input.type === 'radio' && input.checked)
                        || (input.tagName.toLowerCase() === 'textarea' && input.value.trim() !== '')
                        || (isRichEditor && richText !== '')) {
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
        if (formElement) {
            isSubmitting = true;
            syncRichTextEditors();
            formElement.submit();
        }
    });
    window.updateUI();
});
