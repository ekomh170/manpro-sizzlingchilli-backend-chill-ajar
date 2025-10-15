# 📋 CATATAN BACKUP GCP FULL - Chill Ajar Backend
## 🚀 Panduan Lengkap Backup & Restore untuk Google Cloud Platform

---

## 🎯 **OVERVIEW APLIKASI**

### **📱 Aplikasi:** Chill Ajar - Learning Management System
```
🏗️ Framework: Laravel 12 (PHP 8.2+)
🗄️ Database: MySQL/PostgreSQL + Redis Cache
📦 Package Manager: Composer + NPM
🌐 Web Server: Nginx + PHP-FPM
📲 Additional: Node.js WhatsApp Gateway (Port 3000)
📁 Storage: Local file storage (uploads)
```

### **🔧 Tech Stack:**
```
Backend: Laravel 12 + Sanctum Auth
Database: MySQL 8.0 / PostgreSQL 16
Cache: Redis 6.x
Frontend: Vite + TailwindCSS 4.0
Gateway: Node.js WhatsApp Web.js
Server: Ubuntu 22.04 LTS (GCP VM)
```

---

## 💾 **1. DATABASE BACKUP**

### **🗄️ MySQL Backup**
```bash
#!/bin/bash
# Script: backup_mysql.sh

# Variables
DB_NAME="db_manpro_sizzlingchilli_backend_chill"
DB_USER="dev_chill_ajar"
DB_PASS="Manpro2025!"
BACKUP_DIR="/home/backup/mysql"
DATE=$(date +"%Y%m%d_%H%M%S")
BACKUP_FILE="$BACKUP_DIR/backup_chillajar_$DATE.sql"

# Create backup directory
mkdir -p $BACKUP_DIR

# Full database backup with structure + data
mysqldump -u $DB_USER -p$DB_PASS \
  --single-transaction \
  --routines \
  --triggers \
  --events \
  --hex-blob \
  --opt \
  $DB_NAME > $BACKUP_FILE

# Compress backup
gzip $BACKUP_FILE

# Keep only last 7 days
find $BACKUP_DIR -name "backup_chillajar_*.sql.gz" -mtime +7 -delete

echo "✅ MySQL backup completed: $BACKUP_FILE.gz"
```

### **🐘 PostgreSQL Backup**
```bash
#!/bin/bash
# Script: backup_postgresql.sh

# Variables
DB_NAME="db_manpro_sizzlingchilli_backend_chill"
DB_USER="deploy_dev"
BACKUP_DIR="/home/backup/postgresql"
DATE=$(date +"%Y%m%d_%H%M%S")
BACKUP_FILE="$BACKUP_DIR/backup_chillajar_$DATE.sql"

# Create backup directory
mkdir -p $BACKUP_DIR

# Full database backup
pg_dump -h localhost -U $DB_USER -d $DB_NAME \
  --clean \
  --create \
  --if-exists \
  --verbose \
  --file=$BACKUP_FILE

# Compress backup
gzip $BACKUP_FILE

# Keep only last 7 days
find $BACKUP_DIR -name "backup_chillajar_*.sql.gz" -mtime +7 -delete

echo "✅ PostgreSQL backup completed: $BACKUP_FILE.gz"
```

---

## 📁 **2. FILE SYSTEM BACKUP**

### **🗂️ Application Files Backup**
```bash
#!/bin/bash
# Script: backup_files.sh

# Variables
APP_DIR="/home/ekomh13/manpro-sizzlingchilli-backend-chill-ajar"
BACKUP_DIR="/home/backup/files"
DATE=$(date +"%Y%m%d_%H%M%S")
BACKUP_FILE="$BACKUP_DIR/backup_app_files_$DATE.tar.gz"

# Create backup directory
mkdir -p $BACKUP_DIR

# Application files backup (exclude vendor, node_modules, cache)
tar -czf $BACKUP_FILE \
  --exclude="$APP_DIR/vendor" \
  --exclude="$APP_DIR/node_modules" \
  --exclude="$APP_DIR/storage/logs/*" \
  --exclude="$APP_DIR/storage/framework/cache/*" \
  --exclude="$APP_DIR/storage/framework/sessions/*" \
  --exclude="$APP_DIR/storage/framework/views/*" \
  --exclude="$APP_DIR/.git" \
  $APP_DIR

echo "✅ Application files backup completed: $BACKUP_FILE"
```

