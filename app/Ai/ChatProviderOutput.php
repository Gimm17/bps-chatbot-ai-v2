<?php

namespace App\Ai;

class ChatProviderOutput
{
    public function __construct(
        public readonly string $text,
        public readonly array $rawMessages = [],
        public readonly ?string $model = null,
        public readonly int $promptTokens = 0,
        public readonly int $completionTokens = 0
    ) {}
}
