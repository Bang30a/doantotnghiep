// public/js/student_question_banks.js

document.addEventListener('DOMContentLoaded', () => {
    
    const bankContainer = document.getElementById('bankContainer');
    let bankItems = Array.from(document.querySelectorAll('.bank-item'));

    // ==========================================
    // 1. TÌM KIẾM NHANH (Có tối ưu Debounce)
    // ==========================================
    const searchInput = document.getElementById('searchInput');
    
    // Kỹ thuật Debounce: Chờ người dùng gõ xong (300ms) mới thực thi để chống giật lag
    const debounce = (func, delay = 300) => {
        let timer;
        return (...args) => {
            clearTimeout(timer);
            timer = setTimeout(() => func.apply(this, args), delay);
        };
    };

    if (searchInput) {
        searchInput.addEventListener('input', debounce((e) => {
            const value = e.target.value.toLowerCase().trim();
            
            bankItems.forEach(item => {
                // Dùng toán tử Optional Chaining (?.) để không lỗi nếu thiếu thẻ con
                const title = item.querySelector('.title-text')?.textContent.toLowerCase() || '';
                const desc = item.querySelector('.description-text')?.textContent.toLowerCase() || '';
                
                // Dùng includes thay cho indexOf > -1 (Chuẩn ES6)
                const isMatch = title.includes(value) || desc.includes(value);
                item.style.display = isMatch ? '' : 'none';
            });
        }));
    }

    // ==========================================
    // 2. LỌC THEO MÔN HỌC
    // ==========================================
    const subjectFilter = document.getElementById('subjectFilter');
    
    if (subjectFilter) {
        subjectFilter.addEventListener('change', (e) => {
            const value = e.target.value.toLowerCase().trim();
            
            bankItems.forEach(item => {
                if (value === "") {
                    item.style.display = ''; // Hiện tất cả
                } else {
                    const subject = item.querySelector('.badge')?.textContent.toLowerCase() || '';
                    item.style.display = subject.includes(value) ? '' : 'none';
                }
            });
        });
    }

    const sortFilter = document.getElementById('sortFilter');
    if (sortFilter && bankContainer) {
        sortFilter.addEventListener('change', () => {
            const sortedItems = [...bankItems].sort((a, b) => {
                if (sortFilter.value === 'oldest') {
                    return Number(a.dataset.created || 0) - Number(b.dataset.created || 0);
                }

                if (sortFilter.value === 'highest_score') {
                    return Number(b.dataset.score || 0) - Number(a.dataset.score || 0);
                }

                return Number(b.dataset.created || 0) - Number(a.dataset.created || 0);
            });

            sortedItems.forEach(item => bankContainer.appendChild(item));
        });
    }

    const resetFilters = document.getElementById('resetFilters');
    if (resetFilters) {
        resetFilters.addEventListener('click', () => {
            if (searchInput) searchInput.value = '';
            if (subjectFilter) subjectFilter.value = '';
            if (sortFilter) sortFilter.value = 'newest';

            bankItems
                .sort((a, b) => Number(b.dataset.created || 0) - Number(a.dataset.created || 0))
                .forEach(item => {
                    item.style.display = '';
                    if (bankContainer) bankContainer.appendChild(item);
                });
        });
    }

    // ==========================================
    // 3. XỬ LÝ XÓA ĐỀ BẰNG AJAX (CHUẨN LARAVEL)
    // ==========================================
    // Lấy CSRF Token từ thẻ meta trên <head> (Bắt buộc trong Laravel)
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const deleteBtns = document.querySelectorAll('.btn-delete-bank');
    
    deleteBtns.forEach(btn => {
        btn.addEventListener('click', async function(e) {
            e.preventDefault();
            
            if (!confirm('Bạn có chắc chắn muốn xóa đề thi này khỏi ngân hàng không? Hành động này không thể hoàn tác.')) {
                return; // Thoát nếu chọn Cancel
            }

            const bankId = this.dataset.id; // Lấy từ data-id="..."
            const cardElement = this.closest('.bank-item');
            const icon = this.querySelector('i');
            const originalIconClass = icon.className;

            // Đổi icon sang trạng thái đang quay (Loading) và vô hiệu hóa nút
            icon.className = 'spinner-border spinner-border-sm';
            this.disabled = true;

            try {
                const response = await fetch(this.dataset.url || `/student/question-banks/${bankId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    // Xóa mượt mà thẻ HTML bằng CSS Transition thay vì jQuery fadeOut
                    cardElement.style.transition = 'opacity 0.3s ease';
                    cardElement.style.opacity = '0';
                    bankItems = bankItems.filter(item => item !== cardElement);
                    setTimeout(() => cardElement.remove(), 300);
                } else {
                    const resData = await response.json();
                    alert(resData.message || 'Có lỗi xảy ra khi xóa đề thi. Vui lòng thử lại.');
                    // Trả lại nút nếu lỗi
                    icon.className = originalIconClass;
                    this.disabled = false;
                }
            } catch (error) {
                console.error('Lỗi khi gọi API xóa đề:', error);
                alert('Không thể kết nối đến máy chủ. Vui lòng kiểm tra mạng.');
                icon.className = originalIconClass;
                this.disabled = false;
            }
        });
    });
});
