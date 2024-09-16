<?php

namespace App\Validation;

use Config\ErrorTrait;

abstract class APIValidation
{
  protected abstract function getRequestRules(string $type): array;
  /**
   * @param string $type tipo de validação INSERT, UPDATE, DELETE
   * @param array $data dados a serem validados
   * @param string $code código de erro
   */
  public static function execute(string $type, $data, string $errorCode)
  {
    $validation = \Config\Services::validation();
    $rules = (new static())->getRequestRules($type);
    $isValid = $validation->setRules($rules)->run($data);

    if (!$isValid) {
      throw new ErrorTrait(
        payload: $errorCode,
        errors: $validation->getErrors()
      );
    }
  }
}
