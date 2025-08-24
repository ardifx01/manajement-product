<?php

namespace App\Models;

use CodeIgniter\Model;

class OutgoingItems extends Model
{
    protected $table            = 'outgoing_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['product_id', 'date', 'quantity'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'product_id' => 'integer',
        'quantity'   => 'float',
    ];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation rules untuk outgoing items
    protected $validationRules = [
        'product_id' => 'required|integer|is_not_unique[products.id]',
        'date'       => 'required|valid_date',
        'quantity'   => 'required|decimal|greater_than[0]'
    ];

    protected $validationMessages   = [
        'product_id' => [
            'required' => 'Product ID harus diisi',
            'integer' => 'Product ID harus berupa angka',
            'is_not_unique' => 'Product ID tidak valid atau tidak ditemukan'
        ],
        'date' => [
            'required' => 'Tanggal harus diisi',
            'valid_date' => 'Format tanggal tidak valid'
        ],
        'quantity' => [
            'required' => 'Quantity harus diisi',
            'decimal' => 'Quantity harus berupa angka desimal',
            'greater_than' => 'Quantity harus lebih besar dari 0'
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

    // Custom validation methods jika diperlukan
    // Add this method
    public function getMonthlyOutgoing()
    {
        return $this->select("DATE_FORMAT(date, '%Y-%m') as month, SUM(quantity) as total")
                    ->groupBy("DATE_FORMAT(date, '%Y-%m')")
                    ->orderBy('month', 'ASC')
                    ->findAll();
    }
}
