<?php

namespace App\Console\Commands;

use App\Bps\BpsApiClient;
use App\Bps\PublicationIndexer;
use App\Models\PublicationIndex;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class BpsSyncNewPublicationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bps:sync-new-publications {--limit=10 : Number of latest publications to check}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync newly released publications from BPS WebAPI into local database';

    /**
     * Execute the console command.
     */
    public function handle(BpsApiClient $client, PublicationIndexer $indexer): int
    {
        $limit = (int) $this->option('limit');
        $this->info("Checking newly released publications from BPS WebAPI (National & Provinces)...");

        // Priority strategic domains: 0000 (Pusat) + selected provinces
        $domains = ['0000', '7200', '3200', '3100', '3500', '3300', '5100'];
        $synced = 0;

        foreach ($domains as $domId) {
            $resp = $client->get('list/model/publication', [
                'domain' => $domId,
                'page' => 1,
            ]);

            if (! $resp->isOk || empty($resp->rows)) {
                continue;
            }

            foreach (array_slice($resp->rows, 0, 3) as $pub) {
                $pubId = $pub['pub_id'] ?? null;
                $pdfUrl = $pub['pdf'] ?? null;
                $title = $pub['title'] ?? 'Publikasi BPS';
                $rlDate = $pub['rl_date'] ?? null;
                $abstract = $pub['abstract'] ?? null;

                if (! $pubId || ! $pdfUrl) {
                    continue;
                }

                // Check if already indexed
                if (PublicationIndex::where('id', $pubId)->where('status', 'completed')->exists()) {
                    continue;
                }

                $this->line("New publication found: {$title} (Domain: {$domId})");

                $portalUrl = "https://www.bps.go.id";
                if (!empty($rlDate) && !empty($pubId) && !empty($title)) {
                    $parts = explode('-', $rlDate);
                    if (count($parts) === 3) {
                        $slug = Str::slug($title);
                        $portalUrl = "https://www.bps.go.id/id/publication/{$parts[0]}/{$parts[1]}/{$parts[2]}/{$pubId}/{$slug}.html";
                    }
                }

                $res = $indexer->indexFromUrl(
                    pubId: $pubId,
                    pdfUrl: $pdfUrl,
                    title: $title,
                    domainId: $domId,
                    domainName: $domId === '0000' ? 'Nasional' : "Domain {$domId}",
                    rlDate: $rlDate,
                    portalUrl: $portalUrl,
                    abstract: $abstract
                );

                if ($res && $res->status === 'completed') {
                    $synced++;
                }

                if ($synced >= $limit) {
                    break 2;
                }
            }
        }

        $this->info("Sync completed. {$synced} new publication(s) indexed.");
        return self::SUCCESS;
    }
}
