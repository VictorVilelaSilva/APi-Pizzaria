<?php

use App\Models\UsuariosModel;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Testes de integração para a rota POST /usuario/login
 *
 * Valida:
 * - Autenticação com credenciais válidas
 * - Validação de campos obrigatórios
 * - Segurança (senha não retornada)
 * - Erros de autenticação
 */

uses(FeatureTestTrait::class);

beforeEach(function () {
    // Cria um usuário de teste para os testes de login
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
    // Arrange - cria mais usuários com emails diferentes
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

// ==================== TESTES DE VALIDAÇÃO E ERROS ====================
// Nota: Estes testes validam que os erros retornam JSON com status HTTP correto
// O ErrorTrait é capturado no controller e convertido em resposta JSON

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
    expect($response['message'])->toContain('Usuário ou senha inválidos');
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
    expect($response['message'])->toContain('Usuário ou senha inválidos');
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

test('deve lancar excecao quando body nao for json valido', function () {
    // Arrange - string inválida como JSON
    $invalidJson = 'isto-nao-e-json';

    // Act & Assert - deve lançar HTTPException pois JSON inválido é capturado pelo framework
    $this->withBody($invalidJson)
        ->withHeaders(['Content-Type' => 'application/json'])
        ->post('usuario/login');
})->throws(\CodeIgniter\HTTP\Exceptions\HTTPException::class);

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
    $result->assertJSONFragment(['message' => 'Login realizado com sucesso']);

    $response = json_decode($result->getJSON(), true);
    expect($response)
        ->toHaveKey('data')
        ->and($response['data']['email'])->toBe('logintest@example.com');
});

test('deve retornar erro ao processar tipo de dados incorretos', function () {
    // Arrange - email como número e senha como array
    $loginData = [
        'email' => 12345,
        'senha' => ['array', 'invalido'],
    ];

    // Act
    $result = $this->withBody(json_encode($loginData))
        ->withHeaders(['Content-Type' => 'application/json'])
        ->post('usuario/login');

    // Assert - valida que retorna erro (pode ser 401 porque não encontra usuário 12345)
    expect($result->isOK())->toBeFalse();
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

// ==================== TESTES DE STATUS HTTP E ESTRUTURA ====================
// Validação de que os erros retornam status HTTP apropriados e estrutura JSON correta

test('resposta de erro deve conter estrutura JSON padronizada', function () {
    // Arrange
    $loginData = []; // Vai gerar erro de validação

    // Act
    $result = $this->withBody(json_encode($loginData))
        ->withHeaders(['Content-Type' => 'application/json'])
        ->post('usuario/login');

    // Assert
    $result->assertStatus(400);
    $result->assertJSONFragment(['success' => false]);

    $response = json_decode($result->getJSON(), true);
    expect($response)
        ->toBeArray()
        ->toHaveKeys(['success', 'code', 'message', 'errors'])
        ->and($response['success'])->toBeFalse()
        ->and($response['code'])->toBeString()
        ->and($response['message'])->toBeString()
        ->and($response['errors'])->toBeArray();
});

test('erros de validacao e autenticacao devem ter codigos diferentes', function () {
    // Testa erro de validação (400)
    $resultValidation = $this->withBody(json_encode([]))
        ->withHeaders(['Content-Type' => 'application/json'])
        ->post('usuario/login');

    $resultValidation->assertStatus(400);
    $validationResponse = json_decode($resultValidation->getJSON(), true);
    expect($validationResponse['code'])->toBe('ERROR-LOGIN-001');

    // Testa erro de autenticação (401)
    $resultAuth = $this->withBody(json_encode([
        'email' => 'logintest@example.com',
        'senha' => 'senhaErrada',
    ]))
        ->withHeaders(['Content-Type' => 'application/json'])
        ->post('usuario/login');

    $resultAuth->assertStatus(401);
    $authResponse = json_decode($resultAuth->getJSON(), true);
    expect($authResponse['code'])->toBe('ERROR-LOGIN-002');
});
