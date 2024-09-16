<?php

namespace App;

use Config\Services;
use Controllers\AuthController;

$routes = Services::routes();
$routes->group(
    'auth',
    ['namespace' => 'App\Controllers'],
    function ($routes) {
        $routes->post('login', 'AuthController::Login');
        $routes->post('person', 'PersonController::CreatePerson');
    }
);
