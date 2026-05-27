/* ========================================================
   JS BẢNG ĐIỂM LỚP HỌC - DÀNH CHO GIẢNG VIÊN
   ======================================================== */

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchStudent');
    const tableRows = document.querySelectorAll('.student-row');

    // Tính năng tìm kiếm học viên trong bảng
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const value = this.value.toLowerCase().trim();

            tableRows.forEach(row => {
                const studentName = row.querySelector('.student-name').textContent.toLowerCase();
                const studentEmail = row.querySelector('.student-email').textContent.toLowerCase();
                
                if (studentName.includes(value) || studentEmail.includes(value)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        });
    }

    // Nút xuất Excel (Tạm thời thông báo)
    const btnExport = document.getElementById('btn-export-excel');
    if (btnExport) {
        btnExport.addEventListener('click', function() {
            alert('Tính năng xuất Excel đang được khởi tạo dữ liệu...');
        });
    }
});