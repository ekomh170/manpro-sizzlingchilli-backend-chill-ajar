# ERD Fitur Paket

Berikut adalah Entity Relationship Diagram (ERD) dalam format teks untuk fitur paket:

```mermaid
erDiagram
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
    KURSUS {
        int id PK
        string namaKursus
        text deskripsi
        int mentor_id FK
        string fotoKursus
        timestamp created_at
        timestamp updated_at
    }

    PAKET ||--o{ RELASI_ITEM_PAKET : "memiliki"
    ITEM_PAKET ||--o{ RELASI_ITEM_PAKET : "terkait"
    PAKET ||--o{ VISIBILITAS_PAKET : "terlihat di"
    KURSUS ||--o{ VISIBILITAS_PAKET : "memiliki"
```

---

- **PAKET**: Data utama paket.
- **ITEM_PAKET**: Item yang bisa masuk ke dalam paket.
- **RELASI_ITEM_PAKET**: Relasi many-to-many antara paket dan item_paket, dengan atribut tambahan.
- **VISIBILITAS_PAKET**: Relasi paket dengan kursus, mengatur visibilitas paket di kursus tertentu.
- **KURSUS**: Tabel kursus (hanya ditampilkan relasinya).

> Diagram ini bisa di-render otomatis di VS Code atau GitHub jika ekstensi Mermaid diaktifkan.
