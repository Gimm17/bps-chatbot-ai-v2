<?php

namespace App\Console\Commands;

use App\Bps\BpsApiClient;
use App\Bps\PublicationIndexer;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class BpsIndexPublicationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bps:index-publications 
                            {--domain=0000 : BPS domain ID (e.g. 7200 for Sulteng, 0000 for Pusat)}
                            {--keyword=kependudukan : Search keyword (e.g. kependudukan, inflasi, pdrb)}
                            {--limit=3 : Maximum number of publications to index}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download, parse, and index BPS publication PDFs into local knowledge database';

    /**
     * Execute the console command.
     */
    public function handle(BpsApiClient $client, PublicationIndexer $indexer): int
    {
        $domainId = (string) $this->option('domain');
        $keyword = (string) $this->option('keyword');
        $limit = (int) $this->option('limit');

        $this->info("Fetching publications from BPS WebAPI (Domain: {$domainId}, Keyword: {$keyword})...");

        // Resolve domain name and URL
        $domainName = 'Pusat / Nasional';
        $domainUrl = 'https://www.bps.go.id';
        if ($domainId !== '0000') {
            $domResp = $client->get('domain/model/domain', ['type' => 'all']);
            if ($domResp->isOk && !empty($domResp->rows)) {
                foreach ($domResp->rows as $d) {
                    if (($d['domain_id'] ?? '') === $domainId) {
                        $domainName = $d['domain_name'] ?? $domainName;
                        $domainUrl = $d['domain_url'] ?? $domainUrl;
                        break;
                    }
                }
            }
        }

        $resp = $client->get('list/model/publication', [
            'domain' => $domainId,
            'keyword' => $keyword,
            'page' => 1,
        ]);

        if (! $resp->isOk || empty($resp->rows)) {
            $this->error("No publications found or error: " . $resp->errorMessage);
            return self::FAILURE;
        }

        $this->info("Found " . count($resp->rows) . " publications. Starting download & extraction (Limit: {$limit})...");

        $count = 0;
        foreach (array_slice($resp->rows, 0, $limit) as $pub) {
            $pubId = $pub['pub_id'] ?? $pub['id'] ?? null;
            $title = $pub['title'] ?? 'Publikasi BPS';
            $pdfUrl = $pub['pdf'] ?? null;
            $rlDate = $pub['rl_date'] ?? null;
            $abstract = $pub['abstract'] ?? null;

            if (! $pubId || ! $pdfUrl) {
                continue;
            }

            // Construct Portal URL
            $portalUrl = $domainUrl;
            if (!empty($rlDate) && !empty($pubId) && !empty($title)) {
                $parts = explode('-', $rlDate);
                if (count($parts) === 3) {
                    $slug = Str::slug($title);
                    $base = rtrim($domainUrl, '/');
                    $portalUrl = "{$base}/id/publication/{$parts[0]}/{$parts[1]}/{$parts[2]}/{$pubId}/{$slug}.html";
                }
            }

            $this->output->write("Indexing: <comment>{$title}</comment> ... ");

            $result = $indexer->indexFromUrl(
                pubId: $pubId,
                pdfUrl: $pdfUrl,
                title: $title,
                domainId: $domainId,
                domainName: $domainName,
                rlDate: $rlDate,
                portalUrl: $portalUrl,
                abstract: $abstract
            );

            if ($result && $result->status === 'completed') {
                $this->info("DONE ({$result->page_count} pages, {$result->file_size_kb} KB)");
                $count++;
            } else {
                $this->error("FAILED");
            }
        }

        $this->info("Successfully indexed {$count} publication(s) into local database.");
        return self::SUCCESS;
    }
}
