#!/bin/bash
set -e

echo "=== Koblan Deploy Script ==="
echo "PORT: $PORT"

# Run migrations
php artisan migrate --force

# Cache for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=== Starting Laravel server on port $PORT ==="
exec php artisan serve --host=0.0.0.0 --port=$PORT
