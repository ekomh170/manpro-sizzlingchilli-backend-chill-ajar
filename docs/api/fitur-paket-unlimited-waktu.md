# Fitur: Toggle "Paket Tidak Terbatas Waktu"

## Latar Belakang

Saat ini sistem paket menggunakan kolom `tanggal_mulai` dan `tanggal_berakhir` untuk mengatur masa aktif paket. Jika kedua kolom bernilai `NULL`, paket dianggap unlimited. Namun ketika admin mengisi tanggal dan paket sudah melewati `tanggal_berakhir`, paket tidak akan muncul di pilihan user.

**Pain point:** Admin harus memperbarui tanggal secara berkala agar paket tetap muncul — ini membebani operasional, terutama untuk paket yang memang dimaksudkan selalu tersedia.

---

## Masalah yang Diidentifikasi

| Kondisi | Perilaku Saat Ini | Dampak |
|---|---|---|
| `tanggal_berakhir` terlewati | Paket tidak tampil ke user | User tidak bisa memilih paket yang mestinya tersedia |
| Admin lupa update tanggal | Paket "hilang" otomatis | Kehilangan potensi transaksi |
| Paket promo vs paket tetap | Tidak ada perbedaan mekanisme | Tidak bisa membedakan paket berkala vs paket permanen |

---

## Solusi yang Diusulkan

Tambahkan field **`is_unlimited_waktu`** (boolean) di tabel `paket`. Ketika toggle ini diaktifkan (`true`), sistem mengabaikan `tanggal_mulai` dan `tanggal_berakhir` — paket selalu tampil kepada user tanpa perlu admin memperbarui tanggal secara berkala.

### Logika Visibilitas Paket (Setelah Fitur Ini)

```
TAMPIL ke user JIKA:
  - is_unlimited_waktu = true
    → Abaikan tanggal, paket selalu aktif
  ATAU
  - is_unlimited_waktu = false DAN tanggal_mulai <= hari_ini <= tanggal_berakhir
    → Paket aktif dalam rentang waktu yang ditentukan
  ATAU
  - is_unlimited_waktu = false DAN tanggal_mulai IS NULL DAN tanggal_berakhir IS NULL
    → Perilaku lama: NULL = unlimited (backward compatible)
```

---

## Perubahan Teknis yang Diperlukan

### 1. Migration: Tambah Kolom Baru

Buat migration baru untuk menambah kolom `is_unlimited_waktu` ke tabel `paket`:

```php
// database/migrations/xxxx_xx_xx_add_is_unlimited_waktu_to_paket_table.php

Schema::table('paket', function (Blueprint $table) {
    $table->boolean('is_unlimited_waktu')->default(false)->after('tanggal_berakhir');
    // false = gunakan rentang tanggal (perilaku default saat ini)
    // true  = abaikan tanggal, selalu tampil
});
```

**Default `false`** agar semua paket lama tidak berubah perilaku (backward compatible).

---

### 2. Update Model `Paket`

File: `app/Models/Paket.php`

Tambah `is_unlimited_waktu` ke `$fillable` dan tambahkan scope untuk filter paket yang sedang aktif:

```php
protected $fillable = [
    'nama',
    'harga_dasar',
    'diskon',
    'deskripsi',
    'tanggal_mulai',
    'tanggal_berakhir',
    'is_unlimited_waktu',   // <-- tambahkan
    'max_pembelian_per_user',
];

protected $casts = [
    'is_unlimited_waktu' => 'boolean',  // <-- tambahkan
];

// Scope untuk filter paket yang sedang aktif/tampil ke user
public function scopeAktif($query)
{
    return $query->where(function ($q) {
        // Paket unlimited waktu: selalu tampil
        $q->where('is_unlimited_waktu', true)
          // ATAU: tanggal NULL (perilaku lama)
          ->orWhere(function ($q2) {
              $q2->whereNull('tanggal_mulai')
                 ->whereNull('tanggal_berakhir');
          })
          // ATAU: dalam rentang tanggal
          ->orWhere(function ($q2) {
              $q2->where('tanggal_mulai', '<=', now()->toDateString())
                 ->where('tanggal_berakhir', '>=', now()->toDateString());
          });
    });
}
```

---

### 3. Update `PaketController`

File: `app/Http/Controllers/Fitur/PaketController.php`

**a) Tambah validasi di `store()` dan `update()`:**
```php
'is_unlimited_waktu' => 'nullable|boolean',
```

**b) Update method `index()` agar paket yang tampil ke user sudah terfilter:**
```php
// Untuk endpoint publik (user melihat daftar paket)
public function index()
{
    $paket = Paket::with('items')->aktif()->get();
    return response()->json($paket);
}
```

