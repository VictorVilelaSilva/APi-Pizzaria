<?php

namespace App\DTO\Pedido;

use App\DTO\DTO;

class AtualizarStatusInputDTO extends DTO
{
    protected function set()
    {
        return [
            'status' => $this->dto['status'] ?? null,
            'observacao' => $this->dto['observacao'] ?? null,
            'motivo_cancelamento' => $this->dto['motivo_cancelamento'] ?? null,
        ];
    }
}
