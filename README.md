# 🇮🇩 BPS AI Assistant v2
### Asisten Informasi Statistik Publik Resmi — Badan Pusat Statistik

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="280" alt="Laravel 13">
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.3%2B-blue.svg" alt="PHP 8.3+">
  <img src="https://img.shields.io/badge/Laravel-13.x-red.svg" alt="Laravel 13">
  <img src="https://img.shields.io/badge/Vue.js-3.x-emerald.svg" alt="Vue 3">
  <img src="https://img.shields.io/badge/BPS%20WebAPI-Live%20Integration-00ADEF.svg" alt="BPS WebAPI">
  <img src="https://img.shields.io/badge/AI%20Provider-LimitRouter-F7941D.svg" alt="LimitRouter">
  <img src="https://img.shields.io/badge/Tests-28%20Passed-brightgreen.svg" alt="Tests Passed">
</p>

---

## 🌟 Ringkasan Produk

**BPS AI Assistant v2** adalah chatbot kecerdasan buatan (AI) publik resmi yang dirancang untuk membantu masyarakat umum, mahasiswa, peneliti, dan pelaku usaha dalam menanyakan konsep statistik, publikasi, data, metodologi, dan layanan Badan Pusat Statistik (BPS) Republik Indonesia.

Chatbot ini dapat dijalankan sebagai **aplikasi web mandiri (standalone)** maupun **di-embed sebagai widget toggle interaktif** pada portal website BPS resmi manapun.

---

## 🚀 Fitur Utama

- 🔒 **Tanpa Login & Bebas Akses:** Siapapun dapat langsung menggunakan tanpa perlu registrasi atau login.
- 🛡️ **Source Trust & Verifikasi:** Setiap jawaban terhubung langsung ke sumber rujukan resmi (BPS WebAPI & basis pengetahuan BPS terverifikasi).
- 🧭 **Dual Knowledge Retrieval:**
  1. **Jalur BPS WebAPI Resmi:** Mengambil data katalog publikasi, domain wilayah, dan indikator strategis secara live.
  2. **Jalur Local Knowledge Base (RAG):** Menggunakan lexical retrieval untuk konsep statistik (Inflasi, Deflasi, PDRB, Sensus, Susenas, Sakernas, dll).
- 🧩 **Multi-State UI Engine:**
  - **Welcome State:** Avatar BPS AI dan grid 2x2 pertanyaan umum.
  - **Answered State:** Markdown terstruktur, penekanan bold, bullet lists, dan kartu sumber.
  - **Clarification State:** Otomatis meminta klarifikasi wilayah atau tahun/periode jika pertanyaan belum spesifik.
  - **No Evidence State:** Aman dan tidak mengarang jika data tidak ditemukan.
  - **Out of Scope State:** Ramah mengarahkan pengguna kembali ke domain statistik/BPS.
  - **Provider Error & Rate Limit State:** Penanganan error graceful tanpa membocorkan credential internal.
- 📱 **Pixel-Perfect Responsive UI:** Desain sliced langsung dari desain resmi BPS (Desktop, Tablet, Mobile 360px).
- 🔌 **Embeddable Widget:** Script `embed.js` ringan untuk menyematkan toggle "💬 Tanya BPS" di website BPS eksternal.

---

## 🏗️ Arsitektur Sistem

