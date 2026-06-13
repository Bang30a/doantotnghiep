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

    function csvEscape(value) {
        const text = String(value ?? '').replace(/\s+/g, ' ').trim();
        return `"${text.replace(/"/g, '""')}"`;
    }

    function exportTableToCsv(button) {
        const tableSelector = button.dataset.exportTable || '#tableResults';
        const table = document.querySelector(tableSelector);
        if (!table) {
            alert('Không tìm thấy bảng điểm để xuất.');
            return;
        }

        const rows = Array.from(table.querySelectorAll('tr'));
        const csvRows = rows
            .map(row => {
                const cells = Array.from(row.querySelectorAll('th, td'));
                const usefulCells = cells.filter((_, index) => index < cells.length - 1);
                return usefulCells.map(cell => csvEscape(cell.textContent)).join(',');
            })
            .filter(line => line.replace(/","/g, '').replace(/"/g, '').trim() !== '');

        if (csvRows.length <= 1) {
            alert('Chưa có dữ liệu bảng điểm để xuất.');
            return;
        }

        const blob = new Blob(['\uFEFF' + csvRows.join('\n')], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const fileName = button.dataset.exportName || 'bang-diem';

        link.href = URL.createObjectURL(blob);
        link.download = `${fileName}.csv`;
        document.body.appendChild(link);
        link.click();
        link.remove();
        URL.revokeObjectURL(link.href);
    }

    // 3. Xử lý nút xuất điểm
    const btnExport = document.getElementById('btn-export-excel');
    if (btnExport) {
        btnExport.addEventListener('click', function() {
            exportTableToCsv(this);
        });
    }
});
