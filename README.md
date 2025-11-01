# API Pizzaria

Esta é uma API de Pizzaria desenvolvida em PHP utilizando o framework CodeIgniter com banco de dados MySQL.

## Requisitos

- PHP 8.1 ou superior
- Composer instalado
- Docker e Docker Compose (para banco de dados)

## Instalação

1. Clone o repositório:
    ```bash
    git clone <URL_DO_REPOSITORIO>
    ```
2. Navegue até o diretório do projeto:
    ```bash
    cd APi-Pizzaria
    ```
3. Instale as dependências do Composer:
    ```bash
    composer install
    ```

4. Configure o arquivo de ambiente:
    ```bash
    cp .env.example .env
    ```
    Edite o arquivo `.env` se necessário para ajustar as configurações do banco de dados.

> [!IMPORTANT]
> Caso tenha problemas ao executar os comandos do Composer, copie o arquivo `php.ini` do repositório e substitua pelo seu arquivo `php.ini` local.
> O arquivo `php.ini` localiza-se no mesmo diretório que foi adicionado o PHP nas variáveis de ambiente do sistema operacional.

## Configuração do Banco de Dados

### Usando Docker (Recomendado)

1. Inicie os containers do MySQL e phpMyAdmin:
    ```bash
    docker-compose up -d
    ```

2. Verifique se os containers estão rodando:
    ```bash
    docker-compose ps
    ```

3. Execute as migrations para criar as tabelas:
    ```bash
    php spark migrate
    ```

### Acessando o banco de dados

- **MySQL**: `localhost:3306`
  - Usuário: `pizzaria_user`
  - Senha: `root`
  - Database: `pizzaria_db`

- **phpMyAdmin**: `http://localhost:8081`
  - Usuário: `pizzaria_user`
  - Senha: `root`

### Comandos úteis do Docker

```bash
# Iniciar os containers
docker-compose up -d

# Parar os containers
docker-compose down

# Ver logs dos containers
docker-compose logs -f

# Reiniciar os containers
docker-compose restart

# Parar e remover containers e volumes (apaga os dados do banco)
docker-compose down -v
```

## Iniciando a API

Para iniciar a API, execute o seguinte comando:
```bash
php spark serve
```

A API estará disponível em `http://localhost:8080`.

## Endpoints
* Login
POST - http://localhost:8080/usuario/login
    Body:
    ```json
     {
        "email": "victorasadasdasd@gmail.com",
        "senha": "34222750v"
    }
    
    ```
* Cadastro
POST - http://localhost:8080/usuario/register
    Body:
    ```json
    {
        "nome": "",
        "email":"@gmail.com",
        "senha": "",
        "logradouro": "",
        "numero": "",
        "bairro": "",
        "cidade": "",
        "estado": "M",
        "cep": ""
    }
    ```


