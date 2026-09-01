# ketyi.com — Docker task runner
# Usage: `just <recipe>`  •  list all with `just`  or  `just --list`

# Compose command (override with `just compose=... <recipe>` if needed)
compose := "docker compose"
service := "app"

# Show available recipes
default:
    @just --list

# Build the Docker image
build:
    {{compose}} build

# Build without using the cache
rebuild:
    {{compose}} build --no-cache

# Start the app in the background (builds if needed)
up:
    {{compose}} up -d --build
    @echo "→ App running at http://localhost:8000"

# Start in the foreground with live logs
up-fg:
    {{compose}} up --build

# Stop and remove containers
down:
    {{compose}} down

# Stop, remove containers AND volumes (wipes SQLite DB + uploads)
destroy:
    {{compose}} down -v

# Restart the app container
restart:
    {{compose}} restart {{service}}

# Tail application logs
logs:
    {{compose}} logs -f {{service}}

# Open a shell inside the running container
shell:
    {{compose}} exec {{service}} bash

# Open a MySQL shell in the db container
mysql:
    {{compose}} exec db sh -c 'mysql -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE"'

# Run an artisan command, e.g. `just artisan migrate:status`
artisan *args:
    {{compose}} exec {{service}} php artisan {{args}}

# Run a composer command inside the container
composer *args:
    {{compose}} exec {{service}} composer {{args}}

# Run the test suite
test:
    {{compose}} exec {{service}} php artisan test

# Run database migrations
migrate:
    {{compose}} exec {{service}} php artisan migrate --force

# Fresh migrate (drops all tables) — destructive
migrate-fresh:
    {{compose}} exec {{service}} php artisan migrate:fresh --force

# Seed the database
seed:
    {{compose}} exec {{service}} php artisan db:seed --force

# Open Laravel Tinker
tinker:
    {{compose}} exec {{service}} php artisan tinker

# Clear all Laravel caches
fresh-cache:
    {{compose}} exec {{service}} php artisan optimize:clear

# Show container status
ps:
    {{compose}} ps
