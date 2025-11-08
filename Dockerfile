FROM ubuntu:22.04

# Prevent interactive prompts
ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=Asia/Jakarta

# Set working directory
WORKDIR /var/www

# Install system dependencies and PHP 8.3
RUN apt-get update && apt-get install -y \
    software-properties-common \
    ca-certificates \
    lsb-release \
    apt-transport-https \
 && add-apt-repository ppa:ondrej/php -y \
 && apt-get update && apt-get install -y \
    php8.3-fpm \
    php8.3-cli \
    php8.3-common \
    php8.3-mysql \
    php8.3-zip \
    php8.3-gd \
    php8.3-mbstring \
    php8.3-curl \
    php8.3-xml \
    php8.3-bcmath \
    php8.3-intl \
    php8.3-opcache \
    php8.3-redis \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    locales \
    zip \
    unzip \
    jpegoptim \
    optipng \
    pngquant \
    gifsicle \
    vim \
    nano \
    emacs-nox \
    git \
    curl \
    wget \
    supervisor \
 && rm -rf /var/lib/apt/lists/*

# Configure locale
RUN locale-gen en_US.UTF-8
ENV LANG=en_US.UTF-8
ENV LANGUAGE=en_US:en
ENV LC_ALL=en_US.UTF-8

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Configure PHP-FPM
RUN sed -i 's/;catch_workers_output = yes/catch_workers_output = yes/' /etc/php/8.3/fpm/pool.d/www.conf \
 && sed -i 's/;php_flag\[display_errors\] = off/php_flag[display_errors] = on/' /etc/php/8.3/fpm/pool.d/www.conf \
 && sed -i 's/;php_admin_value\[error_log\] = \/var\/log\/fpm-php.www.log/php_admin_value[error_log] = \/var\/www\/storage\/logs\/php-fpm.log/' /etc/php/8.3/fpm/pool.d/www.conf

# Configure PHP logging
RUN echo "log_errors = On" >> /etc/php/8.3/fpm/php.ini \
 && echo "error_log = /var/www/storage/logs/php-errors.log" >> /etc/php/8.3/fpm/php.ini \
 && echo "error_reporting = E_ALL" >> /etc/php/8.3/fpm/php.ini \
 && echo "display_errors = On" >> /etc/php/8.3/fpm/php.ini \
 && echo "display_startup_errors = On" >> /etc/php/8.3/fpm/php.ini

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

# Symlink storage (akan error jika sudah ada, tapi tidak masalah)
RUN php artisan storage:link || true

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
 && echo 'command=/usr/sbin/php-fpm8.3 -F -R' >> /etc/supervisor/conf.d/supervisord.conf \
 && echo 'autostart=true' >> /etc/supervisor/conf.d/supervisord.conf \
 && echo 'autorestart=true' >> /etc/supervisor/conf.d/supervisord.conf \
 && echo 'stdout_logfile=/var/www/storage/logs/php-fpm-stdout.log' >> /etc/supervisor/conf.d/supervisord.conf \
 && echo 'stderr_logfile=/var/www/storage/logs/php-fpm-stderr.log' >> /etc/supervisor/conf.d/supervisord.conf \
 && echo 'stdout_logfile_maxbytes=10MB' >> /etc/supervisor/conf.d/supervisord.conf \
 && echo 'stderr_logfile_maxbytes=10MB' >> /etc/supervisor/conf.d/supervisord.conf

# Expose port for php-fpm
EXPOSE 9000

# Start supervisor (which will start php-fpm)
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
