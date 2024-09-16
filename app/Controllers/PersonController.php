<?php

namespace App\Controllers;

use App\DTO\InputPersonDTO;
use App\UseCases\Person\CreatePersonUseCase;
use CodeIgniter\HTTP\ResponseInterface;

class PersonController extends BaseController
{
    public function CreatePerson(): ResponseInterface
    {
        $bodyRequest = $this->request->getJSON(True);
        $createPersonUseCase = new CreatePersonUseCase();
        $inputPersonDTO = new InputPersonDTO($bodyRequest);
        $outputPersonDTO = $createPersonUseCase->execute($inputPersonDTO);

        $reponseData = [];
        return $this->response->setJSON([
            'message' => 'Pessoa criada com sucesso.',
            'data' => $outputPersonDTO
        ])->setStatusCode(200);
    }
}
