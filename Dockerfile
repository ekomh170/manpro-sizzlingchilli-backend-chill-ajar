FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev libpq-dev \
    && docker-php-ext-install pdo_pgsql mbstring zip exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

COPY . .

# (PRODUCTION RENDER) JANGAN copy .env_deploy ke .env, biarkan Laravel baca dari environment variable Render
# RUN if [ -f .env_deploy ]; then cp .env_deploy .env; fi

RUN composer install --no-dev --optimize-autoloader

# Set permissions for storage and bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache
RUN chown -R www-data:www-data storage bootstrap/cache public/storage && chmod -R 775 storage bootstrap/cache public/storage

# HAPUS config:cache untuk Render, karena butuh file .env di build time
# RUN php artisan config:cache

# Expose port
EXPOSE 8080

# Start: migrate, db:seed, storage:link, serve
CMD php artisan migrate --force && php artisan db:seed --force && php artisan storage:link && php artisan serve --host=0.0.0.0 --port=8080

# Catatan troubleshooting:
# Untuk Render, JANGAN copy/generate .env di container. Semua env penting diisi lewat dashboard Render.
# Jika terjadi Bad Gateway di Render:
# - Pastikan semua environment variable (APP_KEY, DB_*) sudah benar dan tidak kosong
# - Cek log deploy untuk error migration/seeder/serve
# - Pastikan database bisa diakses dari container
# - Lihat NOTE_Deploy_Render.txt untuk troubleshooting detail
