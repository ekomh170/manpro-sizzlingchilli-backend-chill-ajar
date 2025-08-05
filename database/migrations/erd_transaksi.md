# Mekanisme Perhitungan Total Transaksi Paket

## Alur Perhitungan

1. **User memilih paket**
- Paket terdiri dari beberapa item (opsional) dan memiliki harga_dasar (harga utama paket, bisa berbeda dari total seluruh item jika ada diskon atau custom harga).
2. **User memilih mentor**
   - Setiap mentor memiliki tarif/harga per sesi (misal: kolom `biayaPerSesi` di tabel mentor).
3. **Total transaksi**
   - Total yang harus dibayar user adalah:

     **total_transaksi = harga_dasar_paket + biaya_mentor**

   - Contoh:
     - User pilih Paket NgeTask & Chill (harga_dasar = 10.000) dan mentor A (biayaPerSesi = 25.000)
     - Total transaksi: 10.000 + 25.000 = 35.000

## Update Table Transaksi

- Pada saat transaksi dibuat, simpan:
  - `paket_id` (opsional, jika transaksi berbasis paket)
  - `mentor_id`
  - `pelanggan_id`
  - `jumlah` (total_transaksi)
  - `statusPembayaran`, `metodePembayaran`, dll

- Contoh kode update (di controller/service):

```php
$paket = Paket::find($paket_id);
$mentor = Mentor::find($mentor_id);
$total = $paket->harga_dasar + $mentor->biayaPerSesi;

Transaksi::create([
    'pelanggan_id' => $user_id,
    'mentor_id' => $mentor_id,
    'paket_id' => $paket_id, // jika ada
    'jumlah' => $total,
    'statusPembayaran' => 'pending',
    // ...field lain
]);
```

> Pastikan field `paket_id` sudah ada di tabel transaksi jika ingin menyimpan referensi ke paket.

---

- Jika ada diskon, promo, atau biaya tambahan, tambahkan ke perhitungan sebelum menyimpan transaksi.
- Dokumentasikan logika ini di backend agar tim lain paham mekanisme total transaksi.
