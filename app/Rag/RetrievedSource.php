<?php

namespace App\Rag;

class RetrievedSource
{
    public function __construct(
        public readonly string $sourceId,
        public readonly string $title,
        public readonly ?string $url,
        public readonly string $content,
        public readonly float $score = 0.0,
        public readonly string $sourceStatus = 'DEMO_NOT_VERIFIED',
        public readonly string $category = 'general'
    ) {}
}
