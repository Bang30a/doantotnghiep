/* ========================================================
   FILE: public/js/teacher_student_details.js
   Mô tả: Khởi tạo các tiện ích giao diện cho trang Chi tiết
======================================================== */

$(document).ready(function() {
    // 1. Khởi tạo Bootstrap Tooltips
    // Dùng để hiển thị dòng chữ "Xem bài giải chi tiết" khi rê chuột vào nút "Chấm bài"
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // 2. Hiệu ứng làm nổi bật (Highlight) hàng trong bảng khi click
    $('.hover-row').on('click', function() {
        $('.hover-row').removeClass('bg-light');
        $(this).addClass('bg-light');
    });
});