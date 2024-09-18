<?php

namespace App\Validation;

use App\Validation\APIValidation;

class RegisterValidation extends APIValidation
{
    protected const QUERY_RULES = [
        "nome" => [
            'label' => 'nome',
            'rules' => [
                'required'
            ]
        ],
        "email" => [
            'label' => 'email',
            'rules' => [
                'required'
            ]
        ],
        'senha' => [
            'label' => 'senha',
            'rules' => [
                'required'
            ]
        ],
        "logradouro" => [
            'label' => 'logradouro',
            'rules' => [
                'required'
            ]
        ],
        "numero" => [
            'label' => 'numero',
            'rules' => [
                'required'
            ]
        ],
        "bairro" => [
            'label' => 'bairro',
            'rules' => [
                'required'
            ]
        ],
        "cidade" => [
            'label' => 'cidade',
            'rules' => [
                'required'
            ]
        ],
        "estado" => [
            'label' => 'estado',
            'rules' => [
                'required'
            ]
        ],
        "cep" => [
            'label' => 'cep',
            'rules' => [
                'required'
            ]
        ],

    ];

    public function getRequestRules(string $type): array
    {
        $rules = [
            'QUERY' => self::QUERY_RULES,
        ];
        return $rules[$type] ?? self::QUERY_RULES;
    }
}
