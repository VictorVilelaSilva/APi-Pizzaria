<?php

use Config\Services;
use Modules\Usuario\Controllers\UsuarioController;

$routes = Services::routes();

$routes->group(
    'usuario',
    ['namespace' => 'Modules\Usuario\Controllers'],
    function ($routes) {
        // Rotas públicas (sem autenticação)
        $routes->post('login', [UsuarioController::class, 'Login']);
        $routes->post('register', [UsuarioController::class, 'Register']);
    }
);
