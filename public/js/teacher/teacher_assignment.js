/* ========================================================
   JAVASCRIPT CORE: FORM VALIDATION GIAO BÀI TẬP
======================================================== */

document.addEventListener('DOMContentLoaded', function() {
    const deadlineInput = document.getElementById('deadline_input');
    const assignmentForm = document.getElementById('assignmentForm');
    const btnSubmit = document.getElementById('btnSubmitForm');
    const variantCountInput = document.getElementById('variant_count');
    const shuffleQuestionsInput = document.getElementById('shuffle_questions');

    if (variantCountInput && shuffleQuestionsInput) {
        variantCountInput.addEventListener('input', function() {
            let value = parseInt(variantCountInput.value || '1', 10);

            if (Number.isNaN(value) || value < 1) value = 1;
            if (value > 10) value = 10;

            variantCountInput.value = value;

            if (value > 1) {
                shuffleQuestionsInput.checked = true;
            }
        });
    }

    if (deadlineInput) {
        // 1. CHẶN THỜI GIAN QUÁ KHỨ KHÔNG CHO CHỌN
        const setMinimumDeadline = () => {
            const now = new Date();
            // Xử lý lệch múi giờ Việt Nam (UTC+7) để điền đúng chuẩn ISO vào thẻ input
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            
            // Định dạng chuỗi lấy đúng đến phút: YYYY-MM-DDTHH:mm
            const minDateTime = now.toISOString().slice(0, 16);
            deadlineInput.min = minDateTime;
        };

        // Kích hoạt khóa lịch ngay khi tải trang
        setMinimumDeadline();

        // 2. CHECK LẠI MỘT LẦN NỮA KHI GIÁO VIÊN BẤM SUBMIT
        if (assignmentForm) {
            assignmentForm.addEventListener('submit', function(event) {
                const selectedDate = new Date(deadlineInput.value);
                const currentDate = new Date();
                
                if (selectedDate <= currentDate) {
                    event.preventDefault(); // Chặn đứng lệnh gửi form lên server
                    alert('⚠️ Lỗi cấu hình: Hạn nộp bài tập phải là một mốc thời gian trong tương lai, không được chọn ngày hoặc giờ đã qua bác ơi!');
                    deadlineInput.focus();
                    return false;
                }

                if (variantCountInput) {
                    const variantCount = parseInt(variantCountInput.value || '1', 10);

                    if (Number.isNaN(variantCount) || variantCount < 1 || variantCount > 10) {
                        event.preventDefault();
                        alert('Số lượng mã đề phải nằm trong khoảng từ 1 đến 10.');
                        variantCountInput.focus();
                        return false;
                    }
                }

                // Hiển thị hiệu ứng loading trên nút nếu form hợp lệ
                if (btnSubmit) {
                    btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang phát đề...';
                    btnSubmit.disabled = true;
                }
            });
        }
    }
});
