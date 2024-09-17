<?php

namespace App\UseCases\Register;

use App\Models\enderecosModel;
use App\Models\UsuariosModel;
use Config\ErrorTrait;


class RegisterUseCase
{
    public function execute(array $registerBody): void
    {
        try {
            $usuarioModel = new UsuariosModel();
            $enderecoModel = new enderecosModel();
            $usuarioData = $usuarioModel->findByEmail($registerBody['email']);
            if ($usuarioData) throw new ErrorTrait('ERROR-REGISTER-002');
            $registerBody['senha'] = password_hash($registerBody['senha'], PASSWORD_DEFAULT);
            $idUsuario = $usuarioModel->insert([
                'nome'       => $registerBody['nome'],
                'email'      => $registerBody['email'],
                'senha'      => $registerBody['senha'],
                'created_at' => date('Y-m-d H:i:s')
            ]);
            foreach ($registerBody['enderecos'] as &$endereco) {
                $endereco['id_usuario'] = $idUsuario;
            }
            $enderecoModel->insertBatch($registerBody['enderecos']);
        } catch (\Exception $e) {
            throw new ErrorTrait('ERROR-REGISTER-003');
        }
    }
}