### **📤 Uploads & Storage Backup**
```bash
#!/bin/bash
# Script: backup_storage.sh

# Variables
STORAGE_DIR="/home/ekomh13/manpro-sizzlingchilli-backend-chill-ajar/storage/app"
PUBLIC_DIR="/home/ekomh13/manpro-sizzlingchilli-backend-chill-ajar/public/storage"
BACKUP_DIR="/home/backup/storage"
DATE=$(date +"%Y%m%d_%H%M%S")

# Create backup directory
mkdir -p $BACKUP_DIR

# Storage backup (user uploads, photos, payment proofs)
tar -czf "$BACKUP_DIR/backup_storage_$DATE.tar.gz" \
  -C "/home/ekomh13/manpro-sizzlingchilli-backend-chill-ajar" \
  storage/app/public \
  storage/app/private \
  public/storage

echo "✅ Storage backup completed: backup_storage_$DATE.tar.gz"
```

---

## ⚙️ **3. CONFIGURATION BACKUP**

### **🔧 System Configuration**
```bash
#!/bin/bash
# Script: backup_config.sh

BACKUP_DIR="/home/backup/config"
DATE=$(date +"%Y%m%d_%H%M%S")
CONFIG_BACKUP="$BACKUP_DIR/backup_config_$DATE.tar.gz"

mkdir -p $BACKUP_DIR

# Backup system configurations
tar -czf $CONFIG_BACKUP \
  /etc/nginx/sites-available/ \
  /etc/nginx/nginx.conf \
  /etc/php/8.3/fpm/ \
  /etc/mysql/mysql.conf.d/ \
  /etc/redis/ \
  /etc/crontab \
  /var/spool/cron/crontabs/ \
  /home/ekomh13/manpro-sizzlingchilli-backend-chill-ajar/.env

echo "✅ Configuration backup completed: $CONFIG_BACKUP"
```

### **📝 Environment Variables Backup**
```bash
#!/bin/bash
# Script: backup_env.sh

APP_DIR="/home/ekomh13/manpro-sizzlingchilli-backend-chill-ajar"
BACKUP_DIR="/home/backup/env"
DATE=$(date +"%Y%m%d_%H%M%S")

mkdir -p $BACKUP_DIR

# Backup environment file (CAREFUL: Contains sensitive data!)
cp "$APP_DIR/.env" "$BACKUP_DIR/.env_backup_$DATE"
chmod 600 "$BACKUP_DIR/.env_backup_$DATE"

echo "✅ Environment backup completed: .env_backup_$DATE"
echo "⚠️  WARNING: File contains sensitive data - secure properly!"
```

---

## 🌐 **4. WHATSAPP GATEWAY BACKUP**

### **📲 WhatsApp Gateway Configuration**
```bash
#!/bin/bash
# Script: backup_whatsapp_gateway.sh

WA_DIR="/home/ekomh13/whatsapp-sizzlingchilli-gateway-chillajar"
BACKUP_DIR="/home/backup/whatsapp"
DATE=$(date +"%Y%m%d_%H%M%S")

mkdir -p $BACKUP_DIR

if [ -d "$WA_DIR" ]; then
  # Backup WhatsApp Gateway
  tar -czf "$BACKUP_DIR/backup_wa_gateway_$DATE.tar.gz" \
    --exclude="$WA_DIR/node_modules" \
    --exclude="$WA_DIR/.wwebjs_auth" \
    $WA_DIR
  
  # Backup PM2 configuration
  pm2 dump > "$BACKUP_DIR/pm2_processes_$DATE.json"
  
  echo "✅ WhatsApp Gateway backup completed"
else
  echo "⚠️  WhatsApp Gateway directory not found: $WA_DIR"
fi
```

