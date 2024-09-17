<?php

namespace App\Models;

use App\Entities\PersonEntity;
use CodeIgniter\Model;

class enderecosModel extends Model
{
    protected $table         = 'enderecos';
    protected $allowedFields =
    [
        'id_usuario',
        'logradouro',
        'numero',
        'bairro',
        'cidade',
        'estado',
        'cep',
        'created_at',
    ];

    protected $returnType    = 'array';
    protected $useTimestamps = true;

    public function findDataByUserId(string $id): ?array
    {
        return $this->where('id', $id)->first();
    }
}
