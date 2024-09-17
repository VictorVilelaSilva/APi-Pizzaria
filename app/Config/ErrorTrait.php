<?php

namespace Config;

/**
 * Classe para tratamento de erros no controller. Para seguir o fluxo do code igniter, retornando apenas $this->response no controller
 * @param string|array $payload Array ou String com a mensagem de erro para ser retornada no controller
 * @param int $statusCode Código de status HTTP
 */
class ErrorTrait extends \Exception
{
    protected string|array $payload;
    protected int $statusCode;

    public function __construct(string $code, $errors = [], ?string $detail = null)
    {
        ResponseTrait::Error($code, $errors, $detail);
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
