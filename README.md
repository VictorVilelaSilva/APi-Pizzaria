# API Pizzaria

Esta é uma API de Pizzaria desenvolvida em PHP utilizando o framework CodeIgniter.

## Requisitos

- PHP instalado
- Composer instalado

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
    
> [!IMPORTANT]
> Caso tenha problemas ao executar os comandos do Composer, copie o arquivo `php.ini` do repositório e substitua pelo seu arquivo `php.ini` local.
> O arquivo `php.ini` localiza-se no mesmo diretório que foi adicionado o PHP nas variáveis de ambiente do sistema operacional.

## Iniciando a API

Para iniciar a API, execute o seguinte comando:
```bash
php spark serve
```

A API estará disponível em `http://localhost:8080`.

## Endpoints
* Login
POST - http://localhost:8080/usuario/login
* Cadastro
POST - http://localhost:8080/usuario/register


