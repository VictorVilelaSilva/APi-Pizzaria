<?php

namespace App\DTO;

class LoginOutputDTO
{
    public string $uuid;
    public string $username;
    public string $password;
    public string $email;
    public string $createdAt;
    public string $updatedAt;

    public function __construct(array $input)
    {
        $this->uuid          = $input['uuid']          ?? '';
        $this->username      = $input['username']      ?? '';
        $this->password      = $input['password']      ?? '';
        $this->createdAt     = $input['created_at']    ?? '';
        $this->updatedAt     = $input['updated_at']    ?? '';

    }
}
