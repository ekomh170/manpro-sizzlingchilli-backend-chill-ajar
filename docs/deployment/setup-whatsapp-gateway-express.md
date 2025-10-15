# Panduan Setup WhatsApp Gateway (whatsapp-web.js) untuk Chill Ajar

## 1. Persiapan
- Pastikan Node.js & npm sudah terinstall di server/PC.
- Siapkan satu nomor WhatsApp khusus sistem (misal: +62 851-7302-8290) yang akan digunakan sebagai pengirim notifikasi.

## 2. Instalasi whatsapp-web.js Gateway
1. Clone atau download source code gateway open source (misal: https://github.com/pedroslopez/whatsapp-web.js-example atau project serupa).
2. Masuk ke folder project gateway:
   ```bash
   cd whatsapp-web.js-example
   npm install
   ```
3. (Opsional) Edit kode agar endpoint `/send-message` menerima parameter `sender` dan hanya mengirim dari nomor sistem.

## 3. Jalankan Gateway
- Jalankan server dengan perintah:
  ```bash
  node index.js
  ```
- Saat pertama kali dijalankan, scan QR code dengan aplikasi WhatsApp pada HP nomor sistem.
- Pastikan status gateway online dan siap menerima request.

## 4. Konfigurasi Backend Laravel
- Endpoint pengiriman WhatsApp sudah otomatis mengirim ke `http://localhost:3000/send-message` dengan parameter:
  - `phone`: nomor tujuan (format 62xxxxxxxxxxx)
  - `message`: isi pesan
  - `sender`: nomor sistem (harus sama dengan nomor yang login di gateway)
- Tidak perlu mengisi FONNTE_API_KEY di .env jika sudah pakai gateway ini.

## 5. Validasi Pengiriman
- Jalankan backend Laravel & gateway whatsapp-web.js.
- Lakukan aksi "Selesai Sesi" di UI pentest/admin.
- Pastikan pelanggan menerima pesan WhatsApp dari nomor sistem.
- Cek log backend jika terjadi error (storage/logs/laravel.log).

## 6. Troubleshooting
- Jika pesan tidak terkirim:
  - Pastikan gateway berjalan & status online.
  - Pastikan nomor sistem sudah login dan tidak logout di HP.
  - Cek apakah endpoint menerima request (lihat log gateway).
  - Pastikan format nomor tujuan benar (62xxxxxxxxxxx).
- Jika ingin deploy di server, pastikan port 3000 terbuka dan Node.js berjalan sebagai service.
- Untuk keamanan, endpoint WhatsApp Gateway di backend Laravel diatur melalui variabel <code>.env</code> (<b>WHATSAPP_GATEWAY_URL</b>), sehingga IP/URL gateway tidak hardcode di kode. Ganti <code>WHATSAPP_GATEWAY_URL</code> di <code>.env</code> ke <code>http://13.250.41.78:3000/send-message</code> jika sudah deploy di server publik.
- Jangan expose port 3000 ke publik tanpa firewall/IP whitelist jika server production.

## 7. Catatan
- Nomor sistem hanya bisa aktif di satu device/browser pada satu waktu.
- Jika nomor sistem logout, scan ulang QR code.
- Untuk multi-device/multi-sender, perlu modifikasi gateway lebih lanjut.

---

Jika butuh bantuan lebih lanjut, cek dokumentasi whatsapp-web.js atau hubungi developer backend.
