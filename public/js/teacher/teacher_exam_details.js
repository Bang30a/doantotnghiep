document.addEventListener('DOMContentLoaded', function() {
    
    // 1. TÍNH NĂNG TÌM KIẾM HỌC SINH TRONG BẢNG ĐIỂM
    const searchInput = document.getElementById('searchStudent');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('.student-row');

            rows.forEach(row => {
                const studentName = row.querySelector('.student-name').textContent.toLowerCase();
                if (studentName.includes(filter)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        });
    }

    // 2. GIỮ TRẠNG THÁI TAB KHI TẢI LẠI TRANG (NẾU CẦN)
    const hash = window.location.hash;
    if (hash) {
        const triggerEl = document.querySelector(`.nav-tabs button[data-bs-target="${hash}"]`);
        if (triggerEl) {
            bootstrap.Tab.getInstance(triggerEl).show();
        }
    }

    // 3. XỬ LÝ NÚT XUẤT ĐIỂM (DUMMY)
    const btnExport = document.getElementById('btn-export-excel');
    if (btnExport) {
        btnExport.addEventListener('click', function() {
            alert('Tính năng xuất Excel đang được tích hợp. Vui lòng chờ trong phiên bản tới!');
        });
    }
});