<?php

namespace App\Ai;

class ScopeGuard
{
    private const GREETING_PATTERNS = [
        '/^(halo|hai|hi|hello|hei|hey)\b/i',
        '/^(selamat\s+(pagi|siang|sore|malam|datang))\b/i',
        '/^(assalamu[\'\s]?alaikum|sampurasun|kulonuwun)\b/i',
        '/^(pagi|siang|sore|malam)\b/i',
        '/^(ping|p)\b/i',
    ];

    private const OUT_OF_SCOPE_PATTERNS = [
        '/\b(puisi|pantun|cerpen|novel|lagu|lirik|drama)\b/i',
        '/\b(resep|masak|memasak|bumbu|makanan\s+enak)\b/i',
        '/\b(coding|javascript|python|php|html|css|react|laravel|java|c\+\+|sql\s+query)\b/i',
        '/\b(cinta|pacar|jodoh|zodiak|ramalan|horoskop|mimpi)\b/i',
        '/\b(game|gaming|cheat|mabar|mobile\s+legends|ff|free\s+fire|pubg)\b/i',
        '/\b(obat|penyakit|gejala|diagnosis|medis|resep\s+dokter)\b/i',
        '/\b(hukum|pidana|perdata|pengacara|gugatan|sidang)\b/i',
        '/\b(cerita\s+lucu|lelucon|joke|standup)\b/i',
        '/\b(film|anime|manga|sinopsis|drakor|aktor|artis)\b/i',
    ];

    private const IN_SCOPE_KEYWORDS = [
        'bps', 'badan pusat statistik', 'statistik', 'data', 'angka', 'tabel', 'grafik',
        'inflasi', 'deflasi', 'ihk', 'indeks harga konsumen', 'sbh', 'survei biaya hidup',
        'pdrb', 'pdb', 'produk domestik', 'pertumbuhan ekonomi', 'ekonomi',
        'penduduk', 'kependudukan', 'demografi', 'piramida', 'rasio jenis kelamin',
        'sensus', 'sp2020', 'sp2010', 'sensus penduduk', 'sensus pertanian', 'sensus ekonomi',
        'susenas', 'sakernas', 'survei', 'sampling', 'sampel', 'blok sensus',
        'kemiskinan', 'garis kemiskinan', 'penduduk miskin', 'gini ratio', 'ketimpangan',
        'pengangguran', 'tpt', 'angkatan kerja', 'ketenagakerjaan', 'tpak',
        'ekspor', 'impor', 'neraca perdagangan', 'kode hs', 'perdagangan luar negeri',
        'publikasi', 'buku', 'pdf', 'unduh', 'download', 'berita resmi statistik', 'brs',
        'metadata', 'sirusa', 'romantik', 'satu data indonesia', 'sdi',
        'pst', 'pelayanan statistik terpadu', 'konsultasi data', 'data mikro',
        'provinsi', 'kabupaten', 'kota', 'kecamatan', 'desa', 'kelurahan', 'domain',
        'ketua', 'kepala', 'pimpinan', 'pejabat', 'ppid', 'struktur', 'organisasi', 'visi', 'misi', 'tugas', 'fungsi', 'sejarah', 'kantor', 'alamat', 'daryanto', 'amalia',
    ];

    private const PROVINCE_PATTERNS = [
        'indonesia', 'nasional', 'seluruh indonesia',
        'aceh', 'sumatera utara', 'sumut', 'sumatera barat', 'sumbar', 'riau', 'kepulauan riau', 'kepri',
        'jambi', 'sumatera selatan', 'sumsel', 'bengkulu', 'lampung', 'kepulauan bangka belitung', 'babel',
        'dki jakarta', 'jakarta', 'jawa barat', 'jabar', 'jawa tengah', 'jateng', 'di yogyakarta', 'jogja', 'yogyakarta',
        'jawa timur', 'jatim', 'banten', 'bali', 'nusa tenggara barat', 'ntb', 'nusa tenggara timur', 'ntt',
        'kalimantan barat', 'kalbar', 'kalimantan tengah', 'kalteng', 'kalimantan selatan', 'kalsel',
        'kalimantan timur', 'kaltim', 'kalimantan utara', 'kaltara', 'sulawesi utara', 'sulut',
        'sulawesi tengah', 'sulteng', 'sulawesi selatan', 'sulsel', 'sulawesi tenggara', 'sultra',
        'gorontalo', 'sulawesi barat', 'sulbar', 'maluku', 'maluku utara', 'papua', 'papua barat',
        'papua selatan', 'papua tengah', 'papua pegunungan', 'papua barat daya',
        'bandung', 'surabaya', 'semarang', 'medan', 'makassar', 'palembang', 'denpasar', 'bogor', 'bekasi',
    ];

