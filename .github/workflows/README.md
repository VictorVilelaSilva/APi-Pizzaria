# 📚 Documentação Automática no Notion

Este workflow automatiza a criação de documentação no Notion quando um Pull Request é mergeado para `main`.

## 🎯 Como Funciona

1. **Trigger**: Quando um PR é mergeado para `main`
2. **Análise**: O workflow extrai informações do PR e das mudanças no código
3. **IA**: Claude AI analisa as alterações seguindo o template do **Documenter chatmode**
4. **Criação**: Páginas estruturadas são criadas automaticamente no Notion
5. **Notificação**: Um comentário é adicionado ao PR com links para a documentação

## 📋 O Que é Criado

### Página Principal da Task
Criada sob a página **"Pizzaria API"** no Notion com:
- Visão geral e contexto de negócio
- Detalhes técnicos das alterações
- Lista de endpoints criados/modificados (com links para sub-páginas)
- Alterações no banco de dados
- Pontos de integração
- Notas de deploy
- Histórico de commits
- Arquivos modificados

### Sub-Páginas de Endpoints
Para cada endpoint criado/modificado:
- Método HTTP e caminho
- Descrição e localização no código
- Parâmetros da requisição
- Headers necessários
- Exemplos de request/response
- Regras de negócio
- Validações

## ⚙️ Configuração

### 1. Criar Integração no Notion

1. Acesse: https://www.notion.so/my-integrations
2. Clique em **"+ New integration"**
3. Preencha:
   - **Name**: `GitHub Auto-Documenter`
   - **Associated workspace**: Seu workspace
   - **Type**: Internal integration
4. Clique em **"Submit"**
5. **Copie o "Internal Integration Token"** (você vai precisar dele!)

### 2. Compartilhar Página com a Integração

1. Abra a página **"Pizzaria API"** no Notion (ou crie uma se não existir)
2. Clique nos 3 pontinhos no canto superior direito
3. Clique em **"Add connections"**
4. Procure por **"GitHub Auto-Documenter"** e adicione
5. **Copie o ID da página** da URL:
   ```
   https://www.notion.so/workspace/Pizzaria-API-a1b2c3d4e5f67890abcdef1234567890
                                                   ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
                                                   Este é o ID da página (sem os hífens também funciona)
   ```

### 3. Obter Chave da API do Claude (Anthropic)

1. Acesse: https://console.anthropic.com/
2. Crie uma conta ou faça login
3. Vá para **"API Keys"**
4. Clique em **"Create Key"**
5. **Copie a chave** (começa com `sk-ant-...`)

> **Nota**: A API do Claude tem um plano gratuito com créditos iniciais. Depois você pode adicionar créditos conforme necessário.

### 4. Adicionar Secrets no GitHub

1. Vá para seu repositório no GitHub
2. Clique em **Settings** → **Secrets and variables** → **Actions**
3. Clique em **"New repository secret"** e adicione **3 secrets**:

#### Secret 1: ANTHROPIC_API_KEY
- **Name**: `ANTHROPIC_API_KEY`
- **Value**: Sua chave da API do Claude (ex: `sk-ant-api03-...`)

#### Secret 2: NOTION_API_KEY
- **Name**: `NOTION_API_KEY`
- **Value**: O token da integração do Notion (ex: `secret_abc123...`)

#### Secret 3: NOTION_PARENT_PAGE_ID
- **Name**: `NOTION_PARENT_PAGE_ID`
- **Value**: O ID da página "Pizzaria API" (ex: `a1b2c3d4e5f67890abcdef1234567890`)

### 5. Testar o Workflow

1. Crie uma branch: `feature/TEST-123-teste-documentacao`
2. Faça algumas alterações (pode ser simples)
3. Commit e push
4. Crie um Pull Request para `main`
5. Mergea o PR
6. Aguarde ~2-3 minutos
7. Verifique:
   - A aba **"Actions"** no GitHub para ver o progresso
   - Um comentário será adicionado ao PR
   - Uma nova página será criada no Notion!

## 🔍 Estrutura das Páginas Criadas

