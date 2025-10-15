classDiagram
    direction TB
    class User {
        +int id
        +String nama
        +String email
        +String password
        +String nomorTelepon
        +String peran
        +String alamat
        +String fotoProfil
        +datetime created_at
        +login()
        +logout()
        +perbaruiProfil()
        +updateFotoProfil()
    }
    class Admin {
        +int id
        +int user_id
        +kelolaPengguna()
        +kelolaMentor()
        +kelolaKursus()
        +kelolaSesi()
        +verifikasiTransaksi()
    }
    class Mentor {
        +int id
        +int user_id
        +float rating
        +float biayaPerSesi
        +String deskripsi
        +aturJadwal()
        +konfirmasiSesi()
        +terimaTestimoni()
    }
    class Pelanggan {
        +int id
        +int user_id
        +cariMentor()
        +pesanSesi()
        +bayarSesi()
        +beriTestimoni()
    }
    class Kursus {
        +int id
        +String namaKursus
        +String deskripsi
        +int mentor_id
        +String gayaMengajar
        +String fotoKursus
        +tambahKursus()
        +hapusKursus()
        +daftarKursus()
        +aturGayaMengajar()
        +uploadFotoKursus()
    }
    class JadwalKursus {
        +int id
        +int kursus_id
        +date tanggal
        +time waktu
        +String keterangan
        +String tempat
        +tambahJadwal()
        +ubahJadwal()
        +hapusJadwal()
        +daftarJadwal()
    }
    class Sesi {
        +int id
        +int mentor_id
        +int pelanggan_id
        +int kursus_id
        +int jadwal_kursus_id
        +String detailKursus
        +String statusSesi
        +jadwalkanSesi()
        +konfirmasiSesi()
        +selesaikanSesi()
    }
    class Transaksi {
        +int id
        +int pelanggan_id
        +int mentor_id
        +int sesi_id
        +float jumlah
        +String statusPembayaran
        +String metodePembayaran
        +datetime tanggalPembayaran
        +String buktiPembayaran
        +prosesPembayaran()
        +verifikasiPembayaran()
        +kirimTandaTerima()
    }
    class Testimoni {
        +int id
        +int sesi_id
        +int pelanggan_id
        +int mentor_id
        +int rating
        +String komentar
        +datetime tanggal
        +kirimTestimoni()
        +lihatTestimoni()
    }
    User <|-- Admin
    User <|-- Mentor
    User <|-- Pelanggan
    Mentor "1" -- "*" Kursus : mengajar
    Kursus "1" -- "*" JadwalKursus : punyaJadwal
    JadwalKursus "1" -- "*" Sesi : dipilih
    Mentor "1" -- "*" Sesi : mengajar
    Pelanggan "1" -- "*" Sesi : mengikuti
    Sesi "1" -- "1" Transaksi : pembayaran
    Sesi "1" -- "1" Testimoni : testimoni
