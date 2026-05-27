document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('fileInput');
    const displayDiv = document.getElementById('fileNameDisplay');
    const uploadZone = document.querySelector('.upload-zone');

    if(!fileInput || !uploadZone) return;

    // Hàm xử lý hiển thị giao diện khi có file
    function handleFiles(files) {
        if (files.length > 0) {
            const file = files[0];
            const fileName = file.name;
            const fileSize = (file.size / 1024 / 1024).toFixed(2); // Đổi ra MB
            
            // Render giao diện thẻ hiển thị xịn xò
            displayDiv.innerHTML = `
                <div class="file-preview-badge mt-4">
                    <i class="bi bi-file-earmark-check-fill text-success fs-2"></i>
                    <div class="text-start">
                        <div class="text-dark fw-bold mb-1" style="font-size: 0.95rem;">${fileName}</div>
                        <div class="text-muted" style="font-size: 0.8rem;">Dung lượng: ${fileSize} MB</div>
                    </div>
                </div>
            `;
            // Chuyển viền sang màu xanh lá
            uploadZone.style.borderColor = '#22C55E'; 
            uploadZone.style.backgroundColor = '#F0FDF4';
        } else {
            displayDiv.innerHTML = '';
            uploadZone.style.borderColor = ''; 
            uploadZone.style.backgroundColor = '';
        }
    }

    // 1. Khi người dùng click chọn file từ máy
    fileInput.addEventListener('change', function(e) {
        handleFiles(e.target.files);
    });

    // 2. Khi người dùng kéo file lướt qua ô
    uploadZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadZone.classList.add('dragover');
    });

    // 3. Khi người dùng kéo file ra khỏi ô
    uploadZone.addEventListener('dragleave', () => {
        uploadZone.classList.remove('dragover');
    });

    // 4. Khi người dùng thả file vào ô
    uploadZone.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadZone.classList.remove('dragover');
        
        // Gán file vừa thả vào thẻ input ẩn
        if(e.dataTransfer.files.length > 0) {
            fileInput.files = e.dataTransfer.files;
            handleFiles(e.dataTransfer.files);
        }
    });
});