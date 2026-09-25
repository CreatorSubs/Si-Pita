#!/usr/bin/env bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan migrate --force || true
php -S 0.0.0.0:${PORT:-8080} -t public