# 📘 SOP Panduan Deployment Otomatis via SSH (Untuk AI Agent & Developer)

Dokumen ini adalah **Standard Operating Procedure (SOP)** langkah demi langkah yang digunakan untuk mengakses SSH cPanel dan mendeploy aplikasi **BPS AI Assistant v2** (Laravel 13 + Vue 3) secara otomatis (*non-interactive agentic mode*).

---

## 🔑 1. Parameter Kredensial & Server

| Parameter | Nilai Konfigurasi |
|---|---|
| **Host SSH** | `pinnhost.my.id` (Port: **`6699`**) |
| **Username** | `pinnhost` |
| **Password** | Dilihat dari file `deploy/pwsupersecretcpanel.txt` (`EzA%=72,@3D%`) |
| **Hostkey Fingerprint** | `SHA256:+tDxqS2SHavNhLM6I7PFuKYmrmbKLkl+aSdcpenmdxg` |
| **Direktori Target** | `/home/pinnhost/bps-chatbot-v2.pinnhost.my.id/` |
| **Domain Web Produksi** | `https://bps-chatbot-v2.pinnhost.my.id` |
| **Git Repository** | `https://github.com/Gimm17/bps-chatbot-ai-v2.git` (branch `main`) |

---

## 🛠️ 2. Tooling Eksekusi SSH di Windows (Non-Interactive Mode)

Karena di Windows CLI `ssh.exe` sering meminta prompt password interaktif (yang dapat memblokir proses AI Agent), kita menggunakan **PuTTY CLI Tools** yang sudah tersedia di sistem:

### A. Eksekusi Perintah Remote (`plink.exe`):
```powershell
& "C:\Program Files\PuTTY\plink.exe" -P 6699 -batch -hostkey "SHA256:+tDxqS2SHavNhLM6I7PFuKYmrmbKLkl+aSdcpenmdxg" -pw "EzA%=72,@3D%" pinnhost@pinnhost.my.id "<PERINTAH_LINUX>"
```

### B. Upload File / Folder Remote (`pscp.exe`):
```powershell
& "C:\Program Files\PuTTY\pscp.exe" -P 6699 -batch -hostkey "SHA256:+tDxqS2SHavNhLM6I7PFuKYmrmbKLkl+aSdcpenmdxg" -pw "EzA%=72,@3D%" -r <LOCAL_PATH> pinnhost@pinnhost.my.id:<REMOTE_PATH>
```

> **Catatan Kunci untuk Agent:** Parameter `-batch` dan `-hostkey` WAJIB disertakan agar koneksi SSH tidak tertahan oleh konfirmasi *Trust Host Key* dari server.

---

## 🚀 3. Step-by-Step Inisialisasi & Deployment Awal (Initial Deploy)

Ikuti urutan langkah berikut saat melakukan setup pertama kali pada direktori baru:

### Langkah 1: Clone / Pull Source Code dari GitHub
Eksekusi di terminal server:
```bash
cd /home/pinnhost/bps-chatbot-v2.pinnhost.my.id
git init
git remote add origin https://github.com/Gimm17/bps-chatbot-ai-v2.git
git fetch origin main
git checkout -f main
```

---

### Langkah 2: Build Asset Frontend Secara Lokal & Upload ke Server
> **Penting:** cPanel shared hosting umumnya tidak memiliki `node` / `npm` pada PATH CLI default. Oleh karena itu, build frontend selalu dilakukan di lokal lalu di-upload:

1. **Di mesin lokal:**
   ```powershell
   npm run build
   ```
2. **Upload folder `public/build` via `pscp.exe`:**
   ```powershell
   & "C:\Program Files\PuTTY\pscp.exe" -P 6699 -batch -hostkey "SHA256:+tDxqS2SHavNhLM6I7PFuKYmrmbKLkl+aSdcpenmdxg" -pw "EzA%=72,@3D%" -r public/build pinnhost@pinnhost.my.id:/home/pinnhost/bps-chatbot-v2.pinnhost.my.id/public/
   ```

---

### Langkah 3: Setup File `.env` Produksi di Server
Buat file `.env` di `/home/pinnhost/bps-chatbot-v2.pinnhost.my.id/.env`:

