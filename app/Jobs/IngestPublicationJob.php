<?php

namespace App\Jobs;

use App\Bps\PublicationIndexer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class IngestPublicationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $pubId,
        public string $pdfUrl,
        public string $title,
        public string $domainId,
        public string $domainName,
        public ?string $rlDate = null,
        public ?string $portalUrl = null,
        public ?string $abstract = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(PublicationIndexer $indexer): void
    {
        Log::info("Starting background ingestion for publication {$this->pubId}: {$this->title}");

        $result = $indexer->indexFromUrl(
            pubId: $this->pubId,
            pdfUrl: $this->pdfUrl,
            title: $this->title,
            domainId: $this->domainId,
            domainName: $this->domainName,
            rlDate: $this->rlDate,
            portalUrl: $this->portalUrl,
            abstract: $this->abstract
        );

        if ($result && $result->status === 'completed') {
            Log::info("Successfully indexed publication {$this->pubId} ({$result->page_count} pages, {$result->file_size_kb} KB)");
        } else {
            Log::warning("Background indexing failed or skipped for {$this->pubId}");
        }
    }
}
