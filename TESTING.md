# Guia de Testes com Pest PHP

Este projeto usa [Pest PHP](https://pestphp.com/) para testes automatizados no CodeIgniter 4.

## Instalação

O Pest já está instalado no projeto. Para verificar:

```bash
vendor/bin/pest --version
```

## Executando os Testes

### Todos os testes
```bash
composer test
# ou
vendor/bin/pest
```

### Apenas testes unitários
```bash
composer test:unit
# ou
vendor/bin/pest --testsuite=unit
```

### Apenas testes de feature
```bash
composer test:feature
# ou
vendor/bin/pest --testsuite=feature
```

### Com cobertura de código
```bash
composer test:coverage
```

## Estrutura de Testes

```
tests/
├── Feature/              # Testes de funcionalidades (endpoints, integração)
│   ├── ExampleTest.php
│   └── UsuarioControllerTest.php
├── Unit/                 # Testes unitários (models, classes)
│   ├── ExampleTest.php
│   └── UsuariosModelTest.php
├── Pest.php             # Configuração global do Pest
└── TestCase.php         # Classe base para testes
```

## Exemplos de Testes

### Teste Unitário Simples

```php
<?php

use App\Models\UsuariosModel;

test('model possui tabela correta configurada', function () {
    $model = new UsuariosModel();

    $reflection = new ReflectionClass($model);
    $property = $reflection->getProperty('table');
    $property->setAccessible(true);

    expect($property->getValue($model))->toBe('usuarios');
});
```

### Teste com BeforeEach

```php
<?php

beforeEach(function () {
    $this->model = new UsuariosModel();
});

test('pode criar um novo usuario', function () {
    $usuario = [
        'nome' => 'Novo Usuario',
        'email' => 'novousuario@example.com',
        'senha' => password_hash('senha123', PASSWORD_DEFAULT),
    ];

    $id = $this->model->insert($usuario);

    expect($id)->toBeInt();
});
```

### Teste de Feature (API)

```php
<?php

use CodeIgniter\Test\FeatureTestTrait;

uses(FeatureTestTrait::class);

test('endpoint de registro cria novo usuario', function () {
    $novoUsuario = [
        'nome' => 'Usuario Novo',
        'email' => 'novousuario@example.com',
        'senha' => 'senha123',
    ];

    $resultado = $this->withBodyFormat('json')
        ->post('api/register', $novoUsuario);

    $resultado->assertStatus(200);
    $resultado->assertJSONFragment(['message' => 'Registro realizado com sucesso']);
});
```

## Expectations Disponíveis

O Pest oferece uma API fluente de expectations:

```php
expect($value)
    ->toBe('exact match')
    ->toBeString()
    ->toBeInt()
    ->toBeArray()
    ->toBeNull()
    ->toBeTrue()
    ->toBeFalse()
    ->toBeEmpty()
    ->toContain('substring')
    ->toHaveKey('key')
    ->toHaveCount(3)
    ->and($otherValue)->toBe('chained');
```

## Skipping Testes

Para pular testes que ainda não estão prontos:

```php
test('teste em desenvolvimento', function () {
    // ...
})->skip('Aguardando implementação da feature X');
```

## Organizando Testes

### Grupos
```php
test('teste importante', function () {
    // ...
})->group('critical');

// Executar apenas grupo
vendor/bin/pest --group=critical
```

### Datasets
```php
test('validação de email', function ($email, $expected) {
    expect(filter_var($email, FILTER_VALIDATE_EMAIL))->toBe($expected);
})->with([
    ['test@example.com', 'test@example.com'],
    ['invalid-email', false],
]);
```

## Hooks

- `beforeEach()`: Executado antes de cada teste
- `afterEach()`: Executado depois de cada teste
- `beforeAll()`: Executado uma vez antes de todos os testes
- `afterAll()`: Executado uma vez depois de todos os testes

## Configuração do Banco de Dados para Testes

Para os testes que interagem com o banco de dados funcionarem, você precisa configurar o ambiente de testes no CodeIgniter:

1. Configure as variáveis de ambiente no `phpunit.xml`
2. Use migrations e seeders para preparar o banco de dados
3. Use o trait `DatabaseTestTrait` do CodeIgniter

## Recursos Adicionais

- [Documentação do Pest](https://pestphp.com/docs)
- [Documentação de Testes do CodeIgniter 4](https://codeigniter.com/user_guide/testing/index.html)
- [Expectations API](https://pestphp.com/docs/expectations)

## Próximos Passos

1. Configure o banco de dados de testes
2. Remova os `.skip()` dos testes quando estiver pronto
3. Adicione mais testes para cobrir suas funcionalidades
4. Configure CI/CD para executar os testes automaticamente
