# PowerShell Script - Fix ChillAjar Storage Permissions
# Run: .\fix-storage-permissions.ps1

Write-Host "🔧 Fixing ChillAjar Storage Permissions..." -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan

$containerName = "chillajar_app"

# Check if container is running
$containerRunning = docker ps --filter "name=$containerName" --format "{{.Names}}"

if ($containerRunning -ne $containerName) {
    Write-Host "❌ Container $containerName is not running!" -ForegroundColor Red
    Write-Host "Start the container first with: docker-compose up -d" -ForegroundColor Yellow
    exit 1
}

Write-Host ""
Write-Host "1️⃣  Creating storage directories..." -ForegroundColor Yellow
docker exec $containerName mkdir -p /var/www/storage/app/public
docker exec $containerName mkdir -p /var/www/storage/app/public/foto_profil
docker exec $containerName mkdir -p /var/www/storage/app/public/bukti_pembayaran
docker exec $containerName mkdir -p /var/www/storage/app/public/foto_sertifikat
docker exec $containerName mkdir -p /var/www/storage/framework/cache
docker exec $containerName mkdir -p /var/www/storage/framework/sessions
docker exec $containerName mkdir -p /var/www/storage/framework/views
docker exec $containerName mkdir -p /var/www/storage/logs
docker exec $containerName mkdir -p /var/www/bootstrap/cache
Write-Host "✓ Directories created" -ForegroundColor Green

Write-Host ""
Write-Host "2️⃣  Creating symbolic link..." -ForegroundColor Yellow
docker exec $containerName php artisan storage:link
Write-Host "✓ Storage linked" -ForegroundColor Green

Write-Host ""
Write-Host "3️⃣  Setting permissions (775)..." -ForegroundColor Yellow
docker exec $containerName chmod -R 775 /var/www/storage
docker exec $containerName chmod -R 775 /var/www/bootstrap/cache
Write-Host "✓ Permissions set to 775" -ForegroundColor Green

Write-Host ""
Write-Host "4️⃣  Setting ownership (www-data)..." -ForegroundColor Yellow
docker exec $containerName chown -R www-data:www-data /var/www/storage
docker exec $containerName chown -R www-data:www-data /var/www/bootstrap/cache
Write-Host "✓ Ownership set to www-data:www-data" -ForegroundColor Green

Write-Host ""
Write-Host "5️⃣  Clearing Laravel cache..." -ForegroundColor Yellow
docker exec $containerName php artisan config:clear
docker exec $containerName php artisan cache:clear
docker exec $containerName php artisan route:clear
docker exec $containerName php artisan view:clear
Write-Host "✓ Cache cleared" -ForegroundColor Green

Write-Host ""
Write-Host "6️⃣  Verifying permissions..." -ForegroundColor Yellow
Write-Host ""
Write-Host "Storage directories:" -ForegroundColor Cyan
docker exec $containerName ls -la /var/www/storage
Write-Host ""
Write-Host "Public storage link:" -ForegroundColor Cyan
docker exec $containerName ls -la /var/www/public/ | Select-String "storage"
Write-Host ""
Write-Host "Storage/app/public:" -ForegroundColor Cyan
docker exec $containerName ls -la /var/www/storage/app/public

Write-Host ""
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "✅ Storage permissions fixed!" -ForegroundColor Green
Write-Host ""
Write-Host "📝 Next Steps:" -ForegroundColor Yellow
Write-Host "1. Restart Nginx: docker restart chillajar_webserver" -ForegroundColor White
Write-Host "2. Test upload from frontend" -ForegroundColor White
Write-Host "3. Check browser console for CORS errors" -ForegroundColor White
