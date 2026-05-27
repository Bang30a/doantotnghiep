// public/js/student_classrooms.js

document.addEventListener('DOMContentLoaded', function() {
    // Lấy phần tử Modal Tham gia lớp
    const joinClassModal = document.getElementById('joinClassModal');
    
    if (joinClassModal) {
        // Sự kiện: Ngay khi Modal mở lên hoàn tất
        joinClassModal.addEventListener('shown.bs.modal', function () {
            // Tự động focus (đặt con trỏ chuột) vào ô nhập mã lớp
            const codeInput = joinClassModal.querySelector('input[name="code"]');
            if (codeInput) {
                codeInput.focus();
            }
        });
    }
});