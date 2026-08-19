<?php

namespace App\Bps;

use App\Ai\AiProviderInterface;
use App\Ai\ChatProviderInput;
use App\Ai\ChatResult;
use App\Ai\PromptBuilder;
use App\Rag\RetrievedSource;
use Illuminate\Support\Facades\Log;

class BpsAgent
{
    /**
     * @var array Map of [sourceId => ['title' => ..., 'url' => ..., 'snippet' => ...]]
     */
    private array $collectedSources = [];

    public function __construct(
        private readonly BpsApiClient $apiClient,
        private readonly AiProviderInterface $aiProvider,
        private readonly PromptBuilder $promptBuilder
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
            // 1. Search Live BPS API Indicators
            $indicatorSources = $this->lookupLiveIndicators($question);
            $evidenceSources = array_merge($evidenceSources, $indicatorSources);

            // 2. Search Live BPS Publications
            $pubSources = $this->lookupPublications($question);
            $evidenceSources = array_merge($evidenceSources, $pubSources);

            // 3. Search BPS Domains if relevant
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
     * Look up live national indicators from BPS WebAPI (e.g. Inflasi, PDRB, Kemiskinan, Pengangguran, dll)
     */
    private function lookupLiveIndicators(string $question): array
    {
        $resp = $this->apiClient->get('list/model/indicators', [
            'domain' => '0000',
            'page' => 1,
        ]);

        $sources = [];
        if ($resp->isOk && !empty($resp->rows)) {
            $lowerQ = mb_strtolower($question);
            $keywords = ['inflasi', 'ihk', 'harga', 'ekonomi', 'pertumbuhan', 'pdb', 'pdrb', 'miskin', 'kemiskinan', 'pengangguran', 'tpt', 'tenaga kerja', 'penduduk'];

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
                $dataSource = $ind['data_source'] ?? 'Badan Pusat Statistik (BPS)';

                $content = "Indikator Resmi BPS: {$title}\n";
                if ($name) {
                    $content .= "Deskripsi / Angka Resmi: {$name}\n";
                }
                $content .= "Nilai Capaian: {$val} {$unit}\n";
                $content .= "Periode Rilis: {$period}\n";
                $content .= "Sumber Data: {$dataSource}\n";

                // Official URL to SIRuSa or BPS Portal
                $officialUrl = 'https://sirusa.web.bps.go.id/metadata/indikator/' . ($ind['indicator_id'] ?? '45453');
                if (str_contains(mb_strtolower($title), 'inflasi')) {
                    $officialUrl = 'https://sirusa.web.bps.go.id/metadata/indikator/45453';
                }

                $sourceId = 'BPS-IND-' . $varId;
                $snippet = "Indikator resmi BPS: {$title}. Periode: {$period}, Nilai: {$val} {$unit}. Sumber: {$dataSource}";

                $this->collectedSources[$sourceId] = [
                    'title' => $title . ' (' . $period . ')',
                    'url' => $officialUrl,
                    'snippet' => $snippet,
                ];

                $sources[] = new RetrievedSource(
                    sourceId: $sourceId,
                    title: $title . ' — ' . $period,
                    url: $officialUrl,
                    content: $content,
                    score: 1.0,
                    sourceStatus: 'OFFICIAL_BPS_API',
                    category: 'indicator'
                );
            }
        }

        return $sources;
    }

    /**
     * Look up live publications from BPS WebAPI
     */
    private function lookupPublications(string $question): array
    {
        // Extract clean search keyword
        $cleanQ = preg_replace('/(apa|itu|bagaimana|cara|cari|lihat|tampilkan|daftar|publikasi|bps|tentang|terbaru|data|info|informasi|dan|di|indonesia|tolong|mohon|seputar)/i', ' ', $question);
        $cleanQ = trim(preg_replace('/\s+/', ' ', $cleanQ));
        if ($cleanQ === '' || mb_strlen($cleanQ) < 3) {
            $cleanQ = 'Statistik';
        }

        $resp = $this->apiClient->get('list/model/publication', [
            'domain' => '0000',
            'keyword' => $cleanQ,
            'page' => 1,
        ]);

        $sources = [];
        if ($resp->isOk && !empty($resp->rows)) {
            foreach (array_slice($resp->rows, 0, 3) as $pub) {
                $pubId = $pub['pub_id'] ?? $pub['id'] ?? ('PUB-' . uniqid());
                $title = $pub['title'] ?? 'Publikasi BPS';
                $abstract = $pub['abstract'] ?? 'Publikasi statistik resmi Badan Pusat Statistik.';
                $pdfUrl = $pub['pdf'] ?? null;
                $rlDate = $pub['rl_date'] ?? null;

                $content = "Judul Publikasi Resmi BPS: {$title}\n";
                if ($rlDate) {
                    $content .= "Tanggal Rilis Resmi: {$rlDate}\n";
                }
                $content .= "Abstraksi / Ringkasan: " . strip_tags($abstract) . "\n";
                if ($pdfUrl) {
                    $content .= "Tautan Unduh Dokumen PDF Resmi: {$pdfUrl}\n";
                }

                $sourceId = (string) $pubId;
                $this->collectedSources[$sourceId] = [
                    'title' => $title,
                    'url' => $pdfUrl ?? 'https://www.bps.go.id/id/publication',
                    'snippet' => mb_substr(strip_tags($abstract), 0, 160) . '...',
                ];

                $sources[] = new RetrievedSource(
                    sourceId: $sourceId,
                    title: $title,
                    url: $pdfUrl ?? 'https://www.bps.go.id/id/publication',
                    content: $content,
                    score: 0.95,
                    sourceStatus: 'OFFICIAL_BPS_API',
                    category: 'publication'
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
