#!/bin/sh

# Cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Force database migration (uses Render PostgreSQL DB if DATABASE_URL is set)
php artisan migrate --force

# Start PHP-FPM in background
php-fpm &

# Start Nginx in foreground
nginx -g "daemon off;"
