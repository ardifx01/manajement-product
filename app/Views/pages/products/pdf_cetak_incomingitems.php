<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= $title ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            padding: 0;
        }
        .filter-info {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><?= $title ?></h1>
        <p>Generated on: <?= date('d F Y H:i:s') ?></p>
    </div>

    <?php if (!empty($filter_product) || !empty($filter_start_date) || !empty($filter_end_date)): ?>
    <div class="filter-info">
        <strong>Filter Applied:</strong>
        <?php if (!empty($filter_product)): ?>
            <span>Product: <?= $filter_product['name'] ?> (<?= $filter_product['code'] ?>)</span>
        <?php endif; ?>
        <?php if (!empty($filter_start_date)): ?>
            <span> | Start Date: <?= date('d F Y', strtotime($filter_start_date)) ?></span>
        <?php endif; ?>
        <?php if (!empty($filter_end_date)): ?>
            <span> | End Date: <?= date('d F Y', strtotime($filter_end_date)) ?></span>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Product</th>
                <th>Date</th>
                <th class="text-right">Quantity</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total_quantity = 0;
            $no = 1;
            foreach ($incoming_items as $item): 
                $total_quantity += $item['quantity'];
            ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td><?= $item['product_name'] ?> (<?= $item['product_code'] ?>)</td>
                <td><?= date('d F Y H:i', strtotime($item['date'])) ?></td>
                <td class="text-right"><?= number_format($item['quantity'], 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
            
            <?php if (count($incoming_items) == 0): ?>
            <tr>
                <td colspan="4" class="text-center">No data available</td>
            </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right">Total Quantity:</th>
                <th class="text-right"><?= number_format($total_quantity, 0, ',', '.') ?></th>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Printed by: System</p>
    </div>
</body>
</html>