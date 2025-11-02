# 🚀 **Deployment ke Portainer - ChillAjar Backend**

Panduan deployment aplikasi ChillAjar Backend ke server VPS menggunakan Portainer.

## 📌 **Informasi Server**

⚠️ **CREDENTIALS**: Untuk informasi lengkap server dan credentials, lihat file `QUICK-REFERENCE.md` (tidak ter-commit untuk keamanan)

### Server Details
- **IP Public**: [Lihat QUICK-REFERENCE.md]
- **Domain**: [Your Domain]
- **Portainer URL**: [Lihat QUICK-REFERENCE.md]
- **VPN**: Twingate (Required)

### Credentials
- **Username**: [Lihat QUICK-REFERENCE.md atau tanya server admin]
- **Password**: [Lihat QUICK-REFERENCE.md atau tanya server admin]

### Port yang TIDAK Boleh Dipakai
```
7500, 5000, 3000, 9443, 81, 80, 443, 8200, 3306
```

### Port yang Kita Gunakan (Aman ✅)
```
8080  → Laravel API
8081  → PHPMyAdmin
33061 → MySQL
```

---

## 🔧 **Persiapan Sebelum Deploy**

### 1. Install Twingate Client
Download dan install Twingate VPN:
```
https://www.twingate.com/download
```

### 2. Koneksi ke VPN
- Buka Twingate Client
- Login dengan credentials yang diberikan
- Pastikan status "Connected"

### 3. Akses Portainer
Setelah VPN terhubung, akses:
```
https://manage.rekyndness.local:9443
```

---

## 📦 **Deployment via Portainer**

### Metode 1: Docker Compose (Recommended)

#### Step 1: Login ke Portainer
1. Buka Portainer URL (lihat `QUICK-REFERENCE.md`)
2. Login dengan credentials dari server admin
3. Pilih environment yang sesuai

#### Step 2: Create Stack
1. Klik **Stacks** di menu kiri
2. Klik **Add stack**
3. Beri nama: `chillajar-backend`

#### Step 3: Upload Docker Compose
Copy-paste isi file `docker-compose.yml` ke Web Editor, atau:
1. Pilih tab **Upload**
2. Upload file `docker-compose.yml`

#### Step 4: Configure Environment Variables
⚠️ **PENTING**: Jangan gunakan credentials default! Generate baru untuk production!

```env
# Laravel Environment
APP_NAME=ChillAjar
APP_ENV=production
APP_DEBUG=false
APP_URL=[YOUR_DOMAIN_URL]

# Database - GANTI DENGAN CREDENTIALS AMAN!
DB_CONNECTION=mysql
DB_HOST=chillajar_db
DB_PORT=3306
DB_DATABASE=db_manpro_sizzlingchilli_backend_chill
DB_USERNAME=[SECURE_USERNAME]
DB_PASSWORD=[SECURE_PASSWORD]

# JWT Secret - GENERATE BARU! 
# Command: openssl rand -base64 32
JWT_SECRET=[GENERATE_NEW_SECRET]

# Mail Configuration - ISI DENGAN DATA ACTUAL
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=[YOUR_EMAIL]
MAIL_PASSWORD=[YOUR_APP_PASSWORD]
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=[YOUR_EMAIL]
MAIL_FROM_NAME="${APP_NAME}"
```

**Security Commands**:
```bash
# Generate APP_KEY
php artisan key:generate --show

# Generate JWT_SECRET
openssl rand -base64 32

# Generate secure password
openssl rand -base64 16
```

#### Step 5: Deploy Stack
1. Klik **Deploy the stack**
2. Tunggu proses build selesai (5-10 menit pertama kali)
3. Check status containers

---

## 🔍 **Verifikasi Deployment**

### 1. Check Container Status
Di Portainer:
- **Stacks** → pilih `chillajar-backend`
- Pastikan semua container status "running" (hijau)

### 2. Test Internal Access (Via VPN)
```bash
# Test Laravel API
curl http://172.17.0.1:8080/api/health

# Via browser
http://172.17.0.1:8080
```

### 3. Check Logs
Di Portainer, untuk masing-masing container:
1. Klik container name
2. Pilih tab **Logs**
3. Check error messages jika ada

