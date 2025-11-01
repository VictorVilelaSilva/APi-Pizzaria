<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusFieldsToPedidos extends Migration
{
    public function up()
    {
        // Adicionar campos de rastreamento de status
        $fields = [
            'observacoes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'tempo_preparo_estimado' => [
                'type' => 'INT',
                'constraint' => 5,
                'null' => true,
                'comment' => 'Tempo em minutos',
            ],
            'data_confirmacao' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'data_inicio_preparo' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'data_saiu_entrega' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'data_entregue' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'data_cancelamento' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'motivo_cancelamento' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'forma_pagamento' => [
                'type' => 'ENUM',
                'constraint' => ['Dinheiro', 'Cartão Débito', 'Cartão Crédito', 'PIX', 'Vale Refeição'],
                'default' => 'Dinheiro',
            ],
            'troco_para' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
            ],
        ];

        $this->forge->addColumn('pedidos', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('pedidos', [
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
        ]);
    }
}
