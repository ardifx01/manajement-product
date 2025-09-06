<?php

namespace App\Models;

use CodeIgniter\Model;

class IncomingItems extends Model
{
    protected $table            = 'incoming_items';
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

    // Validation
    protected $validationRules = [
        'product_id' => 'required|integer|is_not_unique[products.id]',
        'date' => 'required|valid_date',
        'quantity' => 'required|decimal|greater_than[0]'
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
    // Method untuk data harian
    public function getDailyData($limit = 30)
    {
        $startDate = date('Y-m-d', strtotime("-{$limit} days"));

        return $this->db->query("
            SELECT DATE(date) as day, SUM(quantity) as total
            FROM incoming_items 
            WHERE date >= ?
            GROUP BY DATE(date)
            ORDER BY day ASC
        ", [$startDate])->getResultArray();
    }

    // Method untuk data bulanan
    public function getMonthlyData($limit = 12)
    {
        return $this->db->query("
            SELECT 
                YEAR(date) as year, 
                MONTH(date) as month, 
                CONCAT(YEAR(date), '-', LPAD(MONTH(date), 2, '0')) as period, 
                SUM(quantity) as total
            FROM incoming_items 
            GROUP BY YEAR(date), MONTH(date)
            ORDER BY year DESC, month DESC
            LIMIT ?
        ", [$limit])->getResultArray();
    }

    // Method untuk data tahunan
    public function getYearlyData($limit = 5)
    {
        return $this->db->query("
            SELECT YEAR(date) as year, SUM(quantity) as total
            FROM incoming_items 
            GROUP BY YEAR(date)
            ORDER BY year DESC
            LIMIT ?
        ", [$limit])->getResultArray();
    }

    // Method untuk trend analysis
    public function getMonthlyTrend()
    {
        $currentMonth = date('Y-m');
        $previousMonth = date('Y-m', strtotime('-1 month'));

        $current = $this->db->query("
            SELECT SUM(quantity) as total
            FROM incoming_items 
            WHERE date LIKE ?
        ", ["{$currentMonth}%"])->getRowArray();

        $previous = $this->db->query("
            SELECT SUM(quantity) as total
            FROM incoming_items 
            WHERE date LIKE ?
        ", ["{$previousMonth}%"])->getRowArray();

        return [
            'current' => (int)($current['total'] ?? 0),
            'previous' => (int)($previous['total'] ?? 0)
        ];
    }

    // Total incoming
    public function getTotalIncoming()
    {
        return $this->selectSum('quantity')->get()->getRow()->quantity ?? 0;
    }

    public function getTopProducts($limit = 5)
    {
        return $this->select('product_id, products.name, SUM(quantity) as total, code')
            ->join('products', 'products.id = incoming_items.product_id')
            ->groupBy('product_id')
            ->orderBy('total', 'DESC')
            ->limit($limit)
            ->findAll();
    }
}
