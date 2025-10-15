# Catatan Pengembangan Fitur Paket

## Skema Tabel

### 1. Tabel `paket`
```sql
CREATE TABLE paket (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    harga_dasar INT NOT NULL DEFAULT 0,
    deskripsi TEXT,
    tanggal_mulai DATE NULL, -- NULL artinya unlimited (tidak dibatasi)
    tanggal_berakhir DATE NULL, -- NULL artinya unlimited (tidak dibatasi)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    max_pembelian_per_user INT NULL, -- NULL artinya unlimited, jika diisi membatasi pembelian per user
    deleted_at TIMESTAMP NULL -- soft delete
);
```

### 2. Tabel `item_paket`
```sql
CREATE TABLE item_paket (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    harga INT NOT NULL,
    diskon INT DEFAULT 0, -- Diskon khusus item ini
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL -- soft delete
);
```

### 3. Tabel `relasi_item_paket`
```sql
CREATE TABLE relasi_item_paket (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paket_id INT NOT NULL,
    item_paket_id INT NOT NULL,
    jumlah_item INT NOT NULL DEFAULT 1, -- jumlah item dalam satu paket
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (paket_id) REFERENCES paket(id),
    FOREIGN KEY (item_paket_id) REFERENCES item_paket(id)
);
```

### 4. Tabel `visibilitas_paket`
```sql
CREATE TABLE visibilitas_paket (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kursus_id INT NOT NULL,
    paket_id INT NOT NULL,
    visibilitas BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (kursus_id) REFERENCES kursus(id),
    FOREIGN KEY (paket_id) REFERENCES paket(id)
);
```

---

## Alasan & Catatan Desain
- Kolom `deskripsi` pada `item_paket` dan `paket` untuk fleksibilitas penjelasan.
- Kolom `tanggal_mulai` dan `tanggal_berakhir` pada `paket` untuk mengatur masa aktif/promo paket. Jika NULL, maka paket berlaku unlimited.
- Tabel relasi memungkinkan satu paket terdiri dari banyak item, dan satu item bisa masuk ke banyak paket. Kolom `jumlah` memungkinkan satu paket berisi lebih dari satu item yang sama (misal: 2x sesi, 3x modul, dsb).
- Tabel visibilitas mengatur paket mana yang muncul di kursus tertentu.
- Soft delete (`deleted_at`) hanya diterapkan pada tabel utama (`paket` dan `item_paket`) agar data bisa di-nonaktifkan tanpa dihapus permanen.

- Jika ada limitasi penggunaan paket (misal: hanya bisa dibeli 1x per user), bisa diterapkan logika di backend atau menambah kolom pendukung seperti `max_pembelian_per_user` di tabel `paket`.

## Rencana Pengembangan
1. **Buat migration Laravel** untuk keempat tabel di atas.
2. **Seeder**: Siapkan data dummy untuk pengujian fitur.
3. **Integrasi ke Backend**:
   - Endpoint CRUD untuk paket & item_paket.
   - Endpoint untuk mengelola relasi dan visibilitas.
4. **Testing**: Unit test dan integrasi.
5. **Dokumentasi**: Update README & API docs.

## To-Do
- [ ] Migration Laravel
- [ ] Seeder data dummy
- [ ] CRUD Paket & Item Paket
- [ ] Manajemen relasi & visibilitas
- [ ] Testing & dokumentasi

---

*Update: 2 Agustus 2025*
