# 🚀 Guia Rápido: Configuração em 5 Minutos

## Passo 1: Criar Integração no Notion (2 min)

1. Acesse: https://www.notion.so/my-integrations
2. Clique em **"+ New integration"**
3. Configure:
   - Name: `GitHub Auto-Documenter`
   - Type: Internal integration
4. Clique em **"Submit"**
5. **Copie o token** (guarde em um lugar seguro!)

## Passo 2: Compartilhar Página no Notion (1 min)

1. Abra a página **"Pizzaria API"** no Notion (crie se não existir)
2. Clique nos **3 pontinhos** (⋯) no canto superior direito
3. Selecione **"Add connections"**
4. Procure e adicione **"GitHub Auto-Documenter"**
5. **Copie o ID da página** da URL:
   ```
   https://www.notion.so/seu-workspace/Pizzaria-API-a1b2c3d4e5f67890abcdef1234567890
                                                        ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
                                                        Este é o Page ID
   ```

## Passo 3: Obter Chave da API do Claude (1 min)

1. Acesse: https://console.anthropic.com/
2. Crie conta ou faça login
3. Vá para **"API Keys"**
4. Clique em **"Create Key"**
5. **Copie a chave** (começa com `sk-ant-...`)

> 💡 Você ganha $5 USD em créditos gratuitos!

## Passo 4: Configurar Secrets no GitHub (1 min)

1. No seu repositório, vá para: **Settings** → **Secrets and variables** → **Actions**
2. Clique em **"New repository secret"** e adicione 3 secrets:

### Secret 1
- Name: `ANTHROPIC_API_KEY`
- Value: `sk-ant-api03-...` (sua chave do Claude)

### Secret 2
- Name: `NOTION_API_KEY`
- Value: `secret_...` (token da integração do Notion)

### Secret 3
- Name: `NOTION_PARENT_PAGE_ID`
- Value: `a1b2c3d4e5f6...` (ID da página "Pizzaria API")

## ✅ Pronto! Teste Agora

1. Crie uma branch de teste:
   ```bash
   git checkout -b feature/TEST-001-teste-documentacao
   ```

2. Faça uma mudança simples e commit:
   ```bash
   echo "# Test" >> README.md
   git add .
   git commit -m "test: testar documentação automática"
   git push origin feature/TEST-001-teste-documentacao
   ```

3. Crie um Pull Request para `main`

4. Mergea o PR

5. Aguarde 2-3 minutos e verifique:
   - ✅ Um comentário será adicionado ao PR
   - ✅ Uma página será criada no Notion automaticamente!

## 🎉 Sucesso!

Agora toda vez que você mergear um PR para `main`, a documentação será criada automaticamente seguindo o template do seu **Documenter chatmode**!

---

## 📚 Quer Saber Mais?

Veja o guia completo: [README.md](.github/workflows/README.md)

## ❓ Problemas?

### Documentação não foi criada
1. Verifique se os 3 secrets estão configurados corretamente
2. Vá em **Actions** no GitHub e veja os logs do workflow
3. Confirme que a página "Pizzaria API" está compartilhada com a integração

### Erro "Notion API Error: 404"
- A integração não tem acesso à página
- Compartilhe a página novamente com a integração

### Erro "401 Unauthorized"
- Token do Notion ou Claude está incorreto
- Regenere os tokens e atualize os secrets

---

**Tempo total**: ~5 minutos  
**Custo**: Gratuito (com créditos iniciais do Claude)
