<?php

namespace App\Validation;

use DateTime;

helper('str');
/**
 * Para que a validação falhe, o método deve retornar false.
 */
class ExtraRules
{
    public function required_if($str, string $fields, array $data): bool
    {
        // \badRequest($str);
        [$requiredField, $requiredValue] = explode(',', $fields, 2);

        if (isset($data[$requiredField]) && $data[$requiredField] == $requiredValue) {
            return isset($str) && $str !== "";
        }

        return true;
    }

    //verifica se os campos de um array estão preenchidos
    public function bodyValidationGravame($str, $fields, $data): bool
    {
        [$document_Id, $appointment_number] = explode(',', $fields, 2);

        if ((isset($data[$document_Id]) && !empty($data[$document_Id])) || (isset($data[$appointment_number]) && !empty($data[$appointment_number]))) {
            return true;
        }

        return false;
    }

    //função para buscar um valor em um array de arrays
    public function findKeyRecursive($array, $keyToFind)
    {
        foreach ($array as $key => $value) {
            if ($key == $keyToFind) {
                return $value;
            } elseif (is_array($value)) {
                $result = $this->findKeyRecursive($value, $keyToFind);
                if ($result !== null) {
                    return $result;
                }
            }
        }

        return null;
    }

    public function required_if_gravame($str, string $fields, array $data): bool
    {
        $data                               = $data['data'];
        list($relatedField, $expectedValue) = explode(',', $fields);

        $result = $this->findKeyRecursive($data, $relatedField);
        if (isset($result) && $result == $expectedValue) {
            return !is_null($str);
        }

        return true;
    }

    public function fieldDate_greater_than_currentDate($str, string $fields, array $data): bool
    {
        $fieldDate = $data[$fields] ?? null;
        if (!$fieldDate) {
            $fieldDate = $this->findKeyRecursive($data, $fields);
        }

        if (!$fieldDate || !strtotime($fieldDate)) {
            return false;
        }

        $fieldDate   = new DateTime($fieldDate);
        $currentDate = new DateTime();

        if ($fieldDate > $currentDate) {
            return true;
        }

        return false;
    }

    public function date_current_or_future($date)
    {
        $today = date('Y-m-d');

        return $date >= $today;
    }

    public function accent_check($str)
    {
        if (preg_match('/[áàãâäéèêëíìîïóòõôöúùûüÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÔÖÚÙÛÜ]/', $str)) {
            return false;
        }

        return true;
    }

    public function required_if_cpfcnpj($str, string $field, array $data): bool
    {
        [$requiredField, $requiredType] = explode(',', $field, 2);
        if (isset($data[$requiredField])) {
            $fieldValue = removeNonNumeric($data[$requiredField]);
            if ($requiredType == 'cpf' && (strlen($fieldValue) == 11)) {
                return isset($str) && $str !== "";
            } else if (($requiredType == 'cnpj') && (strlen($fieldValue) == 14)) {
                return isset($str) && $str !== "";
            }
        }

        return true;
    }
    public function alpha_space_br(string $str): bool
    {
        $pattern = '/^[A-Za-záàâãéêíóôõúçÁÀÂÃÉÊÍÓÔÕÚÇ\s]+$/';

        if (preg_match($pattern, $str)) {
            return true;
        }

        return false;
    }

    public function alpha_space_br_contato(string $str): bool
    {
        $str = removeEspecials($str);

        $pattern = '/^[A-Za-záàâãéêíóôõúçÁÀÂÃÉÊÍÓÔÕÚÇ\s]+$/';

        if (preg_match($pattern, $str)) {
            return true;
        }

        return false;
    }

