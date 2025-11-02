# ChillAjar Docker Helper Script (PowerShell Version)
# Mempermudah operasi Docker untuk development di Windows

# Colors
function Print-Success { Write-Host "✓ $args" -ForegroundColor Green }
function Print-Error { Write-Host "✗ $args" -ForegroundColor Red }
function Print-Info { Write-Host "ℹ $args" -ForegroundColor Cyan }
function Print-Warning { Write-Host "⚠ $args" -ForegroundColor Yellow }

function Show-Header {
    Write-Host ""
    Write-Host "╔════════════════════════════════════════╗" -ForegroundColor Blue
    Write-Host "║   ChillAjar Docker Helper Script      ║" -ForegroundColor Blue
    Write-Host "╚════════════════════════════════════════╝" -ForegroundColor Blue
    Write-Host ""
}

function Show-Menu {
    Write-Host "Pilih aksi yang ingin dilakukan:" -ForegroundColor White
    Write-Host ""
    Write-Host "  1) 🚀 Setup awal (first time)" -ForegroundColor White
    Write-Host "  2) ▶️  Start containers" -ForegroundColor White
    Write-Host "  3) ⏹️  Stop containers" -ForegroundColor White
    Write-Host "  4) 🔄 Restart containers" -ForegroundColor White
    Write-Host "  5) 📊 Status containers" -ForegroundColor White
    Write-Host "  6) 📝 Lihat logs" -ForegroundColor White
    Write-Host "  7) 🗄️  Database management" -ForegroundColor White
    Write-Host "  8) 🎨 Laravel artisan commands" -ForegroundColor White
    Write-Host "  9) 📦 Composer commands" -ForegroundColor White
    Write-Host " 10) 🔧 Shell access" -ForegroundColor White
    Write-Host " 11) 🧹 Clean up (reset)" -ForegroundColor White
    Write-Host " 12) ℹ️  Info & URLs" -ForegroundColor White
    Write-Host "  0) ❌ Exit" -ForegroundColor White
    Write-Host ""
}

function Setup-FirstTime {
    Print-Info "Memulai setup awal ChillAjar Backend..."

    # Check if .env exists
    if (-not (Test-Path .env)) {
        Print-Info "Copying .env.docker to .env..."
        Copy-Item .env.docker .env
        Print-Success ".env file created"
    } else {
        Print-Warning ".env sudah ada, skip..."
    }

    # Build and start containers
    Print-Info "Building Docker containers..."
    docker-compose up -d --build

    # Wait for database
    Print-Info "Menunggu MySQL siap (30 detik)..."
    Start-Sleep -Seconds 30

    # Install composer dependencies
    Print-Info "Installing Composer dependencies..."
    docker-compose exec -T app composer install

    # Generate key
    Print-Info "Generating application key..."
    docker-compose exec -T app php artisan key:generate

    # Run migrations
    Print-Info "Running database migrations..."
    docker-compose exec -T app php artisan migrate --seed

    # Storage link
    Print-Info "Creating storage link..."
    docker-compose exec -T app php artisan storage:link

    # Cache config
    Print-Info "Caching configuration..."
    docker-compose exec -T app php artisan config:cache

    Print-Success "Setup selesai!"
    Show-Urls
}

function Start-Containers {
    Print-Info "Starting Docker containers..."
    docker-compose up -d
    Print-Success "Containers started!"
    docker-compose ps
}

function Stop-Containers {
    Print-Info "Stopping Docker containers..."
    docker-compose down
    Print-Success "Containers stopped!"
}

function Restart-Containers {
    Print-Info "Restarting Docker containers..."
    docker-compose restart
    Print-Success "Containers restarted!"
    docker-compose ps
}

function Show-Status {
    Print-Info "Docker containers status:"
    docker-compose ps
}

function Show-Logs {
    Write-Host ""
    Write-Host "Pilih service untuk melihat logs:"
    Write-Host "  1) All services"
    Write-Host "  2) App (PHP-FPM)"
    Write-Host "  3) Webserver (Nginx)"
    Write-Host "  4) Database (MySQL)"
    Write-Host "  5) PHPMyAdmin"
    Write-Host ""
    $logChoice = Read-Host "Pilih [1-5]"

    switch ($logChoice) {
        1 { docker-compose logs -f }
        2 { docker-compose logs -f app }
        3 { docker-compose logs -f webserver }
        4 { docker-compose logs -f db }
        5 { docker-compose logs -f phpmyadmin }
        default { Print-Error "Pilihan tidak valid" }
    }
}

function Database-Menu {
    Write-Host ""
    Write-Host "Database Management:"
    Write-Host "  1) MySQL CLI"
    Write-Host "  2) Export database"
    Write-Host "  3) Import database"
    Write-Host "  4) Fresh migrate"
    Write-Host "  5) Run migrations"
    Write-Host "  6) Run seeders"
    Write-Host ""
    $dbChoice = Read-Host "Pilih [1-6]"

    switch ($dbChoice) {
        1 {
            Print-Info "Opening MySQL CLI..."
            docker-compose exec db mysql -u dev_chill_ajar -pManpro2025! db_manpro_sizzlingchilli_backend_chill
        }
        2 {
            Print-Info "Exporting database..."
            $filename = "backup_$(Get-Date -Format 'yyyyMMdd_HHmmss').sql"
            docker-compose exec db mysqldump -u dev_chill_ajar -pManpro2025! db_manpro_sizzlingchilli_backend_chill | Out-File -Encoding UTF8 $filename
            Print-Success "Database exported to $filename"
        }
        3 {
            $backupFile = Read-Host "Enter backup file path"
            if (Test-Path $backupFile) {
                Print-Info "Importing database..."
                Get-Content $backupFile | docker-compose exec -T db mysql -u dev_chill_ajar -pManpro2025! db_manpro_sizzlingchilli_backend_chill
                Print-Success "Database imported!"
            } else {
                Print-Error "File tidak ditemukan: $backupFile"
            }
        }
        4 {
            $confirm = Read-Host "This will DELETE all data! Continue? (y/n)"
            if ($confirm -eq "y") {
                Print-Info "Running fresh migration..."
                docker-compose exec app php artisan migrate:fresh --seed
                Print-Success "Fresh migration completed!"
            }
        }
        5 {
            Print-Info "Running migrations..."
            docker-compose exec app php artisan migrate
            Print-Success "Migrations completed!"
        }
        6 {
            Print-Info "Running seeders..."
            docker-compose exec app php artisan db:seed
            Print-Success "Seeders completed!"
        }
        default {
            Print-Error "Pilihan tidak valid"
        }
    }
}

