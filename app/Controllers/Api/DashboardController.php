<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\Products;
use App\Models\IncomingItems;
use App\Models\OutgoingItems;

class DashboardController extends ResourceController
{
    use ResponseTrait;

    protected $productModel;
    protected $incomingModel;
    protected $outgoingModel;

    public function __construct()
    {
        $this->productModel = new Products();
        $this->incomingModel = new IncomingItems();
        $this->outgoingModel = new OutgoingItems();
    }

    // Get dashboard summary
    public function summary()
    {
        try {
            $totalProducts = $this->productModel->countAll();
            $totalIncoming = $this->incomingModel->selectSum('quantity')->first();
            $totalOutgoing = $this->outgoingModel->selectSum('quantity')->first();

            $lowStockProducts = $this->productModel->where('stock <', 10)->countAllResults();

            $data = [
                'total_products' => $totalProducts,
                'total_incoming' => (int)($totalIncoming['quantity'] ?? 0),
                'total_outgoing' => (int)($totalOutgoing['quantity'] ?? 0),
                'low_stock_products' => $lowStockProducts,
                'monthly_incoming' => $this->incomingModel->getMonthlyIncoming(),
                'monthly_outgoing' => $this->outgoingModel->getMonthlyOutgoing()
            ];

            return $this->respond([
                'status' => 'success',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }
}