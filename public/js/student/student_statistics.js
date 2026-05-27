// public/js/student_statistics.js

$(document).ready(function() {
    // 1. Kiểm tra sự tồn tại của dữ liệu
    const dbData = window.chartDataFromDB || { line: { labels: [], data: [] }, pie: { labels: [], data: [] } };

    // Lấy mã màu từ CSS Variables (giúp đồng bộ 100% với CSS)
    const style = getComputedStyle(document.body);
    const purpleMain = style.getPropertyValue('--theme-primary').trim() || '#7C3AED';
    const purpleLight = style.getPropertyValue('--theme-light').trim() || '#F5F3FF';

    // ==========================================
    // 1. Biểu đồ Đường (Tiến độ điểm số)
    // ==========================================
    const canvasProgress = document.getElementById('scoreProgressChart');
    if (canvasProgress) {
        const ctxProgress = canvasProgress.getContext('2d');
        
        // Tạo hiệu ứng đổ màu (Gradient)
        let gradientPurple = ctxProgress.createLinearGradient(0, 0, 0, 400);
        gradientPurple.addColorStop(0, 'rgba(124, 58, 237, 0.2)'); 
        gradientPurple.addColorStop(1, 'rgba(124, 58, 237, 0)');   

        new Chart(ctxProgress, {
            type: 'line',
            data: {
                labels: dbData.line.labels,
                datasets: [{
                    label: 'Điểm trung bình',
                    data: dbData.line.data,
                    borderColor: purpleMain,
                    backgroundColor: gradientPurple,
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: purpleMain,
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4 
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0F172A',
                        padding: 12,
                        titleFont: { size: 13, family: "'Plus Jakarta Sans', sans-serif" },
                        loubodyFont: { size: 14, weight: 'bold' },
                        displayColors: false,
                        callbacks: {
                            label: (context) => ` ${context.parsed.y} Điểm`
                        }
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        max: 10, 
                        grid: { borderDash: [5, 5], color: '#e2e8f0' },
                        ticks: { font: { family: "'Plus Jakarta Sans', sans-serif" } }
                    },
                    x: { 
                        grid: { display: false },
                        ticks: { font: { family: "'Plus Jakarta Sans', sans-serif" } }
                    }
                }
            }
        });
    }

    // ==========================================
    // 2. Biểu đồ Tròn (Phân bổ Môn học)
    // ==========================================
    const canvasSubject = document.getElementById('subjectDistributionChart');
    if (canvasSubject) {
        const ctxSubject = canvasSubject.getContext('2d');
        
        // Xử lý dữ liệu mặc định nếu trống
        const hasData = dbData.pie.labels.length > 0;
        const pieLabels = hasData ? dbData.pie.labels : ['Chưa có bài làm'];
        const pieData = hasData ? dbData.pie.data : [1];
        const pieColors = hasData 
            ? [purpleMain, '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#64748B']
            : ['#E2E8F0']; // Màu xám nhạt nếu chưa có dữ liệu

        new Chart(ctxSubject, {
            type: 'doughnut',
            data: {
                labels: pieLabels,
                datasets: [{
                    data: pieData, 
                    backgroundColor: pieColors,
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%', 
                plugins: {
                    legend: { 
                        position: 'bottom', 
                        labels: { 
                            usePointStyle: true, 
                            padding: 20, 
                            font: { size: 12, family: "'Plus Jakarta Sans', sans-serif" } 
                        } 
                    },
                    tooltip: {
                        enabled: hasData, // Chỉ hiện tooltip nếu có data thật
                        backgroundColor: '#0F172A',
                        callbacks: { 
                            label: (context) => ` ${context.parsed} bài` 
                        }
                    }
                }
            }
        });
    }
});