<?php

namespace Tests\Unit;

use App\Entities\PersonEntity;
use PHPUnit\Framework\TestCase;

helper('utils');
class PersonEntityTest extends TestCase
{
    public function testIfUuidIsGenerated(): void
    {
        // Arrange
        $input = [
            'id' => null,
            'uuid' => null,
            'name' => 'Lelouch Lamparouge',
            'email' => 'lelouch@teste.com',
            'phone' => null,
            'address' => null
        ];

        // Act
        $entity = new PersonEntity($input);

        // Assert
        $this->assertNotNull($entity->uuid);
    }
}
