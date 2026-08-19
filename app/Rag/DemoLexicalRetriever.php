<?php

namespace App\Rag;

class DemoLexicalRetriever implements RetrieverInterface
{
    private const MIN_SCORE_THRESHOLD = 0.6;

    private const TITLE_WEIGHT = 3.0;

    private const BODY_WEIGHT = 1.0;

    private const PHRASE_BONUS = 4.0;

    /**
     * Indonesian basic stopwords to filter out during scoring
     */
    private const STOPWORDS = [
        'yang', 'di', 'dan', 'ke', 'dari', 'ini', 'itu', 'untuk', 'pada', 'adalah',
        'sebagai', 'dengan', 'atau', 'dalam', 'bisa', 'ada', 'apa', 'bagaimana', 'saya',
        'kamu', 'dia', 'mereka', 'kita', 'kami', 'secara', 'oleh', 'jika', 'apakah',
        'dimana', 'kapan', 'mengapa', 'berapa', 'tentang', 'seputar', 'tolong', 'jelaskan',
        'beri', 'tahu', 'informasi', 'data', 'mohon',
    ];

    public function __construct(
        private readonly KnowledgeLoader $loader
    ) {}

    /**
     * @return RetrievedSource[]
     */
    public function retrieve(string $question, int $topK = 4): array
    {
        $sources = $this->loader->loadAll();
        if (empty($sources)) {
            return [];
        }

        $query = mb_strtolower(trim($question), 'UTF-8');
        $queryTokens = $this->tokenize($query);

        if (empty($queryTokens)) {
            return [];
        }

        $scored = [];

        foreach ($sources as $source) {
            $score = $this->calculateScore($query, $queryTokens, $source);

            if ($score >= self::MIN_SCORE_THRESHOLD) {
                $scored[] = new RetrievedSource(
                    sourceId: $source->sourceId,
                    title: $source->title,
                    url: $source->url,
                    content: $source->content,
                    score: round($score, 3),
                    sourceStatus: $source->sourceStatus,
                    category: $source->category
                );
            }
        }

        // Sort descending by score
        usort($scored, fn (RetrievedSource $a, RetrievedSource $b) => $b->score <=> $a->score);

        return array_slice($scored, 0, $topK);
    }

    private function calculateScore(string $rawQuery, array $queryTokens, RetrievedSource $source): float
    {
        $title = mb_strtolower($source->title, 'UTF-8');
        $body = mb_strtolower($source->content, 'UTF-8');

        $score = 0.0;

        // Exact phrase matching bonus
        if (mb_strpos($title, $rawQuery) !== false) {
            $score += self::PHRASE_BONUS * 2.0;
        } elseif (mb_strpos($body, $rawQuery) !== false) {
            $score += self::PHRASE_BONUS;
        }

        // Token matching
        foreach ($queryTokens as $token) {
            $tokenLen = mb_strlen($token);
            if ($tokenLen < 2) {
                continue;
            }

            // Title matches (high value)
            if (mb_strpos($title, $token) !== false) {
                $score += self::TITLE_WEIGHT;
            }

            // Body occurrences
            $count = mb_substr_count($body, $token);
            if ($count > 0) {
                // Diminishing returns for word frequency
                $score += self::BODY_WEIGHT * min(3.0, 1.0 + (log($count) * 0.5));
            }
        }

        return $score;
    }

    private function tokenize(string $text): array
    {
        // Replace punctuation with spaces
        $cleaned = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);
        $words = preg_split('/\s+/u', $cleaned, -1, PREG_SPLIT_NO_EMPTY);

        $tokens = [];
        foreach ($words as $word) {
            $word = trim($word);
            if ($word === '' || in_array($word, self::STOPWORDS, true)) {
                continue;
            }
            $tokens[] = $word;
        }

        return $tokens;
    }
}
