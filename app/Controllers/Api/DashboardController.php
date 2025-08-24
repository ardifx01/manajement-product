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

            $lowStockProducts = $this->productModel->where('stock_quantity <', 10)->countAllResults();

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

    // Get all products
    public function products()
    {
        try {
            $products = $this->productModel->getProductsWithStock();
            return $this->respond([
                'status' => 'success',
                'data' => $products
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    // Get incoming items
    public function incomingItems()
    {
        try {
            $incomingItems = $this->incomingModel->getIncomingItemsWithProduct();
            return $this->respond([
                'status' => 'success',
                'data' => $incomingItems
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    // Get outgoing items
    public function outgoingItems()
    {
        try {
            $outgoingItems = $this->outgoingModel->getOutgoingItemsWithProduct();
            return $this->respond([
                'status' => 'success',
                'data' => $outgoingItems
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    // Add new incoming item
    public function addIncoming()
    {
        try {
            $data = $this->request->getJSON(true);

            $validation = \Config\Services::validation();
            $validation->setRules([
                'product_id' => 'required|integer',
                'quantity' => 'required|integer|greater_than[0]',
                'date_received' => 'required|valid_date',
                'supplier' => 'permit_empty|max_length[255]',
                'notes' => 'permit_empty'
            ]);

            if (!$validation->run($data)) {
                return $this->failValidationErrors($validation->getErrors());
            }

            // Add incoming item
            $incomingId = $this->incomingModel->insert($data);

            // Update product stock
            $this->productModel->updateStock($data['product_id'], $data['quantity']);

            return $this->respondCreated([
                'status' => 'success',
                'message' => 'Incoming item added successfully',
                'data' => ['id' => $incomingId]
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    // Add new outgoing item
    public function addOutgoing()
    {
        try {
            $data = $this->request->getJSON(true);

            $validation = \Config\Services::validation();
            $validation->setRules([
                'product_id' => 'required|integer',
                'quantity' => 'required|integer|greater_than[0]',
                'date_shipped' => 'required|valid_date',
                'customer' => 'permit_empty|max_length[255]',
                'notes' => 'permit_empty'
            ]);

            if (!$validation->run($data)) {
                return $this->failValidationErrors($validation->getErrors());
            }

            // Check stock availability
            $product = $this->productModel->find($data['product_id']);
            if (!$product || $product['stock_quantity'] < $data['quantity']) {
                return $this->fail('Insufficient stock', 400);
            }

            // Add outgoing item
            $outgoingId = $this->outgoingModel->insert($data);

            // Update product stock (subtract)
            $this->productModel->updateStock($data['product_id'], -$data['quantity']);

            return $this->respondCreated([
                'status' => 'success',
                'message' => 'Outgoing item added successfully',
                'data' => ['id' => $outgoingId]
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }
}