# Catatan Setup Server Ubuntu GCP untuk Laravel
server-sizzlingchili-chillajar-uas-manpro
---

## Setup Webserver NGINX untuk Backend Laravel di Ubuntu (GCP)

### 1. Update & Install Tools
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install nginx php php-fpm php-mysql php-xml php-mbstring php-curl php-zip php-gd unzip git curl nano net-tools lsof -y
```
- `nano`: text editor CLI
- `net-tools` & `lsof`: untuk cek port/proses (misal: `netstat`, `lsof`)

### 2. (Opsional) Hapus Apache jika terinstall
GCP image Ubuntu kadang sudah include Apache. Hapus agar tidak bentrok port 80:
```bash
sudo systemctl stop apache2
sudo systemctl disable apache2
sudo apt remove --purge apache2* -y
sudo apt autoremove -y
```

### 3. Clone Project Laravel
```bash
cd ~
git clone https://github.com/ekomh170/manpro-sizzlingchilli-backend-chill-ajar.git
cd manpro-sizzlingchilli-backend-chill-ajar
```

### 4. Install Composer (global)
```bash
cd ~
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
composer --version
```

### 5. Install Dependency Laravel
```bash
cd ~/manpro-sizzlingchilli-backend-chill-ajar
composer install
```

### 6. Setup File .env & Generate Key
```bash
cp .env.example .env
nano .env
# Edit DB_DATABASE, DB_USERNAME, DB_PASSWORD sesuai database MySQL Anda
php artisan key:generate
```

### 7. Install & Setup MySQL
```bash
sudo apt install mysql-server -y
sudo systemctl start mysql
sudo mysql_secure_installation
# Masuk ke prompt MySQL (bukan di shell/bash):
sudo mysql -u root -p
# Setelah muncul prompt mysql>, jalankan perintah berikut satu per satu:
# (Jika ingin password root sederhana seperti 'manpro2025', pastikan policy password di-set ke LOW:)
SET GLOBAL validate_password.policy = LOW;
SET GLOBAL validate_password.length = 8;
ALTER USER 'root'@'localhost' IDENTIFIED BY 'manpro2025';
# Setelah itu, buat user dev_chill_ajar dengan password kuat (disarankan untuk production):
CREATE DATABASE db_manpro_sizzlingchilli_backend_chill;
CREATE USER 'dev_chill_ajar'@'localhost' IDENTIFIED BY 'Manpro2025!';
GRANT ALL PRIVILEGES ON db_manpro_sizzlingchilli_backend_chill.* TO 'dev_chill_ajar'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### Catatan Penting MySQL
- Jalankan semua perintah SQL di prompt MySQL, bukan di shell/bash.
- Untuk password root sederhana, set policy ke LOW sebelum ALTER USER.
- User aplikasi WAJIB pakai password kuat.
- Ikuti rekomendasi `mysql_secure_installation` (hapus anonymous user, disable root remote, hapus test db, reload privilege).
- Jika error `Access denied for user 'root'@'localhost'`, pastikan sudah masuk prompt MySQL.

### 8. Permission Laravel (WAJIB setelah clone/install)
```bash
sudo chown -R www-data:www-data storage bootstrap/cache public
sudo chmod -R 775 storage bootstrap/cache public
sudo find storage -type d -exec chmod 775 {} \;
sudo find storage -type f -exec chmod 664 {} \;
sudo find bootstrap/cache -type d -exec chmod 775 {} \;
sudo find bootstrap/cache -type f -exec chmod 664 {} \;
sudo find public -type d -exec chmod 775 {} \;
sudo find public -type f -exec chmod 664 {} \;
sudo -u www-data mkdir -p storage/logs
sudo -u www-data touch storage/logs/laravel.log
sudo chown www-data:www-data storage/logs/laravel.log
sudo chmod 664 storage/logs/laravel.log
```
- Pastikan juga folder storage/framework/views dan storage/framework/sessions bisa diakses dan ditulis oleh www-data:
```bash
sudo chown -R www-data:www-data storage/framework
sudo chmod -R 775 storage/framework
```
- Jika permission error tetap muncul, pastikan folder /home, /home/ekomh13, dan /home/ekomh13/manpro-sizzlingchilli-backend-chill-ajar sudah diberi akses execute untuk others:
```bash
sudo chmod o+x /home
sudo chmod o+x /home/ekomh13
sudo chmod o+x /home/ekomh13/manpro-sizzlingchilli-backend-chill-ajar
```

