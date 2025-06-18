#!/bin/bash
# entrypoint.sh untuk Laravel: hanya migrate dan seed jika tabel users belum ada

set -e

# Fungsi untuk log dengan timestamp
log() {
  echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1"
}

log "Menunggu database siap..."
# Loop tanpa batas sampai database siap
while true; do
  if php artisan migrate:status > /dev/null 2>&1; then
    log "Database sudah bisa diakses."
    break
  else
    log "Database belum siap, tunggu 1 detik..."
    sleep 1
  fi
done

log "Cek tabel users di database..."
if php artisan db:show users > /dev/null 2>&1; then
  log "Database sudah terisi, skip migrate:fresh dan db:seed"
else
  log "Database kosong, jalankan migrate:fresh dan db:seed"
  php artisan migrate:fresh --force
  php artisan db:seed --force
fi

log "Set permission storage dan cache folder"
chmod -R 775 storage bootstrap/cache || true

log "Jalankan storage:link"
php artisan storage:link

log "Jalankan php artisan serve"
php artisan serve --host=0.0.0.0 --port=8080
