<?php

use Config\Services;
use Modules\Pedido\Controllers\PedidoController;

$routes = Services::routes();

$routes->group(
    'pedido',
    [
        'namespace' => 'Modules\Pedido\Controllers',
        'filter' => 'jwtauth' // Todas as rotas de pedido requerem autenticação JWT
    ],
    function ($routes) {
        // Criar pedido
        $routes->post('criar', [PedidoController::class, 'criar']);

        // Listar meus pedidos
        $routes->get('meus-pedidos', [PedidoController::class, 'meusPedidos']);

        // Ver detalhes de um pedido
        $routes->get('(:num)/detalhes', [PedidoController::class, 'detalhes']);

        // Cancelar pedido
        $routes->put('(:num)/cancelar', [PedidoController::class, 'cancelar']);

        // Atualizar status (Admin)
        $routes->patch('(:num)/status', [PedidoController::class, 'atualizarStatus']);

        // Estatísticas
        $routes->get('estatisticas', [PedidoController::class, 'estatisticas']);
    }
);
