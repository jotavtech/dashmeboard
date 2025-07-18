#!/bin/bash
set -e

# Garantir que estamos no diretório correto da aplicação Laravel
cd /var/www

echo "Diretório atual: $(pwd)"
echo "Arquivos disponíveis:"
ls -la

# Verificar se artisan existe
if [ ! -f artisan ]; then
    echo "ERRO: Arquivo artisan não encontrado!"
    echo "Conteúdo do diretório:"
    ls -la
    exit 1
fi

# Verificar se vendor/autoload.php existe
if [ ! -f vendor/autoload.php ]; then
    echo "ERRO: vendor/autoload.php não encontrado!"
    echo "Executando composer install..."
    composer install --no-dev --optimize-autoloader
fi

# Debug das variáveis de ambiente do banco
echo "=== DEBUG BANCO DE DADOS ==="
echo "DB_CONNECTION: ${DB_CONNECTION:-NAO_DEFINIDO}"
echo "DB_HOST: ${DB_HOST:-NAO_DEFINIDO}"
echo "DB_PORT: ${DB_PORT:-NAO_DEFINIDO}"
echo "DB_DATABASE: ${DB_DATABASE:-NAO_DEFINIDO}"
echo "DB_USERNAME: ${DB_USERNAME:-NAO_DEFINIDO}"
echo "=============================="

# Verificar se arquivo .env existe
if [ ! -f .env ]; then
    if [ -f .env.example ]; then
        echo "Copiando .env.example para .env"
        cp .env.example .env
    else
        echo "Criando .env básico"
        cat > .env << EOF
APP_NAME=TodoApp
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=
DB_CONNECTION=mysql
DB_HOST=
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
LOG_LEVEL=error
EOF
    fi
fi

# Gerar chave da aplicação se não existir
if [ -z "$(grep "^APP_KEY=" .env | cut -d= -f2)" ]; then
    echo "Gerando chave da aplicação"
    php artisan key:generate --no-interaction
fi

# Configurar banco baseado na conexão
if [ "${DB_CONNECTION}" = "sqlite" ]; then
    echo "Configurando SQLite..."
    if [ ! -f "${DB_DATABASE}" ]; then
        echo "Criando arquivo SQLite: ${DB_DATABASE}"
        touch "${DB_DATABASE}"
        chmod 664 "${DB_DATABASE}"
    fi
    skip_database=false
elif [ -z "$DB_HOST" ] || [ "$DB_HOST" = "" ] || [ "$DB_HOST" = "CONFIGURE_MANUALLY_IN_DASHBOARD" ]; then
    echo "AVISO: DB_HOST não está definido ou configurado manualmente."
    echo "Iniciando aplicação sem migrações..."
    skip_database=true
else
    skip_database=false
    echo "Configuração MySQL detectada. Tentando conectar..."
    
    # Aguardar banco de dados estar disponível
    echo "Aguardando banco de dados MySQL..."
    attempt=0
    max_attempts=60
    
    until mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" -p"$DB_PASSWORD" -e "SELECT 1;" > /dev/null 2>&1; do
        attempt=$((attempt + 1))
        if [ $attempt -gt $max_attempts ]; then
            echo "Timeout esperando banco MySQL após $max_attempts tentativas."
            echo "Continuando sem migrações..."
            skip_database=true
            break
        fi
        echo "Tentativa $attempt/$max_attempts - Testando conexão MySQL..."
        sleep 5
    done
fi

# Cache de configurações
echo "Configurando cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Executar migrações se banco estiver disponível
if [ "$skip_database" = false ]; then
    echo "Executando migrações..."
    php artisan migrate --force
else
    echo "Banco não disponível, pulando migrações"
fi

# Iniciar PHP-FPM
echo "Iniciando PHP-FPM..."
exec php-fpm 