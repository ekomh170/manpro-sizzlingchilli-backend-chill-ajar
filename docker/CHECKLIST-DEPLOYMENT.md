# ✅ **Checklist Deployment ChillAjar Backend**

Gunakan checklist ini untuk memastikan deployment berjalan lancar.

---

## 📋 **Pre-Deployment**

### 1. Persiapan Lokal
- [ ] Docker Compose file sudah final
- [ ] Environment variables production sudah disiapkan
- [ ] Database migrations sudah di-test
- [ ] API endpoints sudah di-test locally
- [ ] Dokumentasi API sudah lengkap

### 2. Persiapan Server
⚠️ **CREDENTIALS**: Lihat file `QUICK-REFERENCE.md` untuk informasi lengkap dan actual

- [ ] VPN Twingate sudah terinstall
- [ ] Credentials server sudah diterima dari server admin
- [ ] Domain sudah pointing ke IP server
  - [ ] Domain configured
  - [ ] IP pointing verified
  - [ ] DNS Check completed

### 3. Port Configuration
- [ ] Port 8080 untuk Laravel (✅ Aman)
- [ ] Port 8081 untuk PHPMyAdmin (✅ Aman)
- [ ] Port 33061 untuk MySQL (✅ Aman)
- [ ] Tidak menggunakan port restricted: 7500, 5000, 3000, 9443, 81, 80, 443, 8200, 3306

---

## 🌐 **Network Access**

### 1. VPN Connection
- [ ] Twingate Client terinstall
- [ ] VPN status: **Connected**
- [ ] Bisa ping server (IP dari QUICK-REFERENCE.md)

### 2. Portainer Access
- [ ] Bisa akses Portainer URL (lihat QUICK-REFERENCE.md)
- [ ] Login berhasil dengan credentials dari server admin
- [ ] Environment tersedia dan aktif

---

## 🚀 **Deployment Process**

### 1. Create Stack di Portainer
- [ ] Stack name: `chillajar-backend`
- [ ] Docker Compose file uploaded
- [ ] Web editor terisi dengan benar

### 2. Environment Variables
⚠️ **SECURITY CRITICAL**: Jangan gunakan default values! Generate credentials baru!

```env
# Laravel Core
APP_NAME=ChillAjar
APP_ENV=production
APP_DEBUG=false
APP_KEY=                              # ← Generate with: php artisan key:generate --show
APP_URL=[YOUR_DOMAIN_URL]
APP_TIMEZONE=Asia/Jakarta

# Database - GENERATE CREDENTIALS BARU!
DB_CONNECTION=mysql
DB_HOST=chillajar_db
DB_PORT=3306
DB_DATABASE=db_manpro_sizzlingchilli_backend_chill
DB_USERNAME=[SECURE_USERNAME]         # ← GANTI DENGAN USERNAME AMAN!
DB_PASSWORD=[SECURE_PASSWORD]         # ← GANTI DENGAN PASSWORD AMAN!

# JWT Authentication - GENERATE BARU!
JWT_SECRET=                           # ← Generate: openssl rand -base64 32
JWT_TTL=60
JWT_REFRESH_TTL=20160

# Cache & Session
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database

# Mail (Gmail SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=                        # ← Your Gmail
MAIL_PASSWORD=                        # ← App Password (2FA)
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@chillajar.com
MAIL_FROM_NAME="${APP_NAME}"

# WhatsApp Gateway (Optional)
WHATSAPP_API_URL=
WHATSAPP_API_TOKEN=

# File Upload
MAX_UPLOAD_SIZE=20480                 # 20MB in KB
```

**Checklist Environment Variables:**
- [ ] `APP_KEY` generated (baru, jangan pakai yang di .env.example)
- [ ] `APP_URL` sesuai domain
- [ ] `DB_*` credentials sesuai dengan docker-compose
- [ ] `JWT_SECRET` generated (secure random string)
- [ ] `MAIL_*` configured (jika perlu email notification)
- [ ] `APP_DEBUG=false` untuk production

### 3. Deploy Stack
- [ ] Click "Deploy the stack"
- [ ] Wait for build process (5-10 menit pertama kali)
- [ ] Check logs jika ada error

---

## 🔍 **Post-Deployment Verification**

### 1. Container Status
Check di Portainer → Stacks → chillajar-backend:
- [ ] `chillajar_app` - Status: **Running** (hijau)
- [ ] `chillajar_webserver` - Status: **Running** (hijau)
- [ ] `chillajar_db` - Status: **Running** (hijau)
- [ ] `chillajar_phpmyadmin` - Status: **Running** (hijau)

### 2. Container Logs
Check logs untuk masing-masing container, pastikan tidak ada error:
- [ ] App logs: Tidak ada PHP fatal error
- [ ] Webserver logs: Nginx started successfully
- [ ] DB logs: MySQL ready for connections
- [ ] PHPMyAdmin logs: Accessible

---

## 🛠️ **Laravel Setup**

### 1. Access Console
Di Portainer:
- [ ] Pilih container `chillajar_app`
- [ ] Click **Console**
- [ ] Select `/bin/bash`
- [ ] Click **Connect**

### 2. Run Laravel Commands
```bash
# Generate application key (if not set via env)
php artisan key:generate

# Run migrations
php artisan migrate --force

# Seed database (optional, untuk data testing)
php artisan db:seed --force

# Create storage link
php artisan storage:link

# Clear & cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Check Laravel version & status
php artisan --version
php artisan optimize
```

