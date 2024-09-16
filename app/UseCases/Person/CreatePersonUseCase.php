<?php


namespace App\UseCases\Person;

use App\DTO\InputPersonDTO;
use App\DTO\OutputPersonDTO;
use App\Entities\PersonEntity;
use App\Repositories\CI4Model\PersonRepository;
use App\Repositories\IPersonRepository;

class CreatePersonUseCase
{

    private IPersonRepository $personRepository;

    public function __construct(IPersonRepository $personRepository = null)
    {
        $this->personRepository = $personRepository ?? new PersonRepository();
    }

    public function execute(InputPersonDTO $personDTO): OutputPersonDTO
    {
        // validação de contacs e address e person

        $entity = new PersonEntity((array) $personDTO);
        print_r($entity);exit;
        foreach ($personDTO->contacts as $contact)
            $entity->addContact($contact);

        foreach ($personDTO->address as $address)
            $entity->addAddress($address);

        $outputEntity = $this->personRepository->create($entity);
        return new OutputPersonDTO((array)$outputEntity);
    }
}
