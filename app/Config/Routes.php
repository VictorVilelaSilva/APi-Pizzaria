<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->set404Override('App\Controllers\ErrorsController::show404');

require APPPATH . 'Routes.php';
    

