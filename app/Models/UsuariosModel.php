<?php

namespace App\Models;

use App\Entities\PersonEntity;
use CodeIgniter\Model;

class UsuariosModel extends Model
{
    protected $table         = 'usuarios';
    protected $allowedFields =
    [
        'nome',
        'email',
        'senha',
        'created_at',
    ];

    protected $returnType    = 'array';
    protected $useTimestamps = true;

    public function findById(string $id): ?array
    {
        return $this->where('id', $id)->first();
    }

    public function findByEmail(string $email): ?array
    {
        return $this->where('email', $email)->first();
    }
}