```dotenv
APP_NAME="BPS AI Assistant"
APP_ENV=production
APP_KEY=base64:5b+cR0wUuhDqE6cO1T0N1sY1882Y8bQdYfQz099m6xM=
APP_DEBUG=false
APP_URL=https://bps-chatbot-v2.pinnhost.my.id

APP_LOCALE=id
APP_FALLBACK_LOCALE=id
APP_FAKER_LOCALE=id_ID

LOG_CHANNEL=stack
LOG_LEVEL=info

DB_CONNECTION=sqlite
DB_DATABASE=/home/pinnhost/bps-chatbot-v2.pinnhost.my.id/database/database.sqlite

SESSION_DRIVER=database
SESSION_LIFETIME=120

QUEUE_CONNECTION=database
CACHE_STORE=database
CACHE_PREFIX=bps_cache_

# BPS WebAPI Integration
BPS_ENABLED=true
BPS_WEBAPI_KEY=32a4af778c0b74a62c19857b278cab33
BPS_WEBAPI_BASE_URL=https://webapi.bps.go.id
BPS_HTTP_TIMEOUT_SEC=15
BPS_CACHE_ENABLED=true
BPS_CACHE_TTL_HOURS=24

# LimitRouter AI Provider Configuration
AI_DEFAULT_PROVIDER=limitrouter
LIMITROUTER_API_KEY=sk-lr-88443757ed5b6434f5825179cd53fb96eae5b28256716272
LIMITROUTER_BASE_URL=https://limitrouter.com/v1
LIMITROUTER_DEFAULT_MODEL=gemini-3.7-flash
AI_DEMO_MODE=true
AI_TIMEOUT=30

VITE_APP_NAME="${APP_NAME}"
```

---

### Langkah 4: Install Dependensi PHP (Production)
```bash
composer install --no-dev --optimize-autoloader --no-interaction
```

---

### Langkah 5: Setup Database SQLite & Eksekusi Migrasi
```bash
touch database/database.sqlite
chmod 664 database/database.sqlite
php artisan key:generate --force
php artisan migrate --force
```

---

### Langkah 6: Optimasi Cache Laravel
```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

### Langkah 7: Konfigurasi Izin Direktori Storage
```bash
chmod -R 775 storage bootstrap/cache
```

---

### Langkah 8: Pre-Indexing Publikasi Strategis (RAG)
```bash
php artisan bps:index-publications --domain=7200 --keyword=kependudukan --limit=2
```

---

### Langkah 9: Verifikasi Live Endpoint & Testing
Jalankan pengujian REST API:
```powershell
# 1. Health Check
Invoke-RestMethod -Uri "https://bps-chatbot-v2.pinnhost.my.id/api/health"

# 2. Chat Query Test
$body = @{ message = "Apa itu inflasi?" } | ConvertTo-Json
Invoke-RestMethod -Uri "https://bps-chatbot-v2.pinnhost.my.id/api/chat" -Method Post -Body $body -ContentType "application/json"
```

---

## 🔄 4. SOP Update Kode Rutin (Redeploy / CI-CD)

Jika di kemudian hari ada perubahan kode di repository GitHub, jalankan satu baris perintah ini via `plink.exe`:

```powershell
& "C:\Program Files\PuTTY\plink.exe" -P 6699 -batch -hostkey "SHA256:+tDxqS2SHavNhLM6I7PFuKYmrmbKLkl+aSdcpenmdxg" -pw "EzA%=72,@3D%" pinnhost@pinnhost.my.id "cd bps-chatbot-v2.pinnhost.my.id && git pull origin main && php artisan optimize:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache"
```

Jika ada perubahan file frontend (`Vue/CSS/JS`), jalankan `npm run build` lokal terlebih dahulu, lalu upload ulang `public/build` via `pscp.exe`.

---

## 🛡️ 5. Catatan Penting Keamanan:
- File `.env` dan `deploy/pwsupersecretcpanel.txt` **TIDAK BOLEH** ter-commit ke Git (`.gitignore` sudah mengabaikannya).
- Root [`.htaccess`](file:///c:/Users/HP/Laravel/CHAT-BOT-BPSv2/.htaccess) di server secara otomatis memblokir akses HTTP langsung ke file `.env`, `composer.json`, dan direktori `database/`.
