<?php

namespace App\DTO;

use Config\ErrorTrait;

abstract class DTO
{
  protected $dto;

  public function __construct($dto)
  {
    $this->dto = $dto;
  }

  /**
   * #### Método abstrato para ser implementado nas classes filhas
   * @return array formatado DTO (Data Transfer Object)
   */
  abstract protected function set();


  /**
   * ### Transforma um array de uma linha de dados do banco
   * @param array $row array associativo para ser transformado, geralmente: linha de dados do banco
   * @return array formatado DTO (Data Transfer Object)
   */
  public static function get(array $row)
  {
    try {
      return (new static($row))->set();
    } catch (\Throwable $th) {
      throw new ErrorTrait('EDT00001', [
        'message' => $th->getMessage(),
        'trace' => $th->getTrace()
      ]);
    }
  }

  /**
   * ### Transforma um array de linhas de dados do banco
   * @param array $rows array associativo para ser transformado, geralmente: linhas de dados do banco
   * @return array formatado DTO (Data Transfer Object)
   */
  public static function getAll(array $rows)
  {
    $map = array_map(function ($row) {
      return self::get($row);
    }, $rows);

    $map = array_filter($map);
    return array_values($map); // Reindex array to ensure sequential integer keys
  }

  function getJsonKeys()
  {
    $json = [];
    foreach ($this->dto as $key => $value) {
      $isJsonField = substr($key, -5) === '_json';
      if ($isJsonField) {
        $json[$key] = json_decode($this->dto[$key], true);
        continue;
      }
      $json[$key] = $this->dto[$key];
    }
    return $json;
  }
}
