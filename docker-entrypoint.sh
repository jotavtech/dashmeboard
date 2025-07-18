#!/bin/bash

# Exit on any error
set -e

# Create storage directories if they don't exist
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Set proper permissions
chown -R www-data:www-data /var/www
chmod -R 755 /var/www
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Clear and cache Laravel configuration
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true
php artisan cache:clear || true

# Generate application key if not set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Run migrations if needed
php artisan migrate --force || true

# Start php-fpm
exec php-fpm 