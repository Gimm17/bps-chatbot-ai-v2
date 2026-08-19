# Demo Script untuk Ketua Tim

> **Project:** BPS AI Assistant — Web Demo  
> **Provider:** LimitRouter (`https://limitrouter.com/v1`)  
> **Rule:** provider key hanya server-side.


## Durasi 5–8 menit

### 1. Opening
> Ini contoh bagaimana masyarakat nantinya bisa bertanya ke BPS tanpa login. Yang kita tunjukkan bukan hanya chat, tetapi bagaimana AI tetap dikendalikan knowledge dan sumber BPS.

### 2. Welcome Screen
Jelaskan:
- tanpa login;
- suggested questions;
- scope BPS;
- Prototype/Demo badge.

### 3. Pertanyaan definisi
Ketik: `Apa itu inflasi?`

Tunjukkan:
- retrieving/generating;
- jawaban;
- source card.

Narasi:
> Sebelum AI menjawab, backend mencari knowledge yang relevan. Source inilah yang diberikan ke model.

### 4. Pertanyaan ambigu
Ketik: `Berapa jumlah penduduk di sini?`

Target:
AI meminta wilayah/tahun.

Narasi:
> Sistem dirancang bertanya ulang daripada menebak angka.

### 5. Out-of-scope
Ketik pertanyaan non-BPS.

Narasi:
> API tidak kita buka sebagai chatbot umum gratis.

### 6. Gateway
> Website tidak memanggil LimitRouter langsung. Browser memanggil API kita sendiri, jadi model/provider dapat diganti dan API key aman.

### 7. Closing
> Setelah demo disetujui, retrieval sederhana ditingkatkan menjadi Hybrid RAG dengan sumber resmi, API statistik, vector search, reranker, evaluation, dan admin knowledge console.
