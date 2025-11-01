<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateHistoricoPedidosStatus extends Migration
{
    public function up()
    {
        // Tabela para rastrear todas as mudanças de status
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_pedido' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'status_anterior' => [
                'type' => 'ENUM',
                'constraint' => ['Pendente', 'Confirmado', 'Preparando', 'Saiu para entrega', 'Entregue', 'Cancelado'],
                'null' => true,
            ],
            'status_novo' => [
                'type' => 'ENUM',
                'constraint' => ['Pendente', 'Confirmado', 'Preparando', 'Saiu para entrega', 'Entregue', 'Cancelado'],
                'null' => false,
            ],
            'observacao' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'alterado_por_usuario_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_pedido', 'pedidos', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('alterado_por_usuario_id', 'usuarios', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('historico_pedidos_status');
    }

    public function down()
    {
        $this->forge->dropTable('historico_pedidos_status');
    }
}