    public static function validate_no_sequential(string $password, $ascRule = true, $descRule = true): bool
    {
        $password = removeEspecials($password);

        if (!is_numeric($password)) {
            return false;
        }
        if ($ascRule) {
            $ascending = '/(?: 0(?=1|\b)| 1(?=2|\b)| 2(?=3|\b)|  3(?=4|\b)|  4(?=5|\b)|
              5(?=6|\b)|  6(?=7|\b)|  7(?=8|\b)|  8(?=9|\b)|  9\b  ){4}/x';
        }
        if ($descRule) {
            $descending = '/(?: 9(?=8|\b)| 8(?=7|\b)| 7(?=6|\b)|  6(?=5|\b)|  5(?=4|\b)|
              4(?=3|\b)|  3(?=2|\b)|  2(?=1|\b)|  1(?=0|\b)|  0\b  ){4}/x';
        }

        return !preg_match($ascending, $password) && !preg_match($descending, $password);
    }

    public function alpha_space_br_if_PF($str, string $field, array $data): bool
    {
        if (strlen($data[$field]) == 11) {
            $pattern = '/^[A-Za-záàâãéêíóôõúçÁÀÂÃÉÊÍÓÔÕÚÇ\s]+$/';

            if (preg_match($pattern, $str)) {
                return true;
            }

            return false;
        }

        return true;
    }

    public function manufactory_model_year_valid_date(string $str): bool
    {
        $currentYear = date('Y');

        if ($str <= $currentYear) {
            return true;
        } elseif ($str == $currentYear) {
            return true;
        }

        return false;
    }

    public function fristFieldDate_greater_than_secondFieldDate($str, string $fields, array $data): bool
    {
        // \badRequest($data);
        [$keyFristDate, $keySecondDate] = explode(',', $fields, 2);
        $fristFieldDate                 = $data[$keyFristDate]  ?? null;
        $secondFieldDate                = $data[$keySecondDate] ?? null;
        if (!$fristFieldDate) {
            $fristFieldDate = $this->findKeyRecursive($data, $fields);
        }
        if (!$secondFieldDate) {
            $secondFieldDate = $this->findKeyRecursive($data, $fields);
        }
        if ((!$fristFieldDate or !strtotime($fristFieldDate) and (!$secondFieldDate or !strtotime($secondFieldDate)))) {
            return false;
        }

        $fristFieldDate  = new DateTime($fristFieldDate);
        $secondFieldDate = new DateTime($secondFieldDate);

        if ($fristFieldDate > $secondFieldDate) {
            return false;
        }

        return true;
    }

    public function fieldDate_less_than_currentDate($str, string $fields, array $data): bool
    {
        $fieldDate = $data[$fields] ?? null;
        if (!$fieldDate) {
            $fieldDate = $this->findKeyRecursive($data, $fields);
        }

        if (!$fieldDate || !strtotime($fieldDate)) {
            return false;
        }

        $fieldDate   = new DateTime($fieldDate);
        $currentDate = new DateTime();

        if ($fieldDate < $currentDate) {
            return true;
        }

        return false;
    }

    public function dateGreaterThanToday($str, string $fields, array $data): bool
    {

        $fieldDate = $data[$fields] ?? null;
        if (!$fieldDate) {
            $fieldDate = $this->findKeyRecursive($data, $fields);
        }

        if (!$fieldDate || !strtotime($fieldDate)) {
            return false;
        }

        $fieldDate = new DateTime($fieldDate);
        $currentDate = new DateTime();

        if ($fieldDate < $currentDate) {
            return true;
        }

        return false;
    }

    public function required_is_not($str, string $fields, array $data)
    {
        $db                              = db_connect();
        [$requiredField, $requiredValue] = explode(',', $fields, 2);
        $requiredValue                   = explode(',', $requiredValue);

        $value = $db->table('inf_type_occupation')->select('type')->where('uuid', $data[$requiredField])->get()
        ->getRowArray();

        if (isset($data[$requiredField])) {
            $result = in_array($value['type'], $requiredValue);
            if ($result) {
                return true;
            }
        }

        return  isset($str) && $str !== "";
    }

    public function validateCnpj($str, string $field, $data): bool
    {
        [$requiredField] = explode(',', $field, 2);

        return validar_cnpj($data[$requiredField]);
    }

    public function startDateGreaterThanEndDate($str, string $fields, array $data): bool
    {
        [$keyFirstDate, $keySecondDate] = explode(',', $fields, 2);
        $firstFieldDate = $data[$keyFirstDate] ?? null;
        $secondFieldDate = $data[$keySecondDate] ?? null;
        if (!$firstFieldDate) {
            $firstFieldDate = $this->findKeyRecursive($data, $fields);
        }
        if (!$secondFieldDate) {
            $secondFieldDate = $this->findKeyRecursive($data, $fields);
        }
        if ((!$firstFieldDate or !strtotime($firstFieldDate) and (!$secondFieldDate or !strtotime($secondFieldDate)))) {
            return false;
        }

        $firstFieldDate = new DateTime($firstFieldDate);
        $secondFieldDate = new DateTime($secondFieldDate);

        if ($firstFieldDate <= $secondFieldDate) {
            return true;
        }

        return false;
    }
}
