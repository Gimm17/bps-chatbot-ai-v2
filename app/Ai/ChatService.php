<?php

namespace App\Ai;

use App\Bps\BpsAgent;
use App\Rag\Citation;
use App\Rag\RetrieverInterface;
use Illuminate\Support\Facades\Log;

class ChatService
{
    public function __construct(
        private readonly AiProviderInterface $provider,
        private readonly RetrieverInterface $retriever,
        private readonly ScopeGuard $scopeGuard,
        private readonly PromptBuilder $promptBuilder,
        private readonly ?BpsAgent $bpsAgent = null
    ) {}

    /**
     * Process an incoming user message and return a safe normalized ChatResponse.
     *
     * @param  string       $userMessage  Current message from user.
     * @param  string|null  $conversationId  Optional session identifier.
     * @param  array        $history      Previous turns: [['role'=>'user','content'=>'...'], ...]
     */
    public function handle(string $userMessage, ?string $conversationId = null, array $history = []): ChatResponse
    {
        $message = trim($userMessage);

        // Build a "context-enriched" version of the message by resolving entities
        // from recent history (geography, period, topic), so ScopeGuard sees them.
        $enrichedMessage = $this->enrichMessageFromHistory($message, $history);

        // Step 1: Scope Classification (use enriched message so missing params resolve)
        $decision = $this->scopeGuard->classify($enrichedMessage);

        // Handle Greeting — use original message to not inflate greetings with injected context
        if ($decision->inScope && $decision->intent === 'greeting') {
            $greeting = $this->promptBuilder->getGreetingResponse();

            return ChatResponse::create(
                status: $greeting->status,
                answer: $greeting->answer,
                citations: []
            );
        }

        // Handle Out of Scope
        if (! $decision->inScope) {
            $oos = $this->promptBuilder->getOutOfScopeResponse();

            return ChatResponse::create(
                status: 'out_of_scope',
                answer: $oos->answer,
                citations: []
            );
        }

        // Handle Clarification Required
        if (! empty($decision->missing)) {
            $clarification = $this->promptBuilder->getClarificationResponse($decision->missing);

            return ChatResponse::create(
                status: 'clarification_required',
                clarificationQuestion: $clarification->clarificationQuestion,
                citations: []
            );
        }

        // Step 2: Attempt BPS Live API Agent (if enabled and applicable)
        // Use enriched message so BpsAgent also sees resolved geography/period.
        if ($this->shouldUseBpsAgent() && $decision->intent !== 'institutional_profile') {
            try {
                $bpsResult = $this->bpsAgent?->run($enrichedMessage, $decision->intent);
                if ($bpsResult !== null && $bpsResult->status === 'answered') {
                    $collectedSources = $this->bpsAgent?->getCollectedSources() ?? [];
                    $citations = Citation::fromBpsSources($collectedSources, $bpsResult->citationSourceIds);

                    return ChatResponse::create(
                        status: $bpsResult->status,
                        answer: $bpsResult->answer,
                        clarificationQuestion: $bpsResult->clarificationQuestion,
                        citations: $citations
                    );
                }
            } catch (\Throwable $e) {
                Log::warning('BpsAgent live path failed, proceeding to lexical fallback: '.$e->getMessage());
            }
        }

        // Step 3: Local Knowledge RAG Fallback
        $sources = $this->retriever->retrieve($enrichedMessage, 4);

        if (empty($sources)) {
            return ChatResponse::create(
                status: 'no_evidence',
                answer: 'Saya belum menemukan sumber data atau publikasi BPS yang cukup untuk menjawab pertanyaan tersebut secara pasti. Silakan coba sebutkan indikator, istilah statistik, atau periode spesifik yang Anda maksud.',
                citations: []
            );
        }

        // Step 4: Build Prompt with Retrieved Evidence
        $systemPrompt = $this->promptBuilder->build($sources);

        // Prepare LLM conversation history (max 10 turns = 5 pairs) for coherent multi-turn dialogue.
        $llmHistory = $this->buildLlmHistory($history, 10);

        $input = new ChatProviderInput(
            systemPrompt: $systemPrompt,
            userMessage: $enrichedMessage,
            conversationHistory: $llmHistory
        );

        try {
            $output = $this->provider->chat($input);
            $result = ChatResult::parse($output->text);

            $citations = Citation::fromSources($sources, $result->citationSourceIds);

            return ChatResponse::create(
                status: $result->status,
                answer: $result->answer,
                clarificationQuestion: $result->clarificationQuestion,
                citations: $citations
            );
        } catch (\Throwable $e) {
            Log::error('ChatService provider execution failed: '.$e->getMessage());

            return ChatResponse::create(
                status: 'provider_error',
                answer: 'Layanan AI sedang tidak dapat dihubungi. Silakan coba kembali beberapa saat lagi.',
                citations: []
            );
        }
    }

