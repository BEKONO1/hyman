#!/bin/bash
set -e

echo "=== Railway Deployment Start ==="

# Use DB_ variables directly from Railway
DB_HOST_VAL=${DB_HOST:-${MYSQLHOST:-mysql.railway.internal}}
DB_PORT_VAL=${DB_PORT:-${MYSQLPORT:-3306}}
DB_DATABASE_VAL=${DB_DATABASE:-${MYSQLDATABASE:-railway}}
DB_USERNAME_VAL=${DB_USERNAME:-${MYSQLUSER:-root}}
DB_PASSWORD_VAL=${DB_PASSWORD:-${MYSQLPASSWORD:-}}

# Get APP_URL from Railway or use default
APP_URL_VAL=${APP_URL:-https://hyman-production.up.railway.app}

# Redis configuration - check if Redis variables exist
if [ -n "$REDISHOST" ] || [ -n "$REDIS_HOST" ]; then
    REDIS_HOST_VAL=${REDISHOST:-${REDIS_HOST:-127.0.0.1}}
    REDIS_PORT_VAL=${REDISPORT:-${REDIS_PORT:-6379}}
    REDIS_PASSWORD_VAL=${REDISPASSWORD:-${REDIS_PASSWORD:-}}
    CACHE_DRIVER_VAL="redis"
    SESSION_DRIVER_VAL="redis"
    echo "Redis detected - using Redis for cache/session"
else
    CACHE_DRIVER_VAL="file"
    SESSION_DRIVER_VAL="file"
    echo "No Redis detected - using file for cache/session"
fi

# Check if .env exists (meaning installation was already done)
if [ -f ".env" ]; then
    echo "Updating existing .env file..."
    
    # Update APP_URL and ASSET_URL in existing .env
    sed -i "s|^APP_URL=.*|APP_URL=${APP_URL_VAL}|g" .env
    sed -i "s|^ASSET_URL=.*|ASSET_URL=${APP_URL_VAL}|g" .env
    
    # Add ASSET_URL if it doesn't exist
    if ! grep -q "^ASSET_URL=" .env; then
        echo "ASSET_URL=${APP_URL_VAL}" >> .env
    fi
    
    # Update database settings if needed
    sed -i "s|^DB_HOST=.*|DB_HOST=${DB_HOST_VAL}|g" .env
    sed -i "s|^DB_PORT=.*|DB_PORT=${DB_PORT_VAL}|g" .env
    sed -i "s|^DB_DATABASE=.*|DB_DATABASE=${DB_DATABASE_VAL}|g" .env
    sed -i "s|^DB_USERNAME=.*|DB_USERNAME=${DB_USERNAME_VAL}|g" .env
    sed -i "s|^DB_PASSWORD=.*|DB_PASSWORD=${DB_PASSWORD_VAL}|g" .env
    
    # Update cache/session drivers
    sed -i "s|^CACHE_DRIVER=.*|CACHE_DRIVER=${CACHE_DRIVER_VAL}|g" .env
    sed -i "s|^SESSION_DRIVER=.*|SESSION_DRIVER=${SESSION_DRIVER_VAL}|g" .env
    
    # Remove Redis config if not available
    if [ "$CACHE_DRIVER_VAL" = "file" ]; then
        sed -i "/^REDIS_/d" .env
    fi
    
    echo "APP_URL and ASSET_URL set to: ${APP_URL_VAL}"
    echo "CACHE_DRIVER: ${CACHE_DRIVER_VAL}"
    echo "SESSION_DRIVER: ${SESSION_DRIVER_VAL}"
else
    echo "Creating new .env file..."
    
    cat > .env << EOF
APP_NAME="Handyman Service"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=${APP_URL_VAL}
ASSET_URL=${APP_URL_VAL}
LOG_CHANNEL=stderr
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=${DB_HOST_VAL}
DB_PORT=${DB_PORT_VAL}
DB_DATABASE=${DB_DATABASE_VAL}
DB_USERNAME=${DB_USERNAME_VAL}
DB_PASSWORD=${DB_PASSWORD_VAL}

CACHE_DRIVER=${CACHE_DRIVER_VAL}
QUEUE_CONNECTION=sync
SESSION_DRIVER=${SESSION_DRIVER_VAL}
FILESYSTEM_DRIVER=public
EOF

    # Add Redis config if available
    if [ "$CACHE_DRIVER_VAL" = "redis" ]; then
        cat >> .env << EOF

REDIS_HOST=${REDIS_HOST_VAL}
REDIS_PORT=${REDIS_PORT_VAL}
REDIS_PASSWORD=${REDIS_PASSWORD_VAL}
EOF
    fi
fi

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

echo "Running migrations..."
php artisan migrate --force --no-interaction || true

echo "Clearing cache..."
php artisan config:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true

echo "Caching config..."
php artisan config:cache --no-interaction || true

echo "=== Starting Server on port ${PORT:-8000} ==="
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
