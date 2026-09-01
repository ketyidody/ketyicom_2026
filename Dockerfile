# syntax=docker/dockerfile:1

# ---------------------------------------------------------------------------
# Stage 1: Install PHP dependencies with Composer
# ---------------------------------------------------------------------------
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
# Skip scripts/autoload-dump here (artisan not fully available yet); done in final stage
RUN composer install \
        --no-dev \
        --no-interaction \
        --prefer-dist \
        --no-scripts \
        --no-autoloader

# ---------------------------------------------------------------------------
# Stage 2: Build frontend assets with Vite
# ---------------------------------------------------------------------------
FROM node:20-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci --legacy-peer-deps

# Vite needs the source + laravel plugin manifest paths to build.
# app.js imports Ziggy from vendor/, so pull that in from the composer stage.
COPY vite.config.js tailwind.config.js postcss.config.js jsconfig.json ./
COPY resources ./resources
COPY public ./public
COPY --from=vendor /app/vendor/tightenco/ziggy ./vendor/tightenco/ziggy
RUN npm run build

# ---------------------------------------------------------------------------
# Stage 3: Final runtime image (PHP-FPM + Nginx + Supervisor)
# ---------------------------------------------------------------------------
FROM php:8.3-fpm-alpine AS app

# System deps + runtime libraries for the PHP extensions we build
RUN apk add --no-cache \
        nginx \
        supervisor \
        bash \
        git \
        icu-libs \
        libpng \
        libjpeg-turbo \
        libwebp \
        freetype \
        libzip \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        icu-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        libwebp-dev \
        freetype-dev \
        libzip-dev \
        oniguruma-dev \
        sqlite-dev \
    && docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype \
    && docker-php-ext-install -j"$(nproc)" \
        gd \
        pdo_sqlite \
        pdo_mysql \
        bcmath \
        exif \
        intl \
        zip \
        pcntl \
    && apk del .build-deps

# Composer binary (for artisan/composer inside the container)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Application source
COPY . .

# Vendored PHP dependencies and built frontend assets from earlier stages
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build

# Finish composer setup now that the full app (artisan) is present
RUN composer dump-autoload --no-dev --optimize --no-interaction \
    && mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache database \
    && chown -R www-data:www-data storage bootstrap/cache database public

# Container configuration
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/zz-app.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

EXPOSE 8000

ENTRYPOINT ["entrypoint"]
CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
