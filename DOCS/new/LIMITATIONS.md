# Kekurangan, Kendala, Solusi & Status Implementasi — BPS AI Assistant v2

> Dokumen komprehensif kendala teknis (BPS WebAPI, LLM behavior, performa, UI/UX mobile) beserta solusi teknis yang telah diimplementasikan di versi v2.
>
> **Update:** 19 Agustus 2026. Live di https://bps-chatbot-v2.pinnhost.my.id.

---

## 📊 Matriks Status Kendala & Solusi

### A. Integrasi BPS WebAPI & Data Publikasi

| Kendala Awal | Dampak | Solusi yang Diimplementasikan di v2 | Status |
|---|---|---|---|
| API hanya return metadata publikasi (judul/abstract), bukan isi angka dalam PDF | Query data tahunan terpencil sulit terjawab dari WebAPI mentah | Dibuatkan **PDF Ingestion Pipeline** (`IngestPublicationJob` & `ExtractPublicationTablesTool`) berbasis `smalot/pdfparser` dengan caching lokal di `storage/app/publications/`. | ✅ **TERSELESAIKAN** |
| Data proyeksi penduduk per tahun kalender | Sensus/proyeksi sering berjarak 5 tahunan | Sistem menyajikan data periode terdekat yang valid (contoh: proyeksi 2026/2025) secara jujur dan transparan. | ✅ **TERSELESAIKAN** |
| Endpoint Glosarium live API tidak selalu stabil | Definisi konsep statistik berisiko gagal | Fallback otomatis ke **Metadata Konsep SIRuSa & PPID RAG** lokal tanpa bergantung pada ketersediaan API luar. | ✅ **TERSELESAIKAN** |
| WAF BPS (PerimeterX) memblokir curl langsung | Scraping HTTP standar terblokir | Request dienkapsulasi melalui Laravel `Http::withHeaders()` dengan User-Agent resmi browser dan pooling session. | ✅ **TERSELESAIKAN** |

---

### B. Akurasi Kelembagaan & PPID Daerah

| Kendala Awal | Dampak | Solusi yang Diimplementasikan di v2 | Status |
|---|---|---|---|
| Kekeliruan nama pimpinan BPS Provinsi Sulteng vs BPS Kota Palu | AI menjawab pejabat yang salah karena nama daerah mirip | Dibuatkan korpus pengetahuan terpisah: **BPS Provinsi Sulteng (Satker 7200, Dr. Daryanto, M.M.)** vs **BPS Kota Palu (Satker 7271, Agus Santoso, S.ST., M.Si.)** di `data/knowledge/SRC-DEMO-016` & `SRC-DEMO-018`. | ✅ **TERSELESAIKAN** |
| Permintaan alamat kantor dan layanan PPID | Kurang detail | Penambahan rute intent `institutional_profile` di `ScopeGuard.php` untuk merespons profil instansi, alamat, dan website resmi. | ✅ **TERSELESAIKAN** |

---

### C. UX, Mobile Viewport & Kemudahan Akses (PWA / Embed)

| Kendala Awal | Dampak | Solusi yang Diimplementasikan di v2 | Status |
|---|---|---|---|
| Tampilan mobile sempit mengalami tabrakan teks navbar | Tombol Chat Baru & Install nabrak judul | Implementasi **3-dots dropdown menu** di mobile (`Header.vue`) dan tombol aksi 32px ikon ringkas. | ✅ **TERSELESAIKAN** |
| Layar mobile terpotong / double scrollbar saat keyboard aktif | Tombol kirim tertutup keyboard virtual | Penerapan layout **Single-Viewport Height (`100dvh`)** di `ChatApp.vue` dengan scrolling internal chat area. | ✅ **TERSELESAIKAN** |
| Popup embed fullscreen menutupi seluruh layar HP & tombol enter tertutup | Sulit mengetik dan menutup di mobile | Pengaturan ukuran responsif popup embed di atas tombol toggle floating (`embed.js`), font deskriptif di desktop. | ✅ **TERSELESAIKAN** |
| Aplikasi hanya bisa diakses via browser web | Kurang praktis di perangkat mobile | **PWA Full Suite** (`manifest.webmanifest`, `sw.js` cache `bps-ai-cache-v2`, 1-click install prompt). | ✅ **TERSELESAIKAN** |
| Warna website belum seragam dengan identitas BPS | Visual kurang representatif | Redesain total dengan **Palet Warna Resmi BPS (8-Color System)**: `#0093DD`, `#EB891B`, `#68B92E`, `#1F2937`, `#64748B`, `#E2E8F0`, `#F8FAFC`, `#FFFFFF`. | ✅ **TERSELESAIKAN** |
