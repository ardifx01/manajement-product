<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= $report_title ?> - MUN-PRC</title>
    <style>
        /* Reset CSS */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'Arial', sans-serif; 
            margin: 20px; 
            color: #333;
            line-height: 1.4;
        }
        
        /* Header Styles */
        .header { 
            text-align: center; 
            margin-bottom: 30px;
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 20px;
        }
        
        .header h1 { 
            margin: 0; 
            color: #2c3e50; 
            font-size: 28px;
            font-weight: bold;
        }
        
        .header .subtitle {
            color: #7f8c8d;
            font-size: 16px;
            margin-top: 5px;
        }
        
        /* Company Info */
        .company-info {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .company-address {
            font-size: 14px;
            color: #7f8c8d;
        }
        
        /* Filter Info */
        .filter-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #3498db;
            margin-bottom: 25px;
        }
        
        .filter-info h3 {
            margin: 0 0 10px 0;
            color: #2c3e50;
            font-size: 16px;
        }
        
        .filter-info p {
            margin: 3px 0;
            font-size: 14px;
        }
        
        /* Table Styles */
        .table-container {
            margin-top: 20px;
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 12px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        
        th {
            background-color: #34495e;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        
        /* Total Summary */
        .total-summary {
            margin-top: 25px;
            padding: 15px;
            background: #e8f4f8;
            border-radius: 8px;
            border-left: 4px solid #3498db;
        }
        
        .total-summary h3 {
            margin: 0 0 10px 0;
            color: #2c3e50;
        }
        
        .total-item {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }
        
        /* Footer */
        .footer {
            margin-top: 40px;
            text-align: right;
            font-size: 12px;
            color: #7f8c8d;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        
        /* No Data */
        .no-data {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Company Header -->
    <div class="company-info">
        <div class="company-name">MUN-PRC</div>
        <div class="company-address">Jl. Kartama Perum Adhi Karya Kota Pekanbaru Riau</div>
    </div>
    
    <!-- Report Header -->
    <div class="header">
        <h1><?= $report_title ?></h1>
        <div class="subtitle">Periode: <?= date('d/m/Y H:i') ?></div>
    </div>
    
    <!-- Filter Information -->
    <div class="filter-info">
        <h3>FILTER LAPORAN</h3>
        <p><strong>Jenis Laporan:</strong> <?= $report_type == 'incoming' ? 'Barang Masuk' : 'Barang Keluar' ?></p>
        <p><strong>Produk:</strong> 
            <?= (empty($filters['product_id']) || $filters['product_id'] == 'all') ? 'Semua Produk' : 'ID: ' . $filters['product_id'] ?>
        </p>
        <?php if ($report_type == 'outgoing' && !empty($filters['customer'])): ?>
        <p><strong>Customer:</strong> <?= $filters['customer'] ?></p>
        <?php endif; ?>
        <p><strong>Tanggal Mulai:</strong> 
            <?= !empty($filters['start_date']) ? date('d/m/Y', strtotime($filters['start_date'])) : 'Semua Tanggal' ?>
        </p>
        <p><strong>Tanggal Akhir:</strong> 
            <?= !empty($filters['end_date']) ? date('d/m/Y', strtotime($filters['end_date'])) : 'Semua Tanggal' ?>
        </p>
    </div>
    
    <!-- Table -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="15%">Tanggal</th>
                    <th width="15%">Kode Produk</th>
                    <th width="20%">Nama Produk</th>
                    <th width="10%">Quantity</th>
                    <th width="10%">Satuan</th>
                    <?php if ($report_type == 'outgoing'): ?>
                    <th width="15%">Customer</th>
                    <?php endif; ?>
                    <th width="<?= $report_type == 'outgoing' ? '20%' : '25%' ?>">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($items)): ?>
                    <?php 
                    $no = 1;
                    foreach ($items as $item): 
                    ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><?= date('d/m/Y', strtotime($item['date'])) ?></td>
                        <td><?= $item['product_code'] ?? '-' ?></td>
                        <td><?= $item['product_name'] ?? '-' ?></td>
                        <td class="text-right"><?= number_format($item['quantity']) ?></td>
                        <td class="text-center"><?= $item['product_unit'] ?? '-' ?></td>
                        <?php if ($report_type == 'outgoing'): ?>
                        <td><?= $item['customer'] ?? '-' ?></td>
                        <?php endif; ?>
                        <td><?= $item['description'] ?? '-' ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?= $report_type == 'outgoing' ? '8' : '7' ?>" class="no-data">
                            Tidak ada data <?= $report_type == 'incoming' ? 'barang masuk' : 'barang keluar' ?> ditemukan
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Total Summary -->
    <?php if (!empty($items)): ?>
    <div class="total-summary">
        <h3>RINGKASAN</h3>
        <div class="total-item">
            <span class="text-bold">Total Transaksi:</span>
            <span class="text-bold"><?= count($items) ?> Transaksi</span>
        </div>
        <div class="total-item">
            <span class="text-bold">Total Quantity:</span>
            <span class="text-bold"><?= number_format($total_quantity) ?> Unit</span>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Footer -->
    <div class="footer">
        <p>Dicetak pada: <?= $generated_at ?> | MUN-PRC</p>
    </div>
</body>
</html>