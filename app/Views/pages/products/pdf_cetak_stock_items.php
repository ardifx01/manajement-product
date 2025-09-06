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
            margin-bottom: 20px;
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
        
        /* Summary Cards */
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .summary-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #3498db;
            text-align: center;
        }
        
        .summary-card.low-stock {
            border-left-color: #e74c3c;
        }
        
        .summary-card.total-value {
            border-left-color: #27ae60;
        }
        
        .summary-card h3 {
            margin: 0 0 10px 0;
            color: #2c3e50;
            font-size: 14px;
        }
        
        .summary-card .number {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .summary-card .unit {
            font-size: 12px;
            color: #7f8c8d;
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
            font-size: 11px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
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
        
        /* Stock status colors */
        .stock-low {
            background-color: #ffe6e6;
            color: #c0392b;
            font-weight: bold;
        }
        
        .stock-medium {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .stock-good {
            background-color: #e6f7ff;
            color: #2980b9;
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
        <div class="subtitle">Laporan Seluruh Stok Produk</div>
    </div>
    
    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card">
            <h3>TOTAL PRODUK</h3>
            <div class="number"><?= number_format($total_products) ?></div>
            <div class="unit">Jenis Produk</div>
        </div>
        
        <div class="summary-card">
            <h3>TOTAL STOK</h3>
            <div class="number"><?= number_format($total_stock) ?></div>
            <div class="unit">Unit</div>
        </div>
        
        <div class="summary-card low-stock">
            <h3>STOK RENDAH</h3>
            <div class="number"><?= number_format($low_stock_count) ?></div>
            <div class="unit">Produk (Dibawah 10 unit)</div>
        </div>
        
        <div class="summary-card total-value">
            <h3>NILAI TOTAL STOK</h3>
            <div class="number">Rp <?= isset($total_value) ? number_format($total_value, 0, ',', '.') : '0' ?></div>
            <div class="unit">Total Nilai</div>
        </div>
    </div>
    
    <!-- Table -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="15%">Kode Produk</th>
                    <th width="20%">Nama Produk</th>
                    <th width="15%">Kategori</th>
                    <th width="10%">Stok</th>
                    <th width="10%">Satuan</th>
                    <th width="10%">Status Stok</th>
                    <th width="15%">Harga</th>
                    <th width="15%">Nilai Stok</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($products)): ?>
                    <?php 
                    $no = 1;
                    foreach ($products as $product): 
                        // Tentukan status stok
                        $stockStatus = '';
                        $statusClass = '';
                        
                        if ($product['stock'] <= 10) {
                            $stockStatus = 'RENDAH';
                            $statusClass = 'stock-low';
                        } elseif ($product['stock'] <= 50) {
                            $stockStatus = 'SEDANG';
                            $statusClass = 'stock-medium';
                        } else {
                            $stockStatus = 'BAIK';
                            $statusClass = 'stock-good';
                        }
                        
                        // Hitung nilai stok
                        $stockValue = isset($product['price']) ? $product['stock'] * $product['price'] : 0;
                    ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><?= $product['code'] ?? '-' ?></td>
                        <td><?= $product['name'] ?? '-' ?></td>
                        <td><?= $product['category_name'] ?? 'Tidak Berkategori' ?></td>
                        <td class="text-right"><?= number_format($product['stock']) ?></td>
                        <td class="text-center"><?= $product['unit'] ?? '-' ?></td>
                        <td class="text-center <?= $statusClass ?>"><?= $stockStatus ?></td>
                        <td class="text-right">
                            <?= isset($product['price']) ? 'Rp ' . number_format($product['price'], 0, ',', '.') : '-' ?>
                        </td>
                        <td class="text-right">
                            <?= 'Rp ' . number_format($stockValue, 0, ',', '.') ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="no-data">
                            Tidak ada data produk ditemukan
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        <p>Dicetak pada: <?= $generated_at ?> | MUN-PRC </p>
        <p>Total Data: <?= number_format($total_products) ?> produk</p>
    </div>
</body>
</html>