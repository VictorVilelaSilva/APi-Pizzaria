<?php

namespace App\Repositories\InMemmory;

use App\Entities\PersonEntity;
use App\Repositories\IPersonRepository;

class InMemmoryPersonRepository implements IPersonRepository
{
    private array $db = [];

    public function create(PersonEntity $entity): PersonEntity
    {
        $entity->uuid = generate_uuid();
        $entity->createdAt = date('Y-m-d H:i:s');
        unset($entity->updatedAt);
        $this->db[] = $entity->toArray();
        return new PersonEntity($entity->toArray());
    }


    public function findByUuid(string $uuid): ?PersonEntity
    {
        foreach ($this->db as $row) {
            if ($row['uuid'] === $uuid) {
                return new PersonEntity($row);
            }
        }
        return null;
    }

    public function getByEmail(string $email): ?PersonEntity
    {
        foreach ($this->db as $row) {
            if ($row['email'] === $email) {
                return $row;
            }
        }
        return null;
    }

    public function deleteByUuid(string $uuid): bool
    {
        foreach ($this->db as $key => $row) {
            if ($row['uuid'] === $uuid) {
                unset($this->db[$key]);
                return true;
            }
        }
        return false;
    }

    public function findAll(): array
    {
        return $this->db;
    }

    public function clean(): void
    {
        $this->db = [];
    }
}
