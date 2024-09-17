<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Enderecos extends Migration
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
            'id_usuario' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'logradouro' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'numero' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
            ],
            'bairro' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'cidade' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'estado' => [
                'type' => 'VARCHAR',
                'constraint' => '2',
            ],
            'cep' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
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
        $this->forge->addForeignKey('id_usuario', 'usuarios', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('enderecos');
    }

    public function down()
    {
        $this->forge->dropTable('enderecos');
    }
}
