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

RUN composer install --no-dev --optimize-autoloader

# Set permissions for storage dan bootstrap/cache saja (public/storage akan jadi symlink)
RUN chmod -R 775 storage bootstrap/cache
RUN chown -R www-data:www-data storage bootstrap/cache

# Expose port
EXPOSE 8080

# Start: migrate:fresh, db:seed, storage:link, serve
CMD php artisan migrate:fresh --force && php artisan db:seed --force && php artisan storage:link && php artisan serve --host=0.0.0.0 --port=8080

# Set permissions for storage dan bootstrap/cache saja (public/storage akan jadi symlink)
RUN chmod -R 775 storage bootstrap/cache
RUN chown -R www-data:www-data storage bootstrap/cache

# Catatan troubleshooting:
# Untuk Render, JANGAN copy/generate .env di container. Semua env penting diisi lewat dashboard Render.
# Jika terjadi Bad Gateway di Render:
# - Pastikan semua environment variable (APP_KEY, DB_*) sudah benar dan tidak kosong
# - Cek log deploy untuk error migration/seeder/serve
# - Pastikan database bisa diakses dari container
# - Lihat NOTE_Deploy_Render.txt untuk troubleshooting detail
# Permission public/storage mengikuti symlink ke storage/app/public