    /**
     * Enrich the current user message with resolved context from recent history.
     *
     * If the user says just "inflasi" but earlier said "data sulteng" → "Provinsi Sulawesi Tengah",
     * we prepend the resolved context so downstream (ScopeGuard, BpsAgent, RAG) understands.
     *
     * Strategy: extract the last mentioned geography, period, and topic from history,
     * then if current message is very short (likely a follow-up), append them.
     */
    private function enrichMessageFromHistory(string $message, array $history): string
    {
        if (empty($history)) {
            return $message;
        }

        // Only enrich if the current message is a short follow-up (< 5 words, no geography/period)
        $wordCount = str_word_count($message);
        $messageLower = mb_strtolower($message, 'UTF-8');

        // Check if current message already has geography or period info
        $hasGeography = $this->scopeGuard->hasGeographyEntity($messageLower);
        $hasPeriod    = $this->scopeGuard->hasPeriodEntity($messageLower);

        // Take last 6 turns of history to scan for context
        $recentHistory = array_slice($history, -6);

        // Build a combined context string from all recent messages
        $historyText = implode(' ', array_map(fn($turn) => $turn['content'] ?? '', $recentHistory));
        $historyLower = mb_strtolower($historyText, 'UTF-8');

        $contextParts = [];

        // Resolve geography from history if missing in current message
        if (! $hasGeography) {
            $resolvedGeo = $this->scopeGuard->extractGeographyFromText($historyLower);
            if ($resolvedGeo) {
                $contextParts[] = $resolvedGeo;
            }
        }

        // Resolve period from history if missing in current message
        if (! $hasPeriod) {
            $resolvedPeriod = $this->scopeGuard->extractPeriodFromText($historyLower);
            if ($resolvedPeriod) {
                $contextParts[] = $resolvedPeriod;
            }
        }

        // If context was resolved, prefix it to the message naturally
        if (! empty($contextParts)) {
            $contextStr = implode(', ', $contextParts);

            return "Data {$contextStr}: {$message}";
        }

        return $message;
    }

    /**
     * Build a trimmed conversation history array for the LLM.
     * Only passes 'user' and 'assistant' roles with plain content.
     *
     * @param  array  $history  Raw history from frontend
     * @param  int    $maxTurns Max individual turns (not pairs) to keep
     * @return array
     */
    private function buildLlmHistory(array $history, int $maxTurns = 10): array
    {
        if (empty($history)) {
            return [];
        }

        // Take only the last N turns
        $sliced = array_slice($history, -$maxTurns);

        $llmHistory = [];
        foreach ($sliced as $turn) {
            $role    = $turn['role'] ?? '';
            $content = trim($turn['content'] ?? '');

            if (! in_array($role, ['user', 'assistant'], true) || $content === '') {
                continue;
            }

            // For assistant messages, strip markdown citations noise for cleaner context
            // (The LLM only needs the plain answer text, not the full citation block)
            $llmHistory[] = [
                'role'    => $role,
                'content' => $content,
            ];
        }

        return $llmHistory;
    }

    private function shouldUseBpsAgent(): bool
    {
        return $this->bpsAgent !== null
            && config('bps.enabled', true) === true
            && ! empty(config('bps.key'));
    }
}
