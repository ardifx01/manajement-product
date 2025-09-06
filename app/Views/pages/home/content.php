<?= $this->extend('pages/layouts/index') ?>
<?= $this->section('content') ?>

<div class="main-content">
    <div class="container-fluid">
        <div class="row clearfix">
            <!-- Widget untuk Total Produk -->
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="widget">
                    <div class="widget-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="state">
                                <h6>Total Produk</h6>
                                <h2 id="total-products">0</h2>
                            </div>
                            <div class="icon">
                                <i class="ik ik-package"></i>
                            </div>
                        </div>
                        <small class="text-small mt-10 d-block">Jumlah produk dalam sistem</small>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-info" role="progressbar" id="product-progress" style="width: 0%;"></div>
                    </div>
                </div>
            </div>

            <!-- Widget untuk Barang Masuk -->
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="widget">
                    <div class="widget-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="state">
                                <h6>Barang Masuk</h6>
                                <h2 id="total-incoming">0</h2>
                                <small id="incoming-trend" class="text-success"></small>
                            </div>
                            <div class="icon">
                                <i class="ik ik-download"></i>
                            </div>
                        </div>
                        <small class="text-small mt-10 d-block">Total barang masuk</small>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-success" role="progressbar" id="incoming-progress" style="width: 0%;"></div>
                    </div>
                </div>
            </div>

            <!-- Widget untuk Barang Keluar -->
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="widget">
                    <div class="widget-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="state">
                                <h6>Barang Keluar</h6>
                                <h2 id="total-outgoing">0</h2>
                                <small id="outgoing-trend" class="text-warning"></small>
                            </div>
                            <div class="icon">
                                <i class="ik ik-upload"></i>
                            </div>
                        </div>
                        <small class="text-small mt-10 d-block">Total barang keluar</small>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-warning" role="progressbar" id="outgoing-progress" style="width: 0%;"></div>
                    </div>
                </div>
            </div>

            <!-- Widget untuk Stok Rendah -->
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="widget">
                    <div class="widget-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="state">
                                <h6>Stok Rendah</h6>
                                <h2 id="low-stock">0</h2>
                            </div>
                            <div class="icon">
                                <i class="ik ik-alert-triangle"></i>
                            </div>
                        </div>
                        <small class="text-small mt-10 d-block">Perlu perhatian segera</small>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-danger" role="progressbar" id="lowstock-progress" style="width: 0%;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row untuk Grafik dan Tabel -->
        <div class="row clearfix">
            <!-- Grafik Perbulan -->
            <div class="col-lg-8 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>Grafik</h3>
                        <div class="card-header-right">
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-secondary active" data-period="month">Bulanan</button>
                                <button class="btn btn-sm btn-outline-secondary" data-period="day">Harian</button>
                                <button class="btn btn-sm btn-outline-secondary" data-period="year">Tahunan</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="monthlyChart" height="135"></canvas>
                    </div>
                </div>
            </div>

            <!-- Tabel Presentase -->
            <div class="col-lg-4 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>Presentase Movement</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Jenis</th>
                                        <th>Total</th>
                                        <th>Presentase</th>
                                        <th>Trend</th>
                                    </tr>
                                </thead>
                                <tbody id="percentage-table">
                                    <tr>
                                        <td>Barang Masuk</td>
                                        <td id="incoming-total">0</td>
                                        <td id="incoming-percent">0%</td>
                                        <td id="incoming-trend-percent">0%</td>
                                    </tr>
                                    <tr>
                                        <td>Barang Keluar</td>
                                        <td id="outgoing-total">0</td>
                                        <td id="outgoing-percent">0%</td>
                                        <td id="outgoing-trend-percent">0%</td>
                                    </tr>
                                    <tr class="table-info">
                                        <td>Net Movement</td>
                                        <td id="net-total">0</td>
                                        <td>-</td>
                                        <td id="net-trend-percent">0%</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row untuk Top Products -->
        <div class="row clearfix">
            <!-- Top Incoming Products -->
            <div class="col-lg-4 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>Top 5 Barang Masuk</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Kode</th>
                                        <th>Quantity</th>
                                    </tr>
                                </thead>
                                <tbody id="top-incoming-table">
                                    <tr>
                                        <td colspan="3">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Outgoing Products -->
            <div class="col-lg-4 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>Top 5 Barang Keluar</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Kode</th>
                                        <th>Quantity</th>
                                    </tr>
                                </thead>
                                <tbody id="top-outgoing-table">
                                    <tr>
                                        <td colspan="3">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock Overview Chart -->
            <div class="col-lg-4 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3>Stock Overview</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="StockOverviewChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Load Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<script>
    $(document).ready(function() {
        let monthlyChart = null;
        let stockOverviewChart = null;

        // Fungsi untuk mengambil data dashboard
        function loadDashboardData() {
            $.ajax({
                url: '<?= site_url('/api/v1/dashboard/summary') ?>',
                method: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    // Tampilkan loading indicator
                    $('#total-products, #total-incoming, #total-outgoing, #low-stock').html('<i class="ik ik-loader"></i>');
                },
                success: function(response) {
                    if (response.status === 'success') {
                        updateDashboardWidgets(response.data);
                        updatePercentageTable(response.data);
                        updateTopProducts(response.data);
                        renderMonthlyChart(response.data.monthly_data);
                    } else {
                        showError('Gagal memuat data dashboard');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching dashboard data:', error);
                    showError('Terjadi kesalahan saat memuat data');
                }
            });
            loadStockOverviewData(); // Load stock overview data
        }

        // Update dashboard widgets
        function updateDashboardWidgets(data) {
            $('#total-products').text(data.total_products || 0);
            $('#total-incoming').text(data.total_incoming || 0);
            $('#total-outgoing').text(data.total_outgoing || 0);
            $('#low-stock').text(data.low_stock_products || 0);

            // Update trend indicators
            updateTrendIndicator('#incoming-trend', data.percentage_data.incoming.trend);
            updateTrendIndicator('#outgoing-trend', data.percentage_data.outgoing.trend);

            // Update progress bars
            $('#product-progress').css('width', Math.min(100, (data.total_products || 0) / 500 * 100) + '%');
            $('#incoming-progress').css('width', Math.min(100, (data.total_incoming || 0) / 200 * 100) + '%');
            $('#outgoing-progress').css('width', Math.min(100, (data.total_outgoing || 0) / 150 * 100) + '%');
            $('#lowstock-progress').css('width', Math.min(100, (data.low_stock_products || 0) / 50 * 100) + '%');
        }

        // Update percentage table
        function updatePercentageTable(data) {
            $('#incoming-total').text(data.percentage_data.incoming.total);
            $('#outgoing-total').text(data.percentage_data.outgoing.total);
            $('#net-total').text(data.percentage_data.net.total);

            $('#incoming-percent').text(data.percentage_data.incoming.percentage + '%');
            $('#outgoing-percent').text(data.percentage_data.outgoing.percentage + '%');

            updateTrendCell('#incoming-trend-percent', data.percentage_data.incoming.trend);
            updateTrendCell('#outgoing-trend-percent', data.percentage_data.outgoing.trend);
            updateTrendCell('#net-trend-percent', data.percentage_data.net.trend);
        }

        // Update top products tables
        function updateTopProducts(data) {
            updateTopProductsTable('#top-incoming-table', data.top_incoming_products);
            updateTopProductsTable('#top-outgoing-table', data.top_outgoing_products);
        }

        function updateTopProductsTable(selector, products) {
            let html = '';
            if (products && products.length > 0) {
                products.forEach((product, index) => {
                    html += `
                    <tr>
                        <td>${product.name || 'N/A'}</td>
                        <td>${product.code || 'N/A'}</td>
                        <td>${product.total || 0}</td>
                    </tr>
                `;
                });
            } else {
                html = '<tr><td colspan="3">Tidak ada data</td></tr>';
            }
            $(selector).html(html);
        }

        // Render monthly chart
        function renderMonthlyChart(data) {
            const ctx = document.getElementById('monthlyChart').getContext('2d');

            if (monthlyChart) {
                monthlyChart.destroy();
            }

            monthlyChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                            label: 'Barang Masuk',
                            data: data.incoming,
                            borderColor: '#28a745',
                            backgroundColor: 'rgba(40, 167, 69, 0.1)',
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: 'Barang Keluar',
                            data: data.outgoing,
                            borderColor: '#ffc107',
                            backgroundColor: 'rgba(255, 193, 7, 0.1)',
                            fill: true,
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Trend Barang Masuk & Keluar'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Quantity'
                            }
                        }
                    }
                }
            });
        }

        // Helper functions for trend indicators
        function updateTrendIndicator(selector, trend) {
            const element = $(selector);
            element.removeClass('text-success text-danger text-muted');

            if (trend > 0) {
                element.addClass('text-success').html(`<i class="ik ik-arrow-up"></i> ${Math.abs(trend)}%`);
            } else if (trend < 0) {
                element.addClass('text-danger').html(`<i class="ik ik-arrow-down"></i> ${Math.abs(trend)}%`);
            } else {
                element.addClass('text-muted').html('0%');
            }
        }

        function updateTrendCell(selector, trend) {
            const element = $(selector);
            element.removeClass('text-success text-danger text-muted');

            if (trend > 0) {
                element.addClass('text-success').html(`+${trend}%`);
            } else if (trend < 0) {
                element.addClass('text-danger').html(`${trend}%`);
            } else {
                element.addClass('text-muted').html('0%');
            }
        }

        // Fungsi untuk menampilkan pesan error
        function showError(message) {
            $('#total-products, #total-incoming, #total-outgoing, #low-stock').html('Error');
            console.error(message);
        }

        // Fungsi untuk mengambil data stock overview
        function loadStockOverviewData() {
            $.ajax({
                url: '<?= site_url('/api/v1/dashboard/stock-overview') ?>',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        renderStockOverviewChart(response.data);
                    } else {
                        console.error('Gagal memuat data stock overview');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching stock overview data:', error);
                }
            });
        }

        // Render stock overview chart
        function renderStockOverviewChart(data) {
            const ctx = document.getElementById('StockOverviewChart').getContext('2d');

            // Hancurkan chart sebelumnya jika ada
            if (stockOverviewChart) {
                stockOverviewChart.destroy();
            }

            // Data untuk chart
            const chartData = {
                labels: ['critical', 'low', 'medium', 'high'],
                datasets: [{
                    data: [
                        data.stock_levels.critical,
                        data.stock_levels.low,
                        data.stock_levels.medium,
                        data.stock_levels.high
                    ],
                    backgroundColor: [
                        '#dc3545', // Merah untuk kritis
                        '#ffc107', // Kuning untuk rendah
                        '#17a2b8', // Biru untuk sedang
                        '#28a745' // Hijau untuk tinggi
                    ],
                    borderWidth: 1
                }]
            };

            // Options untuk chart
            const options = {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'Distribusi Tingkat Stok (' + data.total_products + ' produk)'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const percentage = data.percentages[label.toLowerCase()] || 0;
                                return `${label}: ${value} produk (${percentage}%)`;
                            }
                        }
                    }
                }
            };

            // Buat chart
            stockOverviewChart = new Chart(ctx, {
                type: 'doughnut',
                data: chartData,
                options: options
            });
        }


        // Event listener untuk period buttons
        $('[data-period]').click(function() {
            $('[data-period]').removeClass('active');
            $(this).addClass('active');

            const period = $(this).data('period');
            loadAnalyticsData(period);
        });

        // Load data analytics berdasarkan period
        function loadAnalyticsData(period = 'month') {
            $.ajax({
                url: '<?= site_url('/api/v1/dashboard/analytics') ?>',
                method: 'GET',
                data: {
                    type: period,
                    limit: period === 'year' ? 5 : 12
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        renderMonthlyChart(response.data);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching analytics data:', error);
                }
            });
        }

        // Load data pertama kali
        loadDashboardData();

        // Optional: Refresh data setiap 1 menit
        setInterval(loadDashboardData, 60000);

    });
</script>

<?= $this->endSection() ?>