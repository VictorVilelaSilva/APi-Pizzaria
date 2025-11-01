<?php

namespace App\Filters;

use App\Libraries\JWT\JWTService;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class JWTAuthFilter implements FilterInterface
{
    /**
     * Antes da requisição - valida o token JWT
     *
     * @param RequestInterface $request
     * @param array|null $arguments
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $jwtService = new JWTService();

        // Extrai o token do header Authorization
        $authHeader = $request->getHeaderLine('Authorization');
        $token = $jwtService->extractToken($authHeader);

        if (!$token) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON([
                    'error' => true,
                    'message' => 'Token não fornecido'
                ]);
        }

        // Valida o token
        $decoded = $jwtService->validateToken($token);

        if (!$decoded) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON([
                    'error' => true,
                    'message' => 'Token inválido ou expirado'
                ]);
        }

        // Adiciona os dados do usuário à requisição
        $request->userData = $decoded;

        return $request;
    }

    /**
     * Depois da requisição
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array|null $arguments
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nada a fazer após a requisição
        return $response;
    }
}
