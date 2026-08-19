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
     */
    public function handle(string $userMessage, ?string $conversationId = null): ChatResponse
    {
        $message = trim($userMessage);

        // Step 1: Scope Classification
        $decision = $this->scopeGuard->classify($message);

        // Handle Greeting
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
        if ($this->shouldUseBpsAgent()) {
            try {
                $bpsResult = $this->bpsAgent?->run($message, $decision->intent);
                if ($bpsResult !== null) {
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
        $sources = $this->retriever->retrieve($message, 4);

        if (empty($sources)) {
            return ChatResponse::create(
                status: 'no_evidence',
                answer: 'Saya belum menemukan sumber data atau publikasi BPS yang cukup untuk menjawab pertanyaan tersebut secara pasti. Silakan coba sebutkan indikator, istilah statistik, atau periode spesifik yang Anda maksud.',
                citations: []
            );
        }

        // Step 4: Build Prompt with Retrieved Evidence
        $systemPrompt = $this->promptBuilder->build($sources);

        $input = new ChatProviderInput(
            systemPrompt: $systemPrompt,
            userMessage: $message
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

    private function shouldUseBpsAgent(): bool
    {
        return $this->bpsAgent !== null
            && config('bps.enabled', true) === true
            && ! empty(config('bps.key'));
    }
}
