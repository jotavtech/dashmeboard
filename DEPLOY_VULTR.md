# Deploy na Vultr

## Configuração da Instância Vultr

### 1. Acesse o Console da Vultr

1. Vá para o dashboard da Vultr
2. Clique na sua instância `216.238.105.47`
3. Clique em **"Console"** para acessar via navegador
4. **Login:** `root`
5. **Senha:** [verificar no dashboard da Vultr ou email]

### 2. Configuração Inicial

No console da Vultr, execute:

```bash
# Atualizar o sistema
apt update && apt upgrade -y

# Instalar Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sh get-docker.sh

# Instalar Docker Compose
apt install docker-compose -y

# Adicionar usuário ao grupo docker
usermod -aG docker root

# Reiniciar para aplicar as mudanças
reboot
```

### 3. Após o reboot, conectar novamente e configurar o projeto

```bash
# Criar diretório para o projeto
mkdir -p /var/www/todo-app
cd /var/www/todo-app

# Clonar o repositório da branch master
git clone -b master https://github.com/seu-usuario/estudosphp-1.git .

# Ou se já tiver o repositório, fazer pull
# git pull origin master
```

### 4. Configurar o projeto

```bash
# Copiar arquivo de ambiente
cp .env.example .env

# Editar o .env com as configurações corretas
nano .env
```

### 5. Configurações do .env (Branch Master)

```env
APP_NAME=TodoApp
APP_ENV=production
APP_DEBUG=false
APP_URL=http://216.238.105.47

# Banco Neon (credenciais da branch master)
DATABASE_URL=postgresql://neondb_owner:npg_9FBrJ6RTyZvo@ep-curly-sun-a8qiaz27-pooler.eastus2.azure.neon.tech/neondb?sslmode=require&channel_binding=require
DB_CONNECTION=pgsql
DB_HOST=ep-curly-sun-a8qiaz27-pooler.eastus2.azure.neon.tech
DB_PORT=5432
DB_DATABASE=neondb
DB_USERNAME=neondb_owner
DB_PASSWORD=npg_9FBrJ6RTyZvo
DB_SSLMODE=require

# Session e Cache
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

# Logging
LOG_LEVEL=error
LOG_CHANNEL=stack
```

### 6. Fazer o Deploy

```bash
# Construir e iniciar os containers
docker-compose up -d --build

# Verificar se está funcionando
docker-compose ps

# Ver logs
docker-compose logs -f
```

### 7. Comandos Úteis

```bash
# Parar os containers
docker-compose down

# Reiniciar
docker-compose restart

# Ver logs
docker-compose logs -f php

# Executar comandos no container
docker-compose exec php php artisan migrate
docker-compose exec php php artisan cache:clear
docker-compose exec php php artisan config:clear
```

### 8. Acessar a Aplicação

Após o deploy, acesse:
```
http://216.238.105.47
```

### 9. Troubleshooting

#### Se houver problemas:

1. **Verificar logs**:
   ```bash
   docker-compose logs -f
   ```

2. **Verificar se a porta 80 está aberta**:
   ```bash
   netstat -tlnp | grep :80
   ```

3. **Verificar firewall**:
   ```bash
   ufw status
   # Se necessário, abrir porta 80
   ufw allow 80
   ```

4. **Reiniciar tudo**:
   ```bash
   docker-compose down
   docker-compose up -d --build
   ```

5. **Verificar se as tabelas foram criadas**:
   ```bash
   docker-compose exec php php artisan migrate:status
   ```

### 10. Configuração de Domínio (Opcional)

Se quiser usar um domínio:

1. Configure o DNS para apontar para `216.238.105.47`
2. Atualize o `APP_URL` no `.env`
3. Reinicie os containers

### 11. Backup e Manutenção

```bash
# Backup do banco (se necessário)
docker-compose exec php php artisan db:backup

# Atualizar código da branch master
git pull origin master
docker-compose up -d --build
```

### 12. Como colar comandos no console

- **Windows/Linux**: `Ctrl + V`
- **Mac**: `Cmd + V`
- **Alternativo**: Botão direito → Paste
- **Se não funcionar**: `Shift + Insert` 