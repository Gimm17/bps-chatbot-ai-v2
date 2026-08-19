<div align="center">

# 🇮🇩 BPS AI Assistant v2
### Asisten Informasi & Layanan Statistik Publik Resmi — Badan Pusat Statistik

<p align="center">
  <img src="https://img.shields.io/badge/Status-Live%20Production-success?style=for-the-badge&logo=vercel&logoColor=white" alt="Live Demo">
  <img src="https://img.shields.io/badge/Badan%20Pusat%20Statistik-Official%20WebAPI-0093DD?style=for-the-badge&logo=indonesia&logoColor=white" alt="BPS WebAPI">
  <img src="https://img.shields.io/badge/PWA-Ready%20%26%20Installable-EB891B?style=for-the-badge&logo=pwa&logoColor=white" alt="PWA Ready">
  <img src="https://img.shields.io/badge/Color%20System-Official%20BPS%20Palette-68B92E?style=for-the-badge" alt="BPS Palette">
  <img src="https://img.shields.io/badge/Interface-Vue%203%20SPA-0093DD?style=for-the-badge&logo=vuedotjs&logoColor=white" alt="Vue 3">
</p>

<p align="center">
  🌐 <b>Live Production Website:</b> <a href="https://bps-chatbot-v2.pinnhost.my.id">https://bps-chatbot-v2.pinnhost.my.id</a><br>
  🧩 <b>Live Embed Demo (Host Portfolio):</b> <a href="https://portfolio.gimmhost.my.id">https://portfolio.gimmhost.my.id</a>
</p>

---

</div>

## 🌟 Tentang BPS AI Assistant v2

**BPS AI Assistant v2** adalah platform asisten kecerdasan buatan (AI) percakapan resmi yang dirancang khusus untuk memfasilitasi akses publik terhadap **data statistik, konsep/definisi, metodologi survei & sensus, katalog publikasi, serta profil kelembagaan Badan Pusat Statistik (BPS) Republik Indonesia**.

Platform ini menggabungkan arsitektur **Hybrid RAG (Retrieval-Augmented Generation)** dengan integrasi langsung ke **BPS WebAPI real-time** (mencakup Pusat dan seluruh **549 Satuan Kerja Provinsi serta Kabupaten/Kota**) dan **Portal PPID BPS**.

---

## ✨ Fitur Unggulan Sistem

- 🏛️ **Terhubung Langsung ke BPS WebAPI (549 Wilayah):** Menarik data indikator strategis nasional dan daerah secara *real-time* (Inflasi, Pertumbuhan Ekonomi/PDRB, Kemiskinan, Pengangguran/TPT, Kependudukan, IPM).
- 👔 **Integrasi Profil Pejabat & PPID Daerah:** Mengenali struktur pimpinan BPS Pusat (Dr. Amalia Adininggar Widyasanti), BPS Provinsi Sulawesi Tengah (Dr. Daryanto, M.M. - Satker 7200), hingga BPS Kabupaten/Kota seperti BPS Kota Palu (Agus Santoso, S.ST., M.Si. - Satker 7271) secara akurat tanpa tertukar.
- 📱 **Progressive Web App (PWA) 1-Click Install:** Dapat diinstal langsung ke homescreen Android, iOS, Windows, dan MacOS sebagai native app dengan dukungan caching Service Worker (`sw.js`).
- ☁️ **Cloud Bubble Embeddable Widget:** Widget mengambang modular berlogo Awan Statistik BPS yang dapat dipasang di seluruh portal web pemerintah/OPD hanya dengan 1 baris script.
- 🎨 **Palet Warna Resmi BPS (8-Color System):** Antarmuka modern yang mematuhi standar identitas visual BPS: Biru BPS (`#0093DD`), Oranye BPS (`#EB891B`), Hijau BPS (`#68B92E`), Charcoal Text (`#1F2937`), Muted Text (`#64748B`), Border (`#E2E8F0`), Background (`#F8FAFC`), dan Surface (`#FFFFFF`).
- 🛡️ **Multi-Layer Guardrails (ScopeGuard):** Memfilter prompt injection, jailbreak, dan pertanyaan di luar cakupan BPS (resep/coding/dsb) secara deterministik.
- 🔗 **Rujukan Terverifikasi (Official Verified Citations):** Setiap jawaban faktual dilengkapi kartu sumber resmi BPS yang dapat diklik langsung untuk verifikasi publik.
- 📲 **Ultra-Responsive Mobile Layout (`100dvh`):** Tampilan layar ponsel terkunci pas di viewport tanpa scroll ganda (*no double scrollbar*), dilengkapi menu ringkas 3-titik untuk layar kecil (320px–360px).

---

## 💡 Contoh Pertanyaan yang Bisa Diajukan

| Kategori | Contoh Pertanyaan Pengguna |
|---|---|
| **Definisi & Konsep** | *"Apa itu inflasi dan bagaimana BPS menghitungnya?"* |
| **Data Makroekonomi** | *"Berapa pertumbuhan ekonomi dan PDRB Sulawesi Tengah?"* |
| **Kependudukan Daerah** | *"Data penduduk Sulawesi Tengah tahun 2025"* |
| **Buku Publikasi** | *"Tampilkan publikasi kependudukan Sulawesi Tengah 2025"* |
| **Kelembagaan & Pejabat** | *"Siapa Kepala BPS Pusat?"*, *"Siapa Kepala BPS Kota Palu dan alamat kantornya?"* |
| **Ketenagakerjaan** | *"Apa itu Tingkat Pengangguran Terbuka (TPT)?"* |
| **Layanan BPS** | *"Bagaimana cara mengakses layanan Pelayanan Statistik Terpadu (PST)?"* |

---

## 🔌 Cara Pasang Widget di Website Eksternal / Portal Pemda

Untuk menyematkan BPS AI Assistant sebagai tombol floating **"☁️ Tanya BPS"** pada portal web manapun, cukup tambahkan tag berikut sebelum penutup `</body>`:

```html
<!-- BPS AI Assistant Standalone Cloud Bubble Widget -->
<script src="https://bps-chatbot-v2.pinnhost.my.id/embed.js" defer></script>
```

---

## 🏗️ Struktur & Dokumentasi Proyek

Dokumentasi arsitektur dan teknis lengkap dapat dilihat pada direktori `DOCS/` serta dokumen PDF/Word yang tersedia di root proyek:
- `DOKUMEN_1_ALUR_LOGIKA_WORKFLOW_DAN_TEKNIS_BPS_AI.docx` & `.pdf`
- `DOKUMEN_2_ARSITEKTUR_FRAMEWORK_DAN_GLOSARIUM_TEKNOLOGI_BPS_AI.docx` & `.pdf`
- `DOCS/01_PRODUCT/` — Product Requirements & User Stories
- `DOCS/02_ARCHITECTURE/` — System Architecture & Data Flow
- `DOCS/05_AI/` — ScopeGuard, PromptBuilder, Model Routing
- `DOCS/06_RAG/` — RAG Retrieval & Knowledge Corpus Format

---

<div align="center">
  <p><b>Badan Pusat Statistik Republik Indonesia</b></p>
  <p><i>Menyediakan Data Berkualitas untuk Indonesia Maju</i></p>
</div>
