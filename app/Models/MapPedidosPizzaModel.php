<?php

namespace App\Models;

use CodeIgniter\Model;

class MapPedidosPizzaModel extends Model
{
    protected $table         = 'map_pedidos_pizza';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'id_pedido',
        'id_pizza',
        'quantidade',
        'preco_unitario',
        'observacoes',
    ];

    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;

    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Buscar itens de um pedido com detalhes das pizzas
     */
    public function getItensByPedido(int $idPedido): array
    {
        return $this->select('
                map_pedidos_pizza.*,
                pizzas.nome as pizza_nome,
                pizzas.ingredientes as pizza_ingredientes,
                pizzas.img_url as pizza_img_url
            ')
            ->join('pizzas', 'pizzas.id = map_pedidos_pizza.id_pizza', 'left')
            ->where('map_pedidos_pizza.id_pedido', $idPedido)
            ->findAll();
    }

    /**
     * Adicionar múltiplos itens ao pedido
     */
    public function addItensPedido(int $idPedido, array $itens): bool
    {
        $db = \Config\Database::connect();
        $db->transStart();

        foreach ($itens as $item) {
            $this->insert([
                'id_pedido' => $idPedido,
                'id_pizza' => $item['id_pizza'],
                'quantidade' => $item['quantidade'],
                'preco_unitario' => $item['preco_unitario'],
                'observacoes' => $item['observacoes'] ?? null,
            ]);
        }

        $db->transComplete();

        return $db->transStatus();
    }

    /**
     * Calcular valor total dos itens de um pedido
     */
    public function calcularValorTotal(int $idPedido): float
    {
        $result = $this->selectSum('(quantidade * preco_unitario)', 'total')
            ->where('id_pedido', $idPedido)
            ->first();

        return (float) ($result['total'] ?? 0);
    }
}
