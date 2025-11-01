<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPriceToMapPedidosPizza extends Migration
{
    public function up()
    {
        // Adicionar preço unitário para histórico (caso o preço da pizza mude no futuro)
        $fields = [
            'preco_unitario' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
                'comment' => 'Preço da pizza no momento da compra',
            ],
            'observacoes' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Observações do item (ex: sem cebola, borda recheada)',
            ],
        ];

        $this->forge->addColumn('map_pedidos_pizza', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('map_pedidos_pizza', [
            'preco_unitario',
            'observacoes',
        ]);
    }
}