```
┌────────────────────────────────────────────────────────┐
│                   Browser Client                       │
│  (Vue 3 App / Sliced UI / Embedded Floating Widget)    │
└───────────────────────────┬────────────────────────────┘
                            │ POST /api/chat
                            ▼
┌────────────────────────────────────────────────────────┐
│            Laravel 13 Internal API Layer               │
│  - Input Validation & Payload Size Limit               │
│  - Rate Limiter (15 req/min/IP)                        │
│  - ScopeGuard (Heuristic Layer + Injection Filter)     │
└──────────────┬──────────────────────────┬──────────────┘
               │                          │
   [In-Scope / BPS Intent]    [Fallback / Definition]
               │                          │
               ▼                          ▼
┌──────────────────────────┐  ┌──────────────────────────┐
│        BpsAgent          │  │   DemoLexicalRetriever   │
│  (Live BPS WebAPI)       │  │ (data/knowledge/*.md)    │
│  - 24h SQLite Cache      │  │ - Tokenizer & Scoring    │
│  - Credential Redactor   │  │ - Threshold Check        │
└──────────────┬───────────┘  └───────────┬──────────────┘
               │                          │
               └───────────┬──────────────┘
                           │ Grounded Evidence + PromptBuilder
                           ▼
┌────────────────────────────────────────────────────────┐
│               LimitRouterProvider Adapter              │
│       (Server-Side AI Gateway -> LimitRouter)          │
│                Model: gemini-3.7-flash                 │
└────────────────────────────────────────────────────────┘
```

> **Security Rule:** API key LimitRouter dan BPS WebAPI sepenuhnya berada di sisi server (*server-side only*). Browser tidak pernah memanggil endpoint AI eksternal secara langsung.

---

## ⚡ Quick Start (Instalasi Lokal)

### 1. Clone Repository & Setup
```bash
git clone https://github.com/Gimm17/bps-chatbot-ai-v2.git
cd bps-chatbot-ai-v2

# Install dependensi PHP
composer install

# Install dependensi Frontend
npm install
```

### 2. Environment Configuration
Salin `.env.example` ke `.env`:
```bash
cp .env.example .env
php artisan key:generate
```

Isi konfigurasi di `.env`:
```dotenv
LIMITROUTER_API_KEY=sk-lr-...
LIMITROUTER_DEFAULT_MODEL=gemini-3.7-flash
BPS_ENABLED=true
BPS_WEBAPI_KEY=32a4af778c0b74a62c19857b278cab33
```

### 3. Database Migration
```bash
touch database/database.sqlite
php artisan migrate
```

### 4. Build Frontend & Jalankan Server
```bash
npm run build
php artisan serve
```

Buka `http://localhost:8000` pada browser Anda.

---

## 🧪 Pengujian & Verifikasi

Jalankan test suite otomatis dengan PHPUnit:
```bash
php artisan test
```

Semua 28 unit dan feature tests terverifikasi PASS (74 assertions):
- ✅ `ScopeGuardTest`: Klasifikasi in-scope, out-of-scope, salam, dan deteksi prompt injection.
- ✅ `ChatResultTest`: Parsing JSON dari model dan recursive code-fence cleaning.
- ✅ `CitationTest`: Pembuatan kartu rujukan terverifikasi dan unverified.
- ✅ `DemoLexicalRetrieverTest`: Pencarian relevan konsep inflasi, PDRB, dan thresholding.
- ✅ `ApiChatTest`: Endpoint POST `/api/chat`, validasi input, dan mock provider.
- ✅ `SecurityTest`: Pemeriksaan tidak ada kebocoran API key pada response atau bundle client.

---

## 📦 Cara Deploy di cPanel Shared Hosting

Aplikasi ini sudah dioptimalkan untuk berjalan di cPanel shared hosting (seperti `bps-chatbot-v2.pinnhost.my.id`):

1. **Root `.htaccess`:** Sudah menyertakan aturan rewrite otomatis ke `public/` dan memblokir akses file sensitif (`.env`, `composer.json`, `artisan`).
2. **Deploy Script:** Gunakan script otomatis di `deploy/deploy.sh` atau via SSH helper:
   ```bash
   bash deploy/deploy.sh
   ```

---

## 🔌 Cara Menggunakan Widget di Website BPS Resmi

Cukup sematkan satu baris script berikut sebelum tag `</body>` pada halaman web BPS:

```html
<script src="https://bps-chatbot-v2.pinnhost.my.id/build/assets/embed.js" defer></script>
```

Tombol toggle "💬 Tanya BPS" akan muncul otomatis di sudut kanan bawah.

---

## 📄 Lisensi

Dikembangkan untuk Badan Pusat Statistik Republik Indonesia.
