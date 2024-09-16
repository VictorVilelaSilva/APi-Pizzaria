<?php

namespace App\Repositories;

use App\Entities\PersonEntity;

interface IPersonRepository
{
    public function create(PersonEntity $entity): PersonEntity;
    public function findByUuid(string $uuid): ?PersonEntity;
    public function getByEmail(string $email): ?PersonEntity;
    public function deleteByUuid(string $uuid): bool;
    public function findAll(): array;
    public function clean(): void;
}
