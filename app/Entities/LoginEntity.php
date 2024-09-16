<?php

namespace App\Entities;

class LoginEntity
{
    public ?int $id         = null;
    public ?string $uuid       = null;
    public ?string $username   = null;
    public ?string $password   = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    public function __construct(array $data = [])
    {
        $this->id         = $data['id']         ?? null;
        $this->uuid       = $data['uuid']       ?? null;
        $this->username   = $data['username']   ?? null;
        $this->password   = $data['password']   ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
    }


    // public function addContact(array $contact)
    // {
    //     $this->contacts[] = new ContactsEntity($contact);
    // }

    // public function addAddress(array $address)
    // {
    //     $this->address[] = new AddressEntity($address);
    // }
}
