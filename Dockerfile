FROM php:8.3-fpm

# Set working directory
WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libicu-dev \
    libpq-dev \
 && rm -rf /var/lib/apt/lists/*

# Install PHP extensions required by Laravel
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
    opcache

# Copy Composer from official image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application code
COPY . .

# Install PHP dependencies (no dev for production)
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader --no-scripts

# Create necessary directories if they don't exist
RUN mkdir -p storage/framework/sessions \
 && mkdir -p storage/framework/views \
 && mkdir -p storage/framework/cache \
 && mkdir -p storage/logs \
 && mkdir -p bootstrap/cache

# Symlink storage
RUN php artisan storage:link

# Set proper permissions
RUN chown -R www-data:www-data /var/www \
 && chmod -R 775 storage \
 && chmod -R 775 bootstrap/cache

# Expose port for php-fpm
EXPOSE 9000

# Start php-fpm
CMD ["php-fpm"]
