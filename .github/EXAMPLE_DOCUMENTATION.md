# Exemplo de Documentação Gerada

Este arquivo mostra como ficará a estrutura da documentação criada automaticamente no Notion.

## 📊 Estrutura das Páginas

```
Pizzaria API (Página Principal no Seu Workspace)
│
└── Task - PDB-4638 - Adicionar Gestão de Usuários
    ├── Seção: Visão Geral da Task
    │   ├── Summary (resumo da implementação)
    │   ├── Contexto de Negócio
    │   └── Informações do Ticket
    │
    ├── Seção: Detalhes Técnicos
    │   ├── Alterações Realizadas (lista)
    │   ├── Endpoints da API (links para sub-páginas)
    │   ├── Alterações no Banco de Dados
    │   ├── Pontos de Integração
    │   ├── Arquivos Modificados (toggle)
    │   └── Histórico de Commits (toggle)
    │
    ├── Seção: Notas de Deploy
    │   ├── Variáveis de Ambiente
    │   ├── Alterações de Configuração
    │   ├── Passos de Migração
    │   └── Dependências
    │
    └── Sub-páginas (uma para cada endpoint):
        ├── POST - /createUser
        │   ├── Descrição
        │   ├── Localização da Rota
        │   ├── Parâmetros da Requisição
        │   ├── Headers da Requisição
        │   ├── Exemplo de Requisição
        │   ├── Resposta de Sucesso
        │   ├── Resposta de Erro
        │   ├── Regras de Negócio
        │   └── Validações
        │
        ├── PUT - /updateUser
        │   └── (mesma estrutura)
        │
        └── DELETE - /deleteUser
            └── (mesma estrutura)
```

## 📝 Exemplo de Conteúdo

### Página Principal

````markdown
# Task - PDB-4638 - Adicionar Gestão de Usuários

## 📋 Visão Geral da Task

Esta task implementou um sistema completo de gestão de usuários para a API da pizzaria, incluindo funcionalidades de criação, atualização e exclusão de usuários com validações de CPF/CNPJ e integração com o sistema de autenticação.

---

📊 **Informações da Task**
- **Ticket**: PDB-4638
- **Developer**: VictorVilelaSilva
- **Data**: 31/10/2025
- **Status**: Completed

