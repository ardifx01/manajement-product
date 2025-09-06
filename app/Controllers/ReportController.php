<?php

namespace App\Controllers;

use App\Models\IncomingItems;
use App\Models\OutgoingItems;
use App\Models\Products;
use Dompdf\Dompdf;
use Dompdf\Options;

class ReportController extends BaseController
{
    protected $incomingItemsModel;
    protected $outgoingItemsModel;
    protected $productsModel;

    public function __construct()
    {
        $this->incomingItemsModel = new IncomingItems();
        $this->outgoingItemsModel = new OutgoingItems();
        $this->productsModel = new Products();
        helper(['url', 'form']);
    }

    /**
     * Cetak PDF Laporan Barang Masuk
     */
    public function incomingItemsPdf()
    {
        try {
            // Ambil parameter filter
            $productId = $this->request->getGet('product_id');
            $startDate = $this->request->getGet('start_date');
            $endDate = $this->request->getGet('end_date');

            // Build query dengan join ke products
            $builder = $this->incomingItemsModel->select('
                incoming_items.*, 
                products.code as product_code,
                products.name as product_name,
                products.unit as product_unit
            ')
                ->join('products', 'products.id = incoming_items.product_id');

            // Filter by product_id
            if (!empty($productId) && $productId != 'all') {
                $builder->where('incoming_items.product_id', $productId);
            }

            // Filter by date range
            if (!empty($startDate)) {
                $builder->where('DATE(incoming_items.date) >=', $startDate);
            }
            if (!empty($endDate)) {
                $builder->where('DATE(incoming_items.date) <=', $endDate);
            }

            // Get data dengan urutan terbaru
            $items = $builder->orderBy('incoming_items.date', 'DESC')
                ->orderBy('incoming_items.created_at', 'DESC')
                ->findAll();

            // Hitung total quantity
            $totalQuantity = 0;
            foreach ($items as $item) {
                $totalQuantity += (int)$item['quantity'];
            }

            // Data untuk view
            $data = [
                'items' => $items,
                'total_quantity' => $totalQuantity,
                'filters' => [
                    'product_id' => $productId,
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ],
                'report_title' => 'LAPORAN BARANG MASUK',
                'report_type' => 'incoming',
                'generated_at' => date('d/m/Y H:i:s')
            ];

            // Load HTML content
            $html = view('pages/products/pdf_cetak_reports_tems', $data);

            // Setup DomPDF
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            $options->set('defaultFont', 'Arial');
            $options->set('isPhpEnabled', true);

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);

            // Setup paper size and orientation
            $dompdf->setPaper('A4', 'landscape');

            // Render PDF
            $dompdf->render();

            // Generate filename
            $filename = 'laporan-barang-masuk-' . date('Y-m-d-H-i-s') . '.pdf';

            // Output PDF to browser
            $dompdf->stream($filename, [
                'Attachment' => false
            ]);

            exit;
        } catch (\Exception $e) {
            die('Error generating PDF: ' . $e->getMessage());
        }
    }

    /**
     * Cetak PDF Laporan Barang Keluar
     */
    public function outgoingItemsPdf()
    {
        try {
            // Ambil parameter filter
            $productId = $this->request->getGet('product_id');
            $startDate = $this->request->getGet('start_date');
            $endDate = $this->request->getGet('end_date');
            $customer = $this->request->getGet('customer');

            // Build query dengan join ke products
            $builder = $this->outgoingItemsModel->select('
                outgoing_items.*, 
                products.code as product_code,
                products.name as product_name,
                products.unit as product_unit
            ')
                ->join('products', 'products.id = outgoing_items.product_id');

            // Filter by product_id
            if (!empty($productId) && $productId != 'all') {
                $builder->where('outgoing_items.product_id', $productId);
            }

            // Filter by customer
            if (!empty($customer)) {
                $builder->like('outgoing_items.customer', $customer);
            }

            // Filter by date range
            if (!empty($startDate)) {
                $builder->where('DATE(outgoing_items.date) >=', $startDate);
            }
            if (!empty($endDate)) {
                $builder->where('DATE(outgoing_items.date) <=', $endDate);
            }

            // Get data dengan urutan terbaru
            $items = $builder->orderBy('outgoing_items.date', 'DESC')
                ->orderBy('outgoing_items.created_at', 'DESC')
                ->findAll();

            // Hitung total quantity
            $totalQuantity = 0;
            foreach ($items as $item) {
                $totalQuantity += (int)$item['quantity'];
            }

            // Data untuk view
            $data = [
                'items' => $items,
                'total_quantity' => $totalQuantity,
                'filters' => [
                    'product_id' => $productId,
                    'customer' => $customer,
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ],
                'report_title' => 'LAPORAN BARANG KELUAR',
                'report_type' => 'outgoing',
                'generated_at' => date('d/m/Y H:i:s')
            ];

            // Load HTML content
            $html = view('pages/products/pdf_cetak_reports_tems', $data);

            // Setup DomPDF
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            $options->set('defaultFont', 'Arial');
            $options->set('isPhpEnabled', true);

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);

            // Setup paper size and orientation
            $dompdf->setPaper('A4', 'landscape');

            // Render PDF
            $dompdf->render();

            // Generate filename
            $filename = 'laporan-barang-keluar-' . date('Y-m-d-H-i-s') . '.pdf';

            // Output PDF to browser
            $dompdf->stream($filename, [
                'Attachment' => false
            ]);

            exit;
        } catch (\Exception $e) {
            die('Error generating PDF: ' . $e->getMessage());
        }
    }

    /**
     * Cetak PDF Laporan Stok Produk (Semua Data tanpa filter)
     */
    public function stockReportPdf()
    {
        try {
            // Get semua data produk dengan join kategori
            $products = $this->productsModel->select('
            products.*,
            categories.name as category_name
        ')
                ->join('categories', 'categories.id = products.category_id', 'left')
                ->orderBy('products.name', 'ASC')
                ->findAll();

            // Hitung summary
            $totalProducts = count($products);
            $totalStock = 0;
            $totalValue = 0;
            $lowStockCount = 0;

            foreach ($products as $product) {
                $totalStock += (int)$product['stock'];

                // Hitung total value (stock * harga, jika ada field price)
                if (isset($product['price'])) {
                    $totalValue += (int)$product['stock'] * (float)$product['price'];
                }

                // Hitung produk dengan stok rendah (asumsi stok rendah <= 10)
                if ($product['stock'] <= 10) {
                    $lowStockCount++;
                }
            }

            // Data untuk view
            $data = [
                'products' => $products,
                'total_products' => $totalProducts,
                'total_stock' => $totalStock,
                'total_value' => $totalValue,
                'low_stock_count' => $lowStockCount,
                'report_title' => 'LAPORAN STOK PRODUK',
                'report_type' => 'stock',
                'generated_at' => date('d/m/Y H:i:s')
            ];

            // Load HTML content
            $html = view('pages/products/pdf_cetak_stock_items', $data);

            // Setup DomPDF
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            $options->set('defaultFont', 'Arial');
            $options->set('isPhpEnabled', true);

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);

            // Setup paper size and orientation
            $dompdf->setPaper('A4', 'landscape'); // Landscape karena data banyak

            // Render PDF
            $dompdf->render();

            // Generate filename
            $filename = 'laporan-stok-produk-' . date('Y-m-d-H-i-s') . '.pdf';

            // Output PDF to browser
            $dompdf->stream($filename, [
                'Attachment' => false
            ]);

            exit;
        } catch (\Exception $e) {
            die('Error generating stock PDF: ' . $e->getMessage());
        }
    }
}
