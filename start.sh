#!/usr/bin/env bash

# Bersihkan cache agar konfigurasi terbaru terbaca
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Jalankan migrasi database (opsional, lewati jika gagal agar container tidak langsung mati)
php artisan migrate --force || true

# Jalankan server PHP menggunakan port dari environment variable PORT
echo "Starting PHP server on port ${PORT:-8080}..."
exec php -S 0.0.0.0:${PORT:-8080} -t public