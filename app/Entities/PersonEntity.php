<?php

namespace App\Entities;


helper('utils');
class PersonEntity
{

    public ?string $id = null;
    public ?string $name = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;
    public array $contacts = [];
    public array $address = [];

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
    }


    public function addContact(array $contact)
    {
        $this->contacts[] = new ContactsEntity($contact);
    }

    public function addAddress(array $address)
    {
        $this->address[] = new AddressEntity($address);
    }
}
