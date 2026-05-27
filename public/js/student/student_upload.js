// public/js/student_upload.js

document.addEventListener('DOMContentLoaded', () => {
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const fileNameDisplay = document.getElementById('fileNameDisplay');

    if (!dropZone || !fileInput) return; // Thoát sớm nếu không có element, code phía sau đỡ phải lồng trong block if

    // Hàm dùng chung để xử lý hiển thị tên file
    const handleFileSelect = (files) => {
        if (files && files.length > 0) {
            fileNameDisplay.textContent = `Đã chọn: ${files[0].name}`;
            fileNameDisplay.classList.remove('d-none');
        }
    };

    // 1. Bấm vào khu vực viền nét đứt -> mở cửa sổ chọn file
    dropZone.addEventListener('click', () => fileInput.click());

    // 2. Xử lý khi người dùng chọn file qua cửa sổ
    fileInput.addEventListener('change', (e) => handleFileSelect(e.target.files));
    
    // 3. Xử lý hiệu ứng khi kéo thả file vào (Drag & Drop)
    // Ngăn chặn hành vi mặc định của trình duyệt (mở file sang tab mới)
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => e.preventDefault());
    });

    // Thêm hiệu ứng UI khi kéo file vào
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => dropZone.classList.add('dragover'));
    });

    // Bỏ hiệu ứng UI khi kéo file ra ngoài hoặc thả xong
    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => dropZone.classList.remove('dragover'));
    });

    // Nhận file khi thả vào
    dropZone.addEventListener('drop', (e) => {
        const files = e.dataTransfer.files;
        if (files.length) {
            fileInput.files = files; // Gán file vào thẻ input ẩn
            handleFileSelect(files); // Gọi hàm cập nhật UI
        }
    });
});