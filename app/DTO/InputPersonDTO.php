<?php

namespace App\DTO;

class InputPersonDTO
{

    public string $name;
    public string $email;
    public array $contacts;
    public array $address;

    public function __construct(array $input)
    {
        $this->name       = $input['name']    ?? '';
        $this->email      = $input['email']   ?? '';
        $this->contacts   = $input['contacts']   ?? [];
        $this->address    = $input['address'] ?? [];
    }
}
