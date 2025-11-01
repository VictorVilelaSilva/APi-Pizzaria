<?php

namespace App\Libraries\JWT;

use Config\JWT as JWTConfig;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JWTService
{
    private string $secretKey;
    private string $algorithm;
    private int $expirationTime;

    public function __construct()
    {
        $config = new JWTConfig();
        $this->secretKey = $config->secretKey;
        $this->algorithm = $config->algorithm;
        $this->expirationTime = $config->expirationTime;
    }

    /**
     * Gera um token JWT
     *
     * @param array $payload Dados do usuário (id, email, etc)
     * @return string Token JWT
     */
    public function generateToken(array $payload): string
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + $this->expirationTime;

        $data = [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'data' => $payload
        ];

        return JWT::encode($data, $this->secretKey, $this->algorithm);
    }

    /**
     * Valida e decodifica um token JWT
     *
     * @param string $token Token JWT
     * @return object|null Dados decodificados ou null se inválido
     */
    public function validateToken(string $token): ?object
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, $this->algorithm));
            return $decoded->data;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Extrai o token do header Authorization
     *
     * @param string|null $authHeader Header Authorization
     * @return string|null Token extraído
     */
    public function extractToken(?string $authHeader): ?string
    {
        if (!$authHeader) {
            return null;
        }

        // Remove "Bearer " do início
        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Define o tempo de expiração em segundos
     *
     * @param int $seconds Segundos
     * @return self
     */
    public function setExpirationTime(int $seconds): self
    {
        $this->expirationTime = $seconds;
        return $this;
    }
}
