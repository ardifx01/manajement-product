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
            $period = $this->request->getGet('period') ?? 'month';
            $limit = $this->request->getGet('limit') ?? 12;

            $totalProducts = $this->productModel->countAll();
            
            // Use custom methods from models
            $totalIncoming = $this->incomingModel->getTotalIncoming();
            $totalOutgoing = $this->outgoingModel->getTotalOutgoing();
            
            $lowStockProducts = $this->productModel->where('stock <', 10)->countAllResults();

            $data = [
                'total_products' => $totalProducts,
                'total_incoming' => $totalIncoming,
                'total_outgoing' => $totalOutgoing,
                'low_stock_products' => $lowStockProducts,
                'net_movement' => $totalIncoming - $totalOutgoing,
                
                // Data for charts
                'daily_data' => $this->getDailyData($limit),
                'monthly_data' => $this->getMonthlyData($limit),
                'yearly_data' => $this->getYearlyData($limit),
                
                // Data for percentage table
                'percentage_data' => $this->getPercentageData($totalIncoming, $totalOutgoing),
                
                // Top products
                'top_incoming_products' => $this->incomingModel->getTopProducts(5),
                'top_outgoing_products' => $this->outgoingModel->getTopProducts(5),
                
                // Current period data
                'current_period_data' => $this->getPeriodData($period)
            ];

            return $this->respond([
                'status' => 'success',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Dashboard Error: ' . $e->getMessage());
            return $this->failServerError('Internal server error: ' . $e->getMessage());
        }
    }

    /**
     * Get daily data for charts
     */
    private function getDailyData($limit = 30)
    {
        $endDate = date('Y-m-d');
        $startDate = date('Y-m-d', strtotime("-{$limit} days"));
        
        // Get incoming items data
        $incoming = $this->incomingModel
            ->select("DATE(created_at) as date, SUM(quantity) as total")
            ->where('created_at >=', $startDate)
            ->where('created_at <=', $endDate . ' 23:59:59')
            ->groupBy("DATE(created_at)")
            ->orderBy('date', 'ASC')
            ->findAll();
        
        // Get outgoing items data
        $outgoing = $this->outgoingModel
            ->select("DATE(created_at) as date, SUM(quantity) as total")
            ->where('created_at >=', $startDate)
            ->where('created_at <=', $endDate . ' 23:59:59')
            ->groupBy("DATE(created_at)")
            ->orderBy('date', 'ASC')
            ->findAll();

        // Create complete date range for consistent response
        $labels = [];
        $incomingData = [];
        $outgoingData = [];
        
        $currentDate = $startDate;
        
        for ($i = 0; $i < $limit; $i++) {
            $formattedDate = date('M j', strtotime($currentDate));
            $labels[] = $formattedDate;
            
            // Find matching data or set to 0
            $incomingTotal = 0;
            foreach ($incoming as $item) {
                if ($item['date'] == $currentDate) {
                    $incomingTotal = (int)$item['total'];
                    break;
                }
            }
            $incomingData[] = $incomingTotal;
            
            $outgoingTotal = 0;
            foreach ($outgoing as $item) {
                if ($item['date'] == $currentDate) {
                    $outgoingTotal = (int)$item['total'];
                    break;
                }
            }
            $outgoingData[] = $outgoingTotal;
            
            $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
        }

        return [
            'labels' => $labels,
            'incoming' => $incomingData,
            'outgoing' => $outgoingData
        ];
    }

    /**
     * Get monthly data for charts
     */
    private function getMonthlyData($limit = 12)
    {
        $incoming = $this->incomingModel
            ->select("DATE_FORMAT(created_at, '%Y-%m') as period, SUM(quantity) as total")
            ->where('created_at >=', date('Y-m-01', strtotime("-{$limit} months")))
            ->groupBy("DATE_FORMAT(created_at, '%Y-%m')")
            ->orderBy('period', 'ASC')
            ->findAll();

        $outgoing = $this->outgoingModel
            ->select("DATE_FORMAT(created_at, '%Y-%m') as period, SUM(quantity) as total")
            ->where('created_at >=', date('Y-m-01', strtotime("-{$limit} months")))
            ->groupBy("DATE_FORMAT(created_at, '%Y-%m')")
            ->orderBy('period', 'ASC')
            ->findAll();

        return [
            'labels' => array_column($incoming, 'period'),
            'incoming' => array_column($incoming, 'total'),
            'outgoing' => array_column($outgoing, 'total')
        ];
    }

    /**
     * Get yearly data for charts
     */
    private function getYearlyData($limit = 5)
    {
        $incoming = $this->incomingModel
            ->select("YEAR(created_at) as year, SUM(quantity) as total")
            ->where('created_at >=', date('Y-01-01', strtotime("-{$limit} years")))
            ->groupBy("YEAR(created_at)")
            ->orderBy('year', 'ASC')
            ->findAll();

        $outgoing = $this->outgoingModel
            ->select("YEAR(created_at) as year, SUM(quantity) as total")
            ->where('created_at >=', date('Y-01-01', strtotime("-{$limit} years")))
            ->groupBy("YEAR(created_at)")
            ->orderBy('year', 'ASC')
            ->findAll();

        return [
            'labels' => array_column($incoming, 'year'),
            'incoming' => array_column($incoming, 'total'),
            'outgoing' => array_column($outgoing, 'total')
        ];
    }

    /**
     * Get percentage data for tables
     */
    private function getPercentageData($totalIncoming, $totalOutgoing)
    {
        $totalMovement = $totalIncoming + $totalOutgoing;

        // Calculate percentages
        $incomingPercentage = $totalMovement > 0 ? ($totalIncoming / $totalMovement) * 100 : 0;
        $outgoingPercentage = $totalMovement > 0 ? ($totalOutgoing / $totalMovement) * 100 : 0;

        // Monthly data for trend analysis
        $monthlyTrend = $this->getMonthlyTrend();

        return [
            'total_movement' => $totalMovement,
            'incoming' => [
                'total' => $totalIncoming,
                'percentage' => round($incomingPercentage, 2),
                'trend' => $monthlyTrend['incoming_trend']
            ],
            'outgoing' => [
                'total' => $totalOutgoing,
                'percentage' => round($outgoingPercentage, 2),
                'trend' => $monthlyTrend['outgoing_trend']
            ],
            'net' => [
                'total' => $totalIncoming - $totalOutgoing,
                'trend' => $monthlyTrend['net_trend']
            ]
        ];
    }

    /**
     * Get monthly trend for percentage change
     */
    private function getMonthlyTrend()
    {
        // Get current month data
        $currentMonthStart = date('Y-m-01');
        $currentMonthEnd = date('Y-m-t');
        
        $currentIncoming = $this->incomingModel
            ->selectSum('quantity')
            ->where('created_at >=', $currentMonthStart)
            ->where('created_at <=', $currentMonthEnd . ' 23:59:59')
            ->get()
            ->getRowArray();
            
        $currentOutgoing = $this->outgoingModel
            ->selectSum('quantity')
            ->where('created_at >=', $currentMonthStart)
            ->where('created_at <=', $currentMonthEnd . ' 23:59:59')
            ->get()
            ->getRowArray();
            
        // Get previous month data
        $prevMonthStart = date('Y-m-01', strtotime('-1 month'));
        $prevMonthEnd = date('Y-m-t', strtotime('-1 month'));
        
        $prevIncoming = $this->incomingModel
            ->selectSum('quantity')
            ->where('created_at >=', $prevMonthStart)
            ->where('created_at <=', $prevMonthEnd . ' 23:59:59')
            ->get()
            ->getRowArray();
            
        $prevOutgoing = $this->outgoingModel
            ->selectSum('quantity')
            ->where('created_at >=', $prevMonthStart)
            ->where('created_at <=', $prevMonthEnd . ' 23:59:59')
            ->get()
            ->getRowArray();

        // Calculate trends
        $incomingCurrent = $currentIncoming['quantity'] ?? 0;
        $incomingPrevious = $prevIncoming['quantity'] ?? 0;
        $outgoingCurrent = $currentOutgoing['quantity'] ?? 0;
        $outgoingPrevious = $prevOutgoing['quantity'] ?? 0;
        
        $incomingChange = $incomingPrevious > 0 ? 
            (($incomingCurrent - $incomingPrevious) / $incomingPrevious) * 100 : 
            ($incomingCurrent > 0 ? 100 : 0);
        
        $outgoingChange = $outgoingPrevious > 0 ? 
            (($outgoingCurrent - $outgoingPrevious) / $outgoingPrevious) * 100 : 
            ($outgoingCurrent > 0 ? 100 : 0);
        
        $netCurrent = $incomingCurrent - $outgoingCurrent;
        $netPrevious = $incomingPrevious - $outgoingPrevious;
        
        $netChange = $netPrevious != 0 ? (($netCurrent - $netPrevious) / abs($netPrevious)) * 100 : 
            ($netCurrent != 0 ? ($netCurrent > 0 ? 100 : -100) : 0);

        return [
            'incoming_trend' => round($incomingChange, 2),
            'outgoing_trend' => round($outgoingChange, 2),
            'net_trend' => round($netChange, 2)
        ];
    }

    /**
     * Get data for specific period
     */
    private function getPeriodData($period = 'month')
    {
        switch ($period) {
            case 'day':
                return $this->getDailyData(30);
            
            case 'year':
                return $this->getYearlyData(5);
            
            case 'month':
            default:
                return $this->getMonthlyData(12);
        }
    }

    /**
     * Get detailed analytics data
     */
    public function analytics()
    {
        try {
            $type = $this->request->getGet('type') ?? 'monthly';
            $limit = $this->request->getGet('limit') ?? 12;

            switch ($type) {
                case 'daily':
                    $data = $this->getDailyData($limit);
                    break;
                
                case 'yearly':
                    $data = $this->getYearlyData($limit);
                    break;
                
                case 'monthly':
                default:
                    $data = $this->getMonthlyData($limit);
                    break;
            }

            return $this->respond([
                'status' => 'success',
                'data' => $data,
                'type' => $type,
                'limit' => $limit
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Analytics Error: ' . $e->getMessage());
            return $this->failServerError('Internal server error');
        }
    }

    /**
     * Get stock overview data
     */
    public function stockOverview()
    {
        try {
            $stockLevels = [
                'critical' => $this->productModel->where('stock <', 5)->countAllResults(),
                'low' => $this->productModel->where('stock >=', 5)->where('stock <', 10)->countAllResults(),
                'medium' => $this->productModel->where('stock >=', 10)->where('stock <', 50)->countAllResults(),
                'high' => $this->productModel->where('stock >=', 50)->countAllResults()
            ];

            $totalProducts = array_sum($stockLevels);

            return $this->respond([
                'status' => 'success',
                'data' => [
                    'stock_levels' => $stockLevels,
                    'percentages' => [
                        'critical' => $totalProducts > 0 ? round(($stockLevels['critical'] / $totalProducts) * 100, 2) : 0,
                        'low' => $totalProducts > 0 ? round(($stockLevels['low'] / $totalProducts) * 100, 2) : 0,
                        'medium' => $totalProducts > 0 ? round(($stockLevels['medium'] / $totalProducts) * 100, 2) : 0,
                        'high' => $totalProducts > 0 ? round(($stockLevels['high'] / $totalProducts) * 100, 2) : 0
                    ],
                    'total_products' => $totalProducts
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Stock Overview Error: ' . $e->getMessage());
            return $this->failServerError('Internal server error');
        }
    }
}