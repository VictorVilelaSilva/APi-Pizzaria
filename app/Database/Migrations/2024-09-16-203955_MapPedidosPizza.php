<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MapPedidosPizza extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_pedido' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'id_pizza' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'quantidade' => [
                'type' => 'INT',
                'constraint' => 5,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_pedido', 'pedidos', 'id');
        $this->forge->addForeignKey('id_pizza', 'pizzas', 'id');
        $this->forge->createTable('map_pedidos_pizza');
    }

    public function down()
    {
        $this->forge->dropTable('map_pedidos_pizza');
    }
}
