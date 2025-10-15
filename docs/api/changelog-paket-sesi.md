# Changelog - Penambahan Paket ID pada Tabel Sesi

**Branch:** `tambah-paket_id-pada-tabel-sesi`  
**Merged to:** `master`  
**Date:** October 15, 2025

---

## 📋 Ringkasan Perubahan

Merge ini menambahkan fitur **sistem paket pembelajaran** yang terintegrasi dengan pemesanan sesi. Pelanggan kini dapat memilih paket (bundle items seperti modul PDF, video tutorial, dll) saat memesan sesi dengan mentor.

---

## 🎯 Tujuan Fitur

1. **Meningkatkan nilai jual**: Pelanggan dapat membeli paket pembelajaran tambahan
2. **Fleksibilitas harga**: Sistem otomatis menghitung total biaya (paket + sesi mentor)
3. **Tracking paket**: Setiap sesi tercatat menggunakan paket apa (atau tanpa paket)
4. **Pengelolaan item**: Admin dapat mengatur item paket dan harga akan auto-update

---

## 🔧 File yang Diubah

### 1. **app/Http/Controllers/Fitur/SesiController.php**
**Perubahan:**
- ✅ Menambahkan relasi `paket` pada query index & show
- ✅ Response API sekarang include data paket lengkap

**Contoh Response:**
```json
{
  "id": 1,
  "paket_id": 2,
  "jumlahSementara": 150000,
  "paket": {
    "id": 2,
    "nama": "Paket Premium",
    "harga_dasar": 100000,
    "diskon": 10000,
    "items": [...]
  }
}
```

---

### 2. **app/Http/Controllers/Fitur/ItemPaketController.php**
**Perubahan Besar:**
- ✅ Auto-rekalkulasi harga paket saat item diupdate
- ✅ Auto-rekalkulasi harga paket saat item dihapus
- ✅ Method `recalculateHargaDasarForPaket()` untuk kalkulasi otomatis

**Formula Perhitungan:**
```php
harga_dasar = sum(max(item.harga - item.diskon, 0) * jumlah_item)
```

**Contoh Kasus:**
- Admin update harga "Modul PDF" dari Rp 20.000 → Rp 25.000
- Sistem otomatis update `harga_dasar` semua paket yang punya "Modul PDF"

---

### 3. **app/Http/Controllers/Fitur/KursusController.php**
**Perubahan:**
- ✅ Setiap kursus wajib punya minimal 1 paket
- ✅ Admin bisa atur paket mana yang visible per kursus
- ✅ Load relasi `visibilitasPaket.paket.items` di semua endpoint
- ✅ Support FormData untuk field `visibilitas_paket`

**Request Create/Update Kursus:**
```json
{
  "namaKursus": "Kalkulus Dasar",
  "mentor_id": 1,
  "paket_ids": [1, 2, 3],
  "visibilitas_paket": [
    {"paket_id": 1, "visibilitas": true},
    {"paket_id": 2, "visibilitas": false}
  ]
}
```

---

### 4. **app/Http/Controllers/Pelanggan/PelangganController.php**
**Perubahan:**
- ✅ `pesanSesi()`: Support parameter `paket_id` (nullable/optional)
- ✅ `pesanSesi()`: Kalkulasi otomatis `jumlahSementara`
- ✅ `daftarSesiMentor()`: Include relasi `paket.items`
- ✅ `daftarSesiTransaksi()`: Include relasi `paket.items`

**Logic Perhitungan Biaya:**
```php
if (paket_id ada) {
    // Hitung harga paket dari items
    biayaPaket = sum(item.harga - item.diskon)
    biayaPaket = max(biayaPaket - paket.diskon, 0)
    jumlahSementara = biayaPaket + mentor.biayaPerSesi
} else {
    jumlahSementara = mentor.biayaPerSesi
}
```

**Contoh:**
- Mentor: biayaPerSesi = Rp 50.000
- Paket Premium: harga_dasar = Rp 100.000, diskon = Rp 10.000
- **Total:** Rp 140.000 (90.000 + 50.000)

---

### 5. **routes/api.php**
**Perubahan:**
- ✅ Semua route sudah compatible dengan fitur paket

---

## 🗄️ Database Changes

### Tabel `sesi`
**Kolom Baru:**
```sql
paket_id (bigint unsigned, nullable, foreign key -> paket.id)
```

**Relasi:**
- `sesi.paket_id` → `paket.id` (belongsTo)

---

## 📊 Model Relationships

### Sesi Model
```php
protected $fillable = [
    'mentor_id',
    'pelanggan_id',
    'kursus_id',
    'jadwal_kursus_id',
    'detailKursus',
    'jumlahSementara',
    'paket_id',        // ← NEW
    'statusSesi',
];

public function paket()
{
    return $this->belongsTo(Paket::class, 'paket_id');
}
```

### Paket Model (existing)
```php
public function items()
{
    return $this->belongsToMany(ItemPaket::class, 'relasi_item_paket')
        ->withPivot('jumlah_item');
}
```

---

## 💼 Use Cases