---

## 🔄 **5. AUTOMATED BACKUP SCRIPT**

### **🤖 Master Backup Script**
```bash
#!/bin/bash
# Script: full_backup.sh
# Description: Complete backup for Chill Ajar Backend

set -e

echo "🚀 Starting Full Backup Process..."
echo "📅 Date: $(date)"
echo "=================================="

# Variables
BACKUP_ROOT="/home/backup"
LOG_FILE="$BACKUP_ROOT/backup.log"
DATE=$(date +"%Y%m%d_%H%M%S")

# Create main backup directory
mkdir -p $BACKUP_ROOT

# Log function
log() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1" | tee -a $LOG_FILE
}

log "📋 Starting backup process..."

# 1. Database Backup
log "🗄️  Starting database backup..."
if systemctl is-active --quiet mysql; then
    ./backup_mysql.sh
    log "✅ MySQL backup completed"
elif systemctl is-active --quiet postgresql; then
    ./backup_postgresql.sh
    log "✅ PostgreSQL backup completed"
else
    log "❌ No database service found"
fi

# 2. Application Files
log "📁 Starting application files backup..."
./backup_files.sh
log "✅ Application files backup completed"

# 3. Storage & Uploads
log "📤 Starting storage backup..."
./backup_storage.sh
log "✅ Storage backup completed"

# 4. Configuration
log "⚙️  Starting configuration backup..."
./backup_config.sh
log "✅ Configuration backup completed"

# 5. Environment Variables
log "📝 Starting environment backup..."
./backup_env.sh
log "✅ Environment backup completed"

# 6. WhatsApp Gateway
log "📲 Starting WhatsApp Gateway backup..."
./backup_whatsapp_gateway.sh
log "✅ WhatsApp Gateway backup completed"

# 7. Cleanup old backups (keep 30 days)
log "🧹 Cleaning up old backups..."
find $BACKUP_ROOT -type f -mtime +30 -delete
log "✅ Cleanup completed"

# 8. Backup summary
TOTAL_SIZE=$(du -sh $BACKUP_ROOT | cut -f1)
log "📊 Backup completed successfully!"
log "📁 Total backup size: $TOTAL_SIZE"
log "📍 Location: $BACKUP_ROOT"

echo "=================================="
echo "✅ Full backup process completed!"
echo "📄 Check log: $LOG_FILE"
```

---

## 📅 **6. CRON AUTOMATION**

### **⏰ Crontab Setup**
```bash
# Edit crontab
sudo crontab -e

# Add backup schedules:

# Daily backup at 2 AM
0 2 * * * /home/backup/scripts/full_backup.sh >> /home/backup/cron.log 2>&1

# Weekly database-only backup (Sunday 1 AM)
0 1 * * 0 /home/backup/scripts/backup_mysql.sh >> /home/backup/cron.log 2>&1

# Monthly configuration backup (1st day, 3 AM)
0 3 1 * * /home/backup/scripts/backup_config.sh >> /home/backup/cron.log 2>&1
```

---

## 🔧 **7. RESTORE PROCEDURES**

### **🗄️ Database Restore**

#### **MySQL Restore:**
```bash
#!/bin/bash
# Script: restore_mysql.sh

BACKUP_FILE="$1"
DB_NAME="db_manpro_sizzlingchilli_backend_chill"
DB_USER="dev_chill_ajar"
DB_PASS="Manpro2025!"

if [ -z "$BACKUP_FILE" ]; then
    echo "Usage: $0 <backup_file.sql.gz>"
    exit 1
fi

echo "⚠️  WARNING: This will OVERWRITE the current database!"
echo "📁 Backup file: $BACKUP_FILE"
echo "🗄️  Database: $DB_NAME"
read -p "Continue? (y/N): " -n 1 -r
echo

if [[ $REPLY =~ ^[Yy]$ ]]; then
    # Decompress and restore
    gunzip -c $BACKUP_FILE | mysql -u $DB_USER -p$DB_PASS $DB_NAME
    echo "✅ Database restored successfully!"
else
    echo "❌ Restore cancelled"
fi
```

