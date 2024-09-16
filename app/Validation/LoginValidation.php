<?php

namespace App\Validation;

use App\Validation\APIValidation;

class LoginValidation extends APIValidation
{
    protected const QUERY_RULES = [
        "username" => [
            'label' => 'username',
            'rules' => [
                'required'
            ]
        ],
        'passowrd' => [
            'label' => 'password',
            'rules' => [
                'required',
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