### Use Case 1: Pelanggan Pesan Sesi **TANPA** Paket
```json
POST /api/pelanggan/pesan-sesi
{
  "mentor_id": 1,
  "pelanggan_id": 2,
  "kursus_id": 3,
  "jadwal_kursus_id": 5,
  "paket_id": null,
  "statusSesi": "pending"
}
```
**Hasil:** 
- `jumlahSementara` = Rp 50.000 (hanya biaya mentor)
- `paket_id` = null

---

### Use Case 2: Pelanggan Pesan Sesi **DENGAN** Paket
```json
POST /api/pelanggan/pesan-sesi
{
  "mentor_id": 1,
  "pelanggan_id": 2,
  "kursus_id": 3,
  "jadwal_kursus_id": 5,
  "paket_id": 2,
  "statusSesi": "pending"
}
```
**Hasil:**
- Paket Premium: harga_dasar Rp 100.000, diskon Rp 10.000
- Items: 2x Modul PDF (Rp 20.000 - Rp 2.000) + 1x Video (Rp 50.000 - Rp 5.000)
- `jumlahSementara` = Rp 140.000 (paket Rp 90.000 + mentor Rp 50.000)
- Pelanggan dapat: Modul PDF (2 buah) + Video Tutorial (1 buah) + Sesi dengan mentor

---

### Use Case 3: Admin Update Harga Item
**Skenario:**
- Item "Modul PDF" harga Rp 20.000 → Rp 25.000
- Paket Basic punya 2x Modul PDF
- Paket Premium punya 3x Modul PDF

**Yang Terjadi:**
1. Admin update item via `PUT /api/item-paket/1`
2. Sistem deteksi harga berubah → trigger `recalculateHargaDasarForPaket()`
3. Paket Basic: `harga_dasar` update otomatis
4. Paket Premium: `harga_dasar` update otomatis

---

### Use Case 4: Admin Atur Visibilitas Paket
**Skenario:**
- Kursus "Kalkulus Dasar" punya 3 paket: Basic, Standard, Premium
- Admin ingin hide paket "Standard" sementara

**Request:**
```json
PUT /api/kursus/1
{
  "visibilitas_paket": [
    {"paket_id": 1, "visibilitas": true},
    {"paket_id": 2, "visibilitas": false},  // ← hidden
    {"paket_id": 3, "visibilitas": true}
  ]
}
```

**Hasil:**
- Pelanggan hanya lihat Paket Basic & Premium
- Paket Standard tetap ada di database, tapi tidak ditampilkan

---

## 🎨 Business Logic

### 1. Perhitungan Harga Paket
```
STEP 1: Hitung total harga items
= sum((item.harga - item.diskon) * jumlah_item)

STEP 2: Kurangi diskon paket
= harga_items - paket.diskon

STEP 3: Tambahkan biaya mentor
= harga_paket + mentor.biayaPerSesi
```

### 2. Auto-Recalculation
- ✅ Update harga item → recalculate semua paket terkait
- ✅ Update diskon item → recalculate semua paket terkait
- ✅ Delete item (soft delete) → recalculate semua paket terkait
- ✅ Item yang soft-deleted **tidak** dihitung dalam kalkulasi

### 3. Validasi Kursus
- ✅ Setiap kursus wajib punya minimal 1 paket aktif
- ✅ Error jika `paket_ids` kosong saat create kursus

---

## 📈 Benefits

### Untuk Pelanggan:
- 🎁 Dapat bonus items (modul, video) saat beli paket
- 💰 Lebih hemat dengan paket bundle
- 📦 Flexibilitas pilih paket atau tidak

### Untuk Admin:
- 🔧 Update harga item sekali, semua paket auto-update
- 👁️ Kontrol paket mana yang visible per kursus
- 📊 Tracking pembelian paket per sesi

### Untuk Mentor:
- 💼 Biaya sesi tetap + bonus dari paket
- 📚 Dapat include materials dalam paket

---

## 🧪 Testing Checklist

- [ ] Pesan sesi tanpa paket → `jumlahSementara` = biaya mentor only
- [ ] Pesan sesi dengan paket → `jumlahSementara` = paket + mentor
- [ ] Update harga item → harga paket auto-update
- [ ] Delete item → harga paket auto-update (exclude item deleted)
- [ ] Create kursus tanpa paket → error validation
- [ ] Update visibilitas paket → paket hidden/show correctly
- [ ] Response API include relasi `paket.items`

---

## 📝 Migration Required

**Database Migration:**
```sql
-- Tambah kolom paket_id di tabel sesi
ALTER TABLE sesi 
ADD COLUMN paket_id BIGINT UNSIGNED NULL,
ADD FOREIGN KEY (paket_id) REFERENCES paket(id) ON DELETE SET NULL;
```

**Note:** Migration sudah ada di folder `database/migrations/`

---

## 🚀 Deployment Steps

1. Pull branch `master` terbaru
2. Run migration: `php artisan migrate`
3. Clear cache: `php artisan cache:clear`
4. Test API endpoints dengan data paket

---

## 📞 Contact

Jika ada pertanyaan tentang fitur ini, hubungi:
- Developer: SizzlingChilli Team
- Repository: manpro-sizzlingchilli-backend-chill-ajar

---

**End of Changelog**
