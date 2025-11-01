<?php

namespace App\Services\Pedido;

use App\Models\PedidosModel;
use App\Models\MapPedidosPizzaModel;
use App\Models\HistoricoPedidosStatusModel;
use App\Models\PizzasModel;
use App\Models\enderecosModel;
use App\DTO\Pedido\PedidoOutputDTO;
use App\DTO\Pedido\ItemPedidoOutputDTO;
use Config\ErrorTrait;

class PedidoService
{
    private PedidosModel $pedidosModel;
    private MapPedidosPizzaModel $mapPedidosPizzaModel;
    private HistoricoPedidosStatusModel $historicoModel;
    private PizzasModel $pizzasModel;
    private enderecosModel $enderecosModel;

    public function __construct()
    {
        $this->pedidosModel = new PedidosModel();
        $this->mapPedidosPizzaModel = new MapPedidosPizzaModel();
        $this->historicoModel = new HistoricoPedidosStatusModel();
        $this->pizzasModel = new PizzasModel();
        $this->enderecosModel = new enderecosModel();
    }

    /**
     * Criar novo pedido com transação
     */
    public function criarPedido(array $data): array
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Validar se o endereço pertence ao usuário
            $endereco = $this->enderecosModel->find($data['id_endereco']);
            if (!$endereco || $endereco['id_usuario'] != $data['id_usuario']) {
                throw new ErrorTrait('EPEDIDO001', errors: ['endereco' => 'Endereço não pertence ao usuário']);
            }

            // Validar itens e calcular valor total
            $itensValidados = [];
            $valorTotal = 0;

            foreach ($data['itens'] as $item) {
                $pizza = $this->pizzasModel->find($item['id_pizza']);
                if (!$pizza) {
                    throw new ErrorTrait('EPEDIDO002', errors: ['pizza' => "Pizza ID {$item['id_pizza']} não encontrada"]);
                }

                $quantidade = (int) $item['quantidade'];
                $precoUnitario = (float) $pizza['preco'];
                $subtotal = $quantidade * $precoUnitario;
                $valorTotal += $subtotal;

                $itensValidados[] = [
                    'id_pizza' => $pizza['id'],
                    'quantidade' => $quantidade,
                    'preco_unitario' => $precoUnitario,
                    'observacoes' => $item['observacoes'] ?? null,
                ];
            }

            // Calcular tempo estimado de preparo (10 min por pizza + 5 min base)
            $totalPizzas = array_sum(array_column($itensValidados, 'quantidade'));
            $tempoEstimado = 5 + ($totalPizzas * 10);

            // Criar pedido
            $pedidoId = $this->pedidosModel->insert([
                'id_usuario' => $data['id_usuario'],
                'id_endereco' => $data['id_endereco'],
                'valor_total' => $valorTotal,
                'status' => 'Pendente',
                'observacoes' => $data['observacoes'] ?? null,
                'forma_pagamento' => $data['forma_pagamento'],
                'troco_para' => $data['troco_para'] ?? null,
                'tempo_preparo_estimado' => $tempoEstimado,
            ]);

            if (!$pedidoId) {
                throw new ErrorTrait('EPEDIDO003', errors: ['pedido' => 'Erro ao criar pedido']);
            }

            // Adicionar itens ao pedido
            $sucessoItens = $this->mapPedidosPizzaModel->addItensPedido($pedidoId, $itensValidados);
            if (!$sucessoItens) {
                throw new ErrorTrait('EPEDIDO004', errors: ['itens' => 'Erro ao adicionar itens ao pedido']);
            }

            // Registrar no histórico
            $this->historicoModel->insert([
                'id_pedido' => $pedidoId,
                'status_anterior' => null,
                'status_novo' => 'Pendente',
                'observacao' => 'Pedido criado',
                'alterado_por_usuario_id' => $data['id_usuario'],
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new ErrorTrait('EPEDIDO005', errors: ['transacao' => 'Erro na transação do pedido']);
            }

            // Buscar pedido completo
            return $this->buscarPedidoDetalhado($pedidoId);
        } catch (\Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }

