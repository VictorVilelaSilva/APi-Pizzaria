<?php

namespace App\DTO;

class OutputPersonDTO
{
    public int $id;
    public string $uuid;
    public string $name;
    public string $email;
    public array $contacts;
    public array $address;
    public string $createdAt;

    public function __construct(array $input)
    {
        $this->id        = $input['id']         ?? '';
        $this->uuid      = $input['uuid']       ?? '';
        $this->name      = $input['name']       ?? '';
        $this->email     = $input['email']      ?? '';
        $this->contacts     = $input['contacts']      ?? [];
        $this->address   = $input['address']    ?? [];
        $this->createdAt = $input['created_at'] ?? '';
    }
}
