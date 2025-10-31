<?php

use Config\Services;
use Modules\Pizza\Controllers\PizzaController;

$routes = Services::routes();

$routes->group(
    'pizza',
    [
        'namespace' => 'Modules\Pizza\Controllers',
        'filter' => 'jwtauth' // Todas as rotas de pizza requerem autenticação JWT
    ],
    function ($routes) {
        $routes->get('', [PizzaController::class, 'GetAllPizzas']);
        $routes->put('(:segment)', [PizzaController::class, 'EditPizza']);
    }
);
