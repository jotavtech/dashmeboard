FROM php:8.3-fpm

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    default-mysql-client \
    sqlite3 \
    libsqlite3-dev \
    libpq-dev

# Limpar cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensões PHP
RUN docker-php-ext-install pdo_mysql pdo_sqlite pdo_pgsql pgsql mbstring exif pcntl bcmath gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Definir diretório de trabalho
WORKDIR /var/www

# Copiar apenas o conteúdo do diretório todo-app
COPY todo-app/ /var/www/

# Instalar dependências do Composer
RUN composer install --no-dev --optimize-autoloader

# Criar diretórios necessários se não existirem e configurar permissões
RUN mkdir -p /var/www/storage/logs \
    && mkdir -p /var/www/storage/framework/cache \
    && mkdir -p /var/www/storage/framework/sessions \
    && mkdir -p /var/www/storage/framework/testing \
    && mkdir -p /var/www/storage/framework/views \
    && mkdir -p /var/www/bootstrap/cache \
    && mkdir -p /var/www/database \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/database

# Criar script de inicialização
RUN echo '#!/bin/bash\n\
set -e\n\
\n\
# Garantir que estamos no diretório correto da aplicação Laravel\n\
cd /var/www\n\
\n\
echo "Diretório atual: $(pwd)"\n\
echo "Arquivos disponíveis:"\n\
ls -la\n\
\n\
# Verificar se artisan existe\n\
if [ ! -f artisan ]; then\n\
    echo "ERRO: Arquivo artisan não encontrado!"\n\
    echo "Conteúdo do diretório:"\n\
    ls -la\n\
    exit 1\n\
fi\n\
\n\
# Verificar se vendor/autoload.php existe\n\
if [ ! -f vendor/autoload.php ]; then\n\
    echo "ERRO: vendor/autoload.php não encontrado!"\n\
    echo "Executando composer install..."\n\
    composer install --no-dev --optimize-autoloader\n\
fi\n\
\n\
# Debug das variáveis de ambiente do banco\n\
echo "=== DEBUG BANCO DE DADOS ==="\n\
echo "DB_CONNECTION: ${DB_CONNECTION:-NAO_DEFINIDO}"\n\
echo "DB_HOST: ${DB_HOST:-NAO_DEFINIDO}"\n\
echo "DB_PORT: ${DB_PORT:-NAO_DEFINIDO}"\n\
echo "DB_DATABASE: ${DB_DATABASE:-NAO_DEFINIDO}"\n\
echo "DB_USERNAME: ${DB_USERNAME:-NAO_DEFINIDO}"\n\
echo "=============================="\n\
\n\
# Verificar se arquivo .env existe\n\
if [ ! -f .env ]; then\n\
    if [ -f .env.example ]; then\n\
        echo "Copiando .env.example para .env"\n\
        cp .env.example .env\n\
    else\n\
        echo "Criando .env básico"\n\
        cat > .env << EOF\n\
APP_NAME=TodoApp\n\
APP_ENV=production\n\
APP_KEY=\n\
APP_DEBUG=false\n\
APP_URL=\n\
DB_CONNECTION=mysql\n\
DB_HOST=\n\
DB_PORT=3306\n\
DB_DATABASE=\n\
DB_USERNAME=\n\
DB_PASSWORD=\n\
SESSION_DRIVER=database\n\
CACHE_STORE=database\n\
QUEUE_CONNECTION=database\n\
LOG_LEVEL=error\n\
EOF\n\
    fi\n\
fi\n\
\n\
# Gerar chave da aplicação se não existir\n\
if [ -z "$(grep "^APP_KEY=" .env | cut -d= -f2)" ]; then\n\
    echo "Gerando chave da aplicação"\n\
    php artisan key:generate --no-interaction\n\
fi\n\
\n\
# Configurar banco baseado na conexão\n\
if [ "${DB_CONNECTION}" = "sqlite" ]; then\n\
    echo "Configurando SQLite..."\n\
    if [ ! -f "${DB_DATABASE}" ]; then\n\
        echo "Criando arquivo SQLite: ${DB_DATABASE}"\n\
        touch "${DB_DATABASE}"\n\
        chmod 664 "${DB_DATABASE}"\n\
    fi\n\
    skip_database=false\n\
elif [ -z "$DB_HOST" ] || [ "$DB_HOST" = "" ] || [ "$DB_HOST" = "CONFIGURE_MANUALLY_IN_DASHBOARD" ]; then\n\
    echo "AVISO: DB_HOST não está definido ou configurado manualmente."\n\
    echo "Iniciando aplicação sem migrações..."\n\
    skip_database=true\n\
else\n\
    skip_database=false\n\
    echo "Configuração MySQL detectada. Tentando conectar..."\n\
    \n\
    # Aguardar banco de dados estar disponível\n\
    echo "Aguardando banco de dados MySQL..."\n\
    attempt=0\n\
    max_attempts=60\n\
    \n\
    until mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" -p"$DB_PASSWORD" -e "SELECT 1;" > /dev/null 2>&1; do\n\
        attempt=$((attempt + 1))\n\
        if [ $attempt -gt $max_attempts ]; then\n\
            echo "Timeout esperando banco MySQL após $max_attempts tentativas."\n\
            echo "Continuando sem migrações..."\n\
            skip_database=true\n\
            break\n\
        fi\n\
        echo "Tentativa $attempt/$max_attempts - Testando conexão MySQL..."\n\
        sleep 5\n\
    done\n\
fi\n\
\n\
# Cache de configurações\n\
echo "Configurando cache..."\n\
php artisan config:cache\n\
php artisan route:cache\n\
php artisan view:cache\n\
\n\
# Executar migrações se banco estiver disponível\n\
if [ "$skip_database" = false ]; then\n\
    echo "Executando migrações..."\n\
    php artisan migrate --force\n\
else\n\
    echo "Banco não disponível, pulando migrações"\n\
fi\n\
\n\
# Iniciar servidor PHP\n\
echo "Iniciando servidor na porta ${PORT:-8000}"\n\
php -S 0.0.0.0:${PORT:-8000} -t public\n\
' > /var/www/start.sh && chmod +x /var/www/start.sh

# Expor porta (Render usa a variável PORT)
EXPOSE ${PORT:-8000}

CMD ["/var/www/start.sh"] 