<?php

namespace App\Models;

use App\Entities\PersonEntity;
use CodeIgniter\Model;

class ContactsModel extends Model
{
    protected $table         = 'contacts';
    protected $allowedFields =
    [
        'id',
        'id_person',
        'contact',
        'primary',
        'created_at',
        'updated_at'
    ];

    protected $returnType    = 'array';
    protected $useTimestamps = true;
}
