# PRD — BPS AI Assistant v2

> **Project:** BPS AI Assistant v2  
> **Status:** Live Production & Embeddable  
> **Integrasi:** BPS WebAPI (549 Wilayah) + PPID Portal + OpenRouter Multi-Key Resilience Router  

## 🎯 Problem Statement
1. Akses data publik BPS tersebar di ratusan portal daerah (549 satker), menyulitkan masyarakat mencari data spesifik lokal secara cepat.
2. Pertanyaan konsep statistik (inflasi, PDRB, TPT) dan permohonan informasi PPID (profil pejabat, alamat satker) sering membebani petugas PST (*Pelayanan Statistik Terpadu*).
3. Risiko tinggi halusinasi angka jika menggunakan AI generatif umum tanpa grounding data statistik resmi.

## 🚀 Vision
Asisten publik cerdas berbasis web dan PWA tanpa perlu login yang menyajikan data statistik makro, buku publikasi, metodologi, dan profil kelembagaan BPS secara instan, akurat, dan dapat diverifikasi langsung ke sumber resmi.

## 🌟 Core Features (v2)
1. **Live BPS WebAPI Integration (549 Satker):** Penarikan indikator makro strategis dan direktori publikasi Pusat, 38 Provinsi, dan 514 Kabupaten/Kota.
2. **Institutional PPID & Leadership RAG:** Basis pengetahuan terstruktur pejabat struktural BPS Pusat, BPS Provinsi (Satker 7200), dan BPS Kabupaten/Kota (Satker 7271) tanpa tertukar.
3. **Multi-Layer ScopeGuard:** Filter prompt injection, sapaan instan (0 token), dan penolakan sopan topik di luar statistik BPS.
4. **Progressive Web App (PWA):** Instalasi 1-klik ke homescreen Android, iOS, Windows, dan MacOS dengan Service Worker caching.
5. **Cloud Bubble Embeddable Widget:** Script widget mengambang (`embed.js`) untuk penyematan ke website eksternal/portal Pemda.
6. **Official Verified Citations:** Setiap jawaban mengaitkan URL rujukan resmi BPS / SIRuSa / PPID dengan status verifikasi.
7. **Multi-Key Resilience Router:** Failover otomatis antar API key jika terjadi rate limit.
8. **Ultra-Responsive Mobile Viewport (`100dvh`):** Pengalaman mobile single-screen bebas scroll ganda dengan menu ringkas 3-titik.
9. **Official BPS 8-Color Design System:** Penerapan token visual resmi BPS (`#0093DD`, `#EB891B`, `#68B92E`, dsb).