    private const PERIOD_PATTERNS = [
        '/\b(19\d{2}|20\d{2})\b/', // Years e.g. 2020, 2023, 2024, 2025
        '/\b(terbaru|terkini|sekarang|bulan\s+ini|bulan\s+lalu|tahun\s+ini|tahun\s+lalu|terakhir|saat\s+ini)\b/i',
        '/\b(triwulan\s+[1-4]|tw\s+[1-4]|semester\s+[1-2]|q[1-4])\b/i',
        '/\b(januari|februari|maret|april|mei|juni|juli|agustus|september|oktober|november|desember)\b/i',
    ];

    /**
     * Classify question into ScopeDecision.
     */
    public function classify(string $question): ScopeDecision
    {
        $text = trim($question);
        if ($text === '') {
            return ScopeDecision::outOfScope('Pesan kosong.');
        }

        // Layer 0: Greeting detection
        foreach (self::GREETING_PATTERNS as $pattern) {
            if (preg_match($pattern, $text)) {
                return ScopeDecision::inScope('greeting');
            }
        }

        // Layer 1: Prompt Injection Check
        if ($this->isPromptInjection($text)) {
            return ScopeDecision::outOfScope('Permintaan melanggar batasan sistem.');
        }

        // Layer 1: Out-of-scope check
        foreach (self::OUT_OF_SCOPE_PATTERNS as $pattern) {
            if (preg_match($pattern, $text)) {
                return ScopeDecision::outOfScope();
            }
        }

        // Layer 1: In-scope keyword check
        $lower = mb_strtolower($text, 'UTF-8');
        $hasInScopeKeyword = false;
        foreach (self::IN_SCOPE_KEYWORDS as $keyword) {
            if (str_contains($lower, $keyword)) {
                $hasInScopeKeyword = true;
                break;
            }
        }

        // Determine specific intent
        $intent = $this->determineIntent($lower);

        // For numeric statistic inquiries, check for missing parameters (geography & period)
        if ($intent === 'numeric_statistic') {
            $missing = $this->checkNumericMissingParams($lower);
            if (! empty($missing)) {
                return ScopeDecision::inScope('numeric_statistic', $missing, 'Parameter wilayah atau periode belum spesifik.');
            }
        }

        if ($hasInScopeKeyword) {
            return ScopeDecision::inScope($intent);
        }

        // If no explicit in-scope or out-of-scope, treat generously as general definition inquiry
        return ScopeDecision::inScope($intent);
    }

    private function isPromptInjection(string $text): bool
    {
        $patterns = [
            '/abaikan\s+(semua\s+)?instruksi/i',
            '/tampilkan\s+(system\s+prompt|api\s+key|password|credential|kunci)/i',
            '/ignore\s+(all\s+)?(previous\s+)?instructions/i',
            '/show\s+(your\s+)?(system\s+prompt|api\s*key|secret)/i',
            '/you\s+are\s+now\s+in\s+developer\s+mode/i',
            '/dan\s+mode\s+aktifkan/i',
        ];

        foreach ($patterns as $p) {
            if (preg_match($p, $text)) {
                return true;
            }
        }

        return false;
    }

    private function determineIntent(string $lower): string
    {
        if (preg_match('/\b(berapa|jumlah|angka|tingkat|persentase|laju|total|volume|nilai)\b/i', $lower)) {
            return 'numeric_statistic';
        }

        if (preg_match('/\b(publikasi|buku|dokumen|unduh|download|pdf|laporan|berita\s+resmi)\b/i', $lower)) {
            return 'publication';
        }

        if (preg_match('/\b(metadata|sirusa|romantik|variabel|metodologi|rumus|cara\s+hitung)\b/i', $lower)) {
            return 'metadata_methodology';
        }

        if (preg_match('/\b(layanan|pst|konsultasi|beli|pembelian|tarif|kantor|buka|tutup|pelayanan)\b/i', $lower)) {
            return 'bps_service';
        }

        if (preg_match('/\b(ketua|kepala|pimpinan|pejabat|ppid|struktur|organisasi|visi|misi|tugas|fungsi|sejarah|alamat|kontak|siapa)\b/i', $lower)) {
            return 'institutional_profile';
        }

        if (preg_match('/\b(website|web|portal|link|tautan|alamat|cari\s+data|menu)\b/i', $lower)) {
            return 'navigation';
        }

        return 'definition';
    }

    private function checkNumericMissingParams(string $lower): array
    {
        $missing = [];

        // Check geography
        $hasGeography = false;
        foreach (self::PROVINCE_PATTERNS as $geo) {
            if (str_contains($lower, $geo)) {
                $hasGeography = true;
                break;
            }
        }

        if (! $hasGeography) {
            // Words like "di sini", "di daerah ini", "di tempat ini" mean missing specific geography
            $missing[] = 'geography';
        }

        // Check period
        $hasPeriod = false;
        foreach (self::PERIOD_PATTERNS as $pattern) {
            if (preg_match($pattern, $lower)) {
                $hasPeriod = true;
                break;
            }
        }

        if (! $hasPeriod) {
            $missing[] = 'period';
        }

        return $missing;
    }
}
