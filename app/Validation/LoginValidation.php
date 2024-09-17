<?php

namespace App\Validation;

use App\Validation\APIValidation;

class LoginValidation extends APIValidation
{
    protected const QUERY_RULES = [
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
        ]
    ];

    public function getRequestRules(string $type): array
    {
        $rules = [
            'QUERY' => self::QUERY_RULES,
        ];
        return $rules[$type] ?? self::QUERY_RULES;
    }
}
