#!/bin/bash
set -e

echo "Starting ChillAjar Laravel Application..."

# Wait for database to be ready (optional)
# sleep 5

# Create storage symlink if not exists
echo "Creating storage symlink..."
php artisan storage:link || true

# Set proper permissions
echo "Setting permissions..."
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Start supervisord (which will start php-fpm)
echo "Starting supervisord..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
