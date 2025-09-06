<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Optional: API versioning
$routes->group('api/v1', ['namespace' => 'App\Controllers\Api'], function ($routes) {
    $routes->resource('categories', ['controller' => 'CategoriesController']);
    $routes->resource('products', ['controller' => 'ProductsController']);
    $routes->resource('incoming-items', ['controller' => 'IncomingItemsController']);
    $routes->resource('outgoing-items', ['controller' => 'OutgoingItemsController']);

    // dashbort
    $routes->get('dashboard/summary', 'DashboardController::summary');
    $routes->get('dashboard/analytics', 'DashboardController::analytics');
    $routes->get('dashboard/stock-overview', 'DashboardController::stockOverview');
});

$routes->get('reports/incoming-items/pdf', 'ReportController::incomingItemsPdf');
$routes->get('reports/outgoing-items/pdf', 'ReportController::outgoingItemsPdf');
$routes->get('reports/stock/pdf', 'ReportController::stockReportPdf');
