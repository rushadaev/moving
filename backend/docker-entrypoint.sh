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

# Seed essential data
php artisan db:seed --class=RoleSeeder --force
php artisan db:seed --class=LandingServiceSeeder --force

# Start PHP-FPM
php-fpm 