<?php

namespace App\Ai;

class ChatProviderInput
{
    public function __construct(
        public readonly string $systemPrompt,
        public readonly string $userMessage,
        public readonly array $conversationHistory = [],
        public readonly string $locale = 'id-ID',
        public readonly ?string $model = null,
        public readonly ?int $timeout = null
    ) {}
}
