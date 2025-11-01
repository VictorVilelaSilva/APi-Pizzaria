<?php

namespace App\DTO\Pedido;

use App\DTO\DTO;

class ItemPedidoOutputDTO extends DTO
{
    protected function set()
    {
        return [
            'id' => $this->dto['id'] ?? null,
            'id_pizza' => $this->dto['id_pizza'] ?? null,
            'pizza_nome' => $this->dto['pizza_nome'] ?? null,
            'pizza_ingredientes' => $this->dto['pizza_ingredientes'] ?? null,
            'pizza_img_url' => $this->dto['pizza_img_url'] ?? null,
            'quantidade' => (int) ($this->dto['quantidade'] ?? 0),
            'preco_unitario' => isset($this->dto['preco_unitario']) ? number_format((float) $this->dto['preco_unitario'], 2, '.', '') : '0.00',
            'subtotal' => $this->calcularSubtotal(),
            'observacoes' => $this->dto['observacoes'] ?? null,
        ];
    }

    private function calcularSubtotal(): string
    {
        $quantidade = (int) ($this->dto['quantidade'] ?? 0);
        $preco = (float) ($this->dto['preco_unitario'] ?? 0);
        $subtotal = $quantidade * $preco;

        return number_format($subtotal, 2, '.', '');
    }
}
