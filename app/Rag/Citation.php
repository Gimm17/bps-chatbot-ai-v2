<?php

namespace App\Rag;

use JsonSerializable;

class Citation implements JsonSerializable
{
    public function __construct(
        public readonly string $sourceId,
        public readonly string $title,
        public readonly ?string $url = null,
        public readonly ?string $snippet = null,
        public readonly bool $verified = false
    ) {}

    /**
     * Map knowledge sources to citations based on LLM-selected source IDs.
     *
     * @param  RetrievedSource[]  $sources
     * @param  string[]  $selectedIds
     * @return Citation[]
     */
    public static function fromSources(array $sources, array $selectedIds = []): array
    {
        $citations = [];
        $sourceMap = [];

        foreach ($sources as $source) {
            $sourceMap[$source->sourceId] = $source;
        }

        // If specific IDs were selected by LLM, prioritize them
        $targetIds = ! empty($selectedIds) ? $selectedIds : array_keys($sourceMap);

        foreach ($targetIds as $id) {
            if (isset($sourceMap[$id])) {
                $src = $sourceMap[$id];
                $snippet = self::createSnippet($src->content);

                $citations[] = new self(
                    sourceId: $src->sourceId,
                    title: $src->title,
                    url: $src->url,
                    snippet: $snippet,
                    verified: false // Demo fallback is not verified
                );
            }
        }

        return $citations;
    }

    /**
     * Create citation from official BPS API metadata.
     *
     * @param  array  $bpsSources  Map of [sourceId => ['title' => ..., 'url' => ..., 'snippet' => ...]]
     * @param  string[]  $selectedIds
     * @return Citation[]
     */
    public static function fromBpsSources(array $bpsSources, array $selectedIds = []): array
    {
        $citations = [];
        $targetIds = ! empty($selectedIds) ? $selectedIds : array_keys($bpsSources);

        foreach ($targetIds as $id) {
            if (isset($bpsSources[$id])) {
                $meta = $bpsSources[$id];
                $citations[] = new self(
                    sourceId: (string) $id,
                    title: $meta['title'] ?? 'Sumber BPS',
                    url: $meta['url'] ?? null,
                    snippet: $meta['snippet'] ?? null,
                    verified: true // Official BPS API source
                );
            }
        }

        return $citations;
    }

    private static function createSnippet(string $content, int $length = 150): string
    {
        // Strip markdown headers and formatting
        $clean = preg_replace('/^#+\s+/m', '', $content);
        $clean = preg_replace('/[*_`~]/', '', $clean);
        $clean = trim(preg_replace('/\s+/', ' ', $clean));

        if (mb_strlen($clean) <= $length) {
            return $clean;
        }

        return mb_substr($clean, 0, $length).'...';
    }

    public function jsonSerialize(): array
    {
        return [
            'sourceId' => $this->sourceId,
            'title' => $this->title,
            'url' => $this->url,
            'snippet' => $this->snippet,
            'verified' => $this->verified,
        ];
    }
}
