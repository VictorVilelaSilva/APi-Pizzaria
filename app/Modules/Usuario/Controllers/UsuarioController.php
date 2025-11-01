<?php

namespace Modules\Usuario\Controllers;

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
            return $this->respond(
                [
                    'message' => 'Login realizado com sucesso',
                    'data'    => $useCaseReturn
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
