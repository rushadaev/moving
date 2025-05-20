#!/bin/sh
set -e

# Check if vendor directory exists and has autoload.php
if [ ! -f ./vendor/autoload.php ]; then
    echo "Vendor directory not found or incomplete. Installing dependencies..."
    composer install --optimize-autoloader
fi

# Generate application key if not set
if [ -z "$(grep '^APP_KEY=' .env | cut -d '=' -f2)" ]; then
    php artisan key:generate
fi

# Run migrations
php artisan migrate --force

# Create symlink to root .env file
ln -sf /.env /var/www/html/.env

# Start PHP-FPM
php-fpm 