> Catatan: Endpoint admin (jika ada) sebaiknya tetap menampilkan semua paket tanpa filter `aktif()` agar admin bisa melihat dan mengelola paket yang sudah tidak aktif.

---

### 4. Update Query di `KursusController`

File: `app/Http/Controllers/Fitur/KursusController.php`

Saat load kursus beserta paket yang tersedia, tambahkan filter `aktif()`:

```php
$kursus = Kursus::with([
    'mentor.user',
    'jadwalKursus',
    'visibilitasPaket' => function ($query) {
        $query->where('visibilitas', true)
              ->whereHas('paket', fn($q) => $q->aktif()); // <-- tambahkan filter
    },
    'visibilitasPaket.paket.items',
])->get();
```

---

### 5. Update Seeder (Opsional)

File: `database/seeders/PaketSeeder.php`

Untuk paket yang memang dimaksudkan selalu ada (misal paket "NgeChill"), set `is_unlimited_waktu = true`:

```php
Paket::create([
    'nama' => 'NgeChill',
    'is_unlimited_waktu' => true,   // <-- selalu tampil
    'tanggal_mulai' => null,
    'tanggal_berakhir' => null,
    // ...
]);
```

---

## Dampak ke Endpoint API

| Endpoint | Perilaku Sebelum | Perilaku Setelah |
|---|---|---|
| `GET /api/paket` | Semua paket (tidak ada filter waktu) | Hanya paket aktif (filter dengan scope `aktif()`) |
| `GET /api/paket/{id}` | Tidak ada filter | Tetap sama (detail satu paket) |
| `GET /kursus/{id}` | Semua paket visible=true | Paket visible=true + aktif berdasarkan waktu |
| `POST /api/paket` | Tidak terima `is_unlimited_waktu` | Terima field baru |
| `PUT /api/paket/{id}` | Tidak terima `is_unlimited_waktu` | Terima field baru |

---

## Contoh Payload Request (Setelah Fitur)

### Membuat Paket Permanen (Tidak Terbatas Waktu)
```json
POST /api/paket
{
  "nama": "NgeChill",
  "harga_dasar": 150000,
  "diskon": 0,
  "deskripsi": "Paket reguler tanpa batas waktu",
  "is_unlimited_waktu": true,
  "tanggal_mulai": null,
  "tanggal_berakhir": null
}
```

### Membuat Paket Promo Berbatas Waktu
```json
POST /api/paket
{
  "nama": "Promo Lebaran",
  "harga_dasar": 99000,
  "diskon": 20000,
  "deskripsi": "Promo khusus bulan Ramadan",
  "is_unlimited_waktu": false,
  "tanggal_mulai": "2026-03-01",
  "tanggal_berakhir": "2026-04-10"
}
```

---

## Aturan Bisnis

| Kondisi | Hasil |
|---|---|
| `is_unlimited_waktu = true` | Paket selalu tampil, abaikan tanggal |
| `is_unlimited_waktu = false` + tanggal `NULL` | Paket tampil (backward compatible dengan perilaku lama) |
| `is_unlimited_waktu = false` + dalam rentang tanggal | Paket tampil |
| `is_unlimited_waktu = false` + tanggal sudah lewat | Paket tidak tampil ke user |
| `is_unlimited_waktu = false` + belum masuk `tanggal_mulai` | Paket tidak tampil ke user |

---

## Pertimbangan Tambahan

- **Tidak perlu auto-hidden job/scheduler** — visibilitas dihitung real-time saat query, bukan di-flag secara periodik.
- **Backward compatible** — default `false` memastikan semua paket lama tidak berubah perilaku setelah migration.
- **Admin UX**: Di form tambah/edit paket, tampilkan checkbox "Paket tidak terbatas waktu". Jika dicentang, field `tanggal_mulai` dan `tanggal_berakhir` bisa di-disable/disembunyikan di UI.
- **Paket promo vs paket tetap**: Fitur ini secara natural membedakan dua jenis paket — paket promo (berbatas waktu) dan paket reguler (unlimited).

---

## Status

- [ ] Desain & diskusi *(dokumen ini)*
- [ ] Migration: `add_is_unlimited_waktu_to_paket_table`
- [ ] Update Model `Paket` (fillable, casts, scope `aktif()`)
- [ ] Update `PaketController` (validasi + filter endpoint)
- [ ] Update `KursusController` (filter paket aktif saat load kursus)
- [ ] Update Seeder
- [ ] Testing endpoint
- [ ] Update `api-documentation.md`

---

*Dibuat: 15 Mei 2026*