function Artisan-Menu {
    Write-Host ""
    Write-Host "Laravel Artisan Commands:"
    Write-Host "  1) Clear cache"
    Write-Host "  2) Clear config"
    Write-Host "  3) Clear routes"
    Write-Host "  4) Clear views"
    Write-Host "  5) Cache config"
    Write-Host "  6) Cache routes"
    Write-Host "  7) Route list"
    Write-Host "  8) Tinker"
    Write-Host "  9) Custom command"
    Write-Host ""
    $artisanChoice = Read-Host "Pilih [1-9]"

    switch ($artisanChoice) {
        1 { docker-compose exec app php artisan cache:clear }
        2 { docker-compose exec app php artisan config:clear }
        3 { docker-compose exec app php artisan route:clear }
        4 { docker-compose exec app php artisan view:clear }
        5 { docker-compose exec app php artisan config:cache }
        6 { docker-compose exec app php artisan route:cache }
        7 { docker-compose exec app php artisan route:list }
        8 { docker-compose exec app php artisan tinker }
        9 {
            $customCmd = Read-Host "Enter artisan command (without 'php artisan')"
            docker-compose exec app php artisan $customCmd
        }
        default {
            Print-Error "Pilihan tidak valid"
        }
    }
}

function Composer-Menu {
    Write-Host ""
    Write-Host "Composer Commands:"
    Write-Host "  1) Install dependencies"
    Write-Host "  2) Update dependencies"
    Write-Host "  3) Dump autoload"
    Write-Host "  4) Require package"
    Write-Host "  5) Custom command"
    Write-Host ""
    $composerChoice = Read-Host "Pilih [1-5]"

    switch ($composerChoice) {
        1 { docker-compose exec app composer install }
        2 { docker-compose exec app composer update }
        3 { docker-compose exec app composer dump-autoload }
        4 {
            $packageName = Read-Host "Enter package name (e.g., vendor/package)"
            docker-compose exec app composer require $packageName
        }
        5 {
            $customCmd = Read-Host "Enter composer command"
            docker-compose exec app composer $customCmd
        }
        default {
            Print-Error "Pilihan tidak valid"
        }
    }
}

function Shell-Menu {
    Write-Host ""
    Write-Host "Shell Access:"
    Write-Host "  1) App container (bash)"
    Write-Host "  2) App container as www user"
    Write-Host "  3) Database container"
    Write-Host "  4) Webserver container"
    Write-Host ""
    $shellChoice = Read-Host "Pilih [1-4]"

    switch ($shellChoice) {
        1 { docker-compose exec app bash }
        2 { docker-compose exec -u www app bash }
        3 { docker-compose exec db bash }
        4 { docker-compose exec webserver sh }
        default {
            Print-Error "Pilihan tidak valid"
        }
    }
}

function Clean-Up {
    Print-Warning "This will remove all containers, volumes, and data!"
    $confirm = Read-Host "Are you sure? (y/n)"

    if ($confirm -eq "y") {
        Print-Info "Stopping and removing containers..."
        docker-compose down -v

        Print-Info "Removing images..."
        docker rmi chillajar_php_service 2>$null

        Print-Success "Clean up completed!"
        Print-Info "Run setup (option 1) to start fresh"
    } else {
        Print-Info "Cancelled"
    }
}

function Show-Urls {
    Write-Host ""
    Print-Info "=== ChillAjar Backend URLs ==="
    Write-Host ""
    Write-Host "  🌐 Laravel API:     http://localhost:8080" -ForegroundColor White
    Write-Host "  📊 PHPMyAdmin:      http://localhost:8081" -ForegroundColor White
    Write-Host "  🗄️  MySQL:          localhost:33061" -ForegroundColor White
    Write-Host ""
    Write-Host "  Database Credentials:" -ForegroundColor Yellow
    Write-Host "    Database: db_manpro_sizzlingchilli_backend_chill" -ForegroundColor White
    Write-Host "    User:     dev_chill_ajar" -ForegroundColor White
    Write-Host "    Password: Manpro2025!" -ForegroundColor White
    Write-Host ""
}

# Main loop
Clear-Host
Show-Header

while ($true) {
    Show-Menu
    $choice = Read-Host "Pilih [0-12]"

    switch ($choice) {
        1 { Setup-FirstTime }
        2 { Start-Containers }
        3 { Stop-Containers }
        4 { Restart-Containers }
        5 { Show-Status }
        6 { Show-Logs }
        7 { Database-Menu }
        8 { Artisan-Menu }
        9 { Composer-Menu }
        10 { Shell-Menu }
        11 { Clean-Up }
        12 { Show-Urls }
        0 {
            Print-Info "Goodbye!"
            exit 0
        }
        default {
            Print-Error "Pilihan tidak valid"
        }
    }

    Write-Host ""
    Write-Host "Press Enter to continue..." -ForegroundColor Gray
    Read-Host
    Clear-Host
    Show-Header
}
