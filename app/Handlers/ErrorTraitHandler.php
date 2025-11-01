<?php

namespace App\Handlers;

use CodeIgniter\Debug\ExceptionHandlerInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\ErrorTrait;
use Throwable;

/**
 * Handler customizado para exceções do tipo ErrorTrait
 * Retorna respostas JSON formatadas seguindo padrões RESTful
 * 
 * Características:
 * - Retorna status HTTP apropriado (400, 401, 404, 500, etc)
 * - Sempre retorna JSON
 * - Headers CORS apropriados
 * - Logging de erros
 */
class ErrorTraitHandler implements ExceptionHandlerInterface
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function handle(
        Throwable $exception,
        RequestInterface $request,
        ResponseInterface $response,
        int $statusCode,
        int $exitCode
    ): void {
        // Garante que é uma instância de ErrorTrait
        if ($exception instanceof ErrorTrait) {
            $payload = $exception->getPayload();
            $httpStatusCode = $exception->getStatusCode();

            // Log do erro se configurado
            if ($this->config->log && !in_array($httpStatusCode, $this->config->ignoreCodes)) {
                log_message('error', sprintf(
                    '[ErrorTrait] Code: %s | Status: %d | Message: %s | File: %s:%d',
                    $exception->getErrorCode(),
                    $httpStatusCode,
                    $exception->getMessage(),
                    $exception->getFile(),
                    $exception->getLine()
                ));

                // Log erros adicionais se existirem
                $errors = $exception->getErrors();
                if (!empty($errors)) {
                    log_message('error', '[ErrorTrait] Validation Errors: ' . json_encode($errors));
                }
            }

            // Limpa qualquer output buffer que possa existir
            if (ob_get_level() > 0) {
                ob_end_clean();
            }

            // Configura headers apropriados para API RESTful
            $response->setStatusCode($httpStatusCode);
            $response->setContentType('application/json', 'utf-8');

            // Adiciona headers de segurança
            $response->setHeader('X-Content-Type-Options', 'nosniff');
            $response->setHeader('X-Frame-Options', 'DENY');

            // Define o corpo da resposta como JSON
            $response->setBody(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            // Envia a resposta imediatamente
            $response->send();

            // Termina a execução
            exit($exitCode);
        }
    }
}
