<?php

namespace App\UseCases\Register;

use App\Models\enderecosModel;
use App\Models\UsuariosModel;
use Config\ErrorTrait;


class RegisterUseCase
{
    public function execute(array $registerBody): array
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
            $endereco['id_usuario'] = $idUsuario;
            $enderecoModel->insert(
                [
                    'id_usuario' => $idUsuario,
                    'logradouro' => $registerBody['logradouro'],
                    'numero'     => $registerBody['numero'],
                    'bairro'     => $registerBody['bairro'],
                    'cidade'     => $registerBody['cidade'],
                    'estado'     => $registerBody['estado'],
                    'cep'        => $registerBody['cep']
                ]
            );
            return[
                'id_usuario' => $idUsuario,
                'nome'       => $registerBody['nome']
            ];
        } catch (\Exception $e) {
            throw new ErrorTrait('ERROR-REGISTER-003');
        }
    }
}