### 9. Konfigurasi NGINX untuk Laravel
```bash
sudo nano /etc/nginx/sites-available/laravel
```
Isi config:
```nginx
server {
    listen 80;
    server_name _;
    root /home/ekomh13/manpro-sizzlingchilli-backend-chill-ajar/public;
    index index.php index.html;
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock; # Laravel 12: WAJIB PHP 8.2+ (disarankan 8.3)
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    location ~ /\.ht {
        deny all;
    }
}
```
Aktifkan config:
```bash
sudo ln -s /etc/nginx/sites-available/laravel /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

- Setelah konfigurasi, pastikan config default NGINX tidak aktif:
```bash
ls -l /etc/nginx/sites-enabled/
ls -l /etc/nginx/sites-available/
# Jika ada file default:
sudo rm /etc/nginx/sites-enabled/default
sudo systemctl reload nginx
```

### 10. Migrasi & Seeder Database
```bash
sudo -u www-data php artisan migrate --seed
```
Jika muncul error MySQL (Connection refused):
- Pastikan MySQL sudah berjalan: `sudo systemctl status mysql`
- Pastikan .env sudah benar (DB_HOST=127.0.0.1, DB_PORT=3306, DB_DATABASE, DB_USERNAME, DB_PASSWORD)
- Setelah edit .env, WAJIB jalankan: `sudo -u www-data php artisan config:cache`
- Tes koneksi manual ke database: `mysql -u <DB_USERNAME> -p -h 127.0.0.1 <DB_DATABASE>`
- Jika user hanya ada di 'localhost', tambahkan juga di '127.0.0.1' di MySQL (GRANT ALL PRIVILEGES ... TO 'user'@'127.0.0.1')
- Cek file /etc/mysql/mysql.conf.d/mysqld.cnf, pastikan bind-address=127.0.0.1
- Restart MySQL jika ada perubahan config: `sudo systemctl restart mysql`
- Jika tetap error, pastikan migrate dijalankan dengan user www-data, bukan user sendiri.

### 11. Storage Link
```bash
sudo -u www-data php artisan storage:link
```
Jika muncul error:
```
ERROR  The [public/storage] link already exists.
```
Solusi:
```bash
ls -l public | grep storage
# Jika symlink:
sudo rm public/storage
# Jika folder:
sudo rm -rf public/storage
sudo -u www-data php artisan storage:link
```

### 12. Firewall GCP
Pastikan port 80 (HTTP) dan/atau 443 (HTTPS) sudah terbuka di firewall GCP.

### 13. (Opsional) Setup HTTPS
```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx
```

---

## Catatan & Best Practice Deployment Laravel di Ubuntu (GCP)

- Jalankan semua perintah deployment dari root folder project.
- Permission folder/file sangat penting di Ubuntu, WAJIB setelah clone/install.
- Semua perintah artisan yang mengubah file/folder (migrate, config:cache, storage:link, dsb) WAJIB pakai sudo -u www-data.
- Untuk development, owner bisa user sendiri, untuk production WAJIB www-data.
- Untuk production, JANGAN buka port 5173/8000 ke publik.
- Untuk domain, arahkan DNS ke IP VM.
- Jalankan permission ulang SETIAP SELESAI CLONE/DEPLOY ULANG.
- Setelah edit .env, jalankan sudo -u www-data php artisan config:cache.
- Jika ingin akses penuh tanpa sudo -u www-data (dev), ubah owner folder ke user sendiri, tapi sebelum production WAJIB kembalikan ke www-data.

#### Catatan Penting .env
- APP_URL: isi dengan IP/domain server, contoh: http://35.188.34.254
- APP_KEY: generate dengan `php artisan key:generate` setelah copy .env
- APP_ENV: gunakan `production` untuk server publik
- APP_DEBUG: set `false` untuk production
- DB_HOST: 127.0.0.1 jika MySQL di server yang sama
- DB_PORT: 3306 untuk MySQL
- DB_DATABASE, DB_USERNAME, DB_PASSWORD: sesuaikan dengan database yang dibuat
- LOG_LEVEL: gunakan `info` atau `warning` untuk production
- MAIL_FROM_ADDRESS: gunakan email valid (bisa dummy jika hanya testing)
- WA_GATEWAY_URL: isi dengan endpoint WhatsApp Gateway jika ada

### Checklist Deployment Laravel Production (Ubuntu/GCP)
- Website Laravel sudah bisa diakses di http://<IP_VM> (misal: http://35.188.34.254)
- Semua permission storage, bootstrap/cache, public sudah benar (milik www-data, chmod 775/664)
- Tidak ada error permission di storage/logs/laravel.log dan storage/framework/views
- Perintah migrate, storage:link, config:cache, dsb dijalankan dengan sudo -u www-data
- NGINX root sudah mengarah ke folder public project
- File index.php ada di folder public
- .env sudah sesuai kebutuhan production (APP_URL, DB_*, LOG_LEVEL, dsb)
- MySQL listen di 127.0.0.1 dan user sudah di-GRANT ke host 127.0.0.1
- Port 80/443 sudah terbuka di firewall GCP
- Tidak ada service lain (apache2) yang memakai port 80
- NGINX sudah reload dan tidak ada error di /var/log/nginx/error.log

**Yang perlu dicek jika ada error:**
- Cek permission storage, bootstrap/cache, public (ls -l)
- Cek isi dan permission storage/logs/laravel.log
- Cek .env (tidak ada typo/spasi/tanda petik)
- Cek status nginx dan error log: `sudo systemctl status nginx && sudo tail -n 30 /var/log/nginx/error.log`
- Cek status MySQL: `sudo systemctl status mysql`
- Tes koneksi manual ke database: `mysql -u <DB_USERNAME> -p -h 127.0.0.1 <DB_DATABASE>`
- Cek file index.php di folder public
- Cek root di config nginx
- Cek port 80/443 di firewall GCP
- Jika permission error, ulangi chown/chmod ke www-data

---

## Troubleshooting & Best Practice Tambahan

### A. Git: Dubious Ownership & Permission Denied
Jika muncul error:
```
fatal: detected dubious ownership in repository at '/home/ekomh13/manpro-sizzlingchilli-backend-chill-ajar'
To add an exception for this directory, call:
    git config --global --add safe.directory /home/ekomh13/manpro-sizzlingchilli-backend-chill-ajar
