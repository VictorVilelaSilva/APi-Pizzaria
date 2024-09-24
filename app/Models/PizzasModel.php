<?php

namespace App\Models;

use CodeIgniter\Model;

class PizzasModel extends Model
{
    protected $table         = 'pizzas';
    protected $allowedFields =
    [
        "nome",
        "preco",
        "ingredientes",
        "img_url"
    ];

    protected $returnType    = 'array';
    protected $useTimestamps = true;

    public function findById(string $id): ?array
    {
        return $this->where('id', $id)->first();
    }

    public function getAllPizzas(): array
    {
        return $this->select('id,nome, preco, ingredientes, img_url')->findAll();
    }
}
