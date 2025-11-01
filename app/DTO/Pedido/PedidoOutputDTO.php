<?php

namespace App\DTO\Pedido;

use App\DTO\DTO;

class PedidoOutputDTO extends DTO
{
    protected function set()
    {
        return [
            'id' => $this->dto['id'] ?? null,
            'id_usuario' => $this->dto['id_usuario'] ?? null,
            'usuario_nome' => $this->dto['usuario_nome'] ?? null,
            'usuario_email' => $this->dto['usuario_email'] ?? null,
            'id_endereco' => $this->dto['id_endereco'] ?? null,
            'endereco' => $this->formatarEndereco(),
            'valor_total' => isset($this->dto['valor_total']) ? number_format((float) $this->dto['valor_total'], 2, '.', '') : '0.00',
            'status' => $this->dto['status'] ?? 'Pendente',
            'observacoes' => $this->dto['observacoes'] ?? null,
            'tempo_preparo_estimado' => $this->dto['tempo_preparo_estimado'] ?? null,
            'forma_pagamento' => $this->dto['forma_pagamento'] ?? 'Dinheiro',
            'troco_para' => isset($this->dto['troco_para']) ? number_format((float) $this->dto['troco_para'], 2, '.', '') : null,
            'datas' => $this->formatarDatas(),
            'created_at' => $this->dto['created_at'] ?? null,
            'updated_at' => $this->dto['updated_at'] ?? null,
        ];
    }

    private function formatarEndereco(): ?array
    {
        if (empty($this->dto['logradouro'])) {
            return null;
        }

        return [
            'logradouro' => $this->dto['logradouro'] ?? null,
            'numero' => $this->dto['numero'] ?? null,
            'bairro' => $this->dto['bairro'] ?? null,
            'cidade' => $this->dto['cidade'] ?? null,
            'estado' => $this->dto['estado'] ?? null,
            'cep' => $this->dto['cep'] ?? null,
            'endereco_completo' => sprintf(
                '%s, %s - %s, %s/%s - CEP: %s',
                $this->dto['logradouro'] ?? '',
                $this->dto['numero'] ?? '',
                $this->dto['bairro'] ?? '',
                $this->dto['cidade'] ?? '',
                $this->dto['estado'] ?? '',
                $this->dto['cep'] ?? ''
            ),
        ];
    }

    private function formatarDatas(): array
    {
        return [
            'confirmacao' => $this->dto['data_confirmacao'] ?? null,
            'inicio_preparo' => $this->dto['data_inicio_preparo'] ?? null,
            'saiu_entrega' => $this->dto['data_saiu_entrega'] ?? null,
            'entregue' => $this->dto['data_entregue'] ?? null,
            'cancelamento' => $this->dto['data_cancelamento'] ?? null,
        ];
    }
}
