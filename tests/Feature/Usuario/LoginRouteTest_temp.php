<?php

use App\Models\UsuariosModel;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Testes de integraÃ§Ã£o para a rota POST /usuario/login
 *
 * Valida:
 * - AutenticaÃ§Ã£o com credenciais vÃ¡lidas
 * - ValidaÃ§Ã£o de campos obrigatÃ³rios
 * - SeguranÃ§a (senha nÃ£o retornada)
 * - Erros de autenticaÃ§Ã£o
 */

uses(FeatureTestTrait::class);

beforeEach(function () {
    // Cria um usuÃ¡rio de teste para os testes de login
    $this->model = new UsuariosModel();

    $this->testUser = [
        'nome' => 'Usuario Teste Login',
        'email' => 'logintest@example.com',
        'senha' => password_hash('senha123', PASSWORD_DEFAULT),
    ];

    $this->userId = $this->model->insert($this->testUser);
});

test('deve fazer login com sucesso usando credenciais validas', function () {
    // Arrange
    $loginData = [
        'email' => 'logintest@example.com',
        'senha' => 'senha123',
    ];

    // Act
    $result = $this->withBody(json_encode($loginData))
        ->withHeaders(['Content-Type' => 'application/json'])
        ->post('usuario/login');

    // Assert
    $result->assertStatus(200);
    $result->assertJSONFragment(['message' => 'Login realizado com sucesso']);

    $response = json_decode($result->getJSON(), true);

    expect($response)
        ->toBeArray()
        ->toHaveKey('message')
        ->toHaveKey('data')
        ->and($response['data'])->toHaveKey('id')
        ->and($response['data'])->toHaveKey('nome')
        ->and($response['data'])->toHaveKey('email')
        ->and($response['data']['email'])->toBe('logintest@example.com')
        ->and($response['data']['nome'])->toBe('Usuario Teste Login');
});

test('nao deve retornar senha no response de login', function () {
    // Arrange
    $loginData = [
        'email' => 'logintest@example.com',
        'senha' => 'senha123',
    ];

    // Act
    $result = $this->withBody(json_encode($loginData))
        ->withHeaders(['Content-Type' => 'application/json'])
        ->post('usuario/login');

    // Assert
    $response = json_decode($result->getJSON(), true);

    expect($response['data'])
        ->not->toHaveKey('senha')
        ->and($response['data'])->toHaveKey('id')
        ->and($response['data'])->toHaveKey('nome')
        ->and($response['data'])->toHaveKey('email');
});

test('deve retornar estrutura json correta no login bem-sucedido', function () {
    // Arrange
    $loginData = [
        'email' => 'logintest@example.com',
        'senha' => 'senha123',
    ];

    // Act
    $result = $this->withBody(json_encode($loginData))
        ->withHeaders(['Content-Type' => 'application/json'])
        ->post('usuario/login');

    // Assert
    $result->assertStatus(200);

    $response = json_decode($result->getJSON(), true);

    expect($response)
        ->toBeArray()
        ->toHaveKey('message')
        ->toHaveKey('data')
        ->and($response['message'])->toBeString()
        ->and($response['message'])->toBe('Login realizado com sucesso')
        ->and($response['data'])->toBeArray()
        ->and($response['data'])->toHaveKeys(['id', 'nome', 'email']);
});

test('deve retornar id do usuario como inteiro', function () {
    // Arrange
    $loginData = [
        'email' => 'logintest@example.com',
        'senha' => 'senha123',
    ];

    // Act
    $result = $this->withBody(json_encode($loginData))
        ->withHeaders(['Content-Type' => 'application/json'])
        ->post('usuario/login');

    // Assert
    $response = json_decode($result->getJSON(), true);

    expect($response['data']['id'])
        ->toBeInt()
        ->toBe($this->userId);
});

