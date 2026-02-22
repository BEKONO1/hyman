#!/bin/bash
set -e

echo "=== Railway Deployment Start ==="

echo "Creating .env file with Railway variables..."

# Use DB_ variables directly from Railway (they are confirmed working)
DB_HOST_VAL=${DB_HOST:-${MYSQLHOST}}
DB_PORT_VAL=${DB_PORT:-${MYSQLPORT:-3306}}
DB_DATABASE_VAL=${DB_DATABASE:-${MYSQLDATABASE}}
DB_USERNAME_VAL=${DB_USERNAME:-${MYSQLUSER}}
DB_PASSWORD_VAL=${DB_PASSWORD:-${MYSQLPASSWORD}}

cat > .env << EOF
APP_NAME="Handyman Service"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://localhost
LOG_CHANNEL=stderr
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=${DB_HOST_VAL}
DB_PORT=${DB_PORT_VAL}
DB_DATABASE=${DB_DATABASE_VAL}
DB_USERNAME=${DB_USERNAME_VAL}
DB_PASSWORD=${DB_PASSWORD_VAL}

CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
FILESYSTEM_DRIVER=public
EOF

echo "Generating app key..."
php artisan key:generate --force

echo "Discovering packages..."
php artisan package:discover --ansi || true

echo "Creating storage link..."
php artisan storage:link || true

echo "Waiting for database..."
sleep 10

echo "Testing database connection..."
until php artisan tinker --execute="DB::connection()->getPdo();" 2>/dev/null; do
    echo "Waiting for database connection..."
    sleep 2
done

echo "Database connected!"

echo "Checking if database is empty..."
TABLE_EXISTS=$(php artisan tinker --execute="echo Schema::hasTable('users') ? 'yes' : 'no';" 2>/dev/null || echo "no")

if [ "$TABLE_EXISTS" = "no" ]; then
    echo "Database is empty. Importing SQL dump..."
    
    if [ -f "database/sql/handyman_service.sql" ]; then
        echo "Importing with: mysql -h${DB_HOST_VAL} -P${DB_PORT_VAL} -u${DB_USERNAME_VAL} ... ${DB_DATABASE_VAL}"
        mysql -h"${DB_HOST_VAL}" -P"${DB_PORT_VAL}" -u"${DB_USERNAME_VAL}" -p"${DB_PASSWORD_VAL}" "${DB_DATABASE_VAL}" < database/sql/handyman_service.sql || true
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
