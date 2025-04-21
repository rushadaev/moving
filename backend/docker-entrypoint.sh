#!/bin/sh
set -e

# Generate application key if not set
if [ -z "$(grep '^APP_KEY=' .env | cut -d '=' -f2)" ]; then
    php artisan key:generate
fi

# Run migrations
php artisan migrate --force

# Start PHP-FPM
exec "$@" 