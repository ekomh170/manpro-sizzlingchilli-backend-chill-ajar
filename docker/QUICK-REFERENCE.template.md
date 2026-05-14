# 🔐 **Quick Reference - ChillAjar Backend Deployment**

⚠️ **TEMPLATE FILE** - Copy ke `QUICK-REFERENCE.md` dan isi dengan data actual (file tersebut sudah di `.gitignore`)

---

## 🌐 **Server Access**

### VPN (Twingate)
```
Download: https://www.twingate.com/download
Status: Required untuk semua akses server
```

### Portainer Management
```
URL: https://manage.rekyndness.local:9443
Username: [ASK SERVER ADMIN]
Password: [ASK SERVER ADMIN]
```

### Server VPS
```
IP Public: [ASK SERVER ADMIN]
Domain: [YOUR DOMAIN]
IP Internal: 172.17.0.1
```

---

## 🔌 **Port Configuration**

### Ports Kita Gunakan (✅ SAFE)
```
9500  - Laravel API
9501  - PHPMyAdmin
9502 - MySQL
```

### Ports TIDAK Boleh Dipakai (🚫 RESTRICTED)
```
7500, 5000, 3000, 9443, 81, 80, 443, 8200, 3306
```

**Note**: Selalu cek dengan server admin untuk port yang available!

---

## 🗄️ **Database Credentials**

### MySQL (Production)
```
Host: chillajar_db (internal Docker network)
Port: 3306 (internal) / 9502 (external)
Database: db_manpro_sizzlingchilli_backend_chill
Username: [SET IN .env]
Password: [SET IN .env]
Root Password: [SET IN .env]
```

### PHPMyAdmin Access
```
URL Internal: http://172.17.0.1:9501
Server: chillajar_db
Username: root
Password: [USE ROOT PASSWORD FROM .env]
```

---

## 🔑 **Laravel Credentials**

### Admin User (Create After Deploy)
```
Email: [YOUR ADMIN EMAIL]
Password: [SECURE PASSWORD - MIN 12 CHARS]
```

**Security Best Practices**:
- Gunakan password manager untuk generate secure password
- Minimum 12 karakter dengan kombinasi huruf, angka, simbol
- Jangan gunakan default passwords
- Aktifkan 2FA jika tersedia

### Environment Variables (Production)
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=[YOUR PRODUCTION URL]

# GENERATE BARU SAAT DEPLOYMENT - JANGAN PAKAI DEFAULT!
APP_KEY=[GENERATE: php artisan key:generate --show]
JWT_SECRET=[GENERATE: openssl rand -base64 32]

# Database
DB_HOST=chillajar_db
DB_DATABASE=db_manpro_sizzlingchilli_backend_chill
DB_USERNAME=[SECURE USERNAME]
DB_PASSWORD=[SECURE PASSWORD]

# Mail (Optional)
MAIL_USERNAME=[YOUR EMAIL]
MAIL_PASSWORD=[APP PASSWORD]
```

---

## 📍 **Service URLs**

### Development (Local Docker)
```
Laravel API: http://localhost:9500
PHPMyAdmin: http://localhost:9501
MySQL: localhost:9502
```

### Testing (Via VPN)
```
Laravel API: http://172.17.0.1:9500
PHPMyAdmin: http://172.17.0.1:9501
```

### Production (After Reverse Proxy)
```
Laravel API: https://[YOUR DOMAIN]
SSL: Auto-generated (Let's Encrypt)
```

---

## 🛠️ **Quick Commands**

### Laravel Console Access (Portainer)
1. Portainer → Containers → `chillajar_app`
2. Console → `/bin/bash` → Connect
3. Run commands:

```bash
# Generate keys (REQUIRED FIRST TIME)
php artisan key:generate

# Database
php artisan migrate --force
php artisan db:seed --force

# Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Storage
php artisan storage:link

# Create admin
php artisan tinker
```

### Docker Compose Commands (Local)
```bash
# Start all services
docker-compose up -d

# Stop all services
docker-compose down

# View logs
docker-compose logs -f app

# Restart service
docker-compose restart app

# Execute command in container
docker-compose exec app php artisan migrate
```

---

## 📞 **Support Contacts**

### Server & Infrastructure
**[SERVER ADMIN NAME]**
- VPN access issues
- Portainer problems
- Reverse proxy setup
- Port configuration

### Backend Development
**[BACKEND TEAM LEAD]**
- Laravel issues
- API problems
- Database migrations
- Application bugs

---

## ✅ **Quick Deploy Steps**

1. **VPN** → Install Twingate & Connect
2. **Portainer** → Login https://manage.rekyndness.local:9443
3. **Stack** → Create "chillajar-backend"
4. **Upload** → docker-compose.yml
5. **Environment** → Set production variables (JANGAN PAKAI DEFAULT!)
6. **Deploy** → Wait for build
7. **Console** → Run Laravel setup commands
8. **Test** → http://172.17.0.1:8080
9. **Inform** → Notify server admin for reverse proxy
10. **Verify** → https://[YOUR DOMAIN]

---

## 📚 **Documentation Links**

```
Main README: ./README.md
Docker Setup: ./docker/README.md
Portainer Deploy: ./docker/DEPLOYMENT-PORTAINER.md
Deployment Checklist: ./docker/CHECKLIST-DEPLOYMENT.md
```

---

## 🔒 **Security Notes**

### CRITICAL - Jangan Pernah:
- ❌ **Commit credentials ke Git!**
- ❌ **Hardcode passwords di code!**
- ❌ **Share credentials via chat/email (gunakan secure method)**
- ❌ **Gunakan default passwords di production!**
- ❌ **Set APP_DEBUG=true di production!**

### Best Practices:
- ✅ **Gunakan environment variables untuk semua secrets**
- ✅ **Generate APP_KEY dan JWT_SECRET yang baru untuk production**
- ✅ **Gunakan strong passwords (min 12 chars)**
- ✅ **Ganti default admin password setelah deployment**
- ✅ **Backup database secara berkala**
- ✅ **Monitor logs untuk suspicious activity**
- ✅ **Keep dependencies updated**
- ✅ **Use HTTPS di production (via reverse proxy)**

### Password Generation Commands:
```bash
# Generate APP_KEY
php artisan key:generate --show

# Generate JWT_SECRET
openssl rand -base64 32

# Generate random password
openssl rand -base64 16
```

---

## 🔐 **Storing Credentials Securely**

### Recommended Tools:
1. **Password Managers**:
   - 1Password
   - LastPass
   - Bitwarden (Open Source)
   - KeePass

2. **Team Secret Management**:
   - HashiCorp Vault
   - AWS Secrets Manager
   - Azure Key Vault

3. **Environment Variable Management**:
   - Doppler
   - dotenv-vault
   - Infisical

### Local Development:
```bash
# Copy template dan isi dengan data actual
cp .env.docker .env

# Edit .env file (JANGAN COMMIT!)
# File .env sudah di .gitignore
```

---

## 📝 **Actual Credentials Location**

Untuk credentials actual, lihat:
1. **QUICK-REFERENCE.md** (di `.gitignore`, tidak ter-commit)
2. **Password Manager** tim development
3. **Secure documentation** (shared securely dengan tim)
4. **Server admin** untuk server credentials

---

**⚠️ PENTING**:
- File ini adalah **TEMPLATE** yang aman untuk di-commit
- Copy file ini menjadi `QUICK-REFERENCE.md` dan isi dengan data actual
- `QUICK-REFERENCE.md` sudah ada di `.gitignore` dan tidak akan ter-commit

**Last Updated**: November 2, 2025
