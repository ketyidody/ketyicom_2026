#!/usr/bin/env bash
set -e

cd /var/www/html

# Ensure an environment file exists
if [ ! -f .env ]; then
    echo "[entrypoint] .env not found, copying from .env.example"
    cp .env.example .env
fi

# Generate an app key if one is not set
if ! grep -q '^APP_KEY=base64:' .env; then
    echo "[entrypoint] Generating application key"
    php artisan key:generate --force
fi

# Make sure runtime directories are writable by php-fpm/queue (www-data)
mkdir -p storage/app storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache database

# Ensure the SQLite database file exists (only when using the sqlite driver)
if [ "${DB_CONNECTION:-mysql}" = "sqlite" ]; then
    SQLITE_PATH="${DB_DATABASE:-database/database.sqlite}"
    mkdir -p "$(dirname "$SQLITE_PATH")"
    touch "$SQLITE_PATH"
    chown -R www-data:www-data "$(dirname "$SQLITE_PATH")"
fi

# Wait for the MySQL server to accept connections with our credentials before migrating.
if [ "${DB_CONNECTION:-mysql}" = "mysql" ]; then
    echo "[entrypoint] Waiting for MySQL at ${DB_HOST}:${DB_PORT:-3306}..."
    tries=0
    until php -r '
        try {
            new PDO(
                sprintf("mysql:host=%s;port=%s;dbname=%s", getenv("DB_HOST"), getenv("DB_PORT") ?: 3306, getenv("DB_DATABASE")),
                getenv("DB_USERNAME"),
                getenv("DB_PASSWORD")
            );
            exit(0);
        } catch (Throwable $e) {
            exit(1);
        }
    '; do
        tries=$((tries + 1))
        if [ "$tries" -ge 60 ]; then
            echo "[entrypoint] MySQL did not become ready in time, aborting."
            exit 1
        fi
        sleep 2
    done
    echo "[entrypoint] MySQL is ready"
fi

# Link public storage so uploaded images are served
php artisan storage:link || true

# Run database migrations
echo "[entrypoint] Running migrations"
php artisan migrate --force

# Cache framework config/routes/views for performance (skipped in local/debug)
if grep -q '^APP_ENV=production' .env; then
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
else
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
fi

echo "[entrypoint] Starting: $*"
exec "$@"
