<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CmswUsers extends Migration
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
            'login' => [
                'type' => 'VARCHAR',
                'constraint' => 60,
            ],
            'pass' => [
                'type' => 'VARCHAR',
                'constraint' => 60,
            ],
            'cmsw_url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'cmsw_cert_path' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'cmsw_cert_pass' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',

            ],
            'ambiente' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('cmsw_users');
    }

    public function down()
    {
        $this->forge->dropTable('cmsw_users');
    }
}