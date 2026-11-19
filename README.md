# BPS AI Assistant v2

**Asisten statistik AI berbasis Hybrid RAG** untuk Badan Pusat Statistik — terintegrasi real-time ke **BPS WebAPI (549 satker)**, profil PPID, guardrails **ScopeGuard**, PWA offline shell, dan **1-line embeddable widget**.

[![Live Web](https://img.shields.io/badge/LIVE_APP-bps--chatbot--v2.pinnhost.my.id-0093DD?style=for-the-badge)](https://bps-chatbot-v2.pinnhost.my.id/)
![Vue 3](https://img.shields.io/badge/Vue-3_SPA-4FC08D?style=for-the-badge&logo=vuedotjs&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-Backend-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)

<table>
  <tr>
    <td width="50%"><img src="docs/screenshots/live-home.webp" alt="BPS AI Assistant Home" /></td>
    <td width="50%"><img src="docs/screenshots/live-chat.webp" alt="BPS AI Cited Answer" /></td>
  </tr>
</table>

### Fitur Utama

- **Real-Time BPS WebAPI** — Narik indikator strategis nasional & daerah (Inflasi, PDRB, Kemiskinan, TPT, Kependudukan) across 549 wilayah.
- **Verified Citations & PPID** — Setiap jawaban menyertakan rujukan resmi BPS serta profil pimpinan/PPID akurat tanpa tertukar wilayah.
- **ScopeGuard** — Pertahanan deterministik terhadap prompt injection & topik di luar layanan statistik resmi.
- **PWA + Embeddable Cloud Bubble** — Instalasi 1 klik di mobile (`100dvh`) atau tempel di portal Pemda dengan 1 baris script:

```html
<script src="https://bps-chatbot-v2.pinnhost.my.id/embed.js" defer></script>
```

### Worker & Housekeeping

```bash
php artisan queue:work --queue=default --tries=3
php artisan conversations:prune   # jadwal harian hapus chat >30 hari
php artisan stats:usage           # ringkasan pemakaian token
```

Dokumentasi arsitektur & korpus lengkap tersimpan di [`DOCS/`](DOCS/).
