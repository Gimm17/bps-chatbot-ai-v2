<?php

namespace App\Bps;

use App\Ai\AiProviderInterface;
use App\Ai\ChatProviderInput;
use App\Ai\ChatResult;
use App\Ai\PromptBuilder;
use App\Rag\RetrievedSource;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BpsAgent
{
    /**
     * @var array Map of [sourceId => ['title' => ..., 'url' => ..., 'snippet' => ...]]
     */
    private array $collectedSources = [];

    /**
     * Common regional abbreviation and alias mapping.
     */
    private const REGION_ALIASES = [
        'sulteng' => 'Sulawesi Tengah',
        'sulsel' => 'Sulawesi Selatan',
        'sulut' => 'Sulawesi Utara',
        'sultra' => 'Sulawesi Tenggara',
        'sulbar' => 'Sulawesi Barat',
        'gorontalo' => 'Gorontalo',
        'jabar' => 'Jawa Barat',
        'jateng' => 'Jawa Tengah',
        'jatim' => 'Jawa Timur',
        'dki' => 'DKI Jakarta',
        'jakarta' => 'DKI Jakarta',
        'diy' => 'DI Yogyakarta',
        'jogja' => 'DI Yogyakarta',
        'yogyakarta' => 'DI Yogyakarta',
        'banten' => 'Banten',
        'bali' => 'Bali',
        'ntb' => 'Nusa Tenggara Barat',
        'ntt' => 'Nusa Tenggara Timur',
        'sumut' => 'Sumatera Utara',
        'sumbar' => 'Sumatera Barat',
        'sumsel' => 'Sumatera Selatan',
        'riau' => 'Riau',
        'kepri' => 'Kepulauan Riau',
        'jambi' => 'Jambi',
        'bengkulu' => 'Bengkulu',
        'lampung' => 'Lampung',
        'babel' => 'Kepulauan Bangka Belitung',
        'kalbar' => 'Kalimantan Barat',
        'kalteng' => 'Kalimantan Tengah',
        'kalsel' => 'Kalimantan Selatan',
        'kaltim' => 'Kalimantan Timur',
        'kaltara' => 'Kalimantan Utara',
        'maluku' => 'Maluku',
        'malut' => 'Maluku Utara',
        'papua' => 'Papua',
    ];

    public function __construct(
        private readonly BpsApiClient $apiClient,
        private readonly AiProviderInterface $aiProvider,
        private readonly PromptBuilder $promptBuilder,
        private readonly PublicationIndexer $indexer
    ) {}

    public function getCollectedSources(): array
    {
        return $this->collectedSources;
    }

    public function clearSources(): void
    {
        $this->collectedSources = [];
    }

    /**
     * Execute BPS Agent workflow for live statistical and metadata lookups.
     */
    public function run(string $question, string $intent): ?ChatResult
    {
        $this->clearSources();

        $evidenceSources = [];

        try {
            // 0. Detect target domain (Provinsi / Kabupaten / Pusat)
            $resolvedDomain = $this->resolveDomainFromQuestion($question);
            $domainId = $resolvedDomain['domain_id'] ?? '0000';
            $domainName = $resolvedDomain['domain_name'] ?? 'Indonesia (Pusat)';
            $domainUrl = $resolvedDomain['domain_url'] ?? 'https://www.bps.go.id';

            // 1. Search Live BPS Publications for target domain (prioritize publications when year is specific like 2025/2024)
            $pubSources = $this->lookupPublications($question, $domainId, $domainName, $domainUrl);
            $evidenceSources = array_merge($evidenceSources, $pubSources);

            // 2. Search Live BPS API Indicators for target domain
            $indicatorSources = $this->lookupLiveIndicators($question, $domainId, $domainName, $domainUrl);
            $evidenceSources = array_merge($evidenceSources, $indicatorSources);

            // If domain is specific province, also pull national indicators for context if needed
            if ($domainId !== '0000' && count($evidenceSources) < 2) {
                $natSources = $this->lookupLiveIndicators($question, '0000', 'Nasional', 'https://www.bps.go.id');
                $evidenceSources = array_merge($evidenceSources, $natSources);
            }

            // 3. Search BPS Domains if navigation intent
            if ($intent === 'navigation' || str_contains(mb_strtolower($question), 'provinsi') || str_contains(mb_strtolower($question), 'daerah')) {
                $domSources = $this->lookupDomains($question);
                $evidenceSources = array_merge($evidenceSources, $domSources);
            }
        } catch (\Throwable $e) {
            Log::warning('BpsAgent tool execution failed: ' . $e->getMessage());
        }

        if (empty($evidenceSources)) {
            // Return null so ChatService falls back to local knowledge base
            return null;
        }

        // Limit evidence to top 5
        $evidenceSources = array_slice($evidenceSources, 0, 5);

        // Build prompt with live BPS WebAPI evidence
        $systemPrompt = $this->promptBuilder->build($evidenceSources);

        $input = new ChatProviderInput(
            systemPrompt: $systemPrompt,
            userMessage: $question
        );

        $output = $this->aiProvider->chat($input);
        $result = ChatResult::parse($output->text);

        // If the model gave no_evidence but we collected valid official sources, attempt synthesis retry
        if ($result->status === 'no_evidence' && !empty($this->collectedSources)) {
            $retryPrompt = $systemPrompt . "\n\nPERINGATAN: Data resmi BPS berikut berhasil ditarik dari BPS WebAPI. Tolong jelaskan dan rangkum data ini secara lengkap untuk menjawab pertanyaan pengguna:\n";
            $retryInput = new ChatProviderInput(
                systemPrompt: $retryPrompt,
                userMessage: 'Tolong jelaskan dan rangkum data BPS resmi di atas untuk pertanyaan: ' . $question
            );
            $retryOutput = $this->aiProvider->chat($retryInput);
            $retryResult = ChatResult::parse($retryOutput->text);

            if ($retryResult->status === 'answered' && !empty($retryResult->answer)) {
                return $retryResult;
            }
        }

        return $result;
    }

    /**
     * Resolve BPS Domain ID from user question.
     */
    private function resolveDomainFromQuestion(string $question): array
    {
        $lowerQ = mb_strtolower($question);

        // Check alias mapping first
        foreach (self::REGION_ALIASES as $alias => $fullName) {
            if (preg_match('/\b' . preg_quote($alias, '/') . '\b/i', $lowerQ)) {
                $lowerQ .= ' ' . mb_strtolower($fullName);
            }
        }

        $resp = $this->apiClient->get('domain/model/domain', ['type' => 'all']);
        if ($resp->isOk && !empty($resp->rows)) {
            // Prioritize province domains (ending with 00) or exact city/regency match
            $matched = null;
            $longestMatchLen = 0;

            foreach ($resp->rows as $dom) {
                $name = mb_strtolower($dom['domain_name'] ?? '');
                if ($name !== '' && str_contains($lowerQ, $name)) {
                    if (mb_strlen($name) > $longestMatchLen) {
                        $longestMatchLen = mb_strlen($name);
                        $matched = $dom;
                    }
                }
            }

            if ($matched !== null) {
                return $matched;
            }
        }

        return [
            'domain_id' => '0000',
            'domain_name' => 'Pusat / Nasional',
            'domain_url' => 'https://www.bps.go.id',
        ];
    }

    /**
     * Look up live publications from BPS WebAPI for specific domain.
     */
    private function lookupPublications(string $question, string $domainId, string $domainName, string $domainUrl): array
    {
        // Extract root topic keyword for robust BPS WebAPI keyword search
        $lowerQ = mb_strtolower($question);
        $cleanQ = 'Statistik';

        $topics = [
            'kependudukan' => ['penduduk', 'kependudukan', 'populasi', 'sensus'],
            'inflasi' => ['inflasi', 'ihk', 'harga'],
            'kemiskinan' => ['miskin', 'kemiskinan', 'gini'],
            'ketenagakerjaan' => ['pengangguran', 'tpt', 'tenaga kerja', 'kerja', 'sakernas', 'upah'],
            'pdrb' => ['pdrb', 'pdb', 'ekonomi', 'pertumbuhan'],
            'ekspor' => ['ekspor', 'impor', 'perdagangan'],
            'pertanian' => ['tani', 'pertanian', 'panen'],
            'pariwisata' => ['wisata', 'pariwisata', 'hotel'],
        ];

        foreach ($topics as $primaryKeyword => $matchers) {
            foreach ($matchers as $m) {
                if (str_contains($lowerQ, $m)) {
                    $cleanQ = $primaryKeyword;
                    break 2;
                }
            }
        }

        $resp = $this->apiClient->get('list/model/publication', [
            'domain' => $domainId,
            'keyword' => $cleanQ,
            'page' => 1,
        ]);

        $sources = [];
        if ($resp->isOk && !empty($resp->rows)) {
            // Extract requested year if any (e.g. 2025, 2024, 2023)
            preg_match('/\b(20\d{2})\b/', $question, $yearMatch);
            $targetYear = $yearMatch[1] ?? null;

            // Sort publications prioritizing target year
            $rows = $resp->rows;
            if ($targetYear) {
                usort($rows, function ($a, $b) use ($targetYear) {
                    $aHas = str_contains($a['title'] ?? '', $targetYear) ? 1 : 0;
                    $bHas = str_contains($b['title'] ?? '', $targetYear) ? 1 : 0;
                    return $bHas <=> $aHas;
                });
            }

            foreach (array_slice($rows, 0, 3) as $pub) {
                $pubId = $pub['pub_id'] ?? $pub['id'] ?? ('PUB-' . uniqid());
                $title = $pub['title'] ?? 'Publikasi BPS';
                $abstract = $pub['abstract'] ?? 'Publikasi statistik resmi Badan Pusat Statistik.';
                $pdfUrl = $pub['pdf'] ?? null;
                $rlDate = $pub['rl_date'] ?? null;

                // Construct exact BPS Portal Publication HTML URL
                $portalUrl = $domainUrl;
                if (!empty($rlDate) && !empty($pubId) && !empty($title)) {
                    $parts = explode('-', $rlDate);
                    if (count($parts) === 3) {
                        $slug = Str::slug($title);
                        $base = rtrim($domainUrl, '/');
                        $portalUrl = "{$base}/id/publication/{$parts[0]}/{$parts[1]}/{$parts[2]}/{$pubId}/{$slug}.html";
                    }
                }

                $content = "Buku Publikasi Resmi BPS {$domainName}: {$title}\n";
                if ($rlDate) {
                    $content .= "Tanggal Rilis Resmi: {$rlDate}\n";
                }
                $content .= "Wilayah: {$domainName}\n";
                $content .= "Halaman Resmi Web Portal BPS: {$portalUrl}\n";
                if ($pdfUrl) {
                    $content .= "Tautan Unduh Dokumen PDF Resmi: {$pdfUrl}\n";
                }
                $content .= "Abstraksi / Ringkasan Isi Publikasi:\n" . strip_tags($abstract) . "\n";

                // Check if publication PDF text is already indexed locally
                $indexed = \App\Models\PublicationIndex::find($pubId);
                if ($indexed && $indexed->status === 'completed' && !empty($indexed->extracted_text)) {
                    $pdfSnippets = $this->indexer->searchSnippets($question, $domainId, 1);
                    $extractedChunk = !empty($pdfSnippets) ? $pdfSnippets[0]['snippet'] : mb_substr($indexed->extracted_text, 0, 3000);
                    $content .= "\n=== ISI DETAIL DOKUMEN BUKU PDF (TEREKSTRAKSI RESMI) ===\n" . $extractedChunk . "\n";
                } else {
                    // Trigger background ingestion job so next queries have instant full-text access
                    if ($pdfUrl) {
                        try {
                            \App\Jobs\IngestPublicationJob::dispatch(
                                pubId: (string) $pubId,
                                pdfUrl: (string) $pdfUrl,
                                title: (string) $title,
                                domainId: (string) $domainId,
                                domainName: (string) $domainName,
                                rlDate: $rlDate ? (string) $rlDate : null,
                                portalUrl: (string) $portalUrl,
                                abstract: $abstract ? (string) $abstract : null
                            );
                        } catch (\Throwable $e) {
                            // Non-blocking queue failure fallback
                        }
                    }
                }

                $sourceId = (string) $pubId;
                $this->collectedSources[$sourceId] = [
                    'title' => $title . ' — ' . $domainName,
                    'url' => $portalUrl ?: ($pdfUrl ?: $domainUrl),
                    'snippet' => "Publikasi resmi BPS {$domainName}: {$title} (Rilis: {$rlDate}). Memuat data tabel kependudukan dan proyeksi resmi. " . mb_substr(strip_tags($abstract), 0, 120) . '...',
                ];

                $sources[] = new RetrievedSource(
                    sourceId: $sourceId,
                    title: $title . ' — ' . $domainName,
                    url: $portalUrl ?: ($pdfUrl ?: $domainUrl),
                    content: $content,
                    score: $targetYear && str_contains($title, $targetYear) ? 1.0 : 0.95,
                    sourceStatus: 'OFFICIAL_BPS_API',
                    category: 'publication'
                );
            }
        }

        return $sources;
    }

    /**
     * Look up live indicators from BPS WebAPI for specific domain.
     */
    private function lookupLiveIndicators(string $question, string $domainId, string $domainName, string $domainUrl): array
    {
        $resp = $this->apiClient->get('list/model/indicators', [
            'domain' => $domainId,
            'page' => 1,
        ]);

        $sources = [];
        if ($resp->isOk && !empty($resp->rows)) {
            $lowerQ = mb_strtolower($question);
            $keywords = ['penduduk', 'kependudukan', 'inflasi', 'ihk', 'harga', 'ekonomi', 'pertumbuhan', 'pdb', 'pdrb', 'miskin', 'kemiskinan', 'pengangguran', 'tpt', 'tenaga kerja', 'upah', 'gaji', 'ekspor', 'impor'];

            // Filter indicators matching user question keywords
            $matchedRows = [];
            foreach ($resp->rows as $ind) {
                $title = mb_strtolower($ind['title'] ?? '');
                $name = mb_strtolower($ind['name'] ?? '');

                foreach ($keywords as $kw) {
                    if (str_contains($lowerQ, $kw) && (str_contains($title, $kw) || str_contains($name, $kw))) {
                        $matchedRows[] = $ind;
                        break;
                    }
                }
            }

            // If direct match found, use matched; otherwise take top strategic indicators
            $targetRows = !empty($matchedRows) ? $matchedRows : [];

            foreach (array_slice($targetRows, 0, 3) as $ind) {
                $varId = $ind['var'] ?? $ind['indicator_id'] ?? uniqid();
                $title = $ind['title'] ?? 'Indikator BPS';
                $name = $ind['name'] ?? '';
                $val = $ind['value'] ?? '';
                $unit = $ind['unit'] ?? '';
                $period = $ind['periode'] ?? '';
                $dataSource = $ind['data_source'] ?? ("Badan Pusat Statistik (BPS) " . $domainName);

                $content = "Indikator Utama BPS Wilayah {$domainName}: {$title}\n";
                if ($name) {
                    $content .= "Deskripsi / Angka Resmi: {$name}\n";
                }
                $content .= "Nilai Capaian: {$val} {$unit}\n";
                $content .= "Periode Rilis: {$period}\n";
                $content .= "Wilayah: {$domainName} (Domain ID: {$domainId})\n";
                $content .= "Sumber Data: {$dataSource}\n";

                // Official URL
                $officialUrl = $domainUrl ?: 'https://www.bps.go.id';
                if ($domainId === '0000' && str_contains(mb_strtolower($title), 'inflasi')) {
                    $officialUrl = 'https://sirusa.web.bps.go.id/metadata/indikator/45453';
                }

                $sourceId = 'BPS-IND-' . $domainId . '-' . $varId;
                $snippet = "Indikator resmi BPS {$domainName}: {$title}. Periode: {$period}, Nilai: {$val} {$unit}. Sumber: {$dataSource}";

                $this->collectedSources[$sourceId] = [
                    'title' => $title . ' — ' . $domainName . ' (' . $period . ')',
                    'url' => $officialUrl,
                    'snippet' => $snippet,
                ];

                $sources[] = new RetrievedSource(
                    sourceId: $sourceId,
                    title: $title . ' — ' . $domainName . ' (' . $period . ')',
                    url: $officialUrl,
                    content: $content,
                    score: 0.9,
                    sourceStatus: 'OFFICIAL_BPS_API',
                    category: 'indicator'
                );
            }
        }

        return $sources;
    }

    /**
     * Look up BPS Regional Domains
     */
    private function lookupDomains(string $question): array
    {
        $resp = $this->apiClient->get('domain/model/domain', [
            'type' => 'all',
        ]);

        $sources = [];
        if ($resp->isOk && !empty($resp->rows)) {
            $lowerQ = mb_strtolower($question);
            $matched = [];
            foreach ($resp->rows as $dom) {
                $name = $dom['domain_name'] ?? '';
                if ($name !== '' && str_contains($lowerQ, mb_strtolower($name))) {
                    $matched[] = $dom;
                }
            }

            if (empty($matched)) {
                $matched = array_slice($resp->rows, 0, 3);
            }

            foreach (array_slice($matched, 0, 3) as $dom) {
                $domId = $dom['domain_id'] ?? ('DOM-' . uniqid());
                $name = $dom['domain_name'] ?? 'BPS Wilayah';
                $url = $dom['domain_url'] ?? 'https://www.bps.go.id';

                $sourceId = 'DOM-' . $domId;
                $this->collectedSources[$sourceId] = [
                    'title' => 'Portal BPS ' . $name,
                    'url' => $url,
                    'snippet' => "Portal Layanan Statistik Resmi BPS {$name}: {$url}",
                ];

                $sources[] = new RetrievedSource(
                    sourceId: $sourceId,
                    title: 'BPS ' . $name,
                    url: $url,
                    content: "Portal Layanan Data BPS Wilayah {$name}: {$url}",
                    score: 0.8,
                    sourceStatus: 'OFFICIAL_BPS_API',
                    category: 'domain'
                );
            }
        }

        return $sources;
    }
}
