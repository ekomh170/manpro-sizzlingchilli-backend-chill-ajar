#!/bin/bash
# entrypoint.sh untuk Laravel: hanya migrate dan seed jika tabel users belum ada

# ==========================================================
# CATATAN DEPLOY RENDER & SCHEDULER (PENTING)
#
# Untuk deployment di Render:
# - Jangan jalankan scheduler (php artisan schedule:work) di web service!
# - Web service hanya menjalankan aplikasi web (misal: php artisan serve).
# - Untuk scheduler, tambahkan Background Worker di dashboard Render
#   dengan command: php artisan schedule:work
# - Command seperti mentor:update-rating dijalankan otomatis oleh scheduler.
#
# Dengan cara ini, web service dan scheduler berjalan terpisah dan stabil.
#
# Data production TIDAK akan hilang karena migrate:fresh & seeding hanya
# dijalankan otomatis jika tabel users masih kosong (cek script ini).
# ==========================================================

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

log "Cek data di tabel users..."
USER_COUNT=$(php artisan tinker --execute="echo \\DB::table('users')->count();")
if [ "$USER_COUNT" -gt 0 ]; then
  log "Database sudah ada data users ($USER_COUNT), skip migrate:fresh dan db:seed"
else
  log "Database kosong, jalankan migrate:fresh dan db:seed untuk data dummy"
  php artisan migrate:fresh --force
  php artisan db:seed --force
fi

log "Set permission storage dan cache folder"
chmod -R 775 storage bootstrap/cache || true

log "Jalankan storage:link"
php artisan storage:link

log "Jalankan php artisan serve"
php artisan serve --host=0.0.0.0 --port=8080
