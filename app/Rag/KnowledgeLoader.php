<?php

namespace App\Rag;

use Illuminate\Support\Facades\File;

class KnowledgeLoader
{
    /**
     * @var RetrievedSource[]|null
     */
    private ?array $cache = null;

    public function __construct(
        private readonly string $knowledgePath = ''
    ) {}

    public function getKnowledgePath(): string
    {
        return $this->knowledgePath !== ''
            ? $this->knowledgePath
            : base_path('data/knowledge');
    }

    /**
     * Load and parse all Markdown files with YAML frontmatter.
     *
     * @return RetrievedSource[]
     */
    public function loadAll(): array
    {
        if ($this->cache !== null) {
            return $this->cache;
        }

        $path = $this->getKnowledgePath();
        if (! File::isDirectory($path)) {
            return [];
        }

        $files = File::files($path);
        $sources = [];

        foreach ($files as $file) {
            if ($file->getExtension() !== 'md') {
                continue;
            }

            $content = File::get($file->getRealPath());
            $parsed = $this->parseFrontmatter($content);

            if ($parsed !== null) {
                $sources[] = $parsed;
            }
        }

        $this->cache = $sources;

        return $sources;
    }

    /**
     * Clear loaded cache.
     */
    public function clearCache(): void
    {
        $this->cache = null;
    }

    /**
     * Parse YAML frontmatter from markdown content.
     */
    private function parseFrontmatter(string $rawContent): ?RetrievedSource
    {
        $pattern = '/^---\s*\r?\n(.*?)\r?\n---\s*\r?\n(.*)$/s';

        if (! preg_match($pattern, $rawContent, $matches)) {
            return null;
        }

        $frontmatterBlock = $matches[1];
        $body = trim($matches[2]);

        $meta = [];
        foreach (explode("\n", $frontmatterBlock) as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            if (str_contains($line, ':')) {
                [$key, $value] = explode(':', $line, 2);
                $meta[trim($key)] = trim($value);
            }
        }

        $id = $meta['id'] ?? ('SRC-'.md5($body));
        $title = $meta['title'] ?? 'Dokumen BPS';
        $url = ! empty($meta['source_url']) ? $meta['source_url'] : null;
        $status = $meta['source_status'] ?? 'DEMO_NOT_VERIFIED';
        $category = $meta['category'] ?? 'general';

        return new RetrievedSource(
            sourceId: $id,
            title: $title,
            url: $url,
            content: $body,
            score: 0.0,
            sourceStatus: $status,
            category: $category
        );
    }
}
