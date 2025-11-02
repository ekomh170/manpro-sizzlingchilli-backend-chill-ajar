#!/bin/bash

# ChillAjar Docker Helper Script
# Mempermudah operasi Docker untuk development

set -e

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Functions
print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

show_header() {
    echo ""
    echo "╔════════════════════════════════════════╗"
    echo "║   ChillAjar Docker Helper Script      ║"
    echo "╚════════════════════════════════════════╝"
    echo ""
}

show_menu() {
    echo "Pilih aksi yang ingin dilakukan:"
    echo ""
    echo "  1) 🚀 Setup awal (first time)"
    echo "  2) ▶️  Start containers"
    echo "  3) ⏹️  Stop containers"
    echo "  4) 🔄 Restart containers"
    echo "  5) 📊 Status containers"
    echo "  6) 📝 Lihat logs"
    echo "  7) 🗄️  Database management"
    echo "  8) 🎨 Laravel artisan commands"
    echo "  9) 📦 Composer commands"
    echo " 10) 🔧 Shell access"
    echo " 11) 🧹 Clean up (reset)"
    echo " 12) ℹ️  Info & URLs"
    echo "  0) ❌ Exit"
    echo ""
    echo -n "Pilih [0-12]: "
}

setup_first_time() {
    print_info "Memulai setup awal ChillAjar Backend..."
    
    # Check if .env exists
    if [ ! -f .env ]; then
        print_info "Copying .env.docker to .env..."
        cp .env.docker .env
        print_success ".env file created"
    else
        print_warning ".env sudah ada, skip..."
    fi
    
    # Build and start containers
    print_info "Building Docker containers..."
    docker-compose up -d --build
    
    # Wait for database
    print_info "Menunggu MySQL siap (30 detik)..."
    sleep 30
    
    # Install composer dependencies
    print_info "Installing Composer dependencies..."
    docker-compose exec -T app composer install
    
    # Generate key
    print_info "Generating application key..."
    docker-compose exec -T app php artisan key:generate
    
    # Run migrations
    print_info "Running database migrations..."
    docker-compose exec -T app php artisan migrate --seed
    
    # Storage link
    print_info "Creating storage link..."
    docker-compose exec -T app php artisan storage:link
    
    # Cache config
    print_info "Caching configuration..."
    docker-compose exec -T app php artisan config:cache
    
    print_success "Setup selesai!"
    show_urls
}

start_containers() {
    print_info "Starting Docker containers..."
    docker-compose up -d
    print_success "Containers started!"
    docker-compose ps
}

stop_containers() {
    print_info "Stopping Docker containers..."
    docker-compose down
    print_success "Containers stopped!"
}

restart_containers() {
    print_info "Restarting Docker containers..."
    docker-compose restart
    print_success "Containers restarted!"
    docker-compose ps
}

show_status() {
    print_info "Docker containers status:"
    docker-compose ps
}

show_logs() {
    echo ""
    echo "Pilih service untuk melihat logs:"
    echo "  1) All services"
    echo "  2) App (PHP-FPM)"
    echo "  3) Webserver (Nginx)"
    echo "  4) Database (MySQL)"
    echo "  5) PHPMyAdmin"
    echo ""
    echo -n "Pilih [1-5]: "
    read log_choice
    
    case $log_choice in
        1) docker-compose logs -f ;;
        2) docker-compose logs -f app ;;
        3) docker-compose logs -f webserver ;;
        4) docker-compose logs -f db ;;
        5) docker-compose logs -f phpmyadmin ;;
        *) print_error "Pilihan tidak valid" ;;
    esac
}

database_menu() {
    echo ""
    echo "Database Management:"
    echo "  1) MySQL CLI"
    echo "  2) Export database"
    echo "  3) Import database"
    echo "  4) Fresh migrate"
    echo "  5) Run migrations"
    echo "  6) Run seeders"
    echo ""
    echo -n "Pilih [1-6]: "
    read db_choice
    
    case $db_choice in
        1)
            print_info "Opening MySQL CLI..."
            docker-compose exec db mysql -u dev_chill_ajar -pManpro2025! db_manpro_sizzlingchilli_backend_chill
            ;;
        2)
            print_info "Exporting database..."
            docker-compose exec db mysqldump -u dev_chill_ajar -pManpro2025! db_manpro_sizzlingchilli_backend_chill > backup_$(date +%Y%m%d_%H%M%S).sql
            print_success "Database exported!"
            ;;
        3)
            echo -n "Enter backup file path: "
            read backup_file
            if [ -f "$backup_file" ]; then
                print_info "Importing database..."
                docker-compose exec -T db mysql -u dev_chill_ajar -pManpro2025! db_manpro_sizzlingchilli_backend_chill < "$backup_file"
                print_success "Database imported!"
            else
                print_error "File tidak ditemukan: $backup_file"
            fi
            ;;
        4)
            print_warning "This will DELETE all data! Continue? (y/n): "
            read confirm
            if [ "$confirm" = "y" ]; then
                print_info "Running fresh migration..."
                docker-compose exec app php artisan migrate:fresh --seed
                print_success "Fresh migration completed!"
            fi
            ;;
        5)
            print_info "Running migrations..."
            docker-compose exec app php artisan migrate
            print_success "Migrations completed!"
            ;;
        6)
            print_info "Running seeders..."
            docker-compose exec app php artisan db:seed
            print_success "Seeders completed!"
            ;;
        *)
            print_error "Pilihan tidak valid"
            ;;
    esac
}

