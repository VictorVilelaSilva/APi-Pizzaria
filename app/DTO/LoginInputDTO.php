<?php

namespace App\DTO;

class LoginInputDTO
{

    public string $username;
    public string $passowrd;


    public function __construct(array $bodyRequest)
    {
        $this->username       = $bodyRequest['username']   ?? '';
        $this->passowrd       = $bodyRequest['passowrd']   ?? '';
    }
}
