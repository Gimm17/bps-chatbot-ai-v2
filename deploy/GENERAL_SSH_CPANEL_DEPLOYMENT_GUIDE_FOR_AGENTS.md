# 🌐 Universal Guide: Automated cPanel SSH Deployment for AI Agents

> **Tujuan Dokumen:** Panduan standar (*Standard Operating Procedure*) yang dapat digunakan oleh AI Coding Agent manapun (di berbagai project) untuk melakukan deployment otomatis ke cPanel / Shared Hosting berbasis Linux dari environment host Windows secara **non-interaktif (100% otomatis)**.

---

## 📋 1. Konfigurasi Variabel Template

Sebelum memulai proses deployment, kumpulkan dan petakan variabel berikut:

```yaml
# Parameter Server SSH
SSH_HOST: "nama-host-atau-ip.com"          # Contoh: pinnhost.my.id atau 195.88.211.25
SSH_PORT: 22                              # Port SSH (Port standar 22 atau custom misal 6699, 2222)
SSH_USER: "username_cpanel"               # Username akun cPanel
SSH_PASSWORD: "password_cpanel"           # Password akun cPanel
SSH_HOSTKEY: "SHA256:xxxxxxxxxxxxxxxxx"   # Fingerprint SSH server (didapat dari test koneksi pertama)

# Parameter Project & Direktori
REMOTE_DIR: "/home/<SSH_USER>/<domain-anda.com>"  # Path root folder di server
DOMAIN_URL: "https://domain-anda.com"             # URL website produksi
GIT_REPO_URL: "https://github.com/<user>/<repo>.git"
GIT_BRANCH: "main"                                # Branch utama (main / master)
```

---

## 🛠️ 2. Tooling Eksekusi Non-Interaktif di Windows

### Masalah pada Windows:
Perintah `ssh.exe` bawaan Windows tidak memiliki flag bawaan untuk passing password via command line (dan `sshpass` tidak tersedia di Windows). Hal ini dapat memicu prompt password interaktif yang membuat AI Agent terhenti (*stuck*).

### Solusi Terbaik:
Gunakan **PuTTY Command-Line Tools** (`plink.exe` dan `pscp.exe`) yang terinstall di `C:\Program Files\PuTTY\`:

### A. Eksekusi Perintah Linux Remote (`plink.exe`):
```powershell
& "C:\Program Files\PuTTY\plink.exe" -P <SSH_PORT> -batch -hostkey "<SSH_HOSTKEY>" -pw "<SSH_PASSWORD>" <SSH_USER>@<SSH_HOST> "<PERINTAH_LINUX>"
```

### B. Upload File / Folder Rekursif (`pscp.exe`):
```powershell
& "C:\Program Files\PuTTY\pscp.exe" -P <SSH_PORT> -batch -hostkey "<SSH_HOSTKEY>" -pw "<SSH_PASSWORD>" -r <LOCAL_PATH> <SSH_USER>@<SSH_HOST>:<REMOTE_PATH>
```

> ⚠️ **Catatan Wajib:** Parameter `-batch` dan `-hostkey` WAJIB disertakan agar proses tidak tertahan oleh konfirmasi *Host Key Validation*.

---

## 🚀 3. Lifecycle Deployment Step-by-Step

---

### Langkah 1: Tes Koneksi & Dapatkan Hostkey Fingerprint
Lakukan tes koneksi awal untuk memverifikasi akun dan mendapatkan fingerprint hostkey:

```powershell
# Jalankan perintah awal
& "C:\Program Files\PuTTY\plink.exe" -P <SSH_PORT> -batch -pw "<SSH_PASSWORD>" <SSH_USER>@<SSH_HOST> "echo OK"
```

*Jika muncul pesan:*
`The server's ssh-ed25519 key fingerprint is: ssh-ed25519 255 SHA256:abc123xyz...`
Salin bagian `SHA256:abc123xyz...` dan gunakan sebagai nilai `-hostkey "SHA256:abc123xyz..."` pada seluruh perintah berikutnya.

---

### Langkah 2: Inspeksi Environment Server
Periksa ketersediaan tool di server remote:
```powershell
& "C:\Program Files\PuTTY\plink.exe" -P <SSH_PORT> -batch -hostkey "<SSH_HOSTKEY>" -pw "<SSH_PASSWORD>" <SSH_USER>@<SSH_HOST> "php -v; composer --version; git --version; pwd"
```

---

### Langkah 3: Inisialisasi & Tarik Source Code dari Git
Tarik kode terbaru langsung ke direktori target di server:

```powershell
& "C:\Program Files\PuTTY\plink.exe" -P <SSH_PORT> -batch -hostkey "<SSH_HOSTKEY>" -pw "<SSH_PASSWORD>" <SSH_USER>@<SSH_HOST> "cd <REMOTE_DIR> && git init && git remote add origin <GIT_REPO_URL> && git fetch origin <GIT_BRANCH> && git checkout -f <GIT_BRANCH>"
```

