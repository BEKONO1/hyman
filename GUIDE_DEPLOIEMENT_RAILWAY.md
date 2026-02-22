# Guide de Déploiement PHP/Laravel sur Railway

## Table des matières

- [Prérequis](#prérequis)
- [Fichiers de configuration](#fichiers-de-configuration)
- [Configuration de la base de données](#configuration-de-la-base-de-données)
- [Configuration Redis (optionnel)](#configuration-redis-optionnel)
- [Variables d'environnement Railway](#variables-denvironnement-railway)
- [Problèmes courants et solutions](#problèmes-courants-et-solutions)
- [Checklist de déploiement](#checklist-de-déploiement)

---

## Prérequis

- Compte Railway (railway.app)
- Repository GitHub avec votre projet PHP/Laravel
- Railway CLI (optionnel, pour debug)

---

## Fichiers de configuration

### 1. Dockerfile

Créer un fichier `Dockerfile` à la racine du projet:

```dockerfile
FROM php:8.2-apache

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libicu-dev \
    default-mysql-client \
    nodejs \
    npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl fileinfo \
    && pecl install redis && docker-php-ext-enable redis

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .

RUN cp .env.example .env

RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs --no-scripts

RUN npm install --production

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

RUN a2enmod rewrite

COPY ./.htaccess /var/www/html/public/.htaccess

RUN chmod +x start.sh

EXPOSE ${PORT:-8000}

CMD ["./start.sh"]
```

### 2. start.sh

Créer un fichier `start.sh` à la racine:

```bash
#!/bin/bash
set -e

echo "=== Railway Deployment Start ==="

# ============================================
# CONFIGURATION BASE DE DONNÉES
# ============================================
# Railway fournit les variables DB_* ou MYSQL*
DB_HOST_VAL=${DB_HOST:-${MYSQLHOST:-mysql.railway.internal}}
DB_PORT_VAL=${DB_PORT:-${MYSQLPORT:-3306}}
DB_DATABASE_VAL=${DB_DATABASE:-${MYSQLDATABASE:-railway}}
DB_USERNAME_VAL=${DB_USERNAME:-${MYSQLUSER:-root}}
DB_PASSWORD_VAL=${DB_PASSWORD:-${MYSQLPASSWORD:-}}

# ============================================
# CONFIGURATION APP URL
# ============================================
APP_URL_VAL=${APP_URL:-https://votre-app.up.railway.app}

# ============================================
# CONFIGURATION REDIS (optionnel)
# ============================================
if [ -n "$REDISHOST" ]; then
    REDIS_HOST_VAL="$REDISHOST"
    REDIS_PORT_VAL="${REDISPORT:-6379}"
    REDIS_PASSWORD_VAL="${REDISPASSWORD:-}"
    CACHE_DRIVER_VAL="redis"
    SESSION_DRIVER_VAL="redis"
    echo "Redis detected at ${REDIS_HOST_VAL}:${REDIS_PORT_VAL}"
else
    CACHE_DRIVER_VAL="file"
    SESSION_DRIVER_VAL="file"
    echo "No Redis - using file cache"
fi

# ============================================
# CRÉATION/MISE À JOUR DU FICHIER .env
# ============================================
if [ -f ".env" ]; then
    echo "Updating existing .env file..."

    # URL de l'application
    sed -i "s|^APP_URL=.*|APP_URL=${APP_URL_VAL}|g" .env
    sed -i "s|^ASSET_URL=.*|ASSET_URL=${APP_URL_VAL}|g" .env

    if ! grep -q "^ASSET_URL=" .env; then
        echo "ASSET_URL=${APP_URL_VAL}" >> .env
    fi

    # Base de données
    sed -i "s|^DB_HOST=.*|DB_HOST=${DB_HOST_VAL}|g" .env
    sed -i "s|^DB_PORT=.*|DB_PORT=${DB_PORT_VAL}|g" .env
    sed -i "s|^DB_DATABASE=.*|DB_DATABASE=${DB_DATABASE_VAL}|g" .env
    sed -i "s|^DB_USERNAME=.*|DB_USERNAME=${DB_USERNAME_VAL}|g" .env
    sed -i "s|^DB_PASSWORD=.*|DB_PASSWORD=${DB_PASSWORD_VAL}|g" .env

    # Cache et session
    sed -i "s|^CACHE_DRIVER=.*|CACHE_DRIVER=${CACHE_DRIVER_VAL}|g" .env
    sed -i "s|^SESSION_DRIVER=.*|SESSION_DRIVER=${SESSION_DRIVER_VAL}|g" .env

    # Redis
    sed -i "/^REDIS_HOST=/d" .env
    sed -i "/^REDIS_PORT=/d" .env
    sed -i "/^REDIS_PASSWORD=/d" .env

    if [ "$CACHE_DRIVER_VAL" = "redis" ]; then
        echo "REDIS_HOST=${REDIS_HOST_VAL}" >> .env
        echo "REDIS_PORT=${REDIS_PORT_VAL}" >> .env
        [ -n "$REDIS_PASSWORD_VAL" ] && echo "REDIS_PASSWORD=${REDIS_PASSWORD_VAL}" >> .env
    fi
else
    echo "Creating new .env file..."

    cat > .env << EOF
APP_NAME="Mon Application"
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

    if [ "$CACHE_DRIVER_VAL" = "redis" ]; then
        cat >> .env << EOF

REDIS_HOST=${REDIS_HOST_VAL}
REDIS_PORT=${REDIS_PORT_VAL}
EOF
        [ -n "$REDIS_PASSWORD_VAL" ] && echo "REDIS_PASSWORD=${REDIS_PASSWORD_VAL}" >> .env
    fi
fi

# ============================================
# COMMANDES ARTISAN
# ============================================
echo "Generating app key..."
php artisan key:generate --force

echo "Discovering packages..."
php artisan package:discover --ansi || true

echo "Creating storage link..."
php artisan storage:link || true

# ============================================
# ATTENTE BASE DE DONNÉES
# ============================================
echo "Waiting for database..."
sleep 10

echo "Testing database connection..."
until php artisan tinker --execute="DB::connection()->getPdo();" 2>/dev/null; do
    echo "Waiting for database connection..."
    sleep 2
done

echo "Database connected!"

# ============================================
# MIGRATIONS
# ============================================
echo "Running migrations..."
php artisan migrate --force --no-interaction || true

# ============================================
# CACHE
# ============================================
echo "Clearing cache..."
php artisan config:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true

echo "Caching config..."
php artisan config:cache --no-interaction || true

# ============================================
# DÉMARRAGE DU SERVEUR
# ============================================
echo "=== Starting Server on port ${PORT:-8000} ==="
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
```

### 3. Procfile (optionnel)

Créer un fichier `Procfile` à la racine:

```
web: ./start.sh
```

### 4. .env.example

Configurer correctement le fichier `.env.example`:

```env
APP_NAME="Mon Application"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://votre-app.up.railway.app
ASSET_URL=https://votre-app.up.railway.app
LOG_CHANNEL=stderr
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ma_base
DB_USERNAME=root
DB_PASSWORD=

CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
FILESYSTEM_DRIVER=public
```

### 5. config/app.php - Defaults

Modifier les valeurs par défaut dans `config/app.php`:

```php
'url' => env('APP_URL', 'https://votre-app.up.railway.app'),

'asset_url' => env('ASSET_URL', env('APP_URL', 'https://votre-app.up.railway.app')),
```

### 6. config/database.php - Support Railway

Modifier `config/database.php` pour supporter les variables Railway:

```php
'mysql' => [
    'driver' => 'mysql',
    'url' => env('DATABASE_URL'),
    'host' => env('DB_HOST', env('MYSQLHOST', '127.0.0.1')),
    'port' => env('DB_PORT', env('MYSQLPORT', '3306')),
    'database' => env('DB_DATABASE', env('MYSQLDATABASE', 'forge')),
    'username' => env('DB_USERNAME', env('MYSQLUSER', 'forge')),
    'password' => env('DB_PASSWORD', env('MYSQLPASSWORD', '')),
    // ... reste de la config
],
```

### 7. app/Http/Middleware/TrustProxies.php

**IMPORTANT** - Pour que HTTPS fonctionne correctement:

```php
<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    protected $proxies = '*';  // Faire confiance à tous les proxies Railway

    protected $headers = Request::HEADER_X_FORWARDED_FOR |
                         Request::HEADER_X_FORWARDED_HOST |
                         Request::HEADER_X_FORWARDED_PORT |
                         Request::HEADER_X_FORWARDED_PROTO |
                         Request::HEADER_X_FORWARDED_AWS_ELB;
}
```

### 8. config/session.php

Forcer les cookies sécurisés:

```php
'secure' => env('SESSION_SECURE_COOKIE', true),
```

---

## Configuration de la base de données

### Option 1: MySQL Railway

1. Dans Railway, cliquez sur **"Add Service"** → **"Database"** → **"MySQL"**
2. Railway crée automatiquement les variables:
   - `MYSQLHOST`
   - `MYSQLPORT`
   - `MYSQLDATABASE`
   - `MYSQLUSER`
   - `MYSQLPASSWORD`

### Option 2: Utiliser les références de variables

Dans votre service PHP, ajoutez les références:

| Variable      | Valeur                     |
| ------------- | -------------------------- |
| `DB_HOST`     | `${{MySQL.MYSQLHOST}}`     |
| `DB_PORT`     | `${{MySQL.MYSQLPORT}}`     |
| `DB_DATABASE` | `${{MySQL.MYSQLDATABASE}}` |
| `DB_USERNAME` | `${{MySQL.MYSQLUSER}}`     |
| `DB_PASSWORD` | `${{MySQL.MYSQLPASSWORD}}` |

> **Note**: Le nom `MySQL` correspond au nom de votre service MySQL dans Railway.

---

## Configuration Redis (optionnel)

### Ajouter Redis

1. Dans Railway, cliquez sur **"Add Service"** → **"Database"** → **"Redis"**
2. Ajoutez les références dans votre service PHP:

| Variable         | Valeur                     |
| ---------------- | -------------------------- |
| `REDISHOST`      | `${{Redis.REDISHOST}}`     |
| `REDISPORT`      | `${{Redis.REDISPORT}}`     |
| `REDISPASSWORD`  | `${{Redis.REDISPASSWORD}}` |
| `CACHE_DRIVER`   | `redis`                    |
| `SESSION_DRIVER` | `redis`                    |

---

## Variables d'environnement Railway

### Variables obligatoires

| Variable    | Description           | Exemple                          |
| ----------- | --------------------- | -------------------------------- |
| `APP_URL`   | URL publique de l'app | `https://mon-app.up.railway.app` |
| `APP_ENV`   | Environnement         | `production`                     |
| `APP_DEBUG` | Mode debug            | `false`                          |

### Variables de base de données

| Variable      | Description    |
| ------------- | -------------- |
| `DB_HOST`     | Hôte MySQL     |
| `DB_PORT`     | Port MySQL     |
| `DB_DATABASE` | Nom de la base |
| `DB_USERNAME` | Utilisateur    |
| `DB_PASSWORD` | Mot de passe   |

### Variables Redis (optionnel)

| Variable         | Description        |
| ---------------- | ------------------ |
| `REDISHOST`      | Hôte Redis         |
| `REDISPORT`      | Port Redis         |
| `REDISPASSWORD`  | Mot de passe Redis |
| `CACHE_DRIVER`   | `redis`            |
| `SESSION_DRIVER` | `redis`            |

---

## Problèmes courants et solutions

### 1. Erreur 419 (Page expirée / CSRF)

**Cause**: Problème de cookies/session

**Solutions**:

- Vérifier `TrustProxies.php` → `$proxies = '*'`
- Vérifier `SESSION_SECURE_COOKIE` → `true`
- Vérifier `APP_URL` correspond à l'URL Railway

### 2. CSS/JS ne se chargent pas

**Cause**: Mauvaise configuration de `APP_URL`

**Solutions**:

- Définir `APP_URL` dans Railway Variables
- Ajouter `ASSET_URL` avec la même valeur
- Vérifier `config/app.php` pour les valeurs par défaut

### 3. Erreur "Connection refused" MySQL

**Cause**: Variables DB non configurées

**Solutions**:

- Vérifier que le service MySQL existe dans le même projet
- Ajouter les références de variables `${{MySQL.MYSQLHOST}}` etc.
- Vérifier qu'il n'y a pas d'espaces/tabulations au début des valeurs

### 4. Erreur SSL MySQL lors de l'import SQL

**Cause**: MySQL tente une connexion SSL

**Solution**: Utiliser un fichier de config MySQL:

```bash
cat > /tmp/my.cnf << 'EOF'
[client]
ssl=0
skip-ssl
EOF
mysql --defaults-file=/tmp/my.cnf ...
```

### 5. Avertissement "connexion non sécurisée"

**Cause**: Laravel ne détecte pas HTTPS derrière le proxy Railway

**Solution**: Configurer `TrustProxies.php` avec `$proxies = '*'`

### 6. Erreur Redis "port must be int"

**Cause**: Variables Redis non définies ou mal configurées

**Solutions**:

- Vérifier que `REDISHOST` existe (pas `REDIS_HOST`)
- Supprimer `CACHE_DRIVER=redis` si Redis n'est pas configuré
- Ou configurer correctement les références Redis

### 7. Application lente

**Solutions**:

- Ajouter Redis pour le cache
- Augmenter les ressources (CPU/RAM) dans Railway Settings
- Optimiser les requêtes database
- Activer le cache de config: `php artisan config:cache`

### 8. Import SQL échoue

**Cause**: Mot de passe avec caractères spéciaux

**Solution**: Utiliser la variable d'environnement `MYSQL_PWD`:

```bash
export MYSQL_PWD="${DB_PASSWORD_VAL}"
mysql -h"${DB_HOST_VAL}" -u"${DB_USERNAME_VAL}" "${DB_DATABASE_VAL}" < dump.sql
```

---

## Checklist de déploiement

### Avant le déploiement

- [ ] Fichier `Dockerfile` présent à la racine
- [ ] Fichier `start.sh` présent et exécutable
- [ ] Fichier `.env.example` configuré pour production
- [ ] `TrustProxies.php` configuré avec `$proxies = '*'`
- [ ] `config/app.php` avec bonnes URL par défaut
- [ ] `config/database.php` supporte les variables Railway
- [ ] `config/session.php` avec `secure => true`

### Sur Railway

- [ ] Service MySQL créé dans le projet
- [ ] Service PHP connecté au repo GitHub
- [ ] Variables `APP_URL` définie
- [ ] Variables `DB_*` configurées (ou références MySQL)
- [ ] (Optionnel) Service Redis créé et variables configurées

### Après le déploiement

- [ ] L'application s'affiche sans erreur 419
- [ ] CSS/JS se chargent correctement
- [ ] Pas d'avertissement de sécurité dans le navigateur
- [ ] La connexion fonctionne
- [ ] Les migrations ont été exécutées

---

## Structure des fichiers recommandée

```
projet/
├── app/
│   └── Http/Middleware/
│       └── TrustProxies.php     ← IMPORTANT: $proxies = '*'
├── config/
│   ├── app.php                  ← URL par défaut
│   ├── database.php             ← Support MYSQL* vars
│   └── session.php              ← secure => true
├── database/
│   └── sql/
│       └── dump.sql             ← (optionnel) Dump SQL
├── public/
│   └── .htaccess
├── .env.example
├── Dockerfile
├── Procfile                     ← (optionnel)
├── start.sh
└── railway.toml                 ← (optionnel)
```

---

## Commandes utiles

### Terminal Railway

Accéder au terminal Railway:

```bash
# Vider le cache
php artisan config:clear
php artisan cache:clear

# Vérifier la connexion DB
php artisan tinker --execute="DB::connection()->getPdo();"

# Importer un dump SQL
export MYSQL_PWD="${DB_PASSWORD}"
mysql -h"${DB_HOST}" -u"${DB_USERNAME}" "${DB_DATABASE}" < database/sql/dump.sql

# Régénérer la clé
php artisan key:generate --force
```

---

## Notes importantes

1. **Toujours utiliser HTTPS**: Railway fournit HTTPS automatiquement
2. **Ne pas commiter `.env`**: Utiliser `.env.example`
3. **Cache de config**: Essentiel pour les performances
4. **TrustProxies**: Obligatoire pour détecter HTTPS
5. **Variables Railway**: Vérifier qu'il n'y a pas d'espaces au début des valeurs

---

## Support

- Documentation Railway: https://docs.railway.app/
- Documentation Laravel: https://laravel.com/docs
- Railway Discord: https://discord.gg/railway

---

_Dernière mise à jour: Février 2026_
_Basé sur l'expérience de déploiement de hyman-production.up.railway.app_
