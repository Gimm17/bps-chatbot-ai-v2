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
     * Execute BPS Agent workflow for live statistical intents.
     */
    public function run(string $question, string $intent): ?ChatResult
    {
        $this->clearSources();

        $evidenceSources = [];

        try {
            if ($intent === 'publication' || str_contains(mb_strtolower($question), 'publikasi')) {
                $evidenceSources = $this->lookupPublications($question);
            } elseif ($intent === 'numeric_statistic' || $intent === 'metadata_methodology') {
                $evidenceSources = $this->lookupNumericOrMetadata($question);
            } elseif ($intent === 'navigation') {
                $evidenceSources = $this->lookupDomains($question);
            }
        } catch (\Throwable $e) {
            Log::warning('BpsAgent tool execution failed: '.$e->getMessage());
        }

        if (empty($evidenceSources)) {
            // Return null so ChatService falls back to local knowledge base
            return null;
        }

        // Build prompt with live BPS evidence
        $systemPrompt = $this->promptBuilder->build($evidenceSources);

        $input = new ChatProviderInput(
            systemPrompt: $systemPrompt,
            userMessage: $question
        );

        $output = $this->aiProvider->chat($input);
        $result = ChatResult::parse($output->text);

        // If the model gave no_evidence but we collected valid official sources, attempt synthesis retry (Solve LIMITATIONS.md item B & C)
        if ($result->status === 'no_evidence' && ! empty($this->collectedSources)) {
            $retryPrompt = $systemPrompt."\n\nPERINGATAN: Sumber resmi BPS berikut berhasil ditemukan. Rangkumkan jawaban dari data ini:\n";
            $retryInput = new ChatProviderInput(
                systemPrompt: $retryPrompt,
                userMessage: 'Tolong rangkum data BPS yang ditemukan untuk pertanyaan: '.$question
            );
            $retryOutput = $this->aiProvider->chat($retryInput);
            $retryResult = ChatResult::parse($retryOutput->text);

            if ($retryResult->status === 'answered' && ! empty($retryResult->answer)) {
                return $retryResult;
            }
        }

        return $result;
    }

    private function lookupPublications(string $question): array
    {
        // Extract keyword
        $keyword = preg_replace('/(cari|lihat|tampilkan|daftar|publikasi|bps|tentang|terbaru)/i', '', $question);
        $keyword = trim($keyword);
        if ($keyword === '') {
            $keyword = 'Statistik';
        }

        $resp = $this->apiClient->get('list/model/publication', [
            'domain' => '0000',
            'keyword' => $keyword,
            'page' => 1,
        ]);

        $sources = [];
        if ($resp->isOk && ! empty($resp->rows)) {
            foreach (array_slice($resp->rows, 0, 4) as $pub) {
                $pubId = $pub['pub_id'] ?? $pub['id'] ?? ('PUB-'.uniqid());
                $title = $pub['title'] ?? 'Publikasi BPS';
                $abstract = $pub['abstract'] ?? 'Publikasi resmi Badan Pusat Statistik.';
                $pdfUrl = $pub['pdf'] ?? null;
                $rlDate = $pub['rl_date'] ?? null;

                $content = "Judul Publikasi: {$title}\n";
                if ($rlDate) {
                    $content .= "Tanggal Rilis: {$rlDate}\n";
                }
                $content .= "Abstraksi: {$abstract}\n";
                if ($pdfUrl) {
                    $content .= "Tautan Unduh PDF: {$pdfUrl}\n";
                }

                $sourceId = (string) $pubId;
                $this->collectedSources[$sourceId] = [
                    'title' => $title,
                    'url' => $pdfUrl,
                    'snippet' => mb_substr(strip_tags($abstract), 0, 150).'...',
                ];

                $sources[] = new RetrievedSource(
                    sourceId: $sourceId,
                    title: $title,
                    url: $pdfUrl,
                    content: $content,
                    score: 1.0,
                    sourceStatus: 'OFFICIAL_BPS_API',
                    category: 'publication'
                );
            }
        }

        return $sources;
    }

    private function lookupNumericOrMetadata(string $question): array
    {
        // Check domains and indicators
        $resp = $this->apiClient->get('list/model/indicators', [
            'domain' => '0000',
            'page' => 1,
        ]);

        $sources = [];
        if ($resp->isOk && ! empty($resp->rows)) {
            foreach (array_slice($resp->rows, 0, 3) as $ind) {
                $varId = $ind['var_id'] ?? $ind['id'] ?? ('IND-'.uniqid());
                $title = $ind['title'] ?? $ind['name'] ?? 'Indikator BPS';
                $unit = $ind['unit'] ?? '-';

                $content = "Indikator Strategis: {$title}\nSatuan: {$unit}\n";
                $sourceId = 'IND-'.$varId;

                $this->collectedSources[$sourceId] = [
                    'title' => $title,
                    'url' => 'https://www.bps.go.id',
                    'snippet' => "Indikator resmi BPS dengan satuan: {$unit}",
                ];

                $sources[] = new RetrievedSource(
                    sourceId: $sourceId,
                    title: $title,
                    url: 'https://www.bps.go.id',
                    content: $content,
                    score: 0.9,
                    sourceStatus: 'OFFICIAL_BPS_API',
                    category: 'indicator'
                );
            }
        }

        return $sources;
    }

    private function lookupDomains(string $question): array
    {
        $resp = $this->apiClient->get('domain/model/domain', [
            'type' => 'all',
        ]);

        $sources = [];
        if ($resp->isOk && ! empty($resp->rows)) {
            $lowerQ = mb_strtolower($question);
            $matched = [];
            foreach ($resp->rows as $dom) {
                $name = $dom['domain_name'] ?? '';
                if ($name !== '' && str_contains($lowerQ, mb_strtolower($name))) {
                    $matched[] = $dom;
                }
            }

            if (empty($matched)) {
                $matched = array_slice($resp->rows, 0, 4);
            }

            foreach (array_slice($matched, 0, 4) as $dom) {
                $domId = $dom['domain_id'] ?? ('DOM-'.uniqid());
                $name = $dom['domain_name'] ?? 'BPS Wilayah';
                $url = $dom['domain_url'] ?? 'https://www.bps.go.id';

                $sourceId = 'DOM-'.$domId;
                $this->collectedSources[$sourceId] = [
                    'title' => $name,
                    'url' => $url,
                    'snippet' => "Portal BPS Wilayah: {$name} ({$url})",
                ];

                $sources[] = new RetrievedSource(
                    sourceId: $sourceId,
                    title: $name,
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
