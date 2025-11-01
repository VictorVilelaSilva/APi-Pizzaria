<?php

namespace Modules\Usuario\Controllers;

use App\DTO\LoginInputDTO;
use App\Libraries\JWT\JWTService;
use App\Repositories\CI4Model\LoginRepository;
use App\Modules\Usuario\UseCases\LoginUseCase;
use App\Modules\Usuario\UseCases\RegisterUseCase;
use App\Validation\LoginValidation;
use App\Validation\RegisterValidation;
use CodeIgniter\RESTful\ResourceController;
use Config\ErrorTrait;

class UsuarioController extends ResourceController
{
    public function Login()
    {
        try {
            $bodyRequest = $this->request->getJSON(true);
            LoginValidation::execute('QUERY', $bodyRequest, 'ERROR-LOGIN-001');
            $useCaseReturn = (new LoginUseCase())->execute($bodyRequest);
            $jwtService = new JWTService();
            $token = $jwtService->generateToken([
                'id' => $useCaseReturn['id'] ?? null,
                'email' => $useCaseReturn['email'] ?? $bodyRequest['email'],
                'nome' => $useCaseReturn['nome'] ?? null,
            ]);

            return $this->respond(
                [
                    'message' => 'Login realizado com sucesso',
                    'data' => $useCaseReturn,
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            );
        } catch (ErrorTrait $e) {
            return $this->respond(
                $e->getPayload(),
                $e->getStatusCode()
            );
        }
    }

    public function Register()
    {
        try {
            $bodyRequest = $this->request->getJSON(true);
            RegisterValidation::execute('QUERY', $bodyRequest, 'ERROR-REGISTER-001');
            $data = (new RegisterUseCase())->execute($bodyRequest);
            return $this->respond(
                [
                    'message' => 'Registro realizado com sucesso',
                    'data'    => $data
                ]
            );
        } catch (ErrorTrait $e) {
            return $this->respond(
                $e->getPayload(),
                $e->getStatusCode()
            );
        }
    }
}
