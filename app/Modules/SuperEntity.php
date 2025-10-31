<?php

namespace App\Modules;

use CodeIgniter\Entity\Entity;

helper(["format", "str"]);
class SuperEntity extends Entity
{
    /**
     * Constructor.
     * $strict para remover atributos que não são da classe
     * @return void
     *
     */
    public function __construct(?array $data = null, $strict = false)
    {
        parent::__construct($data);

        if ($strict) {
            $this->removeUnsetedAttributes();
        }
    }

    /**
     * Retorna um array com os atributos da classe
     * @return array $attributes
     */
    public static function array($attrs, $strict = false, bool $onlyChanged = true, bool $recursive = true, bool $cast = true)
    {
        // do the same as toArray() and returns
        $childrenEnity = new static($attrs, $strict);
        return $childrenEnity->toArray($onlyChanged, $cast,  $recursive);
    }

    /**
     * Remove atributos que não são da classe
     * @return void
     *
     */
    protected function removeUnsetedAttributes()
    {
        foreach ($this->attributes as $key => $value) {
            // First, check if the attribute is a part of the datamap
            if (isset($this->datamap) && in_array($key, $this->datamap)) {
                continue; // Skip removal if it's part of datamap
            }

            // Next, check if the attribute is a property of the current class
            if (!property_exists($this, $key)) {
                unset($this->attributes[$key]);
            }
        }
    }

    protected function castAs($value, string $attribute, string $method = 'get')
    {
        // Sobrescrevendo os casts para formatar:
        if (array_key_exists($attribute, $this->casts)) {
            $castInto = $this->casts[$attribute];
            if ($castInto === 'date')  return dateToYMD($value);
            // if ($castInto === 'get')  return convertStringToFloat($value);
            if ($method === 'set' && $castInto === 'value')  return convertStringToDouble($value);
            if ($method === 'set' && $castInto === 'moneyToShow')  return moneyToShow($value);
            if ($method === 'set' && $castInto === 'percentToShow')  return percentToShow($value, 4);
            if ($method === 'set' && $castInto === 'dateToShow')  return dateToShow($value);
            if ($method === 'set' && $castInto === 'cpfcnpjToShow')  return cpfcnpjToShow($value);
            if ($method === 'set' && $castInto === 'phoneToShow')  return phoneToShow($value);
            if ($method === 'set' && $castInto === 'cepToShow')  return cepToShow($value);
            if ($method === 'set' && $castInto === 'id')  return getIdFromObject($value);
            if ($method === 'set' && $castInto === 'ids')  return getIdsFromObjects($value);
            if ($castInto === 'removeEspecials')  return removeEspecials($value);
            if ($castInto === 'removeMask')  return removeNonNumeric($value);
        }

        // Default behavior for other cases
        return parent::castAs($value, $attribute, $method);
    }
}
