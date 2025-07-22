# Deploy no Render

Este guia explica como fazer o deploy da aplicação Laravel no Render com MariaDB.

## Pré-requisitos

1. Conta no [Render](https://render.com)
2. Repositório GitHub com o código da aplicação
3. Arquivos configurados:
   - `Dockerfile` (já configurado)
   - `render.yaml` (já configurado)

## Passos para Deploy

### 1. Conectar Repositório

1. Acesse o dashboard do Render
2. Clique em "New +" e selecione "Web Service"
3. Conecte seu repositório GitHub
4. Selecione o repositório da aplicação

### 2. Configurações Básicas

- **Name**: `todo-app` (ou nome de sua escolha)
- **Environment**: `Docker`
- **Region**: Escolha a região mais próxima
- **Branch**: `main` ou `master`
- **Dockerfile Path**: `./Dockerfile`

### 3. Variáveis de Ambiente

O Render utilizará as configurações do arquivo `render.yaml`, que já inclui:

```
APP_NAME=TodoApp
APP_ENV=production
APP_DEBUG=false
APP_KEY=(será gerado automaticamente)
DB_CONNECTION=mysql
DB_HOST=(conectado automaticamente ao MariaDB)
DB_PORT=(conectado automaticamente ao MariaDB)
DB_DATABASE=todoapp
DB_USERNAME=todouser
DB_PASSWORD=(gerado automaticamente)
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
LOG_LEVEL=error
```

### 4. Deploy

1. Clique em "Create Web Service"
2. O Render automaticamente:
   - Criará o banco MariaDB
   - Fará o build da imagem Docker
   - Configurará as variáveis de ambiente
   - Conectará a aplicação ao banco
   - Executará migrações
   - Iniciará a aplicação

## Recursos Criados

- **Banco de dados**: MariaDB (plano free)
- **Web Service**: Aplicação Laravel
- **Sessões**: Armazenadas no banco MariaDB
- **Cache**: Banco de dados
- **Logs**: Configurados para produção

## Monitoramento

- Acesse os logs no dashboard do Render
- A aplicação estará disponível na URL fornecida pelo Render
- Health checks automáticos na porta especificada
- Monitoramento do banco MariaDB separadamente

## Troubleshooting

### Erro "No open HTTP ports detected"
- ✅ **Resolvido**: O Dockerfile foi configurado para usar o servidor built-in do PHP na porta correta

### Problemas de Conexão com Banco
- ✅ **Resolvido**: Script aguarda banco estar disponível antes de executar migrações
- ✅ **Configurado**: Variáveis de ambiente conectadas automaticamente ao MariaDB

### Problemas de Permissão
- ✅ **Resolvido**: Permissões configuradas no Dockerfile para `storage/` e `bootstrap/cache/`

### Variáveis de Ambiente
- Use o arquivo `render.yaml` para configurações automáticas
- Conexão com MariaDB configurada automaticamente via `fromDatabase`

### Migrações
- Executadas automaticamente após conexão com banco
- Logs disponíveis no dashboard do Render

## URLs Importantes

- **Aplicação**: `https://seu-app.onrender.com`
- **Dashboard Render**: `https://dashboard.render.com`

## Comandos Úteis

```bash
# Para desenvolvimento local com Docker
docker build -t todo-app .
docker run -p 8000:8000 -e PORT=8000 todo-app

# Para verificar logs no Render
# Use o dashboard web ou CLI do Render

# Para acessar banco MariaDB (via dashboard Render)
# Acesse as configurações do banco no dashboard
```

## Estrutura do Deploy

O deploy criará dois serviços:
1. **Web Service** (`todo-app`) - Aplicação Laravel
2. **Database** (`todo-mariadb`) - Banco MariaDB

A conexão entre eles é configurada automaticamente via `render.yaml`. 