    /**
     * Buscar pedido detalhado com itens
     */
    public function buscarPedidoDetalhado(int $idPedido, ?int $idUsuario = null): array
    {
        $pedido = $this->pedidosModel->findPedidoCompleto($idPedido);

        if (!$pedido) {
            throw new ErrorTrait('EPEDIDO006', errors: ['pedido' => 'Pedido não encontrado']);
        }

        // Validar se o pedido pertence ao usuário (se idUsuario for informado)
        if ($idUsuario !== null && $pedido['id_usuario'] != $idUsuario) {
            throw new ErrorTrait('EPEDIDO007', errors: ['permissao' => 'Você não tem permissão para visualizar este pedido']);
        }

        // Buscar itens do pedido
        $itens = $this->mapPedidosPizzaModel->getItensByPedido($idPedido);
        $itensFormatados = ItemPedidoOutputDTO::getAll($itens);

        // Buscar histórico
        $historico = $this->historicoModel->getHistoricoPedido($idPedido);

        $pedidoFormatado = PedidoOutputDTO::get($pedido);
        $pedidoFormatado['itens'] = $itensFormatados;
        $pedidoFormatado['historico'] = $historico;

        return $pedidoFormatado;
    }

    /**
     * Listar pedidos do usuário com filtros
     */
    public function listarPedidosUsuario(int $idUsuario, array $filters = []): array
    {
        $page = (int) ($filters['page'] ?? 1);
        $perPage = (int) ($filters['per_page'] ?? 10);

        // Limitar per_page a 50
        $perPage = min($perPage, 50);

        $resultado = $this->pedidosModel->listPedidosByUsuario($idUsuario, $filters, $page, $perPage);

        return [
            'pedidos' => PedidoOutputDTO::getAll($resultado['data']),
            'pagination' => $resultado['pagination'],
        ];
    }

    /**
     * Cancelar pedido
     */
    public function cancelarPedido(int $idPedido, int $idUsuario, string $motivo): array
    {
        $pedido = $this->pedidosModel->find($idPedido);

        if (!$pedido) {
            throw new ErrorTrait('EPEDIDO006', errors: ['pedido' => 'Pedido não encontrado']);
        }

        if ($pedido['id_usuario'] != $idUsuario) {
            throw new ErrorTrait('EPEDIDO007', errors: ['permissao' => 'Você não tem permissão para cancelar este pedido']);
        }

        if (!$this->pedidosModel->podeCancelar($idPedido)) {
            throw new ErrorTrait('EPEDIDO008', errors: ['status' => 'Este pedido não pode ser cancelado. Status atual: ' . $pedido['status']]);
        }

        $this->pedidosModel->update($idPedido, [
            'status' => 'Cancelado',
            'data_cancelamento' => date('Y-m-d H:i:s'),
            'motivo_cancelamento' => $motivo,
        ]);

        $this->historicoModel->insert([
            'id_pedido' => $idPedido,
            'status_anterior' => $pedido['status'],
            'status_novo' => 'Cancelado',
            'observacao' => 'Pedido cancelado pelo cliente: ' . $motivo,
            'alterado_por_usuario_id' => $idUsuario,
        ]);

        return $this->buscarPedidoDetalhado($idPedido);
    }

    /**
     * Atualizar status do pedido (Admin)
     */
    public function atualizarStatus(int $idPedido, string $novoStatus, ?int $usuarioId = null, ?string $observacao = null): array
    {
        $pedido = $this->pedidosModel->find($idPedido);

        if (!$pedido) {
            throw new ErrorTrait('EPEDIDO006', errors: ['pedido' => 'Pedido não encontrado']);
        }

        // Validar fluxo de status
        $fluxosValidos = [
            'Pendente' => ['Confirmado', 'Cancelado'],
            'Confirmado' => ['Preparando', 'Cancelado'],
            'Preparando' => ['Saiu para entrega'],
            'Saiu para entrega' => ['Entregue'],
            'Entregue' => [],
            'Cancelado' => [],
        ];

        $statusAtual = $pedido['status'];
        if (!in_array($novoStatus, $fluxosValidos[$statusAtual])) {
            throw new ErrorTrait('EPEDIDO009', errors: ['status' => "Não é possível alterar de '{$statusAtual}' para '{$novoStatus}'"]);
        }

        $sucesso = $this->pedidosModel->atualizarStatus($idPedido, $novoStatus, $usuarioId, $observacao);

        if (!$sucesso) {
            throw new ErrorTrait('EPEDIDO010', errors: ['status' => 'Erro ao atualizar status']);
        }

        return $this->buscarPedidoDetalhado($idPedido);
    }

    /**
     * Buscar estatísticas do usuário
     */
    public function buscarEstatisticas(int $idUsuario): array
    {
        return $this->pedidosModel->getEstatisticasUsuario($idUsuario);
    }
}
