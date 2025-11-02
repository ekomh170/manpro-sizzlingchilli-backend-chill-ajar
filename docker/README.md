# 🐳 Docker Setup untuk ChillAjar Backend

Panduan lengkap setup Docker untuk development ChillAjar Backend API dengan Nginx, MySQL, dan PHPMyAdmin.

---

## � **Dokumentasi**

### File Dokumentasi di Folder Ini:

| File | Deskripsi | Safe to Commit? |
|------|-----------|-----------------|
| `README.md` | Panduan Docker setup (local development) | ✅ Yes |
| `DEPLOYMENT-PORTAINER.md` | Panduan deployment ke Portainer (production) | ✅ Yes |
| `CHECKLIST-DEPLOYMENT.md` | Checklist lengkap untuk deployment | ✅ Yes |
| `SECURITY-BEST-PRACTICES.md` | Panduan security best practices | ✅ Yes |
| `QUICK-REFERENCE.template.md` | Template untuk quick reference | ✅ Yes |
| `QUICK-REFERENCE.md` | **Berisi credentials actual** | ⚠️ **NO - GITIGNORED** |

⚠️ **PENTING**: 
- File `QUICK-REFERENCE.md` berisi credentials actual dan sudah di `.gitignore`
- Copy `QUICK-REFERENCE.template.md` → `QUICK-REFERENCE.md` dan isi dengan data actual
- JANGAN commit file yang berisi credentials!

---

## �📋 Prerequisites

