<?php

namespace App\Repositories\CI4Model;

use App\Entities\PersonEntity;
use App\Models\AddressModel;
use App\Models\ContactsModel;
use App\Models\PersonModel;
use App\Repositories\IPersonRepository;

class PersonRepository implements IPersonRepository
{
    public function create(PersonEntity $entity): PersonEntity
    {
        $entity->id = generate_uuid();
        $entity->created_at = date('Y-m-d H:i:s');
        unset($entity->updatedAt);

        (new PersonModel())->insert($entity);

        if ($entity->contacts) {
            $contactsModel = new ContactsModel();
            foreach ($entity->contacts as &$contact) {
                $contact->id = generate_uuid();
                $contact->id_person = $entity->id;
            }
            $contactsModel->insertBatch($entity->contacts);
        }

        if ($entity->address) {
            $addressModel = new AddressModel();
            foreach ($entity->address as &$address) {
                $address->id = generate_uuid();
                $address->id_person = $entity->id;
            }
            $addressModel->insertBatch($entity->address);
        }

        return $entity;
    }


    public function findByUuid(string $uuid): ?PersonEntity
    {
        $personData = (new PersonModel())->where('uuid', $uuid)->first();
        return $personData;
    }

    public function getByEmail(string $email): ?PersonEntity
    {
        $personData = (new PersonModel())->where('email', $email)->first();
        return $personData;
    }

    public function deleteByUuid(string $uuid): bool
    {
        return (new PersonModel())->where('uuid', $uuid)->delete();
    }

    public function findAll(): array
    {
        return (new PersonModel())->findAll();
    }

    public function clean(): void
    {
        // (new PersonModel())->truncate();
        return;
    }
}
