<?php

namespace App\UseCases\Login;

use App\DTO\LoginInputDTO;
use App\DTO\LoginOutputDTO;
use App\Entities\LoginEntity;
use App\Repositories\ILoginRepository;
use App\Validation\LoginValidation;

class LoginUseCase
{
    private ILoginRepository $loginRepository;

    public function __construct(ILoginRepository $loginRepository)
    {
        $this->loginRepository = $loginRepository;
    }

    public function execute(LoginInputDTO $loginDTO): LoginOutputDTO
    {     
    	LoginValidation::execute('QUERY', (array)$loginDTO, 'APICMSWPIX01');

        $entity = new LoginEntity((array) $loginDTO);
        badRequest('ola');

        $outputEntity = $this->loginRepository->Login($entity);

        return new LoginOutputDTO((array)$outputEntity);
        
    }
}
