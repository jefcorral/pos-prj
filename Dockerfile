# ---------- Frontend build ----------
FROM node:22-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# ---------- Application ----------
FROM php:8.4-fpm-alpine AS app

RUN apk add --no-cache \
    libpng-dev libjpeg-turbo-dev libwebp-dev libzip-dev icu-dev oniguruma-dev \
    postgresql-dev linux-headers $PHPIZE_DEPS \
    && docker-php-ext-install pdo pdo_pgsql pgsql intl zip opcache bcmath \
    && pecl install redis && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-scripts --no-interaction

COPY . .
COPY --from=frontend /app/public/build ./public/build

RUN composer dump-autoload --optimize \
    && php artisan storage:link --force || true \
    && mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
