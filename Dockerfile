FROM php:8.3-fpm

# Prevent interactive prompts
ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=Asia/Jakarta

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
    libicu-dev \
    locales \
    zip \
    unzip \
    jpegoptim \
    optipng \
    pngquant \
    gifsicle \
    vim \
    git \
    curl \
    wget \
    supervisor \
 && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
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

# Configure locale
RUN locale-gen en_US.UTF-8
ENV LANG=en_US.UTF-8
ENV LANGUAGE=en_US:en
ENV LC_ALL=en_US.UTF-8

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configure PHP-FPM
RUN echo "catch_workers_output = yes" >> /usr/local/etc/php-fpm.d/www.conf \
 && echo "php_flag[display_errors] = on" >> /usr/local/etc/php-fpm.d/www.conf \
 && echo "php_admin_value[error_log] = /var/www/storage/logs/php-fpm.log" >> /usr/local/etc/php-fpm.d/www.conf

# Configure PHP logging
RUN echo "log_errors = On" >> /usr/local/etc/php/php.ini-production \
 && echo "error_log = /var/www/storage/logs/php-errors.log" >> /usr/local/etc/php/php.ini-production \
 && echo "error_reporting = E_ALL" >> /usr/local/etc/php/php.ini-production \
 && echo "display_errors = On" >> /usr/local/etc/php/php.ini-production \
 && echo "display_startup_errors = On" >> /usr/local/etc/php/php.ini-production \
 && cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

# Copy application code
COPY . .

# Install PHP dependencies (no dev for production)
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader --no-scripts

# Create necessary directories if they don't exist
RUN mkdir -p storage/framework/sessions \
 && mkdir -p storage/framework/views \
 && mkdir -p storage/framework/cache \
 && mkdir -p storage/framework/testing \
 && mkdir -p storage/logs \
 && mkdir -p storage/app/public/foto_profil \
 && mkdir -p storage/app/public/bukti_pembayaran \
 && mkdir -p bootstrap/cache

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Set proper permissions for www-data
RUN chown -R www-data:www-data /var/www \
 && chmod -R 775 storage \
 && chmod -R 775 bootstrap/cache \
 && find storage -type f -exec chmod 664 {} \; \
 && find storage -type d -exec chmod 775 {} \;

# Create log file with proper permissions
RUN touch storage/logs/laravel.log \
 && touch storage/logs/php-fpm.log \
 && touch storage/logs/php-errors.log \
 && chown www-data:www-data storage/logs/*.log \
 && chmod 664 storage/logs/*.log

# Configure supervisor for logging
RUN mkdir -p /var/log/supervisor \
 && mkdir -p /etc/supervisor/conf.d

# Create supervisor config for PHP-FPM
RUN echo '[supervisord]' > /etc/supervisor/conf.d/supervisord.conf \
 && echo 'nodaemon=true' >> /etc/supervisor/conf.d/supervisord.conf \
 && echo 'logfile=/var/www/storage/logs/supervisord.log' >> /etc/supervisor/conf.d/supervisord.conf \
 && echo 'loglevel=info' >> /etc/supervisor/conf.d/supervisord.conf \
 && echo '' >> /etc/supervisor/conf.d/supervisord.conf \
 && echo '[program:php-fpm]' >> /etc/supervisor/conf.d/supervisord.conf \
 && echo 'command=/usr/local/sbin/php-fpm -F -R' >> /etc/supervisor/conf.d/supervisord.conf \
 && echo 'autostart=true' >> /etc/supervisor/conf.d/supervisord.conf \
 && echo 'autorestart=true' >> /etc/supervisor/conf.d/supervisord.conf \
 && echo 'stdout_logfile=/var/www/storage/logs/php-fpm-stdout.log' >> /etc/supervisor/conf.d/supervisord.conf \
 && echo 'stderr_logfile=/var/www/storage/logs/php-fpm-stderr.log' >> /etc/supervisor/conf.d/supervisord.conf \
 && echo 'stdout_logfile_maxbytes=10MB' >> /etc/supervisor/conf.d/supervisord.conf \
 && echo 'stderr_logfile_maxbytes=10MB' >> /etc/supervisor/conf.d/supervisord.conf

# Expose port for php-fpm
EXPOSE 9000

# Use entrypoint script
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
