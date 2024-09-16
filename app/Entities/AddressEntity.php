<?php

namespace App\Entities;


helper('utils');
class AddressEntity
{
    public ?string $id = null;
    public ?string $id_person = null;
    public ?string $street = null;
    public ?string $number = null;
    public ?string $neighborhood = null;
    public ?string $city = null;
    public ?string $state = null;
    public ?string $zip_code = null;
    public ?bool $primary = false;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->id_person = $data['id_person'] ?? null;
        $this->street = $data['street'] ?? null;
        $this->number = $data['number'] ?? null;
        $this->neighborhood = $data['neighborhood'] ?? null;
        $this->city = $data['city'] ?? null;
        $this->state = $data['state'] ?? null;
        $this->zip_code = $data['zip_code'] ?? null;
        $this->primary = $data['primary'] ?? false;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
    }
}
