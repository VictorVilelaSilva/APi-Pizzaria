<?php

use App\Models\UsuariosModel;

/**
 * Testes de integração para o UsuariosModel
 *
 * Este arquivo demonstra a sintaxe do Pest PHP para testes de integração.
 * Os testes usam banco de dados em memória (SQLite) configurado automaticamente.
 */

beforeEach(function () {
    $this->model = new UsuariosModel();
});

test('pode buscar usuario por id', function () {
    // Arrange
    $usuario = [
        'nome' => 'Usuario Teste',
        'email' => 'teste@example.com',
        'senha' => password_hash('senha123', PASSWORD_DEFAULT),
    ];

    $id = $this->model->insert($usuario);

    // Act
    $resultado = $this->model->findById($id);

    // Assert
    expect($resultado)
        ->toBeArray()
        ->toHaveKey('nome')
        ->toHaveKey('email')
        ->and($resultado['nome'])->toBe('Usuario Teste')
        ->and($resultado['email'])->toBe('teste@example.com');
});

test('pode buscar usuario por email', function () {
    // Arrange
    $usuario = [
        'nome' => 'Usuario Email',
        'email' => 'emailteste@example.com',
        'senha' => password_hash('senha123', PASSWORD_DEFAULT),
    ];

    $this->model->insert($usuario);

    // Act
    $resultado = $this->model->findByEmail('emailteste@example.com');

    // Assert
    expect($resultado)
        ->toBeArray()
        ->and($resultado['email'])->toBe('emailteste@example.com')
        ->and($resultado['nome'])->toBe('Usuario Email');
});

test('retorna null quando usuario nao existe por id', function () {
    // Act
    $resultado = $this->model->findById('99999999');

    // Assert
    expect($resultado)->toBeNull();
});

test('retorna null quando usuario nao existe por email', function () {
    // Act
    $resultado = $this->model->findByEmail('naexiste@example.com');

    // Assert
    expect($resultado)->toBeNull();
});

test('pode criar um novo usuario', function () {
    // Arrange
    $usuario = [
        'nome' => 'Novo Usuario',
        'email' => 'novousuario@example.com',
        'senha' => password_hash('senha123', PASSWORD_DEFAULT),
    ];

    // Act
    $id = $this->model->insert($usuario);
    $resultado = $this->model->find($id);

    // Assert
    expect($id)->toBeInt()
        ->and($resultado)->toBeArray()
        ->and($resultado['nome'])->toBe('Novo Usuario')
        ->and($resultado['email'])->toBe('novousuario@example.com');
});

// Teste simples que funciona sem banco de dados
test('model possui tabela correta configurada', function () {
    $model = new UsuariosModel();

    // Usando reflexão para acessar propriedade protegida (apenas para teste)
    $reflection = new ReflectionClass($model);
    $property = $reflection->getProperty('table');
    $property->setAccessible(true);

    expect($property->getValue($model))->toBe('usuarios');
});

test('model possui campos permitidos configurados', function () {
    $model = new UsuariosModel();

    $reflection = new ReflectionClass($model);
    $property = $reflection->getProperty('allowedFields');
    $property->setAccessible(true);
    $allowedFields = $property->getValue($model);

    expect($allowedFields)
        ->toBeArray()
        ->toContain('nome')
        ->toContain('email')
        ->toContain('senha');
});
