#!/usr/bin/env bash

echo "--- Clearing Cache ---"
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo "--- Running Migrations ---"
php artisan migrate --force || echo "Migration failed or skipped, continuing..."

echo "--- Starting PHP Built-in Server on port ${PORT:-8080} ---"
php -S 0.0.0.0:${PORT:-8080} -t public