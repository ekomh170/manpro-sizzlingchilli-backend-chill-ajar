# 🚀 Dokumentasi Deployment

Folder ini berisi panduan lengkap untuk deployment aplikasi ChillAjar ke berbagai platform cloud.

## 📄 Panduan Deployment

### Google Cloud Platform (GCP)
- **[setup-server-ubuntu-gcp.md](./setup-server-ubuntu-gcp.md)** - Setup server Ubuntu di GCP untuk Laravel
- **[setup-whatsapp-gateway-gcp.md](./setup-whatsapp-gateway-gcp.md)** - Setup WhatsApp Gateway di GCP (khusus)
- **[backup-gcp-full.md](./backup-gcp-full.md)** - Panduan backup lengkap database & file di GCP

### Amazon Web Services (AWS)
- **[setup-aws.md](./setup-aws.md)** - Setup aplikasi Laravel di AWS
- **[setup-aws-nginx.md](./setup-aws-nginx.md)** - Konfigurasi Nginx di AWS

### WhatsApp Gateway (Express.js)
- **[setup-whatsapp-gateway-express.md](./setup-whatsapp-gateway-express.md)** - Setup Express.js server untuk WhatsApp Web.js

## 🏗️ Arsitektur Deployment

```
┌─────────────────┐         ┌──────────────────┐
│  Laravel API    │ ─HTTP─> │  Express.js      │
│  (GCP/AWS)      │         │  WhatsApp Gateway│
│  Port: 8000     │         │  Port: 3000      │
└─────────────────┘         └──────────────────┘
         │                           │
         │                           │
         ▼                           ▼
   PostgreSQL DB            WhatsApp Web.js API
```

## ⚙️ Langkah Deployment

1. **Persiapan Server**: Ikuti panduan setup-server-ubuntu-gcp.md atau setup-aws.md
2. **Deploy Laravel**: Setup Laravel dengan Nginx/Apache
3. **Setup Database**: Konfigurasi PostgreSQL
4. **Deploy WhatsApp Gateway**: Setup Express.js server (opsional)
5. **Backup**: Implementasi strategi backup menggunakan backup-gcp-full.md

## 🔧 Tools yang Digunakan

- **Laravel 12**: Backend API
- **PostgreSQL**: Database
- **Nginx**: Web Server
- **Express.js**: WhatsApp Gateway
- **PM2**: Process Manager untuk Node.js
- **Certbot**: SSL Certificate (Let's Encrypt)

## 🔗 Link Terkait

- [Kembali ke Dokumentasi Utama](../../README.md)
- [API Documentation](../api/)
- [Infrastructure Analysis](../infrastructure/)
