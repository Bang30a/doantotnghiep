$(document).ready(function() {
    
    // Xử lý công tắc (Toggle) Hiện/Ẩn đáp án
    $('#toggleAnswers').on('change', function() {
        let isChecked = $(this).is(':checked');
        
        // Nếu bật công tắc -> Thêm class 'show-answers' vào toàn bộ khu vực chứa câu hỏi
        if (isChecked) {
            $('.questions-container').addClass('show-answers');
        } else {
            // Nếu tắt -> Bỏ class đi, tự động quay về như cũ
            $('.questions-container').removeClass('show-answers');
        }
    });

});