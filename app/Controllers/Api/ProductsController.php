<?php

namespace App\Controllers\Api;

use App\Models\Products;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class ProductsController extends ResourceController
{
    use ResponseTrait;

    protected $model;

    public function __construct()
    {
        $this->model = new Products();
    }

    /**
     * Get all products
     */
    public function index()
    {
        try {
            $products = $this->model->withCategory()->findAll();

            return $this->respond([
                'status' => 'success',
                'data' => $products,
                'total' => count($products)
            ]);
        } catch (\Throwable $e) {
            return $this->failServerError('Terjadi kesalahan server: ' . $e->getMessage());
        }
    }

    /**
     * Get single product by ID
     */
    public function show($id = null)
    {
        try {
            if (!$id) {
                return $this->failValidationErrors('ID produk diperlukan');
            }

            $product = $this->model->find($id);

            if (!$product) {
                return $this->failNotFound('Produk tidak ditemukan');
            }

            return $this->respond([
                'status' => 'success',
                'data' => $product
            ]);
        } catch (\Throwable $e) {
            return $this->failServerError('Terjadi kesalahan server: ' . $e->getMessage());
        }
    }

    /**
     * Create new product
     */
    public function create()
    {
        try {
            $data = $this->request->getJSON(true);
            if (!$data) {
                return $this->failValidationErrors('Data produk diperlukan');
            }

            // Untuk create, tambahkan is_unique rule sementara
            $createRules = $this->model->getValidationRules();
            $createRules['code'] .= '|is_unique[products.code]';

            $this->model->setValidationRules($createRules);

            if (!$this->model->validate($data)) {
                return $this->failValidationErrors($this->model->errors());
            }

            // Juga validasi menggunakan method isCodeUnique untuk double check
            if (isset($data['code']) && !$this->model->isCodeUnique($data['code'])) {
                return $this->failValidationErrors(['code' => 'Kode produk sudah digunakan']);
            }

            if ($productId = $this->model->insert($data)) {
                $newProduct = $this->model->find($productId);

                return $this->respondCreated([
                    'status' => 'success',
                    'message' => 'Produk berhasil dibuat',
                    'data' => $newProduct
                ]);
            } else {
                return $this->failServerError('Gagal membuat produk');
            }
        } catch (\Throwable $e) {
            return $this->failServerError('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update product
     */
    public function update($id = null)
    {
        try {
            if (!$id) {
                return $this->failValidationErrors('ID produk diperlukan');
            }

            $existing = $this->model->find($id);
            if (!$existing) {
                return $this->failNotFound('Produk tidak ditemukan');
            }

            $data = $this->request->getJSON(true);
            if (!$data) {
                return $this->failValidationErrors('Data produk diperlukan');
            }

            // Validasi dasar tanpa is_unique untuk code
            if (!$this->model->validate($data)) {
                return $this->failValidationErrors($this->model->errors());
            }

            // Validasi manual untuk uniqueness jika code diubah
            if (isset($data['code']) && $data['code'] !== $existing['code']) {
                if (!$this->model->isCodeUnique($data['code'], $id)) {
                    return $this->failValidationErrors(['code' => 'Kode produk sudah digunakan']);
                }
            }

            if ($this->model->update($id, $data)) {
                $updatedProduct = $this->model->find($id);

                return $this->respond([
                    'status' => 'success',
                    'message' => 'Produk berhasil diupdate',
                    'data' => $updatedProduct
                ]);
            } else {
                return $this->failServerError('Gagal mengupdate produk');
            }
        } catch (\Throwable $e) {
            return $this->failServerError('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Delete product
     */
    public function delete($id = null)
    {
        try {
            if (!$id) {
                return $this->failValidationErrors('ID produk diperlukan');
            }

            $existing = $this->model->find($id);
            if (!$existing) {
                return $this->failNotFound('Produk tidak ditemukan');
            }

            if ($this->model->delete($id)) {
                return $this->respondDeleted([
                    'status' => 'success',
                    'message' => 'Produk berhasil dihapus'
                ]);
            } else {
                return $this->failServerError('Gagal menghapus produk');
            }
        } catch (\Throwable $e) {
            return $this->failServerError('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Get products with pagination
     */
    public function paginated()
    {
        try {
            $page = (int) ($this->request->getGet('page') ?? 1);
            $perPage = (int) ($this->request->getGet('per_page') ?? 10);

            $products = $this->model->paginate($perPage, 'default', $page);
            $pager = $this->model->pager;

            return $this->respond([
                'status' => 'success',
                'data' => $products,
                'pagination' => [
                    'current_page' => $pager->getCurrentPage(),
                    'total_pages' => $pager->getPageCount(),
                    'total_items' => $pager->getTotal(),
                    'per_page' => $perPage
                ]
            ]);
        } catch (\Throwable $e) {
            return $this->failServerError('Terjadi kesalahan server: ' . $e->getMessage());
        }
    }

    /**
     * Search products by name or code
     */
    public function search()
    {
        try {
            $searchTerm = $this->request->getGet('q');

            if (!$searchTerm) {
                return $this->failValidationErrors('Parameter pencarian (q) diperlukan');
            }

            $products = $this->model
                ->groupStart()
                ->like('name', $searchTerm)
                ->orLike('code', $searchTerm)
                ->groupEnd()
                ->findAll();

            return $this->respond([
                'status' => 'success',
                'data' => $products,
                'total' => count($products),
                'search_term' => $searchTerm
            ]);
        } catch (\Throwable $e) {
            return $this->failServerError('Terjadi kesalahan server: ' . $e->getMessage());
        }
    }

    /**
     * Get products by category
     */
    public function byCategory($categoryId = null)
    {
        try {
            if (!$categoryId) {
                return $this->failValidationErrors('ID kategori diperlukan');
            }

            $products = $this->model->where('category_id', $categoryId)->findAll();

            return $this->respond([
                'status' => 'success',
                'data' => $products,
                'total' => count($products),
                'category_id' => $categoryId
            ]);
        } catch (\Throwable $e) {
            return $this->failServerError('Terjadi kesalahan server: ' . $e->getMessage());
        }
    }

    /**
     * Get low stock products
     */
    public function lowStock()
    {
        try {
            $threshold = (int) ($this->request->getGet('threshold') ?? 10);

            $products = $this->model->where('stock <=', $threshold)->findAll();

            return $this->respond([
                'status' => 'success',
                'data' => $products,
                'total' => count($products),
                'threshold' => $threshold
            ]);
        } catch (\Throwable $e) {
            return $this->failServerError('Terjadi kesalahan server: ' . $e->getMessage());
        }
    }
}
