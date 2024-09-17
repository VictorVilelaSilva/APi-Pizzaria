<?php

namespace App\Handlers;

class ErrosCode
{
    public array $errors;

    public function __construct() {
        $this->errors = [
            'ERROR-LOGIN-001'    => 'Não foi passado o email ou senha',
            'ERROR-LOGIN-002'    => 'Usuário ou senha inválidos',
            'ERROR-REGISTER-001' => 'Não foi possivel cadastrar o usuário',
            'ERROR-REGISTER-002' => 'Este email já está cadastrado. Tente outro.',
            'ERROR-REGISTER-003' => 'Ocorreu um erro ao tentar cadastrar o usuário. Tente novamente mais tarde',
        ];
    }

    public function getError(string $code): string
    {
        return $this->errors[$code];
    }
   
}
