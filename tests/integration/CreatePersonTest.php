<?php

use App\DTO\InputPersonDTO;
use App\Repositories\InMemmory\InMemmoryPersonRepository;
use App\Repositories\IPersonRepository;
use App\UseCases\Person\CreatePersonUseCase;

use PHPUnit\Framework\TestCase;

class CreatePersonTest extends TestCase
{
    private IPersonRepository $repository;

    protected function setUp(): void     // clean repository before each test
    {
        $this->repository = new InMemmoryPersonRepository(); // $repository = new PersonRepository();
        $this->repository->clean();
    }

    public function testCreatePerson(): void
    {
        // Arrange
        $input = [
            'name' => 'Lelouch Lamparouge',
            'email' => 'lelouch@teste.com'
        ];

        // Act
        $createPersonUseCase = new CreatePersonUseCase($this->repository);
        $inputDTO = new InputPersonDTO($input);
        echo json_encode($inputDTO);
        exit;
        $outputDTO = $createPersonUseCase->execute($inputDTO);

        // Assert
        $this->assertNotEmpty($outputDTO->uuid);
        $this->assertEquals($input['name'], $outputDTO->name);
        $this->assertEquals($input['email'], $outputDTO->email);
        $person = $this->repository->findByUuid($outputDTO->uuid);
        $this->assertEquals($outputDTO->uuid, $person->uuid);
    }
}
