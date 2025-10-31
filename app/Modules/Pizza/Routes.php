<?php

use App\Controllers\PizzaController;
use Config\Services;

$routes = Services::routes();

$routes->group(
    'pizza',
    ['namespace' => 'Modules\Pizza\Controllers'],
    function ($routes) {
        $routes->get('', [PizzaController::class, 'GetAllPizzas']);
        $routes->put('(:segment)', [PizzaController::class, 'EditPizza/$1']);
    }
);