artisan_menu() {
    echo ""
    echo "Laravel Artisan Commands:"
    echo "  1) Clear cache"
    echo "  2) Clear config"
    echo "  3) Clear routes"
    echo "  4) Clear views"
    echo "  5) Cache config"
    echo "  6) Cache routes"
    echo "  7) Route list"
    echo "  8) Tinker"
    echo "  9) Custom command"
    echo ""
    echo -n "Pilih [1-9]: "
    read artisan_choice
    
    case $artisan_choice in
        1) docker-compose exec app php artisan cache:clear ;;
        2) docker-compose exec app php artisan config:clear ;;
        3) docker-compose exec app php artisan route:clear ;;
        4) docker-compose exec app php artisan view:clear ;;
        5) docker-compose exec app php artisan config:cache ;;
        6) docker-compose exec app php artisan route:cache ;;
        7) docker-compose exec app php artisan route:list ;;
        8) docker-compose exec app php artisan tinker ;;
        9)
            echo -n "Enter artisan command (without 'php artisan'): "
            read custom_cmd
            docker-compose exec app php artisan $custom_cmd
            ;;
        *)
            print_error "Pilihan tidak valid"
            ;;
    esac
}

composer_menu() {
    echo ""
    echo "Composer Commands:"
    echo "  1) Install dependencies"
    echo "  2) Update dependencies"
    echo "  3) Dump autoload"
    echo "  4) Require package"
    echo "  5) Custom command"
    echo ""
    echo -n "Pilih [1-5]: "
    read composer_choice
    
    case $composer_choice in
        1) docker-compose exec app composer install ;;
        2) docker-compose exec app composer update ;;
        3) docker-compose exec app composer dump-autoload ;;
        4)
            echo -n "Enter package name (e.g., vendor/package): "
            read package_name
            docker-compose exec app composer require $package_name
            ;;
        5)
            echo -n "Enter composer command: "
            read custom_cmd
            docker-compose exec app composer $custom_cmd
            ;;
        *)
            print_error "Pilihan tidak valid"
            ;;
    esac
}

shell_menu() {
    echo ""
    echo "Shell Access:"
    echo "  1) App container (bash)"
    echo "  2) App container as www user"
    echo "  3) Database container"
    echo "  4) Webserver container"
    echo ""
    echo -n "Pilih [1-4]: "
    read shell_choice
    
    case $shell_choice in
        1) docker-compose exec app bash ;;
        2) docker-compose exec -u www app bash ;;
        3) docker-compose exec db bash ;;
        4) docker-compose exec webserver sh ;;
        *)
            print_error "Pilihan tidak valid"
            ;;
    esac
}

clean_up() {
    print_warning "This will remove all containers, volumes, and data!"
    echo -n "Are you sure? (y/n): "
    read confirm
    
    if [ "$confirm" = "y" ]; then
        print_info "Stopping and removing containers..."
        docker-compose down -v
        
        print_info "Removing images..."
        docker rmi chillajar_php_service 2>/dev/null || true
        
        print_success "Clean up completed!"
        print_info "Run setup (option 1) to start fresh"
    else
        print_info "Cancelled"
    fi
}

show_urls() {
    echo ""
    print_info "=== ChillAjar Backend URLs ==="
    echo ""
    echo "  🌐 Laravel API:     http://localhost:8080"
    echo "  📊 PHPMyAdmin:      http://localhost:8081"
    echo "  🗄️  MySQL:          localhost:33061"
    echo ""
    echo "  Database Credentials:"
    echo "    Database: db_manpro_sizzlingchilli_backend_chill"
    echo "    User:     dev_chill_ajar"
    echo "    Password: Manpro2025!"
    echo ""
}

# Main loop
show_header

while true; do
    show_menu
    read choice
    
    case $choice in
        1) setup_first_time ;;
        2) start_containers ;;
        3) stop_containers ;;
        4) restart_containers ;;
        5) show_status ;;
        6) show_logs ;;
        7) database_menu ;;
        8) artisan_menu ;;
        9) composer_menu ;;
        10) shell_menu ;;
        11) clean_up ;;
        12) show_urls ;;
        0)
            print_info "Goodbye!"
            exit 0
            ;;
        *)
            print_error "Pilihan tidak valid"
            ;;
    esac
    
    echo ""
    echo "Press Enter to continue..."
    read
    clear
    show_header
done
