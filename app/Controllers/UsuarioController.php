<?php

namespace App\Controllers;

use App\DTO\LoginInputDTO;
use App\Repositories\CI4Model\LoginRepository;
use App\UseCases\Login\LoginUseCase;
use App\UseCases\Register\RegisterUseCase;
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
		$useCaseReturn = (new RegisterUseCase())->execute($bodyRequest);
		return $this->respond(
			[
				'message'       => 'Registro realizado com sucesso'
			]
		);
	}
}