**Checklist:**
- [ ] `php artisan migrate --force` berhasil
- [ ] `php artisan storage:link` berhasil
- [ ] Cache cleared dan rebuilt
- [ ] No errors di output

### 3. Create Admin User
```bash
php artisan tinker
```

Di Tinker console, jalankan:
```php
$admin = new App\Models\Admin();
$admin->nama = 'Admin ChillAjar';
$admin->email = 'admin@chillajar.com';
$admin->password = bcrypt('SecurePassword123!');
$admin->no_telp = '081234567890';
$admin->save();
exit
```

**Checklist:**
- [ ] Admin user berhasil dibuat
- [ ] Credentials dicatat dengan aman
- [ ] Test login di Postman/API client

---

## 🌐 **Access Testing**

### 1. Internal Access (Via VPN)
Test dengan curl atau browser:

```bash
# Health check
curl http://172.17.0.1:8080/api/health

# API root
curl http://172.17.0.1:8080/api/

# PHPMyAdmin
# Browser: http://172.17.0.1:8081
```

**Checklist:**
- [ ] Laravel response 200 OK
- [ ] API returns JSON response
- [ ] PHPMyAdmin accessible
- [ ] Database visible di PHPMyAdmin

### 2. Test Database Connection
```bash
# From app container console
php artisan tinker

# Test query
DB::select('SELECT 1');
exit
```

**Checklist:**
- [ ] Database connection successful
- [ ] Query executed without error

---

## 🔗 **Reverse Proxy Setup**

### 1. Informasi ke Server Admin (Febry)
Berikan informasi berikut untuk setup reverse proxy:

```
Service: ChillAjar Backend API
Port Internal: 8080
IP Internal: 172.17.0.1:8080
Domain Target: ekomh29.biz.id
SSL: Auto-generate (Let's Encrypt)
```

**Checklist:**
- [ ] Port info diberikan ke Febry
- [ ] Domain dikonfirmasi
- [ ] Waiting for reverse proxy setup

### 2. Domain Access (After Reverse Proxy)
Setelah Febry setup reverse proxy:

```bash
# Test via domain
curl https://ekomh29.biz.id/api/health

# Browser
https://ekomh29.biz.id
```

**Checklist:**
- [ ] Domain accessible via public internet
- [ ] SSL certificate valid (https)
- [ ] API response correct

---

## 📊 **Monitoring Setup**

### 1. Container Resources
Di Portainer → Containers → Stats:
- [ ] CPU usage normal (<50%)
- [ ] Memory usage stabil
- [ ] Network I/O stabil

### 2. Laravel Logs
```bash
# Via container console
tail -f /var/www/storage/logs/laravel.log
```

**Checklist:**
- [ ] Log rotation working
- [ ] No critical errors
- [ ] Application logs properly

### 3. Database Monitoring
Via PHPMyAdmin:
- [ ] Tables created correctly
- [ ] Data seeded (if applicable)
- [ ] Connections stable

---

## 🧪 **API Testing**

### Test Critical Endpoints

#### 1. Authentication
```bash
# Register (if public)
curl -X POST http://ekomh29.biz.id/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"nama":"Test User","email":"test@example.com","password":"password123"}'

# Login Admin
curl -X POST http://ekomh29.biz.id/api/auth/admin/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@chillajar.com","password":"SecurePassword123!"}'
```

#### 2. Protected Routes
```bash
# Get profile (with token)
curl http://ekomh29.biz.id/api/admin/profile \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

**Checklist:**
- [ ] Login endpoint working
- [ ] Token generated correctly
- [ ] Protected routes require authentication
- [ ] JWT validation working

---

## 🔒 **Security Checklist**

### Production Security
- [ ] `APP_DEBUG=false` in production
- [ ] Secure `APP_KEY` generated
- [ ] Strong `JWT_SECRET` generated
- [ ] Database credentials secure
- [ ] CORS configured properly
- [ ] Rate limiting enabled
- [ ] SQL injection protection (Laravel's default)
- [ ] XSS protection enabled
- [ ] CSRF tokens for web routes

### Server Security
- [ ] VPN required for management access
- [ ] Portainer behind authentication
- [ ] Database not exposed publicly (port 33061 internal only)
- [ ] SSL certificate installed (via reverse proxy)

---

## 📝 **Documentation**

### Update Documentation
- [ ] API endpoints documented
- [ ] Postman collection updated
- [ ] Environment variables documented
- [ ] Deployment process documented
- [ ] Server credentials stored securely

### Team Communication
- [ ] Frontend team informed about API URL
- [ ] API documentation shared
- [ ] Test credentials provided (for staging/dev)

---

## 🚨 **Emergency Contacts**

### Server Issues
**Febry Billiyagi** (Server Admin)
- VPN access issues
- Portainer problems
- Reverse proxy configuration
- Server resource issues

### Application Issues
**Backend Team**
- Laravel errors
- Database problems
- API bugs
- Performance issues

---

## 🎉 **Deployment Complete!**

Jika semua checklist di atas sudah ✅, selamat! Aplikasi ChillAjar Backend sudah berhasil di-deploy ke production!

### Final Verification
- [ ] ✅ All containers running
- [ ] ✅ Laravel setup completed
- [ ] ✅ Database connected
- [ ] ✅ Internal access working
- [ ] ✅ Reverse proxy configured
- [ ] ✅ Domain accessible publicly
- [ ] ✅ SSL certificate installed
- [ ] ✅ API endpoints tested
- [ ] ✅ Admin user created
- [ ] ✅ Documentation updated
- [ ] ✅ Team notified

---

**🚀 Application is now LIVE at: https://ekomh29.biz.id**