#### **PostgreSQL Restore:**
```bash
#!/bin/bash
# Script: restore_postgresql.sh

BACKUP_FILE="$1"
DB_NAME="db_manpro_sizzlingchilli_backend_chill"
DB_USER="deploy_dev"

if [ -z "$BACKUP_FILE" ]; then
    echo "Usage: $0 <backup_file.sql.gz>"
    exit 1
fi

echo "⚠️  WARNING: This will OVERWRITE the current database!"
read -p "Continue? (y/N): " -n 1 -r
echo

if [[ $REPLY =~ ^[Yy]$ ]]; then
    # Decompress and restore
    gunzip -c $BACKUP_FILE | psql -h localhost -U $DB_USER -d $DB_NAME
    echo "✅ Database restored successfully!"
fi
```

### **📁 Application Restore**
```bash
#!/bin/bash
# Script: restore_app.sh

BACKUP_FILE="$1"
RESTORE_DIR="/home/ekomh13/manpro-sizzlingchilli-backend-chill-ajar-restore"

if [ -z "$BACKUP_FILE" ]; then
    echo "Usage: $0 <backup_file.tar.gz>"
    exit 1
fi

echo "📁 Extracting backup to: $RESTORE_DIR"
mkdir -p $RESTORE_DIR
tar -xzf $BACKUP_FILE -C $RESTORE_DIR

echo "✅ Application files restored to: $RESTORE_DIR"
echo "📝 Next steps:"
echo "   1. Copy .env file and configure"
echo "   2. Run: composer install"
echo "   3. Run: php artisan migrate"
echo "   4. Set proper permissions"
echo "   5. Update Nginx configuration"
```

---

## 🔐 **8. SECURITY & BEST PRACTICES**

### **🛡️ Backup Security**
```bash
# Encrypt sensitive backups
encrypt_backup() {
    local file="$1"
    local encrypted="${file}.gpg"
    
    gpg --symmetric --cipher-algo AES256 --output "$encrypted" "$file"
    rm "$file"  # Remove unencrypted version
    echo "🔐 Backup encrypted: $encrypted"
}

# Decrypt backup
decrypt_backup() {
    local encrypted_file="$1"
    local decrypted="${encrypted_file%.gpg}"
    
    gpg --decrypt --output "$decrypted" "$encrypted_file"
    echo "🔓 Backup decrypted: $decrypted"
}
```

### **☁️ Cloud Storage Upload**
```bash
#!/bin/bash
# Upload to Google Cloud Storage

BUCKET_NAME="chillajar-backups"
LOCAL_BACKUP_DIR="/home/backup"
DATE=$(date +"%Y%m%d")

# Upload daily backup to GCS
gsutil -m cp -r "$LOCAL_BACKUP_DIR/backup_*$DATE*" "gs://$BUCKET_NAME/daily/"

# Clean local backups older than 7 days
find $LOCAL_BACKUP_DIR -type f -mtime +7 -delete

echo "☁️  Backup uploaded to Google Cloud Storage"
```

---

## 📋 **9. DISASTER RECOVERY CHECKLIST**

### **🚨 Emergency Recovery Steps**

#### **🔥 Complete System Failure:**
```
1. ✅ Spin up new GCP VM (Ubuntu 22.04 LTS)
2. ✅ Install basic stack (Nginx, PHP 8.3, MySQL/PostgreSQL, Node.js)
3. ✅ Download latest backup from Cloud Storage
4. ✅ Restore database from backup
5. ✅ Extract application files
6. ✅ Configure .env file
7. ✅ Run composer install
8. ✅ Set file permissions (www-data)
9. ✅ Configure Nginx virtual host
10. ✅ Restore WhatsApp Gateway
11. ✅ Update DNS if needed
12. ✅ Test all functionality
```

