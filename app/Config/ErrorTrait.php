<?php

namespace Config;

use App\Handlers\ErrosCode;

/**
 * Classe para tratamento de erros no controller.
 * Comporta-se como uma exceção normal que pode ser capturada e tratada.
 * Retorna respostas JSON com status HTTP apropriados para APIs RESTful.
 * 
 * @param string $code Código do erro
 * @param array $errors Array de erros adicionais
 * @param string|null $detail Detalhes adicionais do erro
 */
class ErrorTrait extends \Exception
{
    protected string $errorCode;
    protected array $errors;
    protected ?string $detail;
    protected int $statusCode;
    protected array $payload;

    /**
     * Mapeamento de códigos de erro para status HTTP
     * Baseado em padrões REST:
     * - 400: Bad Request (erro de validação/entrada)
     * - 401: Unauthorized (falha de autenticação)
     * - 403: Forbidden (sem permissão)
     * - 404: Not Found (recurso não encontrado)
     * - 422: Unprocessable Entity (validação semântica)
     * - 500: Internal Server Error (erro do servidor)
     */
    protected array $httpStatusMap = [
        // Erros de Login/Autenticação
        'ERROR-LOGIN-001' => 400, // Validação falhou
        'ERROR-LOGIN-002' => 401, // Credenciais inválidas

        // Erros de Registro
        'ERROR-REGISTER-001' => 400, // Validação falhou
        'ERROR-REGISTER-002' => 422, // Email já existe

        // Erros genéricos (padrão é 500)
    ];

    public function __construct(string $code, $errors = [], ?string $detail = null)
    {
        $this->errorCode = $code;
        $this->errors = $errors;
        $this->detail = $detail;

        // Define o status HTTP baseado no código de erro
        $this->statusCode = $this->getHttpStatusFromCode($code);

        // Busca a mensagem do erro
        $errorMessage = (new ErrosCode())->getError($code);
        if (!$errorMessage) {
            $errorMessage = $code;
        }

        // Monta o payload da resposta
        $this->payload = [
            'success' => false,
            'code'    => $code,
            'message' => $errorMessage,
        ];

        if (!empty($errors)) {
            $this->payload['errors'] = $errors;
        }

        // Chama o construtor da Exception com a mensagem
        parent::__construct($errorMessage, 0);
    }

    /**
     * Determina o status HTTP apropriado baseado no código de erro
     * 
     * @param string $code Código do erro
     * @return int Status HTTP
     */
    protected function getHttpStatusFromCode(string $code): int
    {
        // Se existe mapeamento específico, usa ele
        if (isset($this->httpStatusMap[$code])) {
            return $this->httpStatusMap[$code];
        }

        // Heurísticas baseadas no prefixo/padrão do código
        if (str_contains($code, 'LOGIN') || str_contains($code, 'AUTH')) {
            return 401; // Unauthorized
        }

        if (str_contains($code, 'VALIDATION') || str_contains($code, 'INVALID')) {
            return 400; // Bad Request
        }

        if (str_contains($code, 'NOTFOUND') || str_contains($code, 'NOT-FOUND')) {
            return 404; // Not Found
        }

        if (str_contains($code, 'FORBIDDEN') || str_contains($code, 'PERMISSION')) {
            return 403; // Forbidden
        }

        // Padrão para erros não mapeados
        return 500; // Internal Server Error
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Permite definir um status HTTP customizado
     * 
     * @param int $statusCode Status HTTP
     * @return self
     */
    public function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }
}
