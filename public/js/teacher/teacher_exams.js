// ==================================================
// JS CHO TRANG DANH SÁCH ĐỀ THI (GIẢNG VIÊN)
// ==================================================

document.addEventListener('DOMContentLoaded', function() {
    
    // Bắt sự kiện xác nhận trước khi xóa đề thi
    const deleteForms = document.querySelectorAll('.form-delete-exam');
    
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // Hiển thị hộp thoại cảnh báo
            const isConfirmed = confirm('Bạn có chắc chắn muốn xóa đề thi này không? Mọi dữ liệu làm bài của học sinh sẽ bị mất vĩnh viễn!');
            
            // Nếu người dùng bấm "Hủy" (Cancel) -> Chặn không cho form submit
            if (!isConfirmed) {
                e.preventDefault();
            }
        });
    });

});