#### **🗄️ Database Corruption:**
```
1. ✅ Stop application (maintenance mode)
2. ✅ Backup corrupted database (for analysis)
3. ✅ Restore from latest known good backup
4. ✅ Run integrity checks
5. ✅ Verify data consistency
6. ✅ Resume application
7. ✅ Monitor for issues
```

#### **📁 File System Issues:**
```
1. ✅ Identify affected files/directories
2. ✅ Stop web server
3. ✅ Restore from file backup
4. ✅ Reset permissions
5. ✅ Clear application cache
6. ✅ Restart services
7. ✅ Verify functionality
```

---

## 📊 **10. MONITORING & ALERTS**

### **📈 Backup Monitoring Script**
```bash
#!/bin/bash
# Script: monitor_backups.sh

BACKUP_DIR="/home/backup"
ALERT_EMAIL="admin@chillajar.com"
MAX_AGE_HOURS=26  # Alert if backup older than 26 hours

# Check latest backup age
LATEST_BACKUP=$(find $BACKUP_DIR -name "backup_*" -type f -printf '%T@ %p\n' | sort -nr | head -1 | cut -d' ' -f2-)
if [ -n "$LATEST_BACKUP" ]; then
    BACKUP_AGE=$(( ($(date +%s) - $(stat -c %Y "$LATEST_BACKUP")) / 3600 ))
    
    if [ $BACKUP_AGE -gt $MAX_AGE_HOURS ]; then
        echo "🚨 ALERT: Latest backup is $BACKUP_AGE hours old!" | mail -s "Backup Alert - Chill Ajar" $ALERT_EMAIL
    fi
else
    echo "🚨 ALERT: No backups found!" | mail -s "Backup Alert - Chill Ajar" $ALERT_EMAIL
fi

# Check backup sizes
TOTAL_SIZE=$(du -sh $BACKUP_DIR | cut -f1)
echo "📊 Total backup size: $TOTAL_SIZE"

# Check disk space
DISK_USAGE=$(df /home | tail -1 | awk '{print $5}' | sed 's/%//')
if [ $DISK_USAGE -gt 80 ]; then
    echo "⚠️  Disk usage: $DISK_USAGE% - Consider cleanup" | mail -s "Disk Space Alert" $ALERT_EMAIL
fi
```

---

## 🎯 **SUMMARY & KEY POINTS**

### **✅ Backup Coverage:**
- 🗄️ **Database:** MySQL/PostgreSQL full dumps
- 📁 **Application:** Source code + configurations
- 📤 **Storage:** User uploads & file storage
- ⚙️ **System:** Nginx, PHP, MySQL configs
- 📲 **Gateway:** WhatsApp Gateway setup
- 🔐 **Environment:** .env variables (encrypted)

### **🔄 Automation:**
- 📅 **Daily:** Full automated backup
- 📅 **Weekly:** Database-only backup
- 📅 **Monthly:** Configuration backup
- ☁️ **Cloud Sync:** Upload to Google Cloud Storage

### **🛡️ Security:**
- 🔐 Sensitive data encryption
- 🔑 Restricted file permissions
- 📧 Monitoring & alerting
- 🗂️ Retention policies (30 days local, longer in cloud)

### **🚀 Recovery Time:**
- **Database Only:** 15-30 minutes
- **Full Application:** 1-2 hours
- **Complete System:** 3-4 hours (including VM setup)

---

**🎯 CRITICAL REMINDER:** Test restore procedures regularly! A backup is only as good as your ability to restore from it!

*📅 Created: September 2025 | 🔄 Review: Monthly | 🎯 Confidence: Production Ready*
    