---

### Langkah 4: Strategi Frontend Build (Solusi Ketiadaan Node.js di cPanel)
> **Fakta cPanel Shared Hosting:** Sebagian besar hosting cPanel tidak menyediakan `node` / `npm` di CLI path default.

**Best Practice:**
1. Lakukan build asset secara lokal di komputer host:
   ```powershell
   npm install && npm run build
   ```
2. Upload folder hasil build (`public/build` atau `dist`) ke server via `pscp.exe`:
   ```powershell
   & "C:\Program Files\PuTTY\pscp.exe" -P <SSH_PORT> -batch -hostkey "<SSH_HOSTKEY>" -pw "<SSH_PASSWORD>" -r public/build <SSH_USER>@<SSH_HOST>:<REMOTE_DIR>/public/
   ```

---

### Langkah 5: Setup File Konfigurasi Environment (`.env`)
Buat atau upload file `.env` produksi di server remote. Pastikan parameter utama diatur:
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=<DOMAIN_URL>`
- Konfigurasi Database (MySQL / SQLite)
- API Keys layanan terkait

---

### Langkah 6: Eksekusi Skrip Setup Otomatis di Server
Untuk menghindari kerumitan escaping karakter string di PowerShell, buat file bash skrip lokal (misal: `setup_remote.sh`), upload ke server, lalu eksekusi:

**Isi skrip `setup_remote.sh`:**
```bash
#!/usr/bin/env bash
set -e

cd <REMOTE_DIR>

# 1. Install Dependensi Backend (Production Only)
composer install --no-dev --optimize-autoloader --no-interaction

# 2. Setup Database (Contoh SQLite)
if [ ! -f "database/database.sqlite" ]; then
    touch database/database.sqlite
    chmod 664 database/database.sqlite
fi

# 3. Generate App Key & Jalankan Migrasi
php artisan key:generate --force
php artisan migrate --force

# 4. Optimasi Cache Framework (Laravel)
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Konfigurasi Izin Folder Storage
chmod -R 775 storage bootstrap/cache

echo "✅ Setup remote selesai!"
```

**Upload & Jalankan Skrip:**
```powershell
# Upload skrip
& "C:\Program Files\PuTTY\pscp.exe" -P <SSH_PORT> -batch -hostkey "<SSH_HOSTKEY>" -pw "<SSH_PASSWORD>" setup_remote.sh <SSH_USER>@<SSH_HOST>:<REMOTE_DIR>/

# Eksekusi skrip di remote
& "C:\Program Files\PuTTY\plink.exe" -P <SSH_PORT> -batch -hostkey "<SSH_HOSTKEY>" -pw "<SSH_PASSWORD>" <SSH_USER>@<SSH_HOST> "cd <REMOTE_DIR> && bash setup_remote.sh"
```

---

### Langkah 7: Verifikasi Hasil Deployment
Uji secara otomatis apakah website sudah live dan merespons dengan benar:

```powershell
# 1. Uji HTTP status code halaman utama
$res = Invoke-WebRequest -Uri "<DOMAIN_URL>" -UseBasicParsing
Write-Host "HTTP Status:" $res.StatusCode

# 2. Uji API Endpoint (jika ada)
Invoke-RestMethod -Uri "<DOMAIN_URL>/api/health"
```

---

## 🔄 4. SOP Update Kode Rutin (Redeploy One-Liner)

Setelah deployment awal selesai, untuk update kode berkala di masa depan, AI Agent cukup menjalankan satu baris perintah:

```powershell
& "C:\Program Files\PuTTY\plink.exe" -P <SSH_PORT> -batch -hostkey "<SSH_HOSTKEY>" -pw "<SSH_PASSWORD>" <SSH_USER>@<SSH_HOST> "cd <REMOTE_DIR> && git pull origin <GIT_BRANCH> && php artisan optimize:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache"
```

*Jika ada perubahan frontend:* Lakukan `npm run build` lokal lalu upload folder `public/build` via `pscp.exe`.

---

## 🛡️ 5. Checklist Keamanan Wajib untuk AI Agent

1. **Proteksi File Kredensial:**
   - File yang memuat password atau API Key (seperti `.env` atau `*secret*.txt`) **WAJIB** masuk ke dalam `.gitignore`.
2. **Proteksi Direktori cPanel (.htaccess):**
   - Pastikan root `.htaccess` memblokir akses langsung ke file sensitif (`.env`, `composer.json`, `database/`, `.git/`).
3. **Izin File (File Permissions):**
   - File kode: `644`
   - Folder/Direktori: `755`
   - Folder writable (`storage/`, `bootstrap/cache/`): `775`
