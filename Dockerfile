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

# Copiar arquivos da aplicação Laravel
COPY . /var/www/

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

# Copiar script de inicialização
COPY docker-entrypoint.sh /var/www/start.sh
RUN chmod +x /var/www/start.sh

# Expor porta (Render usa a variável PORT)
EXPOSE ${PORT:-8000}

RUN chown -R www-data:www-data /var/www/storage \
    && chmod -R 775 /var/www/storage

CMD ["/var/www/start.sh"] 