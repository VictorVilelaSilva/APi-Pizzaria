<?php

namespace App\Models;

use CodeIgniter\Model;

class PedidosModel extends Model
{
    protected $table         = 'pedidos';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'id_usuario',
        'id_endereco',
        'valor_total',
        'status',
        'observacoes',
        'tempo_preparo_estimado',
        'data_confirmacao',
        'data_inicio_preparo',
        'data_saiu_entrega',
        'data_entregue',
        'data_cancelamento',
        'motivo_cancelamento',
        'forma_pagamento',
        'troco_para',
    ];

    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;

    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

    /**
     * Buscar pedido por ID com relacionamentos
     */
    public function findPedidoCompleto(int $id): ?array
    {
        return $this->select('
                pedidos.*,
                usuarios.nome as usuario_nome,
                usuarios.email as usuario_email,
                enderecos.logradouro,
                enderecos.numero,
                enderecos.bairro,
                enderecos.cidade,
                enderecos.estado,
                enderecos.cep
            ')
            ->join('usuarios', 'usuarios.id = pedidos.id_usuario', 'left')
            ->join('enderecos', 'enderecos.id = pedidos.id_endereco', 'left')
            ->where('pedidos.id', $id)
            ->first();
    }

    /**
     * Listar pedidos do usuário com filtros e paginação
     */
    public function listPedidosByUsuario(int $idUsuario, array $filters = [], int $page = 1, int $perPage = 10): array
    {
        $builder = $this->select('
                pedidos.id,
                pedidos.valor_total,
                pedidos.status,
                pedidos.forma_pagamento,
                pedidos.created_at,
                pedidos.updated_at,
                enderecos.logradouro,
                enderecos.numero,
                enderecos.bairro,
                enderecos.cidade
            ')
            ->join('enderecos', 'enderecos.id = pedidos.id_endereco', 'left')
            ->where('pedidos.id_usuario', $idUsuario);

        // Filtro por status
        if (!empty($filters['status'])) {
            $builder->where('pedidos.status', $filters['status']);
        }

        // Filtro por data inicial
        if (!empty($filters['data_inicio'])) {
            $builder->where('pedidos.created_at >=', $filters['data_inicio']);
        }

        // Filtro por data final
        if (!empty($filters['data_fim'])) {
            $builder->where('pedidos.created_at <=', $filters['data_fim']);
        }

        // Ordenação
        $orderBy = $filters['order_by'] ?? 'pedidos.created_at';
        $orderDir = $filters['order_dir'] ?? 'DESC';
        $builder->orderBy($orderBy, $orderDir);

        // Paginação
        $offset = ($page - 1) * $perPage;
        $results = $builder->limit($perPage, $offset)->findAll();

        // Total de registros
        $builderCount = $this->where('id_usuario', $idUsuario);
        if (!empty($filters['status'])) {
            $builderCount->where('status', $filters['status']);
        }
        if (!empty($filters['data_inicio'])) {
            $builderCount->where('created_at >=', $filters['data_inicio']);
        }
        if (!empty($filters['data_fim'])) {
            $builderCount->where('created_at <=', $filters['data_fim']);
        }
        $total = $builderCount->countAllResults();

        return [
            'data' => $results,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => ceil($total / $perPage),
            ],
        ];
    }

    /**
     * Buscar estatísticas do usuário
     */
    public function getEstatisticasUsuario(int $idUsuario): array
    {
        $db = \Config\Database::connect();

        // Total de pedidos
        $totalPedidos = $this->where('id_usuario', $idUsuario)->countAllResults();

        // Total gasto
        $result = $this->selectSum('valor_total', 'total_gasto')
            ->where('id_usuario', $idUsuario)
            ->where('status !=', 'Cancelado')
            ->first();
        $totalGasto = $result['total_gasto'] ?? 0;

        // Pedidos por status
        $pedidosPorStatus = $this->select('status, COUNT(*) as total')
            ->where('id_usuario', $idUsuario)
            ->groupBy('status')
            ->findAll();

        // Pizza mais pedida
        $pizzaMaisPedida = $db->query("
            SELECT 
                p.nome, 
                p.img_url,
                COUNT(mpp.id_pizza) as quantidade
            FROM map_pedidos_pizza mpp
            INNER JOIN pizzas p ON p.id = mpp.id_pizza
            INNER JOIN pedidos ped ON ped.id = mpp.id_pedido
            WHERE ped.id_usuario = ?
            AND ped.status != 'Cancelado'
            GROUP BY mpp.id_pizza, p.nome, p.img_url
            ORDER BY quantidade DESC
            LIMIT 1
        ", [$idUsuario])->getRowArray();

        return [
            'total_pedidos' => $totalPedidos,
            'total_gasto' => number_format($totalGasto, 2, '.', ''),
            'pedidos_por_status' => $pedidosPorStatus,
            'pizza_favorita' => $pizzaMaisPedida,
        ];
    }

    /**
     * Validar se pedido pode ser cancelado (apenas pedidos com status Pendente ou Confirmado)
     */
    public function podeCancelar(int $id): bool
    {
        $pedido = $this->find($id);
        if (!$pedido) {
            return false;
        }

        return in_array($pedido['status'], ['Pendente', 'Confirmado']);
    }

    /**
     * Atualizar status do pedido
     */
    public function atualizarStatus(int $id, string $novoStatus, ?int $usuarioId = null, ?string $observacao = null): bool
    {
        $pedido = $this->find($id);
        if (!$pedido) {
            return false;
        }

        $statusAnterior = $pedido['status'];

        // Atualizar data específica baseada no status
        $dataField = null;
        switch ($novoStatus) {
            case 'Confirmado':
                $dataField = 'data_confirmacao';
                break;
            case 'Preparando':
                $dataField = 'data_inicio_preparo';
                break;
            case 'Saiu para entrega':
                $dataField = 'data_saiu_entrega';
                break;
            case 'Entregue':
                $dataField = 'data_entregue';
                break;
            case 'Cancelado':
                $dataField = 'data_cancelamento';
                break;
        }

        $updateData = ['status' => $novoStatus];
        if ($dataField) {
            $updateData[$dataField] = date('Y-m-d H:i:s');
        }

        // Atualizar pedido
        $this->update($id, $updateData);

        // Registrar no histórico
        $historicoModel = new HistoricoPedidosStatusModel();
        $historicoModel->insert([
            'id_pedido' => $id,
            'status_anterior' => $statusAnterior,
            'status_novo' => $novoStatus,
            'observacao' => $observacao,
            'alterado_por_usuario_id' => $usuarioId,
        ]);

        return true;
    }
}
