# API Documentation - Chill Ajar Backend

## Daftar Isi
- [Overview](#overview)
- [Authentication](#authentication)
- [Endpoints](#endpoints)
  - [Auth](#auth-endpoints)
  - [Admin](#admin-endpoints)
  - [Mentor](#mentor-endpoints)
  - [Pelanggan](#pelanggan-endpoints)
  - [Kursus](#kursus-endpoints)
  - [Sesi](#sesi-endpoints)
  - [Paket](#paket-endpoints)
  - [Item Paket](#item-paket-endpoints)
  - [Jadwal Kursus](#jadwal-kursus-endpoints)
  - [Transaksi](#transaksi-endpoints)
  - [Testimoni](#testimoni-endpoints)
- [Models & Relationships](#models--relationships)
- [Changelog](#changelog)

---

## Overview

Backend API untuk platform "Chill Ajar" - Platform Menjadi Mentor Sebaya. Sistem ini dibangun menggunakan Laravel 12 dengan autentikasi Sanctum dan mendukung manajemen user (admin, mentor, pelanggan), kursus, sesi pengajaran, paket pembelajaran, pembayaran, dan testimoni.

**Base URL:** `http://your-domain.com/api`

**Authentication:** Laravel Sanctum (Bearer Token)

**🧪 Pentest UI:** `http://your-domain.com/pentest`  
Interface interaktif untuk testing API tanpa Postman. Setiap controller memiliki halaman testing sendiri dengan contoh request body yang sudah disiapkan.

**Available Pentest Pages:**
- `/pentest/auth` - Auth Controller
- `/pentest/admin` - Admin Controller
- `/pentest/mentor` - Mentor Controller
- `/pentest/pelanggan` - Pelanggan Controller
- `/pentest/kursus` - Kursus Controller
- `/pentest/sesi` - Sesi Controller
- `/pentest/transaksi` - Transaksi Controller
- `/pentest/testimoni` - Testimoni Controller
- `/pentest/paket` - **Paket Controller** ⭐ NEW
- `/pentest/itempaket` - **Item Paket Controller** ⭐ NEW
- `/pentest/wa` - WhatsApp Gateway Testing

---

## Authentication

Semua endpoint yang memerlukan autentikasi harus menyertakan header:
```
Authorization: Bearer {your-token}
```

---

## Endpoints

### Auth Endpoints

#### POST /login
Login pengguna (admin/mentor/pelanggan)

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "token": "1|xxxxxxxxxxxxx",
  "user": {
    "id": 1,
    "nama": "John Doe",
    "email": "user@example.com",
    "role": "pelanggan"
  }
}
```

#### POST /register
Registrasi pengguna baru

**Request Body:**
```json
{
  "nama": "John Doe",
  "email": "user@example.com",
  "password": "password123",
  "nomorTelepon": "081234567890",
  "alamat": "Jl. Example No. 123"
}
```

#### POST /logout
Logout pengguna (requires auth)

---

### Admin Endpoints

#### GET /admin/users
Daftar semua pengguna (requires auth: admin)

**Response:**
```json
[
  {
    "id": 1,
    "nama": "John Doe",
    "email": "user@example.com",
    "role": "pelanggan",
    "nomorTelepon": "081234567890"
  }
]
```

#### PUT /admin/users/{userId}/role
Ubah role pengguna (requires auth: admin)

**Request Body:**
```json
{
  "role": "mentor"
}
```

#### POST /admin/mentor
Tambah mentor baru (requires auth: admin)

**Request Body:**
```json
{
  "user_id": 1,
  "biayaPerSesi": 50000,
  "deskripsi": "Mentor berpengalaman di bidang matematika",
  "status": "aktif"
}
```

#### PUT /admin/mentor/{id}
Update data mentor (requires auth: admin)

#### DELETE /admin/mentor/{id}
Hapus mentor (requires auth: admin)

#### POST /admin/verifikasi-pembayaran/{transaksiId}
Verifikasi pembayaran pelanggan (requires auth: admin)

**Response:**
```json
{
  "message": "Pembayaran berhasil diverifikasi",
  "transaksi": { ... },
  "wa_result": {
    "status": true,
    "message": "WhatsApp notification sent"
  }
}
```

#### POST /admin/tolak-pembayaran/{transaksiId}
Tolak pembayaran pelanggan (requires auth: admin)

**Request Body:**
```json
{
  "alasan": "Bukti pembayaran tidak valid"
}
```

#### GET /admin/dashboard-info
Statistik untuk dashboard admin (requires auth: admin)

**Response:**
```json
{
  "jumlah_pengguna": 100,
  "jumlah_mentor": 20,
  "jumlah_pelanggan": 80,
  "jumlah_kursus": 50,
  "jumlah_sesi": 200
}
```

---

### Mentor Endpoints

#### GET /mentor/profil-saya
Profil mentor yang sedang login (requires auth: mentor)

**Response:**
```json
{
  "id": 1,
  "user_id": 5,
  "biayaPerSesi": 50000,
  "rating": 4.5,
  "deskripsi": "Mentor matematika berpengalaman",
  "status": "aktif",
  "user": {
    "id": 5,
    "nama": "Jane Mentor",
    "email": "mentor@example.com"
  }
}
```

#### POST /mentor/kursus
Tambah kursus milik sendiri (requires auth: mentor)

**Request Body (multipart/form-data):**
```
namaKursus: "Kalkulus Dasar"
deskripsi: "Belajar kalkulus dari dasar"
fotoKursus: [file]
```

#### PUT /mentor/kursus/{id}
Update kursus milik sendiri (requires auth: mentor)

#### POST /mentor/atur-jadwal
Atur jadwal pengajaran (requires auth: mentor)

**Request Body:**
```json
{
  "kursus_id": 1,
  "tanggal": "2025-10-20",
  "waktu": "14:00:00",
  "gayaMengajar": "online",
  "platformOnline": "Google Meet",
  "linkOnline": "https://meet.google.com/abc-defg-hij"
}
```

#### GET /mentor/daftar-sesi
Daftar sesi yang diampu mentor (requires auth: mentor)

**Response:**
```json
[
  {
    "id": 1,
    "mentor_id": 1,
    "pelanggan_id": 2,
    "kursus_id": 3,
    "jadwal_kursus_id": 5,
    "paket_id": 2,
    "statusSesi": "pending",
    "jumlahSementara": 150000,
    "pelanggan": { ... },
    "kursus": { ... },
    "paket": {
      "id": 2,
      "nama": "Paket Premium",
      "harga_dasar": 100000,
      "diskon": 10000,
      "items": [ ... ]
    }
  }
]
```

#### POST /mentor/mulai-sesi/{sessionId}
Mulai/konfirmasi sesi (requires auth: mentor)

**Response:**
```json
{
  "message": "Sesi dimulai",
  "sesi": { ... },
  "wa_result": {
    "status": true,
    "message": "WhatsApp sent to customer"
  }
}
```

#### POST /mentor/selesai-sesi/{sessionId}
Selesaikan sesi (requires auth: mentor)

#### GET /mentor/daftar-testimoni
Daftar testimoni yang diterima (requires auth: mentor)

#### GET /mentor/dashboard-info
Dashboard info untuk mentor - analytics & calendar (requires auth: mentor)

---

### Pelanggan Endpoints

#### GET /pelanggan/profil-saya
Profil pelanggan yang sedang login (requires auth: pelanggan)

#### GET /pelanggan/daftar-kursus
Daftar kursus yang tersedia

**Response:**
```json
[
  {
    "id": 1,
    "namaKursus": "Kalkulus Dasar",
    "deskripsi": "Belajar kalkulus dari dasar",
    "fotoKursus": "foto_kursus/xxx.jpg",
    "mentor": {
      "id": 1,
      "user": { ... }
    },
    "jadwalKursus": [
      {
        "id": 1,
        "tanggal": "2025-10-20",
        "waktu": "14:00:00",
        "gayaMengajar": "online"
      }
    ],
    "visibilitasPaket": [
      {
        "id": 1,
        "paket_id": 1,
        "visibilitas": true,
        "paket": {
          "id": 1,
          "nama": "Paket Basic",
          "harga_dasar": 50000,
          "diskon": 5000,
          "items": [
            {
              "id": 1,
              "nama": "Modul PDF",
              "harga": 20000,
              "diskon": 2000,
              "pivot": {
                "jumlah_item": 2
              }
            }
          ]
        }
      }
    ]
  }
]
```

#### POST /pelanggan/pesan-sesi
Memesan sesi pengajaran (requires auth: pelanggan)

**Request Body:**
```json
{
  "mentor_id": 1,
  "pelanggan_id": 2,
  "kursus_id": 3,
  "jadwal_kursus_id": 5,
  "detailKursus": "Ingin fokus pada topik turunan",
  "statusSesi": "pending",
  "paket_id": 2
}
```

**Response:**
```json
{
  "message": "Sesi berhasil dipesan",
  "sesi": {
    "id": 10,
    "mentor_id": 1,
    "pelanggan_id": 2,
    "kursus_id": 3,
    "jadwal_kursus_id": 5,
    "paket_id": 2,
    "jumlahSementara": 150000,
    "statusSesi": "pending"
  }
}
```

**Catatan Penting:** 
- Field `paket_id` adalah **nullable/optional**
- Jika `paket_id` diisi, `jumlahSementara` akan dihitung dari: `(harga paket setelah diskon) + biayaPerSesi mentor`
- Jika `paket_id` kosong/null, `jumlahSementara` hanya menggunakan `biayaPerSesi` mentor
- Harga paket dihitung dari total item paket (setelah diskon item) dikurangi diskon paket

#### GET /pelanggan/daftar-sesi
Daftar sesi yang pernah diikuti (sudah diverifikasi pembayarannya) (requires auth: pelanggan)

**Response:**
```json
[
  {
    "id": 1,
    "statusSesi": "end",
    "jumlahSementara": 150000,
    "mentor": { ... },
    "kursus": { ... },
    "jadwalKursus": { ... },
    "paket": {
      "id": 2,
      "nama": "Paket Premium",
      "harga_dasar": 100000,
      "diskon": 10000,
      "items": [
        {
          "id": 1,
          "nama": "Modul PDF Premium",
          "harga": 50000,
          "diskon": 5000,
          "deskripsi": "Modul lengkap dengan video tutorial"
        }
      ]
    },
    "transaksi": { ... },
    "testimoni": { ... }
  }
]
```

#### GET /pelanggan/daftar-sesi-transaksi
Daftar semua sesi untuk halaman riwayat transaksi (requires auth: pelanggan)

#### POST /pelanggan/unggah-bukti/{transaksiId}
Unggah bukti pembayaran (requires auth: pelanggan)

**Request Body (multipart/form-data):**
```
buktiPembayaran: [file]
tanggalPembayaran: "2025-10-15"
```

#### POST /pelanggan/beri-testimoni/{sessionId}
Beri testimoni setelah sesi selesai (requires auth: pelanggan)

**Request Body:**
```json
{
  "rating": 5,
  "komentar": "Mentor sangat membantu!"
}
```

#### GET /pelanggan/profil-info
Statistik pelanggan (requires auth: pelanggan)

**Response:**
```json
{
  "jumlah_sesi": 15,
  "jumlah_mentor": 5,
  "jumlah_kursus": 8
}
```

---

### Kursus Endpoints

#### GET /kursus
Daftar semua kursus (requires auth)

**Response:**
```json
[
  {
    "id": 1,
    "namaKursus": "Kalkulus Dasar",
    "deskripsi": "Belajar kalkulus dari dasar",
    "mentor_id": 1,
    "fotoKursus": "foto_kursus/xxx.jpg",
    "mentor": {
      "id": 1,
      "user": { ... }
    },
    "jadwalKursus": [ ... ],
    "visibilitasPaket": [
      {
        "id": 1,
        "kursus_id": 1,
        "paket_id": 1,
        "visibilitas": true,
        "paket": {
          "id": 1,
          "nama": "Paket Basic",
          "harga_dasar": 50000,
          "diskon": 5000,
          "items": [ ... ]
        }
      }
    ]
  }
]
```

#### POST /kursus
Tambah kursus baru (requires auth: admin)

**Request Body (multipart/form-data):**
```
namaKursus: "Fisika Dasar"
deskripsi: "Belajar fisika dari awal"
mentor_id: 1
fotoKursus: [file]
paket_ids: [1, 2, 3]
visibilitas_paket: [{"paket_id": 1, "visibilitas": true}]
```

**Validasi:**
- `paket_ids` wajib diisi minimal 1 paket
- `visibilitas_paket` opsional untuk mengatur status visibilitas tiap paket

#### PUT /kursus/{id}
Update kursus (requires auth: admin)

**Request Body (multipart/form-data):**
```
namaKursus: "Fisika Lanjutan"
mentor_id: 1
fotoKursus: [file]
paket_ids: [1, 2]
visibilitas_paket: [{"paket_id": 1, "visibilitas": true}, {"paket_id": 2, "visibilitas": false}]
```

#### GET /kursus/{id}
Detail kursus (requires auth)

#### DELETE /kursus/{id}
Hapus kursus (requires auth: admin)

---

### Sesi Endpoints

#### GET /sesi
Daftar semua sesi yang pembayarannya sudah diterima (requires auth)

**Response:**
```json
[
  {
    "id": 1,
    "mentor_id": 1,
    "pelanggan_id": 2,
    "kursus_id": 3,
    "jadwal_kursus_id": 5,
    "paket_id": 2,
    "detailKursus": "Fokus pada turunan",
    "jumlahSementara": 150000,
    "statusSesi": "pending",
    "mentor": { ... },
    "pelanggan": { ... },
    "kursus": { ... },
    "jadwalKursus": { ... },
    "paket": {
      "id": 2,
      "nama": "Paket Premium",
      "items": [ ... ]
    }
  }
]
```

#### POST /sesi
Tambah sesi baru (requires auth)

**Request Body:**
```json
{
  "mentor_id": 1,
  "pelanggan_id": 2,
  "kursus_id": 3,
  "jadwal_kursus_id": 5,
  "paket_id": 2,
  "detailKursus": "Topik khusus",
  "statusSesi": "pending"
}
```

**Catatan:** Field `paket_id` adalah **nullable/optional**

#### GET /sesi/{id}
Detail sesi (requires auth)

**Response:**
```json
{
  "id": 1,
  "mentor_id": 1,
  "pelanggan_id": 2,
  "kursus_id": 3,
  "jadwal_kursus_id": 5,
  "paket_id": 2,
  "statusSesi": "end",
  "jumlahSementara": 150000,
  "mentor": { ... },
  "pelanggan": { ... },
  "kursus": { ... },
  "jadwalKursus": { ... },
  "paket": { ... },
  "transaksi": { ... },
  "testimoni": { ... }
}
```

#### PUT /sesi/{id}
Update sesi (requires auth)

#### DELETE /sesi/{id}
Hapus sesi (requires auth)

#### POST /sesi/{id}/konfirmasi
Konfirmasi sesi dimulai (requires auth: mentor)

**Response:**
```json
{
  "message": "Sesi dimulai",
  "sesi": { ... },
  "wa_result": {
    "status": true,
    "message": "WhatsApp notification sent to customer"
  }
}
```

#### POST /sesi/{id}/selesai
Tandai sesi selesai (requires auth: mentor)

---

### Paket Endpoints

#### GET /paket
Daftar semua paket (requires auth)

**🧪 Test di:** `/pentest/paket`

**Response:**
```json
[
  {
    "id": 1,
    "nama": "Paket Basic",
    "harga_dasar": 50000,
    "diskon": 5000,
    "deskripsi": "Paket pembelajaran dasar",
    "tanggal_mulai": "2025-01-01",
    "tanggal_berakhir": "2025-12-31",
    "max_pembelian_per_user": 3,
    "items": [
      {
        "id": 1,
        "nama": "Modul PDF",
        "harga": 20000,
        "diskon": 2000,
        "deskripsi": "Modul lengkap format PDF",
        "pivot": {
          "jumlah_item": 2
        }
      }
    ]
  }
]
```

#### POST /paket
Tambah paket baru (requires auth: admin)

**Request Body:**
```json
{
  "nama": "Paket Premium",
  "harga_dasar": 100000,
  "diskon": 10000,
  "deskripsi": "Paket pembelajaran premium",
  "tanggal_mulai": "2025-01-01",
  "tanggal_berakhir": "2025-12-31",
  "max_pembelian_per_user": 5,
  "item_paket_ids": [1, 2, 3],
  "jumlah_items": [2, 1, 3]
}
```

#### GET /paket/{id}
Detail paket (requires auth)

#### PUT /paket/{id}
Update paket (requires auth: admin)

#### DELETE /paket/{id}
Hapus paket - soft delete (requires auth: admin)

---

### Item Paket Endpoints

#### GET /item-paket
Daftar semua item paket (requires auth)

**Response:**
```json
[
  {
    "id": 1,
    "nama": "Modul PDF",
    "harga": 20000,
    "diskon": 2000,
    "deskripsi": "Modul pembelajaran format PDF"
  }
]
```

#### POST /item-paket
Tambah item paket baru (requires auth: admin)

**Request Body:**
```json
{
  "nama": "Video Tutorial",
  "harga": 50000,
  "diskon": 5000,
  "deskripsi": "Video tutorial lengkap"
}
```

#### GET /item-paket/{id}
Detail item paket (requires auth)

#### PUT /item-paket/{id}
Update item paket (requires auth: admin)

**Catatan Penting:**
- Jika `harga` atau `diskon` item berubah, sistem akan otomatis **rekalkulasi harga_dasar** untuk semua paket yang menggunakan item tersebut
- Formula: `harga_dasar = sum(max(item.harga - item.diskon, 0) * jumlah_pivot)` untuk semua item dalam paket

#### DELETE /item-paket/{id}
Hapus item paket - soft delete (requires auth: admin)

**Catatan Penting:**
- Item yang dihapus akan di-soft delete
- Sistem akan otomatis **rekalkulasi harga_dasar** untuk semua paket yang menggunakan item tersebut
- Item yang sudah di-soft delete tidak akan dihitung dalam kalkulasi harga paket

---

### Jadwal Kursus Endpoints

#### GET /jadwal-kursus
Daftar semua jadwal kursus (requires auth)

#### POST /jadwal-kursus
Tambah jadwal kursus baru (requires auth)

**Request Body:**
```json
{
  "kursus_id": 1,
  "tanggal": "2025-10-20",
  "waktu": "14:00:00",
  "gayaMengajar": "online",
  "platformOnline": "Google Meet",
  "linkOnline": "https://meet.google.com/abc-defg-hij"
}
```

#### GET /jadwal-kursus/{id}
Detail jadwal kursus (requires auth)

#### PUT /jadwal-kursus/{id}
Update jadwal kursus (requires auth)

#### DELETE /jadwal-kursus/{id}
Hapus jadwal kursus (requires auth)

---

### Transaksi Endpoints

#### GET /transaksi
Daftar semua transaksi (requires auth)

#### POST /transaksi
Tambah transaksi baru (requires auth)

#### GET /transaksi/{id}
Detail transaksi (requires auth)

#### PUT /transaksi/{id}
Update transaksi (requires auth)

#### DELETE /transaksi/{id}
Hapus transaksi (requires auth)

#### POST /transaksi/{id}/unggah-bukti
Unggah bukti pembayaran (requires auth: pelanggan)

#### POST /transaksi/{id}/verifikasi
Verifikasi pembayaran (requires auth: admin)

#### POST /transaksi/{id}/tolak
Tolak pembayaran (requires auth: admin)

---

### Testimoni Endpoints

#### GET /testimoni
Daftar semua testimoni (requires auth)

#### POST /testimoni
Tambah testimoni baru (requires auth)

#### GET /testimoni/{id}
Detail testimoni (requires auth)

#### PUT /testimoni/{id}
Update testimoni (requires auth)

#### DELETE /testimoni/{id}
Hapus testimoni (requires auth)

#### GET /mentor/{mentorId}/testimoni
Daftar testimoni untuk mentor tertentu (requires auth)

---

## Models & Relationships

### Sesi Model
```php
protected $fillable = [
    'mentor_id',
    'pelanggan_id',
    'kursus_id',
    'jadwal_kursus_id',
    'detailKursus',
    'jumlahSementara',
    'paket_id',        // NEW: relasi ke paket
    'statusSesi',
];

// Relationships
public function paket()
{
    return $this->belongsTo(Paket::class, 'paket_id');
}
```

### Paket Model
```php
protected $fillable = [
    'nama',
    'harga_dasar',
    'diskon',
    'deskripsi',
    'tanggal_mulai',
    'tanggal_berakhir',
    'max_pembelian_per_user',
];

// Relationships
public function items()
{
    return $this->belongsToMany(ItemPaket::class, 'relasi_item_paket')
        ->withPivot('jumlah_item')
        ->withTimestamps();
}

public function visibilitas()
{
    return $this->hasMany(VisibilitasPaket::class);
}
```

### ItemPaket Model
```php
protected $fillable = [
    'nama',
    'harga',
    'diskon',
    'deskripsi',
];

// Relationships
public function paket()
{
    return $this->belongsToMany(Paket::class, 'relasi_item_paket')
        ->withPivot('jumlah_item')
        ->withTimestamps();
}
```

### Kursus Model
```php
// Relationships dengan visibilitas paket
public function visibilitasPaket()
{
    return $this->hasMany(VisibilitasPaket::class);
}
```

---

## Changelog

### Version 1.2.0 (October 15, 2025) - Branch: tambah-paket_id-pada-tabel-sesi

#### 🆕 Fitur Baru

**1. Paket dalam Sesi (Sesi Model & Controller)**
- ✅ Menambahkan kolom `paket_id` (nullable) pada tabel sesi
- ✅ Relasi `paket()` di Sesi model untuk mengakses data paket
- ✅ Perhitungan `jumlahSementara` otomatis:
  - Jika ada `paket_id`: `(harga paket setelah diskon) + biayaPerSesi mentor`
  - Jika tidak ada `paket_id`: hanya `biayaPerSesi mentor`
- ✅ Response API sesi sekarang include data paket lengkap dengan items

**2. Sistem Paket & Item Paket (ItemPaketController)**
- ✅ Auto-rekalkulasi `harga_dasar` paket ketika item paket diupdate
- ✅ Auto-rekalkulasi `harga_dasar` paket ketika item paket dihapus (soft delete)
- ✅ Formula perhitungan: `harga_dasar = sum(max(item.harga - item.diskon, 0) * jumlah_item)`
- ✅ Soft delete pada item paket yang dihapus tidak dihitung dalam kalkulasi

**3. Visibilitas Paket per Kursus (KursusController)**
- ✅ Setiap kursus dapat memiliki multiple paket dengan status visibilitas
- ✅ Admin dapat mengatur paket mana yang aktif untuk kursus tertentu
- ✅ Field `paket_ids` wajib minimal 1 paket saat create kursus
- ✅ Field `visibilitas_paket` untuk toggle visibilitas paket per kursus
- ✅ Response API include `visibilitasPaket.paket.items` untuk detail lengkap

**4. Endpoint Pelanggan - Pemesanan dengan Paket**
- ✅ Pelanggan dapat memilih paket saat memesan sesi (optional)
- ✅ Perhitungan biaya otomatis berdasarkan paket yang dipilih
- ✅ Display paket pada riwayat sesi pelanggan
- ✅ Detail items paket ditampilkan di daftar sesi

#### 🔧 Perubahan & Perbaikan

**PelangganController**
- ✨ `pesanSesi()`: Support parameter `paket_id` (nullable)
- ✨ `daftarSesiMentor()`: Include relasi `paket.items` dalam response
- ✨ `daftarSesiTransaksi()`: Include relasi `paket.items` dalam response
- ✨ Perhitungan `jumlahSementara` otomatis dengan/tanpa paket

**KursusController**
- ✨ `index()`: Load relasi `visibilitasPaket.paket.items`
- ✨ `show()`: Load relasi `visibilitasPaket.paket.items`
- ✨ `store()`: Validasi minimal 1 paket wajib dipilih
- ✨ `store()`: Support parameter `paket_ids` dan `visibilitas_paket`
- ✨ `update()`: Update relasi paket via tabel `visibilitas_paket`
- ✨ Support FormData untuk `visibilitas_paket` (decode JSON string)

**ItemPaketController**
- ✨ `update()`: Auto-rekalkulasi harga_dasar paket terkait jika harga/diskon berubah
- ✨ `destroy()`: Auto-rekalkulasi harga_dasar paket terkait setelah soft delete
- ✨ `recalculateHargaDasarForPaket()`: Method helper untuk kalkulasi ulang harga paket

**SesiController**
- ✨ `index()`: Include relasi `paket` dalam response
- ✨ `show()`: Load relasi lengkap termasuk `paket`

**Routes (api.php)**
- ✅ Semua endpoint sudah support fitur paket dalam sesi

#### 📊 Database Changes

**Tabel sesi:**
- Kolom baru: `paket_id` (bigint unsigned, nullable, foreign key ke tabel paket)

**Tabel relasi_item_paket:**
- Relasi many-to-many antara paket dan item_paket
- Kolom: `paket_id`, `item_paket_id`, `jumlah_item`

**Tabel visibilitas_paket:**
- Relasi antara kursus dan paket
- Kolom: `kursus_id`, `paket_id`, `visibilitas` (boolean)

#### 💡 Business Logic

**Perhitungan Harga Paket:**
1. Hitung total harga items: `sum((item.harga - item.diskon) * jumlah_item)`
2. Kurangi diskon paket: `harga_items - paket.diskon`
3. Tambahkan biaya sesi mentor: `harga_paket + mentor.biayaPerSesi`

**Kalkulasi Otomatis:**
- Update harga/diskon item → recalculate semua paket terkait
- Delete item → recalculate semua paket terkait
- Item soft-deleted tidak dihitung dalam kalkulasi

**Visibilitas Paket:**
- Admin mengatur paket mana yang tersedia untuk setiap kursus
- Pelanggan hanya melihat paket yang visibilitas = true
- Satu kursus bisa punya banyak paket dengan status berbeda

#### 🎯 Use Cases

1. **Pelanggan memesan sesi tanpa paket:**
   - Hanya bayar `biayaPerSesi` mentor
   - `paket_id` = null

2. **Pelanggan memesan sesi dengan paket:**
   - Bayar `biayaPerSesi + (harga_paket - diskon_paket)`
   - Dapat benefit items dalam paket (modul, video, dll)
   - `paket_id` terisi

3. **Admin update harga item paket:**
   - Sistem otomatis update `harga_dasar` semua paket terkait
   - Tidak perlu manual update tiap paket

4. **Admin atur visibilitas paket:**
   - Toggle paket mana yang aktif per kursus
   - Pelanggan hanya lihat paket yang aktif

---

## Error Handling

Semua endpoint mengikuti format error response standar:

```json
{
  "message": "Error message description",
  "errors": {
    "field_name": ["Validation error message"]
  }
}
```

**HTTP Status Codes:**
- `200 OK`: Request berhasil
- `201 Created`: Resource berhasil dibuat
- `204 No Content`: Resource berhasil dihapus
- `400 Bad Request`: Request tidak valid
- `401 Unauthorized`: Tidak terautentikasi
- `403 Forbidden`: Tidak memiliki akses
- `404 Not Found`: Resource tidak ditemukan
- `422 Unprocessable Entity`: Validasi gagal
- `500 Internal Server Error`: Server error

---

## Notes

1. **WhatsApp Integration:** 
   - Notifikasi WhatsApp dikirim saat pembayaran diverifikasi/ditolak
   - Notifikasi dikirim saat sesi dimulai ke pelanggan

2. **File Upload:**
   - Foto profil disimpan di `storage/app/public/foto_profil`
   - Foto kursus disimpan di `storage/app/public/foto_kursus`
   - Bukti pembayaran disimpan di `storage/app/public/bukti_pembayaran`
   - Max file size: 10MB

3. **Soft Delete:**
   - Item paket menggunakan soft delete
   - Paket menggunakan soft delete
   - Item yang di-soft delete tidak dihitung dalam kalkulasi harga

4. **Auto Calculation:**
   - `jumlahSementara` dihitung otomatis saat pesan sesi
   - `harga_dasar` paket dikalkulasi ulang saat item berubah
   - Rating mentor diupdate otomatis via scheduler

---

**Last Updated:** October 15, 2025  
**Version:** 1.2.0  
**Branch:** master (merged from tambah-paket_id-pada-tabel-sesi)
