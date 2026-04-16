#!/bin/sh

# Generate APP_KEY if missing
if [ -z "$APP_KEY" ]; then
    php artisan key:generate
fi

# Create storage symlink
php artisan storage:link --force 2>/dev/null || true

# Cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Force database migration
php artisan migrate --force

# Start PHP-FPM in background
php-fpm &

# Start Nginx in foreground
nginx -g "daemon off;"
