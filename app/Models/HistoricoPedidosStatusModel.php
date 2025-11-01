<?php

namespace App\Models;

use CodeIgniter\Model;

class HistoricoPedidosStatusModel extends Model
{
    protected $table         = 'historico_pedidos_status';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'id_pedido',
        'status_anterior',
        'status_novo',
        'observacao',
        'alterado_por_usuario_id',
    ];

    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $createdField  = 'created_at';
    protected $updatedField  = false;
    protected $deletedField  = false;

    /**
     * Buscar histórico de um pedido
     */
    public function getHistoricoPedido(int $idPedido): array
    {
        return $this->select('
                historico_pedidos_status.*,
                usuarios.nome as alterado_por_nome,
                usuarios.email as alterado_por_email
            ')
            ->join('usuarios', 'usuarios.id = historico_pedidos_status.alterado_por_usuario_id', 'left')
            ->where('historico_pedidos_status.id_pedido', $idPedido)
            ->orderBy('historico_pedidos_status.created_at', 'ASC')
            ->findAll();
    }

    /**
     * Buscar último status do pedido
     */
    public function getUltimoStatus(int $idPedido): ?array
    {
        return $this->where('id_pedido', $idPedido)
            ->orderBy('created_at', 'DESC')
            ->first();
    }
}
