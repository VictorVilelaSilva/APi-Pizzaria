<?php

namespace App\Entities;

helper('utils');
class ContactsEntity
{
    public ?string $id = null;
    public ?string $id_person = null;
    public ?string $contact = null;
    public ?bool $primary = false;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->id_person = $data['id_person'] ?? null;
        $this->contact = $data['contact'] ?? null;
        $this->primary = $data['primary'] ?? false;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
    }
}
