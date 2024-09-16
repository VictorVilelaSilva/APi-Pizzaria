<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ApiUsers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'uuid' => [
                'type' => 'VARCHAR',
                'constraint' => 42,
                'null' => false,
            ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('username');
        $this->forge->createTable('api_users');
    }

    public function down()
    {
        $this->forge->dropTable('api_users');
    }
}