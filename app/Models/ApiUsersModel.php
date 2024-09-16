<?php
namespace App\Models;

use CodeIgniter\Model;

class ApiUsersModel extends Model
{
    protected $table         = 'api_users';
    protected $allowedFields =
    [
        'uuid',
        'username',
        'password',
        'created_at',
        'updated_at'
    ];

    protected $returnType    = 'array';
    protected $useTimestamps = true;

    public function usernameExists(string $username): ?array
    {
        return $this->where('username', $username)->first();
    }
}