```
Pizzaria API (Página Principal no Notion)
│
└── Task - PDB-4638 - Adicionar Gestão de Usuários
    ├── POST - /createUser
    ├── PUT - /updateUser
    └── DELETE - /deleteUser
```

## 📊 Monitoramento

### Ver Logs do Workflow
1. Vá para **Actions** no GitHub
2. Clique no workflow **"Auto-Document on Merge"**
3. Clique no run específico
4. Expanda os steps para ver logs detalhados

### Baixar Artefatos
O workflow salva os seguintes arquivos (disponíveis por 30 dias):
- `documentation-payload.json` - Dados extraídos do PR
- `changes.txt` - Lista de arquivos modificados
- `commits.txt` - Histórico de commits
- `full-diff.txt` - Diff completo das mudanças

Para baixar:
1. Vá no run do workflow
2. Role até o final da página
3. Seção **"Artifacts"** → clique em **"documentation-data"**

## 🚨 Troubleshooting

### "ANTHROPIC_API_KEY not configured"
- Verifique se você adicionou o secret com o nome correto
- Confirme que o secret tem um valor válido (começa com `sk-ant-`)

### "NOTION_API_KEY not configured"
- Verifique se você criou a integração no Notion
- Confirme que copiou o token corretamente

### "NOTION_PARENT_PAGE_ID not configured"
- Verifique se você compartilhou a página "Pizzaria API" com a integração
- Confirme que o ID da página está correto

### "Notion API Error: 404"
- A integração não tem acesso à página
- Compartilhe a página "Pizzaria API" com a integração

### "Notion API Error: 401"
- Token da integração inválido ou expirado
- Gere um novo token e atualize o secret

### "Claude API Error"
- Verifique se você tem créditos na conta do Anthropic
- Confirme que a chave da API está correta

### Página criada mas sem endpoints
- Verifique se o código realmente criou/modificou endpoints
- O Claude analisa apenas mudanças detectadas no diff
- Endpoints precisam estar em arquivos `Routes.php` ou `*Controller.php`

## 💰 Custos

### Claude AI (Anthropic)
- **Modelo usado**: Claude 3.5 Sonnet
- **Custo estimado por documentação**: $0.03 - $0.10 USD
- **Créditos gratuitos**: $5 USD ao criar conta
- Suficiente para ~50-150 documentações

### Notion
- **Gratuito** para uso pessoal e pequenas equipes
- A integração não tem custos adicionais

### GitHub Actions
- **Gratuito** para repositórios públicos
- Repositórios privados: 2.000 minutos/mês grátis
- Este workflow usa ~2-5 minutos por execução

## 🔒 Segurança

- ✅ Secrets são criptografados pelo GitHub
- ✅ Tokens não aparecem nos logs
- ✅ A integração do Notion só tem acesso às páginas compartilhadas
- ✅ A API do Claude não armazena seus dados

## 🎨 Personalização

### Modificar o Template
Edite o arquivo: `.github/scripts/claude-documenter.py`

Procure pela função `ask_claude_for_analysis()` e modifique o prompt para ajustar:
- Formato da documentação
- Seções incluídas
- Idioma (já está em PT-BR)
- Detalhes técnicos

### Adicionar Mais Informações
Edite `.github/workflows/auto-document-notion.yml`:
- Adicione mais steps para extrair informações
- Modifique `documentation-payload.json` com novos campos
- Atualize o script Python para usar os novos dados

## 📞 Suporte

Se encontrar problemas:

1. **Verifique os logs** do workflow no GitHub Actions
2. **Confira os secrets** estão todos configurados
3. **Teste a integração** do Notion manualmente
4. **Valide a chave** do Claude no console da Anthropic

## ✨ Benefícios

- ⏱️ **Economia de tempo**: Documentação criada automaticamente
- 📝 **Consistência**: Todas as tasks seguem o mesmo template
- 🤖 **IA inteligente**: Claude analisa o código e extrai informações relevantes
- 🔗 **Rastreabilidade**: Links diretos entre GitHub e Notion
- 📊 **Histórico completo**: Todos os commits e mudanças documentados

---

**Última atualização**: 31 de outubro de 2025
