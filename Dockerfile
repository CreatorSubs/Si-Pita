# Stage 1: Build Vite assets using Node.js
FROM node:20-alpine AS node-builder
WORKDIR /app
COPY . .
RUN npm install && npm run build

FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip nginx

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

COPY --from=node-builder /app/public/build /var/www/public/build

RUN composer install --no-dev --optimize-autoloader --ignore-platform-req=php

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 8080

CMD php artisan config:clear && php artisan cache:clear && php artisan view:clear && php artisan migrate --force && php -S 0.0.0.0:8080 -t public