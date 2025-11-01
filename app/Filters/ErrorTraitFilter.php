<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\ErrorTrait;

/**
 * Filtro para capturar exceções ErrorTrait e convertê-las em respostas JSON
 * Este filtro garante que erros retornem JSON mesmo em requisições HTTP
 */
class ErrorTraitFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Não faz nada antes da requisição
        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Não precisa fazer nada depois, pois as exceções são tratadas pelo handler
        return $response;
    }
}
