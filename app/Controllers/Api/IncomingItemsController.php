<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\IncomingItems;
use App\Models\Products; // Tambahkan model produk
use CodeIgniter\API\ResponseTrait;
use Dompdf\Dompdf;
use Dompdf\Options;

class IncomingItemsController extends BaseController
{
    use ResponseTrait;

    protected $model;
    protected $productModel;

    public function __construct()
    {
        $this->model = new IncomingItems();
        $this->productModel = new Products(); // Inisialisasi model produk
        helper(['url', 'form']);
    }

    /**
     * Get all incoming items
     */
    public function index()
    {
        try {
            $page = $this->request->getGet('page') ?? 1;
            $perPage = $this->request->getGet('per_page') ?? 10;
            $productId = $this->request->getGet('product_id');
            $startDate = $this->request->getGet('start_date');
            $endDate = $this->request->getGet('end_date');

            $builder = $this->model->select('incoming_items.*, products.name as product_name')
                ->join('products', 'products.id = incoming_items.product_id');

            // Filter by product_id
            if ($productId) {
                $builder->where('incoming_items.product_id', $productId);
            }

            // Filter by date range
            if ($startDate) {
                $builder->where('incoming_items.date >=', $startDate);
            }
            if ($endDate) {
                $builder->where('incoming_items.date <=', $endDate);
            }

            $data = $builder->orderBy('incoming_items.date', 'DESC')
                ->paginate($perPage, 'default', $page);

            return $this->respond([
                'status' => 'success',
                'data' => $data,
                'pager' => $this->model->pager->getDetails()
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Get single incoming item
     */
    public function show($id = null)
    {
        try {
            $data = $this->model->select('incoming_items.*, products.name as product_name')
                ->join('products', 'products.id = incoming_items.product_id')
                ->find($id);

            if (!$data) {
                return $this->failNotFound('Incoming item not found');
            }

            return $this->respond([
                'status' => 'success',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Create new incoming item
     */
    public function create()
    {
        try {
            $data = [
                'product_id' => $this->request->getJsonVar('product_id'),
                'date' => $this->request->getJsonVar('date'),
                'quantity' => $this->request->getJsonVar('quantity')
            ];

            // Konversi string date ke format yang benar untuk database
            if (!empty($data['date'])) {
                // Coba berbagai format yang mungkin
                $date = \DateTime::createFromFormat('Y-m-d H:i:s', $data['date']);
                if (!$date) {
                    $date = \DateTime::createFromFormat('Y-m-d\TH:i', $data['date']);
                }
                if (!$date) {
                    $date = \DateTime::createFromFormat('Y-m-d', $data['date']);
                }

                if ($date) {
                    $data['date'] = $date->format('Y-m-d H:i:s');
                } else {
                    $data['date'] = date('Y-m-d H:i:s'); // Waktu sekarang jika invalid
                }
            } else {
                $data['date'] = date('Y-m-d H:i:s'); // Waktu sekarang jika kosong
            }

            if ($this->model->save($data) === false) {
                return $this->failValidationErrors($this->model->errors());
            }

            // Update stok produk
            $this->updateProductStock($data['product_id'], $data['quantity'], 'increase');

            $id = $this->model->getInsertID();
            $newData = $this->model->select('incoming_items.*, products.name as product_name')
                ->join('products', 'products.id = incoming_items.product_id')
                ->find($id);

            return $this->respondCreated([
                'status' => 'success',
                'message' => 'Incoming item created successfully',
                'data' => $newData
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Update incoming item
     */
    public function update($id = null)
    {
        try {
            $existing = $this->model->find($id);
            if (!$existing) {
                return $this->failNotFound('Incoming item not found');
            }

            $data = [
                'product_id' => $this->request->getJsonVar('product_id') ?? $existing['product_id'],
                'date' => $this->request->getJsonVar('date') ?? $existing['date'],
                'quantity' => $this->request->getJsonVar('quantity') ?? $existing['quantity']
            ];

            // Simpan quantity lama untuk perhitungan stok
            $oldQuantity = $existing['quantity'];
            $oldProductId = $existing['product_id'];

            if ($this->model->update($id, $data) === false) {
                return $this->failValidationErrors($this->model->errors());
            }

            // Update stok produk
            if ($oldProductId == $data['product_id']) {
                // Produk sama, hitung selisih quantity
                $quantityDiff = $data['quantity'] - $oldQuantity;
                if ($quantityDiff != 0) {
                    $this->updateProductStock($data['product_id'], abs($quantityDiff), $quantityDiff > 0 ? 'increase' : 'decrease');
                }
            } else {
                // Produk berbeda, kurangi stok produk lama dan tambah stok produk baru
                $this->updateProductStock($oldProductId, $oldQuantity, 'decrease');
                $this->updateProductStock($data['product_id'], $data['quantity'], 'increase');
            }

            $updatedData = $this->model->select('incoming_items.*, products.name as product_name')
                ->join('products', 'products.id = incoming_items.product_id')
                ->find($id);

            return $this->respond([
                'status' => 'success',
                'message' => 'Incoming item updated successfully',
                'data' => $updatedData
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Delete incoming item
     */
    public function delete($id = null)
    {
        try {
            $data = $this->model->find($id);
            if (!$data) {
                return $this->failNotFound('Incoming item not found');
            }

            // Kurangi stok produk sebelum menghapus
            $this->updateProductStock($data['product_id'], $data['quantity'], 'decrease');

            $this->model->delete($id);

            return $this->respondDeleted([
                'status' => 'success',
                'message' => 'Incoming item deleted successfully'
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Get incoming items summary by product
     */
    public function summary()
    {
        try {
            $startDate = $this->request->getGet('start_date');
            $endDate = $this->request->getGet('end_date');

            $builder = $this->model->select('
                product_id,
                products.name as product_name,
                COUNT(*) as total_transactions,
                SUM(quantity) as total_quantity
            ')
                ->join('products', 'products.id = incoming_items.product_id')
                ->groupBy('product_id, products.name');

            if ($startDate) {
                $builder->where('date >=', $startDate);
            }
            if ($endDate) {
                $builder->where('date <=', $endDate);
            }

            $summary = $builder->findAll();

            return $this->respond([
                'status' => 'success',
                'data' => $summary
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Helper method to update product stock
     */
    private function updateProductStock($productId, $quantity, $action = 'increase')
    {
        $product = $this->productModel->find($productId);
        if ($product) {
            if ($action === 'increase') {
                $newStock = $product['stock'] + $quantity;
            } else {
                $newStock = $product['stock'] - $quantity;
                // Pastikan stok tidak negatif
                if ($newStock < 0) {
                    $newStock = 0;
                }
            }

            $this->productModel->update($productId, ['stock' => $newStock]);
        }
    }

    public function pdf()
    {
        try {
            log_message('debug', 'PDF method accessed');

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

            // Get data
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
                'generated_at' => date('d/m/Y H:i:s')
            ];

            // Load HTML content
            $html = view('pdf/incoming_items_report', $data);

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

            exit; // Penting: exit setelah stream

        } catch (\Exception $e) {
            log_message('error', 'PDF Generation Error: ' . $e->getMessage());
            return $this->failServerError('Gagal generate PDF: ' . $e->getMessage());
        }
    }
}
