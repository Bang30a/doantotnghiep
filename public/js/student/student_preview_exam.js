$(document).ready(function() {
    function syncPrintMode() {
        $('body').toggleClass('print-with-answers', $('#toggleAnswers').is(':checked'));
    }

    $('#btnPrintExam').on('click', function() {
        syncPrintMode();
        window.print();
    });

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

        syncPrintMode();
    });

    $(window).on('beforeprint', syncPrintMode);

});
