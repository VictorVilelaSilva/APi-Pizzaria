<?php

namespace App\Models;
use CodeIgniter\Model;

class MessageModel extends Model
{
  protected $DBGroup          = 'message';
  protected $table            = 'inf_message';
  protected $primaryKey       = 'id';
  protected $useAutoIncrement = true;
  protected $returnType       = 'array';
  protected $protectFields    = true;
  protected $allowedFields    = [
    "id",
    "uuid",
    "created_at",
    "updated_at",
    "deleted_at",
    "id_user_ins",
    "id_user_del",
  ];

  // Dates
  protected $useTimestamps = true;
  protected $dateFormat    = 'datetime';
  protected $createdField  = 'created_at';
  protected $updatedField  = 'updated_at';
  protected $deletedField  = 'deleted_at';

  // Validation
  protected $validationRules      = [];
  protected $validationMessages   = [];
  protected $skipValidation       = false;
  protected $cleanValidationRules = true;


  public function getDataById(string $id): array|null
  {
    $query = $this->db->table('inf_message im')
      ->select()
      ->where('im.id', $id)
      ->get();

    return $query->getRowArray();
  }
}
