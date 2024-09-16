<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UsersRelationship extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'relationship_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'api_user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'cmsw_user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
        ]);
        $this->forge->addPrimaryKey('relationship_id');
        $this->forge->addForeignKey('api_user_id', 'api_users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('cmsw_user_id', 'cmsw_users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('users_relationship');
    }

    public function down()
    {
        $this->forge->dropTable('users_relationship');
    }
}