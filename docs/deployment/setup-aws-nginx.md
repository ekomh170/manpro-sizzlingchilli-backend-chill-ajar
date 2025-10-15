# Cara Deploy Laravel ke AWS EC2 dengan Nginx

## 1. Buat EC2 Instance
- Pilih Ubuntu Server (misal: 22.04 LTS)
- Security Group: buka port 22 (SSH), 80 (HTTP), 443 (HTTPS), 3000 (untuk WhatsApp Gateway jika perlu)

## 2. Akses Server via SSH
```
ssh -i "key-anda.pem" ubuntu@<public-ip-ec2>
```

## 3. Install Nginx, PHP, MySQL, Composer
```
sudo apt update
sudo apt install -y nginx mysql-server php-fpm php-mysql php-xml php-mbstring php-curl php-zip unzip git composer
```

## 4. Setup MySQL
```
sudo mysql
# Di prompt MySQL:
CREATE DATABASE manpro_sizzlingchilli_backend_chill_ajar;
ALTER USER 'root'@'localhost' IDENTIFIED BY 'chillajar2829';
FLUSH PRIVILEGES;
EXIT;
```

## 5. Upload/Clone Project
- Upload ZIP lalu extract, atau:
```
git clone <repo-url> /var/www/chill-ajar
cd /var/www/chill-ajar
```

## 6. Install Composer Dependency
```
composer install --no-dev --optimize-autoloader
```

## 7. Copy & Edit .env
- Copy deploy/.env.aws ke root project di server sebagai .env:
```
cp deploy/.env.aws .env
```
- Edit APP_KEY (nanti diisi setelah generate), APP_URL, dan WHATSAPP_GATEWAY_URL sesuai IP EC2.

## 8. Generate Key
```
php artisan key:generate
```

## 9. Set Permission
```
sudo chown -R www-data:www-data /var/www/chill-ajar
sudo chmod -R 775 storage bootstrap/cache
```

## 10. Migrasi & Seed Database
```
php artisan migrate --seed
```

## 11. Link Storage
```
php artisan storage:link
```

## 12. Konfigurasi Nginx
Buat file `/etc/nginx/sites-available/chill-ajar`:
```
server {
    listen 80;
    server_name <public-ip-ec2>;

    root /var/www/chill-ajar/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock; # Cek versi PHP Anda!
    }

    location ~ /\.ht {
        deny all;
    }
}
```
Aktifkan config dan restart nginx:
```
sudo ln -s /etc/nginx/sites-available/chill-ajar /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## 13. Akses Laravel
- Buka browser: `http://<public-ip-ec2>`
- Laravel siap digunakan di production mode.

---

## Catatan
- Untuk WhatsApp Gateway, jalankan Node.js di port 3000 (bisa di server yang sama).
- Untuk domain/SSL, gunakan Let’s Encrypt (certbot).
- Untuk APP_KEY, pastikan sudah di-generate.
- Untuk APP_URL dan WHATSAPP_GATEWAY_URL, pastikan sudah sesuai IP/domain server.

Jika ingin setup domain/SSL atau WhatsApp Gateway di AWS, silakan minta detail step-nya!
