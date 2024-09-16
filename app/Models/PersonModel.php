<?php

namespace App\Models;

use App\Entities\PersonEntity;
use CodeIgniter\Model;

class PersonModel extends Model
{
    protected $table         = 'person';
    protected $allowedFields =
    [
        'id',
        'name'
    ];

    protected $returnType    = 'array';
    protected $useTimestamps = true;

    public function findByUuid(string $uuid): ?PersonEntity
    {
        return $this->where('uuid', $uuid)->first();
    }
}