```
Jalankan:
```bash
git config --global --add safe.directory /home/ekomh13/manpro-sizzlingchilli-backend-chill-ajar
```
Jika muncul error:
```
error: cannot open '.git/FETCH_HEAD': Permission denied
```
Solusi:
```bash
sudo chown -R ekomh13:ekomh13 .git
git pull
```
Jangan pernah menjalankan git dengan sudo, agar file tidak jadi milik root. Jika masih error, cek permission dengan:
```bash
ls -la
ls -la .git
```
Pastikan semua file/folder di dalam project dan .git dimiliki oleh user yang sedang digunakan (misal: ekomh13).

### B. Upload/File Besar (413 Request Entity Too Large)
**Masalah:** Error 413 saat upload file besar (POST/PUT API) dari frontend.
**Solusi:**
1. Edit config NGINX (`/etc/nginx/sites-available/laravel`):
   Tambahkan di dalam blok `server { ... }`:
   ```nginx
   client_max_body_size 20M;
   ```
2. Edit config PHP (`/etc/php/8.3/fpm/php.ini` dan `/etc/php/8.3/cli/php.ini`):
   Cari dan ubah (atau tambahkan jika belum ada):
   ```ini
   upload_max_filesize = 20M
   post_max_size = 20M
   ```
   Tips: Cari cepat di nano dengan `Ctrl+W`, ketik `upload_max_filesize`, tekan Enter.
3. Restart service:
   ```bash
   sudo systemctl reload nginx
   sudo systemctl restart php8.3-fpm
   ```
4. Cek hasil setting:
   ```bash
   sudo nginx -T | grep client_max_body_size
   php -i | grep upload_max_filesize
   php -i | grep post_max_size
   ```
   Pastikan output `client_max_body_size 20M;` muncul, dan upload_max_filesize/post_max_size di PHP juga 20M.
5. Pastikan upload file < 20MB sudah bisa tanpa error 413.

### C. WhatsApp Gateway (Port 3000) Berdampingan dengan Laravel (Port 80)
Agar WhatsApp Gateway (misal Node.js/Express) bisa berjalan di port 3000 bersamaan dengan Laravel (port 80 via NGINX):

1. Jalankan WhatsApp Gateway di Port 3000
   ```bash
   cd ~/whatsapp-sizzlingchilli-gateway-chillajar
   npm install
   # Untuk production, gunakan PM2 agar proses tetap berjalan di background:
   sudo npm install -g pm2
   pm2 start server.js --name wa-gateway
   pm2 save
   pm2 startup
   # Atau untuk development:
   npm start
   # atau
   node index.js
   # Pastikan aplikasi listen di port 3000
   ```
   - Menjalankan WhatsApp Gateway dengan PM2 di port 3000 tidak akan mengganggu Laravel/NGINX di port 80. Keduanya bisa berjalan bersamaan di server yang sama tanpa konflik, karena menggunakan port yang berbeda.
   - Pastikan tidak ada error dan aplikasi berjalan di port 3000.
2. Pastikan Firewall GCP Membuka Port 3000
   - Buka port 3000 di firewall VM GCP agar bisa diakses dari luar (jika memang ingin diakses publik).
   - Tambahkan rule firewall di GCP Console: allow TCP port 3000.
3. Akses WhatsApp Gateway
   - Dari browser atau aplikasi lain, akses dengan: `http://<IP_VM>:3000`
   - Contoh: `http://35.188.34.254:3000`
4. Berdampingan dengan Laravel
   - Tidak ada konflik, karena Laravel (via NGINX) hanya pakai port 80/443, sedangkan WhatsApp Gateway pakai port 3000.
   - Keduanya bisa berjalan bersamaan di satu VM/server.
5. (Opsional) Reverse Proxy dari NGINX
   - Jika ingin akses WhatsApp Gateway via sub-path/domain (misal: `http://domain.com/wa-gateway`), bisa tambahkan reverse proxy di config NGINX.
   - Contoh di `/etc/nginx/sites-available/laravel`:
     ```nginx
     location /wa-gateway/ {
         proxy_pass http://localhost:3000/;
         proxy_set_header Host $host;
         proxy_set_header X-Real-IP $remote_addr;
         proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
         proxy_set_header X-Forwarded-Proto $scheme;
     }
     ```
   - Setelah edit, reload NGINX:
     ```bash
     sudo systemctl reload nginx
     ```
   - Sekarang, akses `http://<IP_VM>/wa-gateway/` akan diteruskan ke aplikasi WhatsApp Gateway di port 3000.

