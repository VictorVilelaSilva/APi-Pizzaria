<?php

if (!function_exists('getAuthenticatedUser')) {
    /**
     * Retorna os dados do usuário autenticado via JWT
     *
     * @return object|null
     */
    function getAuthenticatedUser(): ?object
    {
        $request = service('request');
        return $request->userData ?? null;
    }
}

if (!function_exists('getAuthUserId')) {
    /**
     * Retorna o ID do usuário autenticado
     *
     * @return int|null
     */
    function getAuthUserId(): ?int
    {
        $user = getAuthenticatedUser();
        return $user->id ?? null;
    }
}

if (!function_exists('getAuthUserEmail')) {
    /**
     * Retorna o email do usuário autenticado
     *
     * @return string|null
     */
    function getAuthUserEmail(): ?string
    {
        $user = getAuthenticatedUser();
        return $user->email ?? null;
    }
}

if (!function_exists('isAuthenticated')) {
    /**
     * Verifica se existe um usuário autenticado
     *
     * @return bool
     */
    function isAuthenticated(): bool
    {
        return getAuthenticatedUser() !== null;
    }
}
