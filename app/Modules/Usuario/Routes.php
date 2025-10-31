<?php

use App\Controllers\UsuarioController;
use Config\Services;

$routes = Services::routes();

$routes->group(
    'usuario',
    ['namespace' => 'Modules\Usuario\Controllers'],
    function ($routes) {
        $routes->post('login', [UsuarioController::class, 'Login']);
        $routes->post('register', [UsuarioController::class, 'Register']);
    }
);
