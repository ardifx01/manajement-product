<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Optional: API versioning
$routes->group('api/v1', ['namespace' => 'App\Controllers\Api'], function($routes) {
    $routes->resource('categories', ['controller' => 'CategoriesController']);
    $routes->resource('products', ['controller' => 'ProductsController']);
    $routes->resource('incoming-items', ['controller' => 'IncomingItemsController']);
    $routes->resource('outgoing-items', ['controller' => 'OutgoingItemsController']);

    // costum pdf
    $routes->get('incoming-items/pdf', 'IncomingItemsController::generatePdf');
    $routes->get('outgoing-items/pdf', 'OutgoingItemsController::generatePdf');
    $routes->get('dashboard/summary','DashboardController::summary');
});