Pastikan sudah terinstall di sistem Anda:
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (Windows/Mac)
- [Docker](https://docs.docker.com/engine/install/) & [Docker Compose](https://docs.docker.com/compose/install/) (Linux)
- Git (untuk clone repository)

---

## 🚀 Quick Start

### 1. Clone Repository (jika belum)
```bash
git clone https://github.com/ekomh170/manpro-sizzlingchilli-backend-chill-ajar.git
cd manpro-sizzlingchilli-backend-chill-ajar
```

### 2. Setup Environment File
```bash
# Windows PowerShell
copy .env.docker .env

# Linux/Mac
cp .env.docker .env
```

### 3. Build & Start Docker Containers
```bash
docker-compose up -d --build
```

Tunggu beberapa menit sampai semua container selesai di-build dan running.

### 4. Install Dependencies & Setup Laravel
```bash
# Install composer dependencies
docker-compose exec app composer install

# Generate application key
docker-compose exec app php artisan key:generate

# Run database migrations & seeders
docker-compose exec app php artisan migrate --seed

# Create storage link
docker-compose exec app php artisan storage:link

# Cache configuration
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
```

### 5. Set Permissions (jika diperlukan)
```bash
# Linux/Mac
sudo chown -R $USER:$USER .
chmod -R 775 storage bootstrap/cache

# Windows (jalankan PowerShell sebagai Administrator)
# Biasanya tidak perlu, Docker Desktop sudah handle permissions
```

---

## 🌐 Akses Services

Setelah semua container running, akses service melalui:

| Service | URL | Credentials |
|---------|-----|-------------|
| **Laravel API** | http://localhost:8080 | - |
| **PHPMyAdmin** | http://localhost:8081 | User: `root` atau `dev_chill_ajar`<br>Password: `Manpro2025!` |
| **MySQL** | `localhost:33061` | Host: `localhost`<br>Port: `33061`<br>Database: `db_manpro_sizzlingchilli_backend_chill`<br>User: `dev_chill_ajar`<br>Password: `Manpro2025!` |

---

## 📦 Docker Services

### 1. **app** (PHP-FPM 8.3)
- Container Laravel backend
- PHP 8.3 dengan ekstensi lengkap
- Composer pre-installed
- Port internal: 9000

### 2. **webserver** (Nginx Alpine)
- Web server untuk serve Laravel
- Port: **8080** (HTTP)
- Auto-configured untuk Laravel
- Logs di `storage/logs/nginx/`

### 3. **db** (MySQL 8.0)
- Database server
- Port: **33061** (mapped dari 3306)
- Data persisten di Docker volume `mysql_data`
- Config custom di `docker/mysql/my.cnf`

### 4. **phpmyadmin**
- Web UI untuk manage database
- Port: **8081**
- Auto-login dengan credentials MySQL

---

## 🛠️ Perintah Docker Berguna

### Container Management
```bash
# Lihat status containers
docker-compose ps

# Start containers
docker-compose up -d

# Stop containers
docker-compose down

# Restart specific service
docker-compose restart app

# View logs
docker-compose logs -f
docker-compose logs -f app
docker-compose logs -f webserver
docker-compose logs -f db
```

### Laravel Artisan Commands
```bash
# Jalankan artisan command
docker-compose exec app php artisan <command>

# Contoh:
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:list
docker-compose exec app php artisan tinker
```

### Composer Commands
```bash
# Install packages
docker-compose exec app composer install

# Update packages
docker-compose exec app composer update

# Add new package
docker-compose exec app composer require vendor/package
```

### Database Management
```bash
# Akses MySQL CLI
docker-compose exec db mysql -u dev_chill_ajar -p
# Password: Manpro2025!

# Export database
docker-compose exec db mysqldump -u dev_chill_ajar -pManpro2025! db_manpro_sizzlingchilli_backend_chill > backup.sql

# Import database
docker-compose exec -T db mysql -u dev_chill_ajar -pManpro2025! db_manpro_sizzlingchilli_backend_chill < backup.sql

# Fresh migration
docker-compose exec app php artisan migrate:fresh --seed
```

### Shell Access
```bash
# Masuk ke container app (bash)
docker-compose exec app bash

# Masuk sebagai user www
docker-compose exec -u www app bash

# Masuk ke container db
docker-compose exec db bash
```

---

## 🔧 Troubleshooting

### Problem: Port already in use
**Error**: `Bind for 0.0.0.0:8080 failed: port is already allocated`

**Solusi**:
1. Ganti port di `docker-compose.yml`
2. Atau stop aplikasi yang menggunakan port tersebut

```yaml
webserver:
  ports:
    - "9090:80"  # Ganti dari 8080 ke 9090
```

### Problem: Permission denied pada storage
**Error**: `The stream or file "storage/logs/laravel.log" could not be opened`

**Solusi**:
```bash
# Dari host machine
chmod -R 775 storage bootstrap/cache

# Atau rebuild container
docker-compose down
docker-compose up -d --build
```

### Problem: Database connection refused
**Error**: `SQLSTATE[HY000] [2002] Connection refused`

**Solusi**:
1. Pastikan container `db` running: `docker-compose ps`
2. Cek `.env` DB_HOST harus `db` bukan `localhost`
3. Wait beberapa detik untuk MySQL fully started
4. Clear config cache: `docker-compose exec app php artisan config:clear`

### Problem: Composer install error
**Error**: `Your requirements could not be resolved to an installable set of packages`

**Solusi**:
```bash
# Clear composer cache
docker-compose exec app composer clear-cache

# Update dependencies
docker-compose exec app composer update

# Atau rebuild container
docker-compose down
docker-compose build --no-cache app
docker-compose up -d
```

### Problem: Nginx 502 Bad Gateway
**Error**: `502 Bad Gateway`

**Solusi**:
1. Cek logs: `docker-compose logs webserver`
2. Pastikan PHP-FPM running: `docker-compose ps app`
3. Restart: `docker-compose restart app webserver`

### Problem: MySQL volume corrupt
**Error**: Database tidak bisa start atau data hilang

**Solusi**:
```bash
# HATI-HATI: Ini akan menghapus semua data database!
docker-compose down -v
docker-compose up -d
docker-compose exec app php artisan migrate --seed
```

---

## 🔄 Reset Environment

Jika ingin reset semua (fresh install):

```bash
# Stop dan hapus semua containers + volumes
docker-compose down -v

# Hapus images (opsional)
docker rmi chillajar_php_service

# Rebuild dari awal
docker-compose up -d --build

# Setup ulang Laravel
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate --seed
docker-compose exec app php artisan storage:link
```

---

## 📝 Konfigurasi Custom

### Mengubah PHP Configuration
Edit file `docker/php/local.ini` sesuai kebutuhan, lalu restart:
```bash
docker-compose restart app
```

### Mengubah Nginx Configuration
Edit file `docker/nginx/conf.d/app.conf`, lalu reload:
```bash
docker-compose exec webserver nginx -s reload
# Atau restart
docker-compose restart webserver
```

### Mengubah MySQL Configuration
Edit file `docker/mysql/my.cnf`, lalu restart:
```bash
docker-compose restart db
```

---

## 🚀 Production Deployment

Untuk production, gunakan:
1. ⚠️ Ganti semua password dengan yang strong
2. Set `APP_ENV=production` dan `APP_DEBUG=false` di `.env`
3. Gunakan SSL/HTTPS (tambahkan service untuk Let's Encrypt/Certbot)
4. Pisahkan database ke service external (AWS RDS, dll)
5. Setup backup otomatis untuk database
6. Monitor logs dan performa

**File Production**: Buat `docker-compose.prod.yml` dengan konfigurasi production.

---

## 📚 Dokumentasi Lengkap

- [Docker Documentation](https://docs.docker.com/)
- [Laravel Documentation](https://laravel.com/docs)
- [Nginx Documentation](https://nginx.org/en/docs/)
- [MySQL Documentation](https://dev.mysql.com/doc/)

---

## 🤝 Kontribusi

Jika menemukan issue atau ada saran improvement untuk Docker setup:
1. Buat issue di repository GitHub
2. Atau submit Pull Request

---

## 📧 Support

Jika ada pertanyaan atau butuh bantuan:
- Email: ekomh13@gmail.com
- GitHub Issues: [Create Issue](https://github.com/ekomh170/manpro-sizzlingchilli-backend-chill-ajar/issues)

---

<div align="center">

**🐳 Happy Dockerizing! 🐳**

Made with ❤️ by ChillAjar Team

</div>