test('deve aceitar diferentes emails validos', function () {
    // Arrange - cria mais usuÃ¡rios com emails diferentes
    $emails = [
        'teste.usuario@example.com',
        'user123@test.io',
        'admin@pizzaria.com.br',
    ];

    foreach ($emails as $email) {
        $user = [
            'nome' => 'Usuario ' . $email,
            'email' => $email,
            'senha' => password_hash('senha123', PASSWORD_DEFAULT),
        ];
        $this->model->insert($user);

        // Act
        $loginData = ['email' => $email, 'senha' => 'senha123'];
        $result = $this->withBody(json_encode($loginData))
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post('usuario/login');

        // Assert
        $result->assertStatus(200);
        $response = json_decode($result->getJSON(), true);
        expect($response['data']['email'])->toBe($email);
    }
});

// ==================== TESTES DE VALIDAÃ‡ÃƒO E ERROS ====================
// Nota: Estes testes validam que os erros retornam JSON com status HTTP correto
// O ErrorTrait Ã© capturado no controller e convertido em resposta JSON

test('deve retornar erro 400 quando email nao for enviado', function () {
    // Arrange - dados sem email
    $loginData = [
        'senha' => 'senha123',
    ];

    // Act
    $result = $this->withBody(json_encode($loginData))
        ->withHeaders(['Content-Type' => 'application/json'])
        ->post('usuario/login');

    // Assert
    $result->assertStatus(400);
    $result->assertJSONFragment(['success' => false]);
    $result->assertJSONFragment(['code' => 'ERROR-LOGIN-001']);
    
    $response = json_decode($result->getJSON(), true);
    expect($response)
        ->toHaveKey('message')
        ->toHaveKey('errors')
        ->and($response['success'])->toBeFalse();
});


test('deve retornar erro 400 quando senha nao for enviada', function () {
    // Arrange - dados sem senha
    $loginData = [
        'email' => 'logintest@example.com',
    ];

    // Act
    $result = $this->withBody(json_encode($loginData))
        ->withHeaders(['Content-Type' => 'application/json'])
        ->post('usuario/login');

    // Assert
    $result->assertStatus(400);
    $result->assertJSONFragment(['success' => false]);
    $result->assertJSONFragment(['code' => 'ERROR-LOGIN-001']);
});

test('deve retornar erro 400 quando nenhum dado for enviado', function () {
    // Arrange - objeto vazio
    $loginData = [];

    // Act
    $result = $this->withBody(json_encode($loginData))
        ->withHeaders(['Content-Type' => 'application/json'])
        ->post('usuario/login');

    // Assert
    $result->assertStatus(400);
    $result->assertJSONFragment(['success' => false]);
    $result->assertJSONFragment(['code' => 'ERROR-LOGIN-001']);
});

test('deve retornar erro 400 quando email for string vazia', function () {
    // Arrange
    $loginData = [
        'email' => '',
        'senha' => 'senha123',
    ];

    // Act
    $result = $this->withBody(json_encode($loginData))
        ->withHeaders(['Content-Type' => 'application/json'])
        ->post('usuario/login');

    // Assert
    $result->assertStatus(400);
    $result->assertJSONFragment(['success' => false]);
    $result->assertJSONFragment(['code' => 'ERROR-LOGIN-001']);
});

test('deve retornar erro 400 quando senha for string vazia', function () {
    // Arrange
    $loginData = [
        'email' => 'logintest@example.com',
        'senha' => '',
    ];

    // Act
    $result = $this->withBody(json_encode($loginData))
        ->withHeaders(['Content-Type' => 'application/json'])
        ->post('usuario/login');

    // Assert
    $result->assertStatus(400);
    $result->assertJSONFragment(['success' => false]);
    $result->assertJSONFragment(['code' => 'ERROR-LOGIN-001']);
});

test('deve retornar erro 401 ao fazer login com senha incorreta', function () {
    // Arrange
    $loginData = [
        'email' => 'logintest@example.com',
        'senha' => 'senhaErrada123',
    ];

    // Act
    $result = $this->withBody(json_encode($loginData))
        ->withHeaders(['Content-Type' => 'application/json'])
        ->post('usuario/login');

    // Assert
    $result->assertStatus(401);
    $result->assertJSONFragment(['success' => false]);
    $result->assertJSONFragment(['code' => 'ERROR-LOGIN-002']);
    
    $response = json_decode($result->getJSON(), true);
    expect($response['message'])->toContain('UsuÃ¡rio ou senha invÃ¡lidos');
});

