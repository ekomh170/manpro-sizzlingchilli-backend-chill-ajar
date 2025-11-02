#!/bin/sh
set -e

echo "🚀 Starting ChillAjar Backend Setup..."

# Install system dependencies if not already installed
if [ ! -f /usr/local/bin/composer ]; then
    echo "📦 Installing system dependencies..."
    apt-get update
    apt-get install -y git unzip libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
        libonig-dev libxml2-dev libzip-dev libicu-dev curl
    
    echo "🔧 Configuring and installing PHP extensions..."
    docker-php-ext-configure gd --with-freetype --with-jpeg
    docker-php-ext-install -j$(nproc) pdo pdo_mysql mbstring exif pcntl bcmath gd zip intl opcache
    
    echo "🎵 Installing Composer..."
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
    
    echo "✅ System setup complete!"
else
    echo "✅ System dependencies already installed, skipping..."
fi

# Clone repository and install dependencies if not already done
if [ ! -f /var/www/composer.json ]; then
    echo "📥 Cloning Laravel repository..."
    cd /var/www
    git clone https://github.com/ekomh170/manpro-sizzlingchilli-backend-chill-ajar.git .
    
    echo "📦 Installing Composer dependencies..."
    composer install --no-dev --optimize-autoloader --no-interaction
    
    echo "🔐 Setting permissions..."
    chown -R www-data:www-data /var/www
    chmod -R 775 /var/www/storage /var/www/bootstrap/cache
    
    echo "✅ Laravel setup complete!"
else
    echo "✅ Laravel already installed, skipping..."
fi

echo "🚀 Starting PHP-FPM..."
exec php-fpm
