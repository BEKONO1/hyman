#!/bin/bash
set -e

echo "=== Railway Deployment Start ==="

echo "Discovering packages..."
php artisan package:discover --ansi || true

echo "Creating storage link..."
php artisan storage:link || true

echo "Waiting for database..."
sleep 5

echo "Checking if database is empty..."
TABLE_EXISTS=$(php artisan tinker --execute="echo Schema::hasTable('users') ? 'yes' : 'no';" 2>/dev/null || echo "no")

if [ "$TABLE_EXISTS" = "no" ]; then
    echo "Database is empty. Importing SQL dump..."
    
    if [ -f "database/sql/handyman_service.sql" ]; then
        mysql -h${DB_HOST} -P${DB_PORT} -u${DB_USERNAME} -p${DB_PASSWORD} ${DB_DATABASE} < database/sql/handyman_service.sql || true
        echo "SQL import completed."
    else
        echo "SQL file not found, running migrations..."
        php artisan migrate --force --no-interaction
    fi
else
    echo "Database already has data. Running migrations..."
    php artisan migrate --force --no-interaction || true
fi

echo "Caching config..."
php artisan config:cache --no-interaction || true

echo "Caching routes..."
php artisan route:cache --no-interaction || true

echo "Caching views..."
php artisan view:cache --no-interaction || true

echo "=== Starting Server on port ${PORT:-8000} ==="
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
