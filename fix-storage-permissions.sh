#!/bin/bash

echo "🔧 Fixing ChillAjar Storage Permissions..."
echo "=========================================="

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if running in Docker container
if [ -f /.dockerenv ]; then
    echo -e "${GREEN}✓${NC} Running inside Docker container"
    DOCKER_EXEC=""
else
    echo -e "${YELLOW}!${NC} Running on host, will use docker exec"
    DOCKER_EXEC="docker exec -it chillajar_app"
fi

echo ""
echo "1️⃣  Creating storage directories..."
$DOCKER_EXEC mkdir -p /var/www/storage/app/public
$DOCKER_EXEC mkdir -p /var/www/storage/app/public/foto_profil
$DOCKER_EXEC mkdir -p /var/www/storage/app/public/bukti_pembayaran
$DOCKER_EXEC mkdir -p /var/www/storage/app/public/foto_sertifikat
$DOCKER_EXEC mkdir -p /var/www/storage/framework/cache
$DOCKER_EXEC mkdir -p /var/www/storage/framework/sessions
$DOCKER_EXEC mkdir -p /var/www/storage/framework/views
$DOCKER_EXEC mkdir -p /var/www/storage/logs
$DOCKER_EXEC mkdir -p /var/www/bootstrap/cache
echo -e "${GREEN}✓${NC} Directories created"

echo ""
echo "2️⃣  Creating symbolic link..."
$DOCKER_EXEC php artisan storage:link
echo -e "${GREEN}✓${NC} Storage linked"

echo ""
echo "3️⃣  Setting permissions (775)..."
$DOCKER_EXEC chmod -R 775 /var/www/storage
$DOCKER_EXEC chmod -R 775 /var/www/bootstrap/cache
echo -e "${GREEN}✓${NC} Permissions set to 775"

echo ""
echo "4️⃣  Setting ownership (www-data)..."
$DOCKER_EXEC chown -R www-data:www-data /var/www/storage
$DOCKER_EXEC chown -R www-data:www-data /var/www/bootstrap/cache
echo -e "${GREEN}✓${NC} Ownership set to www-data:www-data"

echo ""
echo "5️⃣  Clearing Laravel cache..."
$DOCKER_EXEC php artisan config:clear
$DOCKER_EXEC php artisan cache:clear
$DOCKER_EXEC php artisan route:clear
$DOCKER_EXEC php artisan view:clear
echo -e "${GREEN}✓${NC} Cache cleared"

echo ""
echo "6️⃣  Verifying permissions..."
echo ""
echo "Storage directories:"
$DOCKER_EXEC ls -la /var/www/storage
echo ""
echo "Public storage link:"
$DOCKER_EXEC ls -la /var/www/public/ | grep storage
echo ""
echo "Storage/app/public:"
$DOCKER_EXEC ls -la /var/www/storage/app/public

echo ""
echo "=========================================="
echo -e "${GREEN}✅ Storage permissions fixed!${NC}"
echo ""
echo "7️⃣  Testing file upload..."
echo "Test with: curl -X POST http://ekomh29.biz.id:8082/api/test-upload"
