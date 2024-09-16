<?php

namespace App\Models;

use App\Entities\PersonEntity;
use CodeIgniter\Model;

class AddressModel extends Model
{
    protected $table         = 'address';
    protected $allowedFields =
    [
        'id',
        'id_person',
        'street',
        'number',
        'neighborhood',
        'city',
        'state',
        'zip_code',
        'country',
        'primary',
        'created_at',
        'updated_at',

    ];

    protected $returnType    = 'array';
    protected $useTimestamps = true;
}
