# Scope & Intent Routing — ScopeGuard

> **Project:** BPS AI Assistant v2  
> **Class Implementation:** `App\Ai\ScopeGuard.php`  

## 🛡️ In-Scope Topics
1. **Statistik Resmi:** Inflasi, PDRB, Kemiskinan, Pengangguran (TPT), Kependudukan, IPM, Impor/Ekspor.
2. **Katalog Publikasi:** Pencarian buku publikasi berkala BPS Pusat, Provinsi, dan Kabupaten/Kota.
3. **Kelembagaan & PPID:** Struktur pimpinan (Kepala BPS RI, Kepala BPS Provinsi Sulteng, Kepala BPS Kota Palu), alamat kantor, visi misi, dan permohonan informasi publik.
4. **Konsep & Metodologi:** Definisi indikator SIRuSa, perbedaan harga konstan vs berlaku, metodologi sensus/survei.
5. **Layanan BPS:** Pelayanan Statistik Terpadu (PST), tata cara konsultasi statistik, dan rekomendasi kegiatan statistik.

## 🚫 Out-of-Scope Topics (Auto-Blocked)
- Penulisan kode pemrograman umum yang tidak terkait API BPS.
- Resep makanan, konsultasi medis, ramalan zodiak, tips asmara, game, dan hiburan umum.
- Upaya manipulasi prompt (*prompt injection*, permintaan sistem prompt rahasia, atau mode developer tak terbatas).

## 🧭 Multi-Layer Routing Architecture
1. **Layer 0 (Greeting Heuristics):** Deteksi salam/sapaan instan (halo, pagi, assalamu'alaikum) -> Fast-response ramah tanpa konsumsi token LLM.
2. **Layer 1 (Jailbreak / Prompt Injection Filter):** Deteksi kata kunci berbahaya -> Langsung kembalikan respons penolakan sopan.
3. **Layer 2 (Out-of-Scope Filter):** Deteksi topik di luar domain BPS -> Arahkan pengguna kembali ke topik statistik resmi.
4. **Layer 3 (Intent Classification):**
   - `numeric_stat` -> BpsAgent panggil Live WebAPI indicators.
   - `publication` -> BpsAgent panggil Live WebAPI publication catalog.
   - `institutional_profile` -> KnowledgeRetriever ambil profil PPID & kantor BPS terkait.
   - `definition` -> KnowledgeRetriever ambil konsep SIRuSa & metodologi BPS.
   - `service` -> KnowledgeRetriever panduan layanan PST.
5. **Layer 4 (Missing Parameter Checker):**
   - Memastikan parameter geografi (wilayah) dan periode waktu (tahun) tersedia jika pengguna meminta data angka spesifik.
