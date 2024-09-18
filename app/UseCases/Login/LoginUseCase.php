<?php

namespace App\UseCases\Login;

use App\Models\UsuariosModel;
use Config\ErrorTrait;


class LoginUseCase
{
    public function execute(array $loginBody): array
    {
        $usuarioModel = new UsuariosModel();
        $usuarioData = $usuarioModel->findByEmail($loginBody['email']);
        if (!$this->UsuarioExistVerification($usuarioData, $loginBody)) {
            throw new ErrorTrait('ERROR-LOGIN-002');
        }
        return [
            'id' => $usuarioData['id'],
            'nome' => $usuarioData['nome'],
            'email' => $usuarioData['email'],
        ];
    }
    private function UsuarioExistVerification($usuarioData, $loginBody): bool
    {
        return $usuarioData && password_verify($loginBody['senha'], $usuarioData['senha']);
    }
}
