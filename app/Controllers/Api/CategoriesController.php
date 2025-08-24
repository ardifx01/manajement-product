<?php

namespace App\Controllers\Api;

use App\Models\Categories;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class CategoriesController extends ResourceController
{
    use ResponseTrait;

    protected $model;
    
    public function __construct()
    {
        $this->model = new Categories();
    }

    /**
     * Get all categories
     */
    public function index()
    {
        try {
            $categories = $this->model->findAll();
            
            return $this->respond([
                'status' => 'success',
                'data' => $categories,
                'total' => count($categories)
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Terjadi kesalahan server: ' . $e->getMessage());
        }
    }

    /**
     * Get single category by ID
     */
    public function show($id = null)
    {
        try {
            $category = $this->model->find($id);
            
            if (!$category) {
                return $this->failNotFound('Kategori tidak ditemukan');
            }
            
            return $this->respond([
                'status' => 'success',
                'data' => $category
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Terjadi kesalahan server: ' . $e->getMessage());
        }
    }

    /**
     * Create new category
     */
    public function create()
    {
        try {
            $data = $this->request->getJSON(true);
            
            // Validasi menggunakan model
            if (!$this->model->validate($data)) {
                return $this->failValidationErrors($this->model->errors());
            }
            
            // Simpan data
            $insertId = $this->model->insert($data);
            
            if (!$insertId) {
                return $this->failServerError('Gagal menyimpan kategori');
            }
            
            // Get the newly created category
            $newCategory = $this->model->find($insertId);
            
            return $this->respondCreated([
                'status' => 'success',
                'message' => 'Kategori berhasil dibuat',
                'data' => $newCategory
            ]);
            
        } catch (\Exception $e) {
            return $this->failServerError('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update category
     */
    public function update($id = null)
    {
        try {
            // Check if category exists
            $existing = $this->model->find($id);
            if (!$existing) {
                return $this->failNotFound('Kategori tidak ditemukan');
            }
            
            $data = $this->request->getJSON(true);
            
            // Validasi menggunakan model
            if (!$this->model->validate($data)) {
                return $this->failValidationErrors($this->model->errors());
            }
            
            // Update data
            if ($this->model->update($id, $data)) {
                $updatedCategory = $this->model->find($id);
                
                return $this->respondUpdated([
                    'status' => 'success',
                    'message' => 'Kategori berhasil diupdate',
                    'data' => $updatedCategory
                ]);
            } else {
                return $this->failServerError('Gagal mengupdate kategori');
            }
            
        } catch (\Exception $e) {
            return $this->failServerError('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Delete category
     */
    public function delete($id = null)
    {
        try {
            // Check if category exists
            $existing = $this->model->find($id);
            if (!$existing) {
                return $this->failNotFound('Kategori tidak ditemukan');
            }
            
            // Delete category
            if ($this->model->delete($id)) {
                return $this->respondDeleted([
                    'status' => 'success',
                    'message' => 'Kategori berhasil dihapus'
                ]);
            } else {
                return $this->failServerError('Gagal menghapus kategori');
            }
            
        } catch (\Exception $e) {
            return $this->failServerError('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Get categories with pagination
     */
    public function paginated()
    {
        try {
            $page = $this->request->getGet('page') ?? 1;
            $perPage = $this->request->getGet('per_page') ?? 10;
            
            $categories = $this->model->paginate($perPage, 'default', $page);
            $pager = $this->model->pager;
            
            return $this->respond([
                'status' => 'success',
                'data' => $categories,
                'pagination' => [
                    'current_page' => $pager->getCurrentPage(),
                    'total_pages' => $pager->getPageCount(),
                    'total_items' => $pager->getTotal(),
                    'per_page' => $perPage
                ]
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Terjadi kesalahan server: ' . $e->getMessage());
        }
    }

    /**
     * Search categories by name
     */
    public function search()
    {
        try {
            $searchTerm = $this->request->getGet('q');
            
            if (!$searchTerm) {
                return $this->failValidationErrors('Parameter pencarian (q) diperlukan');
            }
            
            $categories = $this->model->like('name', $searchTerm)->findAll();
            
            return $this->respond([
                'status' => 'success',
                'data' => $categories,
                'total' => count($categories),
                'search_term' => $searchTerm
            ]);
        } catch (\Exception $e) {
            return $this->failServerError('Terjadi kesalahan server: ' . $e->getMessage());
        }
    }
}