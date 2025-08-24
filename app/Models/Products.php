<?php

namespace App\Models;

use CodeIgniter\Model;

class Products extends Model
{
    protected $table            = 'products';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['category_id', 'name', 'code', 'unit', 'stock', 'crated_at', 'updated_at'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'category_id' => 'required|integer',
        'name'        => 'required|min_length[3]|max_length[255]',
        'code'        => 'if_exist|min_length[2]|max_length[50]',
        'unit'        => 'required|min_length[3]|max_length[50]',
        'stock'       => 'required|numeric'
    ];
    
    protected $validationMessages   = [
        'category_id' => [
            'required' => 'Kategori produk harus diisi',
            'integer'  => 'Kategori produk harus berupa angka'
        ],
        'name' => [
            'required'   => 'Nama produk harus diisi',
            'min_length' => 'Nama produk minimal 3 karakter',
            'max_length' => 'Nama produk maksimal 255 karakter'
        ],
        'code' => [
            'required'   => 'Kode produk harus diisi',
            'min_length' => 'Kode produk minimal 2 karakter',
            'max_length' => 'Kode produk maksimal 50 karakter',
            'is_unique'  => 'Kode produk sudah digunakan'
        ],
        'unit' => [
            'max_length' => 'Satuan produk maksimal 50 karakter'
        ],
        'stock' => [
            'required' => 'Stok produk harus diisi',
            'numeric'  => 'Stok produk harus berupa angka'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get products with category information
     */
    public function withCategory()
    {
        return $this->select('products.*, categories.name as category_name')
            ->join('categories', 'categories.id = products.category_id');
    }
}
