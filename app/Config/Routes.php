<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
//  api
require_once APPPATH . 'Config/Routes/Api.php';
//  web
$routes->get('/', 'Home::index');
// Categories
$routes->get('data-categories', 'Categories::index');
// Products
$routes->get('data-product', 'Products::index');
$routes->get('data-incomingitems', 'Products::incomingItems');
$routes->get('data-outgoingitems', 'Products::outgoingItems');
// Myth:Auth routes
$routes->group('', ['namespace' => 'Myth\Auth\Controllers'], function($routes){
    $routes->get('login', 'AuthController::login', ['as' => 'login']);
    $routes->post('login', 'AuthController::attemptLogin');
    $routes->get('logout', 'AuthController::logout', ['as' => 'logout']);

    $routes->get('register', 'AuthController::register', ['as' => 'register']);
    $routes->post('register', 'AuthController::attemptRegister');

    $routes->get('forgot', 'AuthController::forgotPassword', ['as' => 'forgot']);
    $routes->post('forgot', 'AuthController::attemptForgot');
    $routes->get('reset-password', 'AuthController::resetPassword', ['as' => 'reset-password']);
    $routes->post('reset-password', 'AuthController::attemptReset');
});