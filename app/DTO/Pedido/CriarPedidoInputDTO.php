<?php

namespace App\DTO\Pedido;

use App\DTO\DTO;

class CriarPedidoInputDTO extends DTO
{
    protected function set()
    {
        return [
            'id_usuario' => $this->dto['id_usuario'] ?? null,
            'id_endereco' => $this->dto['id_endereco'] ?? null,
            'itens' => $this->dto['itens'] ?? [],
            'observacoes' => $this->dto['observacoes'] ?? null,
            'forma_pagamento' => $this->dto['forma_pagamento'] ?? 'Dinheiro',
            'troco_para' => isset($this->dto['troco_para']) ? (float) $this->dto['troco_para'] : null,
        ];
    }
}
