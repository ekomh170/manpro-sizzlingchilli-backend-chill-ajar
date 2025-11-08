#!/bin/bash

# ChillAjar Docker Quick Setup Script
# Run this after containers are up: bash quick-setup.sh

echo "=================================="
echo "ChillAjar Docker Quick Setup"
echo "=================================="
echo ""

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Container name
CONTAINER="chillajar_app"

# Check if container is running
if ! docker ps | grep -q $CONTAINER; then
    echo -e "${RED}Error: Container $CONTAINER is not running!${NC}"
    echo "Please start the container first: docker-compose up -d"
    exit 1
fi

echo -e "${YELLOW}[1/8] Installing Composer dependencies...${NC}"
docker exec -it $CONTAINER composer install --optimize-autoloader --no-dev
echo -e "${GREEN}✓ Composer install done${NC}"
echo ""

echo -e "${YELLOW}[2/8] Generating application key...${NC}"
docker exec -it $CONTAINER php artisan key:generate --force
echo -e "${GREEN}✓ Application key generated${NC}"
echo ""

echo -e "${YELLOW}[3/8] Running database migrations...${NC}"
docker exec -it $CONTAINER php artisan migrate --force
echo -e "${GREEN}✓ Migrations completed${NC}"
echo ""

echo -e "${YELLOW}[4/8] Running database seeders...${NC}"
docker exec -it $CONTAINER php artisan db:seed --force
echo -e "${GREEN}✓ Seeders completed${NC}"
echo ""

echo -e "${YELLOW}[5/8] Creating storage symlink...${NC}"
docker exec -it $CONTAINER rm -rf /var/www/public/storage
docker exec -it $CONTAINER php artisan storage:link
echo -e "${GREEN}✓ Storage link created${NC}"
echo ""

echo -e "${YELLOW}[6/8] Creating upload directories...${NC}"
docker exec -it $CONTAINER mkdir -p /var/www/storage/app/public/foto_profil
docker exec -it $CONTAINER mkdir -p /var/www/storage/app/public/bukti_pembayaran
echo -e "${GREEN}✓ Upload directories created${NC}"
echo ""

echo -e "${YELLOW}[7/8] Fixing storage permissions...${NC}"
docker exec -it $CONTAINER chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/www/public/storage
docker exec -it $CONTAINER chmod -R 775 /var/www/storage
docker exec -it $CONTAINER chmod -R 775 /var/www/bootstrap/cache
docker exec -it $CONTAINER chmod -R 775 /var/www/public/storage
docker exec -it $CONTAINER find /var/www/storage/app/public -type f -exec chmod 664 {} \;
docker exec -it $CONTAINER find /var/www/storage/app/public -type d -exec chmod 775 {} \;
echo -e "${GREEN}✓ Permissions fixed${NC}"
echo ""

echo -e "${YELLOW}[8/8] Clearing and caching config...${NC}"
docker exec -it $CONTAINER php artisan config:clear
docker exec -it $CONTAINER php artisan cache:clear
docker exec -it $CONTAINER php artisan route:clear
docker exec -it $CONTAINER php artisan view:clear
docker exec -it $CONTAINER php artisan config:cache
docker exec -it $CONTAINER php artisan route:cache
echo -e "${GREEN}✓ Cache cleared and rebuilt${NC}"
echo ""

echo "=================================="
echo -e "${GREEN}Setup Complete!${NC}"
echo "=================================="
echo ""
echo "Verifying setup..."
echo ""

# Verify storage permissions
echo -e "${YELLOW}Storage permissions:${NC}"
docker exec -it $CONTAINER ls -la /var/www/storage | head -10
echo ""

# Verify symlink
echo -e "${YELLOW}Storage symlink:${NC}"
docker exec -it $CONTAINER ls -la /var/www/public/storage
echo ""

# Verify upload directories
echo -e "${YELLOW}Upload directories:${NC}"
docker exec -it $CONTAINER ls -la /var/www/storage/app/public/
echo ""

echo "=================================="
echo -e "${GREEN}All Done!${NC}"
echo "=================================="
echo ""
echo "Next steps:"
echo "1. Test API: curl http://ekomh29.biz.id:8082/api/cors-check"
echo "2. Test upload from frontend"
echo "3. Check logs: docker logs -f chillajar_app"
echo ""
echo "If you encounter issues:"
echo "- Check SETUP-DOCKER-COMPLETE.md for troubleshooting"
echo "- View logs: docker exec -it chillajar_app tail -f /var/www/storage/logs/laravel.log"
echo ""
