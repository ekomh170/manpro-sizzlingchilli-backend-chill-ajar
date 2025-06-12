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

# Copy .env_deploy to .env if exists
RUN if [ -f .env_deploy ]; then cp .env_deploy .env; fi

RUN composer install --no-dev --optimize-autoloader

# Set permissions for storage and bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# Laravel config cache
RUN php artisan config:cache

# Expose port
EXPOSE 8080

# See <attachments> above for file contents. You may not need to search or read the file again.

# Start with migration, storage:link, then serve
CMD php artisan migrate --force && php artisan storage:link && php artisan serve --host=0.0.0.0 --port=8080
