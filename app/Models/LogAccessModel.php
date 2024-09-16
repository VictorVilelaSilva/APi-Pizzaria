<?php

namespace App\Models;

use App\Models\HBIModel;
use CodeIgniter\Model;

class LogAccessModel extends Model
{
  protected $DBGroup          = 'default';
  protected $table            = 'log_access';
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
    "id_log_query",
    "success",
    "ip_address",
    "request_method",
    "request_endpoint",
    "status_code",
    "code",
    "request_json_body",
    "response_json_body",
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


  public function getDataById(array $data): void
  {

    $this->insert($data);
  }
}
