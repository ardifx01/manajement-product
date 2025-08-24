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
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Fungsi untuk mengambil data dashboard
    function loadDashboardData() {
        $.ajax({
            url: '<?= site_url('/api/v1/dashboard/summary') ?>',
            method: 'GET',
            dataType: 'json',
            beforeSend: function() {
                // Tampilkan loading indicator jika diperlukan
                $('#total-products, #total-incoming, #total-outgoing, #low-stock').html('<i class="ik ik-loader"></i>');
            },
            success: function(response) {
                if (response.status === 'success') {
                    // Update data dashboard
                    $('#total-products').text(response.data.total_products || 0);
                    $('#total-incoming').text(response.data.total_incoming || 0);
                    $('#total-outgoing').text(response.data.total_outgoing || 0);
                    $('#low-stock').text(response.data.low_stock_products || 0);
                    
                    // Update progress bars
                    $('#product-progress').css('width', Math.min(100, (response.data.total_products || 0) / 500 * 100) + '%');
                    $('#incoming-progress').css('width', Math.min(100, (response.data.total_incoming || 0) / 200 * 100) + '%');
                    $('#outgoing-progress').css('width', Math.min(100, (response.data.total_outgoing || 0) / 150 * 100) + '%');
                    $('#lowstock-progress').css('width', Math.min(100, (response.data.low_stock_products || 0) / 50 * 100) + '%');
                } else {
                    showError('Gagal memuat data dashboard');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching dashboard data:', error);
                showError('Terjadi kesalahan saat memuat data');
            }
        });
    }
    
    // Fungsi untuk menampilkan pesan error
    function showError(message) {
        $('#total-products, #total-incoming, #total-outgoing, #low-stock').html('Error');
        
        // Bisa juga tambahkan notifikasi toast atau alert
        // $.notify(message, 'error');
    }
    
    // Load data pertama kali
    loadDashboardData();
    
    // Optional: Refresh data setiap 1 menit
    setInterval(loadDashboardData, 60000);
});
</script>
<?= $this->endSection() ?>