---

## 🛠️ **Setup Awal Aplikasi**

### 1. Akses Console Container
Di Portainer:
1. Pilih container `chillajar_app`
2. Klik **Console**
3. Pilih `/bin/bash`
4. Klik **Connect**

### 2. Jalankan Laravel Setup
```bash
# 1. Generate application key
php artisan key:generate

# 2. Run migrations
php artisan migrate --force

# 3. Seed database (optional)
php artisan db:seed --force

# 4. Create storage link
php artisan storage:link

# 5. Clear cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3. Create Admin User (First Time)
```bash
php artisan tinker

# Di Tinker console:
$admin = new App\Models\Admin();
$admin->nama = 'Admin ChillAjar';
$admin->email = 'admin@chillajar.com';
$admin->password = bcrypt('password123');
$admin->no_telp = '08123456789';
$admin->save();
```

---

## 🌐 **Setup Domain & Reverse Proxy**

### Internal Access (Sementara)
Setelah deploy, informasikan ke Febry untuk setup reverse proxy:

```
Port yang digunakan: 8080 (Laravel)
IP Internal: 172.17.0.1:8080
Domain: ekomh29.biz.id
```

Febry akan setup reverse proxy agar bisa diakses publik via:
```
https://ekomh29.biz.id
```

SSL akan otomatis di-generate oleh server.

---

## 📊 **Monitoring**

### Check Container Resources
Di Portainer:
1. Pilih container
2. Tab **Stats** untuk CPU/Memory usage

### View Logs
```bash
# Via Portainer Console
tail -f /var/www/storage/logs/laravel.log
```

### Database Access
PHPMyAdmin tersedia di:
```
http://172.17.0.1:8081

# Credentials:
Server: chillajar_db
Username: dev_chill_ajar
Password: Manpro2025!
```

---

## 🔄 **Update Aplikasi**

### Via Portainer Web Editor

#### 1. Update Source Code
Jika ada perubahan kode:
1. Build image baru locally
2. Push ke Docker registry (Docker Hub/GitHub Container Registry)
3. Update image tag di Stack

#### 2. Restart Containers
1. Pilih Stack
2. Klik **Update the stack**
3. Enable "Re-pull image and redeploy"
4. Deploy

### Via Git Pull (Jika Volume Mounted)

Di console container:
```bash
cd /var/www
git pull origin master
composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🆘 **Troubleshooting**

### Container Tidak Start
1. Check logs di Portainer
2. Pastikan port tidak conflict
3. Check environment variables

### Database Connection Error
```bash
# Test koneksi dari app container
docker exec -it chillajar_app ping chillajar_db

# Check MySQL status
docker exec -it chillajar_db mysql -u dev_chill_ajar -p
```

### Permission Issues
```bash
# Di console app container
chown -R www-data:www-data /var/www/storage
chown -R www-data:www-data /var/www/bootstrap/cache
chmod -R 775 /var/www/storage
chmod -R 775 /var/www/bootstrap/cache
```

### Aplikasi Lambat
1. Check container resources di Stats
2. Increase memory limit jika perlu
3. Enable opcache (sudah enabled by default)

---

## 📹 **Video Tutorial**

Cara deploy ke Portainer (referensi):
```
https://www.youtube.com/watch?v=2ts1NHQKYuk
```

---

## 📞 **Support**

Jika ada kendala deployment:
1. Check logs container terlebih dahulu
2. Screenshot error message
3. Hubungi Febry Billiyagi untuk bantuan server/network

---

## ✅ **Checklist Deployment**

- [ ] VPN Twingate terinstall dan connected
- [ ] Login ke Portainer berhasil
- [ ] Docker Compose file sudah disiapkan
- [ ] Environment variables sudah dikonfigurasi
- [ ] Stack berhasil di-deploy
- [ ] Semua containers status "running"
- [ ] Laravel setup commands dijalankan
- [ ] Test internal access (http://172.17.0.1:8080)
- [ ] Informasi port ke Febry untuk reverse proxy
- [ ] Test public access via domain
- [ ] Admin user dibuat
- [ ] Database seeded (optional)
- [ ] Monitoring setup

---

**Good luck with the deployment! 🚀**
