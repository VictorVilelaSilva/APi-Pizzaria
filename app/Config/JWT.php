<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class JWT extends BaseConfig
{
    /**
     * Chave secreta para assinar os tokens JWT
     * IMPORTANTE: Altere isso em produção!
     */
    public string $secretKey = 'your-secret-key-change-in-production';

    /**
     * Algoritmo de criptografia
     */
    public string $algorithm = 'HS256';

    /**
     * Tempo de expiração do token em segundos
     * Padrão: 3600 segundos (1 hora)
     */
    public int $expirationTime = 3600;

    /**
     * Tempo de expiração do refresh token em segundos
     * Padrão: 2592000 segundos (30 dias)
     */
    public int $refreshExpirationTime = 2592000;

    public function __construct()
    {
        parent::__construct();

        // Sobrescreve com variável de ambiente se existir
        if ($key = getenv('JWT_SECRET_KEY')) {
            $this->secretKey = $key;
        }

        if ($exp = getenv('JWT_EXPIRATION_TIME')) {
            $this->expirationTime = (int) $exp;
        }
    }
}
