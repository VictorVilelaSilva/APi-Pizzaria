<?php

// app/Language/pt-BR/Validation.php

return [
    'required'                                    => 'Campo {field} é obrigatório.',
    'required_with'                               => 'Campo {field} é obrigatório.',
    'required_without'                            => 'O campo {field} é obrigatório quando o campo {param} não for informado.',
    'required_if'                                 => 'Campo {field} é obrigatório.',
    'required_if_cpfcnpj'                         => 'Campo {field} é obrigatório.',
    'max_length'                                  => 'Campo {field} deve ter no máximo {param} caracteres.',
    'min_length'                                  => 'Campo {field} deve ter no mínimo {param} caracteres.',
    'greater_than'                                => 'O campo {field} ({value}) deve ser maior que {param}.',
    'less_than'                                   => 'O campo {field} ({value}) deve ser menor que {param}.',
    'valid_email'                                 => 'Campo {field} deve ser um e-mail válido.',
    'is_unique'                                   => 'Campo {field} informado já está cadastrado.',
    'valid_date'                                  => 'Campo {field} deve ser {param}.',
    'existeRupCpf'                                => 'O {field} ({value}) não existe em nossa base de dados.',
    'validateCpfCnpj'                             => 'Campo {field} não é válido',
    'integer'                                     => 'O Campo {field} deve ser um número inteiro.',
    'alpha_space'                                 => 'O Campo {field} deve possuir apenas letras.',
    'alpha_space_br'                              => 'O Campo {field} deve possuir apenas letras.',
    'alpha_space_br_contato'                      => 'O Campo {field} deve possuir apenas letras...',
    'numeric'                                     => 'O Campo {field} deve possuir apenas números.',
    'number'                                      => 'O Campo {field} deve possuir apenas números.',
    'decimal'                                     => 'O campo, {field}, deve ser numérico.',
    'in_list'                                     => 'Campo {field} não pode ser \'{value}\' deve ser {param}.',
    'valid_url_strict'                            => 'Campo {field} não é válido.',
    'searchAgent'                                 => 'O {field} não é válido.',
    'validateConsumer'                            => 'O {field} não possui cadastro no sistema.',
    'personExists'                                => 'O {field} ({value}) não está cadastrado no sistema.',
    'loanIdExists'                                => 'A {field} não está cadastrada no sistema.',
    'maritalStatusExists'                         => "O código '{value}' não está referenciado a nenhum ESTADO CIVIL. Verifique na rota [GET] /maritalStates.",
    'isAlreadyLinked'                             => 'O {field} ({value}) já está vinculado a esta operação.',
    'greaterThanDate'                             => 'O campo {field} ({value}) deve ser maior que {param}.',
    'ccbNumberNotExists'                          => 'O {field} ({value}) já está cadastrado no sistema.',
    'validatePixType'                             => 'O {field} ({value}) não está cadastrado no sistema. Verifique a rota [GET] /pixTypes.',
    'is_not_unique'                               => 'O {field} informado não é um registro válido no sistema.',
    'string'                                      => 'O {field} precisa ser uma opção válida.',
    'not_equals'                                  => 'O {field} precisa ser um valor válido no sistema.',
    'bodyValidationGravame'                       => 'É necessário informar o {field} ou o Número do apontamento.',
    'required_if_gravame'                         => 'O campo {field} tem que ser preenchido.',
    'accent_check'                                =>'O campo {field} não pode conter caracteres acentuados.',
    'matches'                                     =>'O campo {field} não corresponde com o campo {param}.',
    'validate_no_sequential'                      =>'O campo {field} não corresponde com o padrão solicitado.',
    'fieldDate_greater_than_currentDate'          =>'A data do campo {field} deve ser maior que a data atual.',
    'alpha_space_br_if_PF'                        => 'O campo {field} deve possuir apenas letras.',
    'manufactory_model_year_valid_date'           => 'O campo {field} aceita apenas anos que sejam menores, iguais ou com no máximo um ano de diferença em relação ao ano atual',
    'fristFieldDate_greater_than_secondFieldDate' => 'O campo {field} deve possuir uma data maior que o outro campo de data previamente inserido.',
    'startDateGreaterThanEndDate'                 => 'A data de início não pode ser maior do que a data de término.',
    'endDateLessThanStartDate'                    => 'A data de término não pode ser menor do que a data de início.',
    'dateGreaterThanToday'                        => 'A data não pode ser maior do que o dia de hoje.',
    'greater_than_equal_to'                       => 'A data de início não pode ser maior do que a data de término.',
    'less_than_equal_to'                          => 'A data de término não pode ser menor do que a data de início.',
    'fieldDate_less_than_currentDate'             =>'A data do campo {field} deve ser menor que a data atual.',
    'date_current_or_future'                      => 'O campo {field} deve ser uma data atual ou futura.',
    'is_there_person_in_company'                  => 'O {field} não está vinculado a empresa logada.',
    'required_is_not'                             => 'O campo {field} é obrigatório.',
    'validateCnpj'                                => 'O CNPJ não é válido',
    'less_than_equal_to'                          => 'O campo {field} deve ser menor ou igual a {param}%.',

    // Adicione outras mensagens de validação aqui
];