🔗 **Pull Request**: [#42](https://github.com/user/repo/pull/42)

## 💼 Contexto de Negócio

O sistema de pizzaria necessitava de um módulo robusto de gestão de usuários para permitir que administradores gerenciem perfis de funcionários, clientes e gerentes. Esta implementação centraliza todas as operações de usuário em uma única API, garantindo validações consistentes e integridade dos dados.

---

## 🔧 Detalhes Técnicos

### Alterações Realizadas

- Criado módulo `AdminConta` com Controllers, Services, Models e DTOs
- Implementado sistema de validação com `ManagerValidation`
- Adicionado suporte a transações de banco de dados
- Criado entidades reutilizáveis (PersonEntity, AddressEntity, PhoneEntity)
- Implementado soft deletes e logging automático de mudanças
- Adicionado filtros de autenticação e autorização

### Endpoints da API Criados/Modificados

Esta task envolveu os seguintes endpoints (clique para ver documentação detalhada):

- **POST** `/createUser`
- **PUT** `/updateUser`
- **DELETE** `/deleteUser`

### Alterações no Banco de Dados

- Criada migration `PDB4638/bankop/ddl/create_users_table`
- Adicionadas colunas: `document_id`, `user_type`, `status`
- Criados índices para `document_id` e `email`

### Pontos de Integração

- Integração com serviço de autenticação OAuth
- Validação de CPF/CNPJ via helper `validar_cpfcnpj()`
- Envio de email de boas-vindas via message database

### 📁 Ver arquivos modificados (47 arquivos)
```
app/Modules/AdminConta/Controllers/ManagerController.php
app/Modules/AdminConta/Services/ManagerService.php
app/Modules/AdminConta/Models/UserModel.php
app/Modules/AdminConta/DTO/Manager/CreateUserDTO.php
...
```

### 📝 Ver histórico de commits
```
a1b2c3d - feat: add user management endpoints (VictorVilelaSilva, 2 hours ago)
b2c3d4e - refactor: extract validation logic to service (VictorVilelaSilva, 1 hour ago)
c3d4e5f - docs: add API documentation (VictorVilelaSilva, 30 minutes ago)
```

---

## 🚀 Notas de Deploy

### Variáveis de Ambiente

- `USER_EMAIL_ENABLED` - Habilitar envio de emails (true/false)
- `USER_DEFAULT_PASSWORD` - Senha padrão para novos usuários

### Alterações de Configuração

- Nenhuma configuração especial necessária

### Passos de Migração

1. Executar migration: `php spark migrate:run:branch -n PDB4638`
2. Verificar que a tabela `users` foi criada
3. Popular dados iniciais se necessário

### Dependências

- Nenhuma nova dependência adicionada
````

### Sub-página: POST - /createUser

````markdown
# POST - /createUser

Endpoint para criação de novos usuários no sistema da pizzaria.

---

## 📍 Localização da Rota

**File**: `app/Modules/AdminConta/Controllers/ManagerController.php`

---

## 📥 Parâmetros da Requisição

```json
{
  "name": "string - Nome completo do usuário",
  "email": "string - Email válido e único",
  "document_id": "string - CPF ou CNPJ válido",
  "user_type": "string - Tipo do usuário (admin, manager, employee, customer)",
  "phone": "string - Telefone no formato (XX) XXXXX-XXXX",
  "address": "object - Objeto com dados de endereço"
}
```

## 📋 Headers da Requisição

```json
{
  "Authorization": "Bearer <token>",
  "Content-Type": "application/json"
}
```

## 📤 Exemplo de Requisição

```json
{
  "name": "João Silva",
  "email": "joao.silva@example.com",
  "document_id": "12345678900",
  "user_type": "employee",
  "phone": "(11) 98765-4321",
  "address": {
    "street": "Rua das Flores",
    "number": "123",
    "city": "São Paulo",
    "state": "SP",
    "zip_code": "01234-567"
  }
}
```

## ✅ Resposta de Sucesso (200)

```json
{
  "data": {
    "id": 42,
    "uuid": "a1b2c3d4-e5f6-7890-abcd-ef1234567890",
    "name": "João Silva",
    "email": "joao.silva@example.com",
    "document_id": "12345678900",
    "user_type": "employee",
    "status": "active",
    "created_at": "2025-10-31T10:30:00Z"
  },
  "message": "Usuário criado com sucesso"
}
```

## ❌ Resposta de Erro (4xx/5xx)

```json
{
  "error": "CPF/CNPJ inválido",
  "code": "EMANAGER023",
  "field": "document_id"
}
```

## 💼 Regras de Negócio

- Email deve ser único no sistema
- CPF/CNPJ deve ser válido segundo algoritmo de validação
- Usuários são criados com status "active" por padrão
- Um email de boas-vindas é enviado após criação
- A senha inicial é gerada automaticamente e enviada por email

## ✔️ Validações

- **name**: Obrigatório, mínimo 3 caracteres, máximo 255 caracteres
- **email**: Obrigatório, formato de email válido, único no banco
- **document_id**: Obrigatório, CPF ou CNPJ válido
- **user_type**: Obrigatório, deve ser um dos valores: admin, manager, employee, customer
- **phone**: Opcional, formato brasileiro (XX) XXXXX-XXXX
- **address**: Opcional, se fornecido todos os campos são obrigatórios
````

## 🎯 Como Claude AI Gera Isso

O script `claude-documenter.py` envia para Claude AI:
1. Informações do PR (título, autor, data, etc)
2. Lista de arquivos modificados
3. Diff completo das alterações
4. Histórico de commits

Claude então:
1. Analisa o código e identifica padrões (Controllers, Services, DTOs)
2. Extrai endpoints dos arquivos `Routes.php`
3. Identifica parâmetros dos DTOs e validações
4. Detecta migrations e alterações de banco
5. Gera JSON estruturado seguindo o template do Documenter chatmode

O script Python então:
1. Converte o JSON em blocos do Notion
2. Cria a página principal
3. Cria sub-páginas para cada endpoint
4. Retorna o link da documentação

## 🌟 Resultado Final

Você terá documentação completa, consistente e automática para cada merge, seguindo **exatamente** o template do seu Documenter chatmode personalizado!

---

**Nota**: Este é apenas um exemplo. A documentação real será gerada com base nas mudanças específicas do seu código.
