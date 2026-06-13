document.addEventListener('DOMContentLoaded', function () {
    let data = {
        classLabels: [],
        classScores: [],

        distributionLabels: [],
        distributionData: [],

        completionLabels: ['Đã nộp', 'Chưa nộp'],
        completionData: [0, 0],

        difficultyLabels: ['Dễ', 'Trung bình', 'Khó'],
        difficultyData: [30, 50, 20],

        perfLabels: ['T1', 'T2', 'T3', 'T4'],
        perfScores: [0, 0, 0, 0],
        perfRates: [0, 0, 0, 0]
    };

    try {
        const dataElement = document.getElementById('stat-data');

        if (dataElement) {
            const parsedData = JSON.parse(dataElement.textContent || '{}');
            data = { ...data, ...parsedData };
        }
    } catch (error) {
        console.error('Lỗi parse dữ liệu thống kê:', error);
    }

    function showEmptyState(canvasId, icon, title, desc) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;

        const container = canvas.closest('.chart-container');
        if (!container) return;

        container.innerHTML = `
            <div class="chart-empty-state">
                <div>
                    <i class="bi ${icon}"></i>
                    <div class="empty-title">${title}</div>
                    <p class="empty-desc">${desc}</p>
                </div>
            </div>
        `;
    }

    function hasData(arr) {
        return Array.isArray(arr) && arr.some(value => Number(value) > 0);
    }

    if (typeof Chart === 'undefined') {
        showEmptyState('classScoreChart', 'bi-bar-chart', 'Chưa tải được biểu đồ', 'Vui lòng kiểm tra kết nối thư viện Chart.js.');
        showEmptyState('scoreDistributionChart', 'bi-pie-chart', 'Chưa tải được biểu đồ', 'Vui lòng kiểm tra kết nối thư viện Chart.js.');
        showEmptyState('completionChart', 'bi-check-circle', 'Chưa tải được biểu đồ', 'Vui lòng kiểm tra kết nối thư viện Chart.js.');
        showEmptyState('difficultyChart', 'bi-question-circle', 'Chưa tải được biểu đồ', 'Vui lòng kiểm tra kết nối thư viện Chart.js.');
        return;
    }

    Chart.defaults.font.family = "'Plus Jakarta Sans', system-ui, sans-serif";
    Chart.defaults.color = '#64748b';

    const rootStyle = getComputedStyle(document.body);
    const theme = {
        primary: rootStyle.getPropertyValue('--teacher-primary').trim() || '#6D28D9',
        primaryDark: rootStyle.getPropertyValue('--teacher-primary-dark').trim() || '#4C1D95',
        teal: rootStyle.getPropertyValue('--teacher-teal').trim() || '#0F766E',
        blue: rootStyle.getPropertyValue('--teacher-blue').trim() || '#2563EB',
        amber: rootStyle.getPropertyValue('--teacher-amber').trim() || '#D97706',
        rose: rootStyle.getPropertyValue('--teacher-rose').trim() || '#E11D48'
    };

    const commonGrid = {
        color: '#eef2f7',
        drawBorder: false
    };

    const commonTooltip = {
        backgroundColor: '#111827',
        titleColor: '#ffffff',
        bodyColor: '#ffffff',
        padding: 12,
        cornerRadius: 10,
        displayColors: false
    };

    // 1. Biểu đồ điểm trung bình theo lớp
    const ctxClassScore = document.getElementById('classScoreChart');

    if (ctxClassScore) {
        if (Array.isArray(data.classLabels) && data.classLabels.length > 0 && hasData(data.classScores)) {
            new Chart(ctxClassScore.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: data.classLabels,
                    datasets: [{
                        label: 'Điểm TB',
                        data: data.classScores,
                        backgroundColor: theme.blue,
                        hoverBackgroundColor: theme.primary,
                        borderRadius: 12,
                        barThickness: 42
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: commonTooltip
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 10,
                            grid: commonGrid,
                            ticks: { stepSize: 2 }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { weight: 700 } }
                        }
                    }
                }
            });
        } else {
            showEmptyState(
                'classScoreChart',
                'bi-bar-chart-line',
                'Chưa có điểm theo lớp',
                'Khi học viên nộp bài, biểu đồ điểm trung bình sẽ hiển thị ở đây.'
            );
        }
    }

    // 2. Biểu đồ phân bố điểm
    const ctxDistribution = document.getElementById('scoreDistributionChart');

    if (ctxDistribution) {
        if (Array.isArray(data.distributionLabels) && data.distributionLabels.length > 0 && hasData(data.distributionData)) {
            new Chart(ctxDistribution.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: data.distributionLabels,
                    datasets: [{
                        label: 'Số lượt bài',
                        data: data.distributionData,
                        backgroundColor: theme.teal,
                        hoverBackgroundColor: theme.blue,
                        borderRadius: 10,
                        barThickness: 38
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: commonTooltip
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: commonGrid,
                            ticks: { precision: 0 }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { weight: 700 } }
                        }
                    }
                }
            });
        } else {
            showEmptyState(
                'scoreDistributionChart',
                'bi-pie-chart',
                'Chưa có phân bố điểm',
                'Dữ liệu phân bố điểm sẽ được tạo sau khi có kết quả bài thi.'
            );
        }
    }

    // 3. Biểu đồ tỷ lệ hoàn thành
    const ctxCompletion = document.getElementById('completionChart');

    if (ctxCompletion) {
        if (hasData(data.completionData)) {
            new Chart(ctxCompletion.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: data.completionLabels || ['Đã nộp', 'Chưa nộp'],
                    datasets: [{
                        data: data.completionData,
                        backgroundColor: [theme.teal, '#FEE2E2'],
                        borderWidth: 4,
                        borderColor: '#ffffff',
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                boxWidth: 8,
                                padding: 18,
                                font: { weight: 700 }
                            }
                        },
                        tooltip: commonTooltip
                    }
                }
            });
        } else {
            showEmptyState(
                'completionChart',
                'bi-check2-circle',
                'Chưa có lượt nộp bài',
                'Tỷ lệ hoàn thành sẽ xuất hiện khi học viên bắt đầu làm bài.'
            );
        }
    }

    // 4. Biểu đồ độ khó
    const ctxDifficulty = document.getElementById('difficultyChart');

    if (ctxDifficulty) {
        const difficultyLabels = data.difficultyLabels || ['Dễ', 'Trung bình', 'Khó'];
        const difficultyData = data.difficultyData || [30, 50, 20];

        new Chart(ctxDifficulty.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: difficultyLabels,
                datasets: [{
                    data: difficultyData,
                    backgroundColor: [theme.teal, theme.amber, theme.rose],
                    borderWidth: 4,
                    borderColor: '#ffffff',
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8,
                            padding: 18,
                            font: { weight: 700 }
                        }
                    },
                    tooltip: commonTooltip
                }
            }
        });
    }

    // 5. Biểu đồ hiệu suất theo tháng
    const ctxPerformance = document.getElementById('performanceChartDynamic');

    if (ctxPerformance) {
        if (hasData(data.perfScores) || hasData(data.perfRates)) {
            new Chart(ctxPerformance.getContext('2d'), {
                type: 'line',
                data: {
                    labels: data.perfLabels,
                    datasets: [
                        {
                            label: 'Điểm TB',
                            data: data.perfScores,
                            borderColor: theme.blue,
                            backgroundColor: 'rgba(37, 99, 235, 0.13)',
                            yAxisID: 'y',
                            tension: 0.42,
                            fill: true,
                            pointRadius: 4,
                            pointHoverRadius: 7,
                            pointBackgroundColor: theme.blue
                        },
                        {
                            label: 'Tỷ lệ hoàn thành (%)',
                            data: data.perfRates,
                            borderColor: theme.teal,
                            backgroundColor: 'rgba(15, 118, 110, 0.12)',
                            yAxisID: 'y1',
                            tension: 0.42,
                            fill: true,
                            pointRadius: 4,
                            pointHoverRadius: 7,
                            pointBackgroundColor: theme.teal
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                boxWidth: 8,
                                padding: 18,
                                font: { weight: 700 }
                            }
                        },
                        tooltip: commonTooltip
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            max: 10,
                            min: 0,
                            grid: commonGrid,
                            ticks: { stepSize: 2 }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            max: 100,
                            min: 0,
                            ticks: { stepSize: 25 },
                            grid: { drawOnChartArea: false }
                        }
                    }
                }
            });
        } else {
            showEmptyState(
                'performanceChartDynamic',
                'bi-graph-up-arrow',
                'Chưa có xu hướng hiệu suất',
                'Biểu đồ sẽ hiển thị khi có kết quả bài thi theo tháng.'
            );
        }
    }
});
