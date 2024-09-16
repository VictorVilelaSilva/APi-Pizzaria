<?php

namespace App\Controllers;

use App\DTO\LoginInputDTO;
use App\Repositories\CI4Model\LoginRepository;
use App\UseCases\Login\LoginUseCase;
use CodeIgniter\RESTful\ResourceController;

class AuthController extends ResourceController
{
	public function Login()
	{
		$bodyRequest = $this->request->getJSON(true);
		$repository = new LoginRepository();
		$loginUseCase = new LoginUseCase($repository);
		$loginInputDTO = new LoginInputDTO($bodyRequest);
		$loginOutputDTO = $loginUseCase->execute($loginInputDTO);


		

		return $this->respond(
			[
				'message'       => 'Login Success',
				'data'          => $loginOutputDTO
			]
		);
	}
}
