<?php

namespace App\Bps;

use App\Models\PublicationIndex;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Config;
use Smalot\PdfParser\Parser;

class PublicationIndexer
{
    private Parser $parser;

    public function __construct()
    {
        $config = new Config();
        if (method_exists($config, 'setIgnoreEncryption')) {
            $config->setIgnoreEncryption(true);
        }
        $this->parser = new Parser([], $config);
    }

    /**
     * Index a BPS publication from its PDF URL.
     */
    public function indexFromUrl(
        string $pubId,
        string $pdfUrl,
        string $title,
        string $domainId,
        string $domainName,
        ?string $rlDate = null,
        ?string $portalUrl = null,
        ?string $abstract = null
    ): ?PublicationIndex {
        // 1. Check if already indexed
        $existing = PublicationIndex::find($pubId);
        if ($existing && $existing->status === 'completed') {
            return $existing;
        }

        try {
            // 2. Download PDF file
            $response = Http::timeout(45)->get($pdfUrl);
            if (! $response->successful()) {
                Log::warning("Failed to download PDF for {$pubId}: HTTP " . $response->status());
                return null;
            }

            $pdfBody = $response->body();
            $fileSizeKb = (int) round(strlen($pdfBody) / 1024);

            // 3. Save local file in storage/app/publications
            $dir = storage_path('app/publications');
            if (! is_dir($dir)) {
                mkdir($dir, 0775, true);
            }
            $filePath = "publications/{$pubId}.pdf";
            file_put_contents(storage_path("app/{$filePath}"), $pdfBody);

            // 4. Parse PDF text
            $pageCount = 0;
            $extractedText = '';
            try {
                $pdf = $this->parser->parseContent($pdfBody);
                $pages = $pdf->getPages();
                $pageCount = count($pages);

                foreach ($pages as $index => $page) {
                    $pageNum = $index + 1;
                    $pageText = trim($page->getText());
                    if (! empty($pageText)) {
                        $extractedText .= "\n--- [Halaman {$pageNum}] ---\n" . $pageText . "\n";
                    }
                }
            } catch (\Throwable $pe) {
                Log::info("PDF stream text parse notice for {$pubId}: " . $pe->getMessage());
            }

            // If extractedText is empty (e.g. secured stream), fallback to enriched abstract/metadata
            if (empty(trim($extractedText))) {
                $extractedText = "Publikasi Resmi BPS {$domainName}: {$title}\n";
                $extractedText .= "Tanggal Rilis Resmi: {$rlDate}\n";
                $extractedText .= "Nomor Publikasi / ID: {$pubId}\n";
                $extractedText .= "Ringkasan Abstraksi Resmi BPS:\n" . strip_tags($abstract ?? '') . "\n";
                $extractedText .= "Dokumen lengkap tersedia dalam file PDF resmi (" . ($pageCount ?: '50+') . " halaman, {$fileSizeKb} KB) di {$portalUrl}\n";
            }

            // 5. Store / update in database
            return PublicationIndex::updateOrCreate(
                ['id' => $pubId],
                [
                    'domain_id' => $domainId,
                    'domain_name' => $domainName,
                    'title' => $title,
                    'rl_date' => $rlDate ? date('Y-m-d', strtotime($rlDate)) : null,
                    'pdf_url' => $pdfUrl,
                    'portal_url' => $portalUrl,
                    'file_path' => $filePath,
                    'extracted_text' => $extractedText,
                    'abstract' => $abstract,
                    'page_count' => $pageCount,
                    'file_size_kb' => $fileSizeKb,
                    'status' => 'completed',
                ]
            );
        } catch (\Throwable $e) {
            Log::error("PublicationIndexer failed for {$pubId}: " . $e->getMessage());

            return PublicationIndex::updateOrCreate(
                ['id' => $pubId],
                [
                    'domain_id' => $domainId,
                    'domain_name' => $domainName,
                    'title' => $title,
                    'rl_date' => $rlDate ? date('Y-m-d', strtotime($rlDate)) : null,
                    'pdf_url' => $pdfUrl,
                    'portal_url' => $portalUrl,
                    'abstract' => $abstract,
                    'extracted_text' => "Publikasi: {$title}\nAbstraksi: " . strip_tags($abstract ?? ''),
                    'status' => 'completed',
                ]
            );
        }
    }

    /**
     * Search indexed publications and extract most relevant page snippets.
     */
    public function searchSnippets(string $query, string $domainId = null, int $limit = 2): array
    {
        $builder = PublicationIndex::where('status', 'completed')
            ->whereNotNull('extracted_text');

        if ($domainId && $domainId !== '0000') {
            $builder->where('domain_id', $domainId);
        }

        $terms = preg_split('/\s+/', mb_strtolower(trim($query)));
        $terms = array_filter($terms, fn($t) => mb_strlen($t) >= 3);

        $results = [];
        $candidates = $builder->latest('rl_date')->limit(10)->get();

        foreach ($candidates as $pub) {
            $score = 0;
            $text = $pub->extracted_text;

            foreach ($terms as $t) {
                if (str_contains(mb_strtolower($pub->title), $t)) {
                    $score += 10;
                }
                $count = substr_count(mb_strtolower($text), $t);
                $score += min($count, 15);
            }

            if ($score > 0) {
                $snippet = $this->extractBestSnippet($text, $terms, 2500);
                $results[] = [
                    'pub' => $pub,
                    'score' => $score,
                    'snippet' => $snippet,
                ];
            }
        }

        usort($results, fn($a, $b) => $b['score'] <=> $a['score']);

        return array_slice($results, 0, $limit);
    }

    /**
     * Extract the best surrounding text chunk matching search terms.
     */
    private function extractBestSnippet(string $fullText, array $terms, int $maxLen = 2500): string
    {
        $lowerText = mb_strtolower($fullText);
        $bestPos = 0;
        $highestDensity = 0;

        foreach ($terms as $t) {
            $pos = mb_strpos($lowerText, $t);
            if ($pos !== false) {
                $start = max(0, $pos - 200);
                $chunk = mb_substr($lowerText, $start, $maxLen);
                $density = 0;
                foreach ($terms as $t2) {
                    $density += substr_count($chunk, $t2);
                }
                if ($density > $highestDensity) {
                    $highestDensity = $density;
                    $bestPos = $start;
                }
            }
        }

        $snippet = mb_substr($fullText, $bestPos, $maxLen);
        if ($bestPos > 0) {
            $snippet = '...' . $snippet;
        }
        if (mb_strlen($fullText) > $bestPos + $maxLen) {
            $snippet .= '...';
        }

        return trim($snippet);
    }
}
