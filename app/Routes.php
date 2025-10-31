<?php

namespace App;

use Config\Services;
use Controllers\AuthController;

$routes = Services::routes();
$routes->group(
    'usuario',
    ['namespace' => 'App\Controllers'],
    function ($routes) {
        $routes->post('login', 'UsuarioController::Login');
        $routes->post('register', 'UsuarioController::Register');
    },
);

$routes->group(
    'pizza',
    ['namespace' => 'App\Controllers'],
    function ($routes) {
        $routes->get('', 'PizzaController::GetAllPizzas');
        $routes->put('(:segment)', 'PizzaController::EditPizza/$1');
    },
);
