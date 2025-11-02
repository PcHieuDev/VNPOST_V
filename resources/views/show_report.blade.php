<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống Kê Báo Cáo - Vietnam Post</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script> 
    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script> 

    <!-- LOGIC ALPINE.JS -->
    <script>
        function statisticsPage() {
            return {
                // bộ lọc thời gian gửi lên API
                filter: {
                    startDate: new Date(new Date().getFullYear(), new Date().getMonth(), 1)
                        .toISOString()
                        .substring(0, 10),
                    endDate: new Date().toISOString().substring(0, 10),
                },

                // nắm tham chiếu các chart để update thay vì vẽ lại
                charts: { 
                    revenueByService: null,
                    reportsByDay: null,
                    revenueByUnit: null
                },

                error: '', // hiển thị lỗi ra màn hình nếu có

                init() {
                    // Khi DOM đã sẵn sàng và Chart.js được load (defer)
                    document.addEventListener('DOMContentLoaded', () => {
                        if (typeof Chart === 'undefined') {
                            console.error("Chart.js chưa được tải!");
                            this.error = "Lỗi thư viện biểu đồ. Vui lòng tải lại trang.";
                            return;
                        }
                        this.fetchChartData();
                    });
                },

                // HÀM GỌI API THẬT
                fetchChartData() {
                    if (typeof Chart === 'undefined') {
                        this.error = "Thư viện biểu đồ chưa sẵn sàng.";
                        return;
                    }

                    const params = new URLSearchParams({
                        startDate: this.filter.startDate,
                        endDate: this.filter.endDate
                    });

                    fetch('/api/statistics?' + params.toString(), {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => {
                        if (!res.ok) {
                            throw new Error('Không lấy được dữ liệu thống kê từ server');
                        }
                        return res.json();
                    })
                    .then(data => {
                        // data có dạng:
                        // {
                        //   range: {...},
                        //   revenueByService: { labels: [...], data: [...] },
                        //   reportsByDay:     { labels: [...], data: [...] },
                        //   revenueByUnit:    { labels: [...], data: [...] }
                        // }

                        this.updateRevenueByServiceChart(data.revenueByService);
                        this.updateReportsByDayChart(data.reportsByDay);
                        this.updateRevenueByUnitChart(data.revenueByUnit);

                        this.error = '';
                    })
                    .catch(err => {
                        console.error(err);
                        this.error = 'Lỗi tải dữ liệu thống kê';
                    });
                },

                // VẼ / UPDATE BIỂU ĐỒ 1: Doanh thu theo Dịch vụ (Pie)
                updateRevenueByServiceChart(chartData) {
                    const canvas = document.getElementById('revenueByServiceChart');
                    if (!canvas) { 
                        console.error("Canvas 'revenueByServiceChart' not found."); 
                        return; 
                    }
                    const ctx = canvas.getContext('2d');

                    if (this.charts.revenueByService) {
                        // cập nhật chart cũ
                        this.charts.revenueByService.data.labels = chartData.labels;
                        this.charts.revenueByService.data.datasets[0].data = chartData.data;
                        this.charts.revenueByService.update();
                    } else {
                        // tạo chart mới
                        this.charts.revenueByService = new Chart(ctx, {
                            type: 'pie',
                            data: {
                                labels: chartData.labels,
                                datasets: [{ 
                                    label: 'Doanh thu (VND)',
                                    data: chartData.data,
                                    backgroundColor: [
                                        'rgba(255, 99, 132, 0.7)',   // đỏ
                                        'rgba(54, 162, 235, 0.7)',   // xanh dương
                                        'rgba(255, 206, 86, 0.7)',   // vàng
                                        'rgba(75, 192, 192, 0.7)',   // teal
                                        'rgba(153, 102, 255, 0.7)',  // tím
                                        'rgba(201, 203, 207, 0.7)',  // xám
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: { 
                                responsive: true,
                                plugins: {
                                    legend: { position: 'top' },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                let label = context.label || '';
                                                if (label) { label += ': '; }
                                                if (context.parsed !== null) {
                                                    label += new Intl.NumberFormat('vi-VN', { 
                                                        style: 'currency', 
                                                        currency: 'VND' 
                                                    }).format(context.parsed);
                                                }
                                                return label;
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    }
                },

                // VẼ / UPDATE BIỂU ĐỒ 2: Số lượng báo cáo theo Ngày (Line)
                updateReportsByDayChart(chartData) {
                    const canvas = document.getElementById('reportsByDayChart');
                    if (!canvas) { 
                        console.error("Canvas 'reportsByDayChart' not found."); 
                        return; 
                    }
                    const ctx = canvas.getContext('2d');

                    if (this.charts.reportsByDay) {
                        this.charts.reportsByDay.data.labels = chartData.labels;
                        this.charts.reportsByDay.data.datasets[0].data = chartData.data;
                        this.charts.reportsByDay.update();
                    } else {
                        this.charts.reportsByDay = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: chartData.labels,
                                datasets: [{ 
                                    label: 'Số lượng báo cáo',
                                    data: chartData.data,
                                    borderColor: 'rgb(75, 192, 192)',
                                    tension: 0.1,
                                    fill: false
                                }]
                            },
                            options: { 
                                responsive: true,
                                scales: { 
                                    y: { beginAtZero: true } 
                                },
                                plugins: { 
                                    legend: { display: false } 
                                }
                            }
                        });
                    }
                },

                // VẼ / UPDATE BIỂU ĐỒ 3: Doanh thu theo Đơn vị hành chính (Bar)
                updateRevenueByUnitChart(chartData) {
                    const canvas = document.getElementById('revenueByUnitChart');
                    if (!canvas) { 
                        console.error("Canvas 'revenueByUnitChart' not found."); 
                        return; 
                    }
                    const ctx = canvas.getContext('2d');

                    if (this.charts.revenueByUnit) {
                        this.charts.revenueByUnit.data.labels = chartData.labels;
                        this.charts.revenueByUnit.data.datasets[0].data = chartData.data;
                        this.charts.revenueByUnit.update();
                    } else {
                        this.charts.revenueByUnit = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: chartData.labels,
                                datasets: [{ 
                                    label: 'Tổng Doanh thu (VND)',
                                    data: chartData.data,
                                    backgroundColor: 'rgba(54, 162, 235, 0.7)', // xanh dương
                                    borderWidth: 1
                                }]
                            },
                            options: { 
                                responsive: true,
                                scales: {
                                    y: { 
                                        beginAtZero: true,
                                        ticks: {
                                            callback: function(value) {
                                                return new Intl.NumberFormat('vi-VN', { 
                                                    style: 'currency', 
                                                    currency: 'VND', 
                                                    notation: 'compact' 
                                                }).format(value);
                                            }
                                        } 
                                    }
                                },
                                plugins: { 
                                    legend: { display: false },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                let label = context.dataset.label || '';
                                                if (label) { label += ': '; }
                                                if (context.parsed.y !== null) {
                                                    label += new Intl.NumberFormat('vi-VN', { 
                                                        style: 'currency', 
                                                        currency: 'VND' 
                                                    }).format(context.parsed.y);
                                                }
                                                return label;
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    }
                },

                // Đăng xuất (nếu bạn cần giữ chức năng này)
                async logout() {
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').content; 
                        await fetch('/api/logout', { 
                            method: 'POST',
                            headers: { 
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json' 
                            }
                        });
                    } catch(e) { 
                        console.error("Logout error:", e);
                    } finally { 
                        window.location.href = '/login'; 
                    }
                },
            }
        }
    </script>

    <link rel="stylesheet" href="{{ asset('css/report_styles.css') }}">

    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #f3f4f6;
        }
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        canvas { 
            max-width: 100%; 
            height: auto; 
        } 
    </style>
</head>
<body x-data="statisticsPage()" class="bg-gray-100">

    @include('layouts.header')

    <div class="banner-wave"></div>

    <div class="report-container"> 
        <h1 class="page-title">Thống Kê Báo Cáo</h1> 

        <!-- Thông báo lỗi -->
        <div x-show="error" class="report-alert alert-error mb-4" x-text="error"></div>

        <!-- Bộ lọc khoảng thời gian -->
        <div class="report-card mb-6 flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-2">
                <label for="startDate" class="text-sm font-medium text-gray-700 whitespace-nowrap">Từ ngày:</label>
                <input type="date" id="startDate" x-model="filter.startDate" class="border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2">
            </div>
            
            <div class="flex items-center gap-2">
                <label for="endDate" class="text-sm font-medium text-gray-700 whitespace-nowrap">Đến ngày:</label>
                <input type="date" id="endDate" x-model="filter.endDate" class="border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2">
            </div>
            
            <button 
                @click="fetchChartData()" 
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium">
                Xem
            </button>
        </div>

        <!-- Lưới biểu đồ -->
        <div class="report-grid"> 
            <div class="report-card"> 
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Doanh thu theo Dịch vụ</h2>
                <canvas id="revenueByServiceChart"></canvas>
            </div>

            <div class="report-card"> 
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Số lượng Báo cáo theo Ngày</h2>
                <canvas id="reportsByDayChart"></canvas>
            </div>
            
            <div class="report-card md:col-span-2"> 
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Tổng Doanh thu theo Đơn vị</h2>
                <canvas id="revenueByUnitChart"></canvas>
            </div>
        </div>
    </div>

</body>
</html>
