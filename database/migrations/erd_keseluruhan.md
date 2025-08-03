# ERD Keseluruhan Sistem

Berikut adalah ERD keseluruhan (utama) dalam format Mermaid agar mudah dibaca di markdown:

```mermaid
erDiagram
    USER {
        int id PK
        string nama
        string email
        string password
        string nomorTelepon
        string peran
        string alamat
        string foto_profil
        timestamp created_at
        timestamp updated_at
    }
    ADMIN {
        int id PK
        int user_id FK
        timestamp created_at
        timestamp updated_at
    }
    MENTOR {
        int id PK
        int user_id FK
        float rating
        float biayaPerSesi
        text deskripsi
        timestamp created_at
        timestamp updated_at
    }
    PELANGGAN {
        int id PK
        int user_id FK
        timestamp created_at
        timestamp updated_at
    }
    KURSUS {
        int id PK
        string namaKursus
        text deskripsi
        int mentor_id FK
        string fotoKursus
        timestamp created_at
        timestamp updated_at
    }
    JADWAL_KURSUS {
        int id PK
        int kursus_id FK
        date tanggal
        time waktu
        string gayaMengajar
        string keterangan
        string tempat
        timestamp created_at
        timestamp updated_at
    }
    SESI {
        int id PK
        int mentor_id FK
        int pelanggan_id FK
        int kursus_id FK
        int jadwal_kursus_id FK
        string detailKursus
        string statusSesi
        timestamp created_at
        timestamp updated_at
    }
    TRANSAKSI {
        int id PK
        int pelanggan_id FK
        int mentor_id FK
        int sesi_id FK
        float jumlah
        string statusPembayaran
        string metodePembayaran
        datetime tanggalPembayaran
        string buktiPembayaran
        timestamp created_at
        timestamp updated_at
    }
    TESTIMONI {
        int id PK
        int sesi_id FK
        int pelanggan_id FK
        int mentor_id FK
        tinyint rating
        string komentar
        date tanggal
        timestamp created_at
        timestamp updated_at
    }
    PAKET {
        int id PK
        string nama
        int harga_dasar
        int diskon
        text deskripsi
        date tanggal_mulai
        date tanggal_berakhir
        int max_pembelian_per_user
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at
    }
    ITEM_PAKET {
        int id PK
        string nama
        int harga
        int diskon
        text deskripsi
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at
    }
    RELASI_ITEM_PAKET {
        int id PK
        int paket_id FK
        int item_paket_id FK
        int jumlah_item
        timestamp created_at
        timestamp updated_at
    }
    VISIBILITAS_PAKET {
        int id PK
        int kursus_id FK
        int paket_id FK
        boolean visibilitas
        timestamp created_at
        timestamp updated_at
    }

    USER ||--o{ ADMIN : "punya"
    USER ||--o{ MENTOR : "punya"
    USER ||--o{ PELANGGAN : "punya"
    MENTOR ||--o{ KURSUS : "mengajar"
    KURSUS ||--o{ JADWAL_KURSUS : "memiliki"
    KURSUS ||--o{ SESI : "memiliki"
    JADWAL_KURSUS ||--o{ SESI : "jadwal"
    MENTOR ||--o{ SESI : "membimbing"
    PELANGGAN ||--o{ SESI : "mengikuti"
    SESI ||--o{ TRANSAKSI : "transaksi"
    SESI ||--o{ TESTIMONI : "testimoni"
    PELANGGAN ||--o{ TRANSAKSI : "melakukan"
    MENTOR ||--o{ TRANSAKSI : "menerima"
    TESTIMONI ||--o{ MENTOR : "untuk"
    TESTIMONI ||--o{ PELANGGAN : "oleh"
    PAKET ||--o{ RELASI_ITEM_PAKET : "memiliki"
    ITEM_PAKET ||--o{ RELASI_ITEM_PAKET : "terkait"
    PAKET ||--o{ VISIBILITAS_PAKET : "terlihat di"
    KURSUS ||--o{ VISIBILITAS_PAKET : "memiliki"
```

---

> Diagram ini bisa di-render otomatis di VS Code atau GitHub jika ekstensi Mermaid diaktifkan. Relasi dan field utama sudah disesuaikan dengan skema migrasi yang ada.
