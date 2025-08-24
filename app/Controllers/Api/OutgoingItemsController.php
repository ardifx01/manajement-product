<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\OutgoingItems;
use App\Models\Products; // Pastikan model Product diimport
use CodeIgniter\API\ResponseTrait;

class OutgoingItemsController extends BaseController
{
    use ResponseTrait;

    protected $model;
    protected $productModel;

    public function __construct()
    {
        $this->model = new OutgoingItems();
        $this->productModel = new Products(); // Inisialisasi model Product
    }

    /**
     * Get all outgoing items
     */
    public function index()
    {
        try {
            $page = $this->request->getGet('page') ?? 1;
            $perPage = $this->request->getGet('per_page') ?? 10;
            $productId = $this->request->getGet('product_id');
            $startDate = $this->request->getGet('start_date');
            $endDate = $this->request->getGet('end_date');

            $builder = $this->model->select('outgoing_items.*, products.name as product_name')
                ->join('products', 'products.id = outgoing_items.product_id');

            if ($productId) {
                $builder->where('outgoing_items.product_id', $productId);
            }

            if ($startDate) {
                $builder->where('outgoing_items.date >=', $startDate);
            }
            if ($endDate) {
                $builder->where('outgoing_items.date <=', $endDate);
            }

            $data = $builder->orderBy('outgoing_items.date', 'DESC')
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
     * Get single outgoing item
     */
    public function show($id = null)
    {
        try {
            $data = $this->model->select('outgoing_items.*, products.name as product_name')
                ->join('products', 'products.id = outgoing_items.product_id')
                ->find($id);

            if (!$data) {
                return $this->failNotFound('Outgoing item not found');
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
     * Create new outgoing item
     */
    public function create()
    {
        $db = \Config\Database::connect();
        $db->transStart(); // Mulai transaksi database

        try {
            $data = [
                'product_id' => $this->request->getJsonVar('product_id'),
                'date' => $this->request->getJsonVar('date'),
                'quantity' => $this->request->getJsonVar('quantity')
            ];

            // Validasi stok cukup
            $product = $this->productModel->find($data['product_id']);
            if (!$product) {
                return $this->failNotFound('Product not found');
            }

            if ($product['stock'] < $data['quantity']) {
                return $this->fail('Insufficient stock', 400);
            }

            if ($this->model->save($data) === false) {
                $db->transRollback();
                return $this->failValidationErrors($this->model->errors());
            }

            // Kurangi stok produk
            $newStock = $product['stock'] - $data['quantity'];
            if (!$this->productModel->update($data['product_id'], ['stock' => $newStock])) {
                $db->transRollback();
                return $this->fail('Failed to update product stock');
            }

            $id = $this->model->getInsertID();
            $newData = $this->model->select('outgoing_items.*, products.name as product_name')
                ->join('products', 'products.id = outgoing_items.product_id')
                ->find($id);

            $db->transComplete(); // Selesaikan transaksi

            return $this->respondCreated([
                'status' => 'success',
                'message' => 'Outgoing item created successfully',
                'data' => $newData
            ]);
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Update outgoing item
     */
    public function update($id = null)
    {
        $db = \Config\Database::connect();
        $db->transStart(); // Mulai transaksi database

        try {
            $existing = $this->model->find($id);
            if (!$existing) {
                return $this->failNotFound('Outgoing item not found');
            }

            $data = [
                'product_id' => $this->request->getJsonVar('product_id') ?? $existing['product_id'],
                'date' => $this->request->getJsonVar('date') ?? $existing['date'],
                'quantity' => $this->request->getJsonVar('quantity') ?? $existing['quantity']
            ];

            // Jika quantity berubah, update stok produk
            if ($data['quantity'] != $existing['quantity'] || $data['product_id'] != $existing['product_id']) {
                // Kembalikan stok produk lama
                $oldProduct = $this->productModel->find($existing['product_id']);
                if ($oldProduct) {
                    $oldStock = $oldProduct['stock'] + $existing['quantity'];
                    $this->productModel->update($existing['product_id'], ['stock' => $oldStock]);
                }

                // Kurangi stok produk baru
                $newProduct = $this->productModel->find($data['product_id']);
                if (!$newProduct) {
                    $db->transRollback();
                    return $this->failNotFound('Product not found');
                }

                if ($newProduct['stock'] < $data['quantity']) {
                    $db->transRollback();
                    return $this->fail('Insufficient stock', 400);
                }

                $newStock = $newProduct['stock'] - $data['quantity'];
                $this->productModel->update($data['product_id'], ['stock' => $newStock]);
            }

            if ($this->model->update($id, $data) === false) {
                $db->transRollback();
                return $this->failValidationErrors($this->model->errors());
            }

            $updatedData = $this->model->select('outgoing_items.*, products.name as product_name')
                ->join('products', 'products.id = outgoing_items.product_id')
                ->find($id);

            $db->transComplete(); // Selesaikan transaksi

            return $this->respond([
                'status' => 'success',
                'message' => 'Outgoing item updated successfully',
                'data' => $updatedData
            ]);
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Delete outgoing item
     */
    public function delete($id = null)
    {
        $db = \Config\Database::connect();
        $db->transStart(); // Mulai transaksi database

        try {
            $data = $this->model->find($id);
            if (!$data) {
                return $this->failNotFound('Outgoing item not found');
            }

            // Kembalikan stok produk
            $product = $this->productModel->find($data['product_id']);
            if ($product) {
                $newStock = $product['stock'] + $data['quantity'];
                $this->productModel->update($data['product_id'], ['stock' => $newStock]);
            }

            $this->model->delete($id);
            
            $db->transComplete(); // Selesaikan transaksi

            return $this->respondDeleted([
                'status' => 'success',
                'message' => 'Outgoing item deleted successfully'
            ]);
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Get outgoing items summary by product
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
            ->join('products', 'products.id = outgoing_items.product_id')
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
}