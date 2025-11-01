<?php

namespace App\Validation\Pedido;

use App\Validation\APIValidation;

class PedidoValidation extends APIValidation
{
    protected function getRequestRules(string $type): array
    {
        $rules = [
            'CRIAR' => [
                'id_endereco' => [
                    'rules' => 'required|integer|is_not_unique[enderecos.id]',
                    'errors' => [
                        'required' => 'O endereço é obrigatório',
                        'integer' => 'O ID do endereço deve ser um número inteiro',
                        'is_not_unique' => 'Endereço não encontrado',
                    ],
                ],
                'itens' => [
                    'rules' => 'required|array_count[1]',
                    'errors' => [
                        'required' => 'Os itens do pedido são obrigatórios',
                        'array_count' => 'O pedido deve ter pelo menos 1 item',
                    ],
                ],
                'itens.*.id_pizza' => [
                    'rules' => 'required|integer|is_not_unique[pizzas.id]',
                    'errors' => [
                        'required' => 'O ID da pizza é obrigatório',
                        'integer' => 'O ID da pizza deve ser um número inteiro',
                        'is_not_unique' => 'Pizza não encontrada',
                    ],
                ],
                'itens.*.quantidade' => [
                    'rules' => 'required|integer|greater_than[0]|less_than_equal_to[50]',
                    'errors' => [
                        'required' => 'A quantidade é obrigatória',
                        'integer' => 'A quantidade deve ser um número inteiro',
                        'greater_than' => 'A quantidade deve ser maior que 0',
                        'less_than_equal_to' => 'A quantidade máxima por item é 50',
                    ],
                ],
                'itens.*.observacoes' => [
                    'rules' => 'permit_empty|max_length[500]',
                    'errors' => [
                        'max_length' => 'As observações não podem ter mais de 500 caracteres',
                    ],
                ],
                'observacoes' => [
                    'rules' => 'permit_empty|max_length[1000]',
                    'errors' => [
                        'max_length' => 'As observações do pedido não podem ter mais de 1000 caracteres',
                    ],
                ],
                'forma_pagamento' => [
                    'rules' => 'required|in_list[Dinheiro,Cartão Débito,Cartão Crédito,PIX,Vale Refeição]',
                    'errors' => [
                        'required' => 'A forma de pagamento é obrigatória',
                        'in_list' => 'Forma de pagamento inválida',
                    ],
                ],
                'troco_para' => [
                    'rules' => 'permit_empty|decimal|greater_than[0]',
                    'errors' => [
                        'decimal' => 'O valor do troco deve ser um número decimal',
                        'greater_than' => 'O valor do troco deve ser maior que 0',
                    ],
                ],
            ],
            'ATUALIZAR_STATUS' => [
                'status' => [
                    'rules' => 'required|in_list[Pendente,Confirmado,Preparando,Saiu para entrega,Entregue,Cancelado]',
                    'errors' => [
                        'required' => 'O status é obrigatório',
                        'in_list' => 'Status inválido',
                    ],
                ],
                'observacao' => [
                    'rules' => 'permit_empty|max_length[500]',
                    'errors' => [
                        'max_length' => 'A observação não pode ter mais de 500 caracteres',
                    ],
                ],
                'motivo_cancelamento' => [
                    'rules' => 'required_with[status,Cancelado]|max_length[500]',
                    'errors' => [
                        'required_with' => 'O motivo do cancelamento é obrigatório',
                        'max_length' => 'O motivo não pode ter mais de 500 caracteres',
                    ],
                ],
            ],
            'CANCELAR' => [
                'motivo_cancelamento' => [
                    'rules' => 'required|max_length[500]',
                    'errors' => [
                        'required' => 'O motivo do cancelamento é obrigatório',
                        'max_length' => 'O motivo não pode ter mais de 500 caracteres',
                    ],
                ],
            ],
        ];

        return $rules[$type] ?? [];
    }
}
