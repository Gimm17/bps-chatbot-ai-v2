<?php

namespace App\Ai;

interface AiProviderInterface
{
    /**
     * List available models from provider.
     *
     * @return array Array of ['id' => string, 'name' => string]
     */
    public function listModels(): array;

    /**
     * Send chat request to provider and return output.
     */
    public function chat(ChatProviderInput $input): ChatProviderOutput;

    /**
     * Send chat request with tools and budget cap.
     */
    public function chatWithTools(ChatProviderInput $input, array $tools = [], int $cap = 4): ChatProviderOutput;
}
