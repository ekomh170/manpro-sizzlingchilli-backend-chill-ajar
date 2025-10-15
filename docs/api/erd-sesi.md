# ERD & Mekanisme Field jumlahSementara pada Sesi

## Struktur Tabel SESI

Tabel `sesi` menyimpan data booking sesi pengajaran antara pelanggan dan mentor. Field penting:

- `mentor_id` (FK): Mentor yang dipilih
- `pelanggan_id` (FK): Pelanggan yang booking
- `kursus_id` (FK): Kursus yang diikuti
- `jadwal_kursus_id` (FK): Jadwal kursus
- `detailKursus`: Catatan/detail tambahan
- `jumlahSementara`: Estimasi harga booking, diisi saat user memilih paket atau hanya mentor
- `statusSesi`: Status sesi (pending, started, end, reviewed)

## Mekanisme jumlahSementara

- Jika user memilih paket, maka:
  - `jumlahSementara = harga paket (setelah diskon) + biayaPerSesi mentor`
- Jika user tidak memilih paket:
  - `jumlahSementara = biayaPerSesi mentor`

Field ini digunakan untuk menampilkan estimasi harga kepada user sebelum pembayaran final dilakukan.

Field `jumlahSementara` tidak berelasi langsung ke paket, hanya menyimpan nilai estimasi saat booking.

Jika ingin histori detail paket, tambahkan field `paket_id` (opsional, sesuai kebutuhan).