test('deve retornar erro 401 ao fazer login com email inexistente', function () {
    // Arrange
    $loginData = [
        'email' => 'emailnaoexiste@example.com',
        'senha' => 'senha123',
    ];

    // Act
    $result = $this->withBody(json_encode($loginData))
        ->withHeaders(['Content-Type' => 'application/json'])
        ->post('usuario/login');

    // Assert
    $result->assertStatus(401);
    $result->assertJSONFragment(['success' => false]);
    $result->assertJSONFragment(['code' => 'ERROR-LOGIN-002']);
    
    $response = json_decode($result->getJSON(), true);
    expect($response['message'])->toContain('UsuÃ¡rio ou senha invÃ¡lidos');
});

test('deve retornar erro 401 com email e senha ambos incorretos', function () {
    // Arrange
    $loginData = [
        'email' => 'usuarioinvalido@test.com',
        'senha' => 'senhaInvalida',
    ];

    // Act
    $result = $this->withBody(json_encode($loginData))
        ->withHeaders(['Content-Type' => 'application/json'])
        ->post('usuario/login');

    // Assert
    $result->assertStatus(401);
    $result->assertJSONFragment(['success' => false]);
    $result->assertJSONFragment(['code' => 'ERROR-LOGIN-002']);
});

test('deve retornar erro quando body nao for json valido', function () {
    // Arrange - string invÃ¡lida como JSON
    $invalidJson = 'isto-nao-e-json';

    // Act
    $result = $this->withBody($invalidJson)
        ->withHeaders(['Content-Type' => 'application/json'])
        ->post('usuario/login');

    // Assert - pode retornar 400 ou 500 dependendo de onde o erro ocorre
    expect($result->getStatusCode())->toBeGreaterThanOrEqual(400);
});

test('deve ignorar campos extras e fazer login com sucesso', function () {
    // Arrange - adiciona campos extras
    $loginData = [
        'email' => 'logintest@example.com',
        'senha' => 'senha123',
        'campoExtra' => 'valor',
        'outroExtra' => 123,
    ];

    // Act
    $result = $this->withBody(json_encode($loginData))
        ->withHeaders(['Content-Type' => 'application/json'])
        ->post('usuario/login');

    // Assert - campos extras devem ser ignorados e login deve funcionar
    $result->assertStatus(200);
    $result->assertJSONFragment(['success' => true]);
});

test('deve retornar erro 400 com tipo de dados incorretos', function () {
    // Arrange - email como nÃºmero e senha como array
    $loginData = [
        'email' => 12345,
        'senha' => ['array', 'invalido'],
    ];

    // Act
    $result = $this->withBody(json_encode($loginData))
        ->withHeaders(['Content-Type' => 'application/json'])
        ->post('usuario/login');

    // Assert
    $result->assertStatus(400);
    $result->assertJSONFragment(['success' => false]);
});

test('deve retornar erro 400 com email null', function () {
    // Arrange
    $loginData = [
        'email' => null,
        'senha' => 'senha123',
    ];

    // Act
    $result = $this->withBody(json_encode($loginData))
        ->withHeaders(['Content-Type' => 'application/json'])
        ->post('usuario/login');

    // Assert
    $result->assertStatus(400);
    $result->assertJSONFragment(['success' => false]);
    $result->assertJSONFragment(['code' => 'ERROR-LOGIN-001']);
});

test('deve retornar erro 400 com senha null', function () {
    // Arrange
    $loginData = [
        'email' => 'logintest@example.com',
        'senha' => null,
    ];

    // Act
    $result = $this->withBody(json_encode($loginData))
        ->withHeaders(['Content-Type' => 'application/json'])
        ->post('usuario/login');

    // Assert
    $result->assertStatus(400);
    $result->assertJSONFragment(['success' => false]);
    $result->assertJSONFragment(['code' => 'ERROR-LOGIN-001']);
});

// ==================== TESTES DE STATUS HTTP ====================
// ValidaÃ§Ã£o de que os erros retornam status HTTP apropriados
