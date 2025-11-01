<?php

namespace Modules\Usuario\Controllers;

use App\Modules\Usuario\UseCases\LoginUseCase;
use App\Modules\Usuario\UseCases\RegisterUseCase;
use App\Validation\LoginValidation;
use App\Validation\RegisterValidation;
use CodeIgniter\RESTful\ResourceController;

class UsuarioController extends ResourceController
{
    public function Login()
    {
        $bodyRequest = $this->request->getJSON(true);
        LoginValidation::execute('QUERY', $bodyRequest, 'ERROR-LOGIN-001');
        $useCaseReturn = (new LoginUseCase())->execute($bodyRequest);
        return $this->respond(
            [
                'message'       => 'Login realizado com sucesso',
                'data'          => $useCaseReturn
            ]
        );
    }

    public function Register()
    {
        $bodyRequest = $this->request->getJSON(true);
        RegisterValidation::execute('QUERY', $bodyRequest, 'ERROR-REGISTER-001');
        $data = (new RegisterUseCase())->execute($bodyRequest);
        return $this->respond(
            [
                'message'       => 'Registro realizado com sucesso',
                'data'          => $data
            ]
        );
    }
}
