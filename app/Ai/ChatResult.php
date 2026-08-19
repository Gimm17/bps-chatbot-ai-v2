<?php

namespace App\Ai;

class ChatResult
{
    public function __construct(
        public readonly string $status = 'answered',
        public readonly ?string $answer = null,
        public readonly ?string $clarificationQuestion = null,
        public readonly array $citationSourceIds = [],
        public readonly ?string $rawText = null
    ) {}

    /**
     * Parse raw LLM output into structured ChatResult.
     */
    public static function parse(string $rawOutput): self
    {
        $trimmed = trim($rawOutput);

        if ($trimmed === '') {
            return new self(
                status: 'no_evidence',
                answer: 'Saya belum menemukan sumber BPS yang cukup untuk menjawab pertanyaan tersebut.',
                rawText: $rawOutput
            );
        }

        // Try extracting JSON from potential markdown code fences
        $jsonCandidate = self::stripCodeFences($trimmed);

        $decoded = json_decode($jsonCandidate, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $status = $decoded['status'] ?? 'answered';
            $answer = $decoded['answer'] ?? null;
            $clarification = $decoded['clarificationQuestion'] ?? $decoded['clarification_question'] ?? null;
            $citationIds = $decoded['citationSourceIds'] ?? $decoded['citation_source_ids'] ?? $decoded['citations'] ?? [];

            // If answer contains nested JSON code fence, clean it
            if ($answer !== null) {
                $answer = self::cleanAnswerText($answer);
            }

            // Normalise status
            $validStatuses = ['answered', 'clarification_required', 'no_evidence', 'out_of_scope', 'rate_limited', 'provider_error'];
            if (! in_array($status, $validStatuses, true)) {
                $status = 'answered';
            }

            // Ensure citation IDs is list of strings
            $cleanCitationIds = [];
            if (is_array($citationIds)) {
                foreach ($citationIds as $item) {
                    if (is_string($item) && $item !== '') {
                        $cleanCitationIds[] = trim($item);
                    } elseif (is_array($item) && isset($item['sourceId'])) {
                        $cleanCitationIds[] = trim($item['sourceId']);
                    }
                }
            }

            return new self(
                status: $status,
                answer: $answer,
                clarificationQuestion: $clarification,
                citationSourceIds: array_values(array_unique($cleanCitationIds)),
                rawText: $rawOutput
            );
        }

        // Fallback: If model returned plain text instead of JSON
        $cleanText = self::cleanAnswerText($trimmed);

        // Check if plain text looks like a clarification request
        if (preg_match('/(wilayah|tahun|periode|provinsi|kabupaten)\s+(mana|apa|yang\s+mana)/i', $cleanText)) {
            return new self(
                status: 'clarification_required',
                clarificationQuestion: $cleanText,
                rawText: $rawOutput
            );
        }

        // Check for inline source IDs like [SOURCE:SRC-001] or [SRC-001]
        preg_match_all('/\[(?:SOURCE:)?(SRC-[A-Za-z0-9_-]+)\]/i', $cleanText, $sourceMatches);
        $extractedCitationIds = ! empty($sourceMatches[1]) ? array_values(array_unique($sourceMatches[1])) : [];

        return new self(
            status: 'answered',
            answer: $cleanText,
            citationSourceIds: $extractedCitationIds,
            rawText: $rawOutput
        );
    }

    /**
     * Aggressively strip markdown code blocks (```json ... ```)
     */
    private static function stripCodeFences(string $text): string
    {
        $text = trim($text);

        // Strip ```json or ``` at beginning and end
        if (preg_match('/^```(?:json)?\s*\r?\n?(.*?)\r?\n?```$/s', $text, $matches)) {
            return trim($matches[1]);
        }

        // Find first { and last }
        $firstBrace = strpos($text, '{');
        $lastBrace = strrpos($text, '}');

        if ($firstBrace !== false && $lastBrace !== false && $lastBrace > $firstBrace) {
            return trim(substr($text, $firstBrace, $lastBrace - $firstBrace + 1));
        }

        return $text;
    }

    /**
     * Clean answer text from raw leftover JSON fences or formatting glitches
     */
    private static function cleanAnswerText(string $text): string
    {
        $text = trim($text);

        // If the answer is literally wrapped in ```json ... ```, strip it
        if (preg_match('/^```(?:json)?\s*\r?\n?(.*?)\r?\n?```$/s', $text, $matches)) {
            $inner = trim($matches[1]);
            $decodedInner = json_decode($inner, true);
            if (is_array($decodedInner) && isset($decodedInner['answer'])) {
                return trim($decodedInner['answer']);
            }
        }

        return $text;
    }
}
