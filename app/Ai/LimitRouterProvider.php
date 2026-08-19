<?php

namespace App\Ai;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class LimitRouterProvider implements AiProviderInterface
{
    public function __construct(
        private readonly string $apiKey,
        private readonly string $baseUrl = 'https://limitrouter.com/v1',
        private readonly string $defaultModel = 'gemini-3.7-flash',
        private readonly int $timeout = 30
    ) {}

    /**
     * List available models from LimitRouter.
     */
    public function listModels(): array
    {
        if (empty($this->apiKey)) {
            return [
                ['id' => $this->defaultModel, 'name' => $this->defaultModel],
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->apiKey,
            ])
                ->timeout(10)
                ->get(rtrim($this->baseUrl, '/').'/models');

            if ($response->successful()) {
                $data = $response->json('data') ?? [];
                $models = [];
                foreach ($data as $item) {
                    $id = $item['id'] ?? '';
                    if ($id !== '') {
                        $models[] = [
                            'id' => $id,
                            'name' => $item['name'] ?? $id,
                        ];
                    }
                }

                return ! empty($models) ? $models : [['id' => $this->defaultModel, 'name' => $this->defaultModel]];
            }
        } catch (\Throwable $e) {
            Log::warning('LimitRouterProvider::listModels failed: '.$e->getMessage());
        }

        return [
            ['id' => $this->defaultModel, 'name' => $this->defaultModel],
        ];
    }

    /**
     * Send chat completion request to LimitRouter.
     */
    public function chat(ChatProviderInput $input): ChatProviderOutput
    {
        if (empty($this->apiKey)) {
            throw new RuntimeException('LimitRouter API key is not configured.');
        }

        $model = $input->model ?? $this->defaultModel;
        $timeout = $input->timeout ?? $this->timeout;

        $messages = [];

        // System prompt
        if ($input->systemPrompt !== '') {
            $messages[] = [
                'role' => 'system',
                'content' => $input->systemPrompt,
            ];
        }

        // Conversation history (if any)
        foreach ($input->conversationHistory as $turn) {
            if (isset($turn['role'], $turn['content'])) {
                $messages[] = [
                    'role' => $turn['role'],
                    'content' => $turn['content'],
                ];
            }
        }

        // Current user message
        $messages[] = [
            'role' => 'user',
            'content' => $input->userMessage,
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->apiKey,
                'Content-Type' => 'application/json',
            ])
                ->timeout($timeout)
                ->post(rtrim($this->baseUrl, '/').'/chat/completions', [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => 0.2,
                ]);

            if (! $response->successful()) {
                $status = $response->status();
                $errBody = $response->json();
                $errMessage = $errBody['error']['message'] ?? $response->body();

                Log::error('LimitRouterProvider request failed', [
                    'status' => $status,
                    'model' => $model,
                    'message' => $errMessage,
                ]);

                if ($status === 429) {
                    throw new RuntimeException('Provider rate limit reached (429).');
                }

                throw new RuntimeException('AI Provider error: '.$status);
            }

            $json = $response->json();
            $choice = $json['choices'][0]['message']['content'] ?? '';
            $usage = $json['usage'] ?? [];

            return new ChatProviderOutput(
                text: $choice,
                rawMessages: $messages,
                model: $json['model'] ?? $model,
                promptTokens: $usage['prompt_tokens'] ?? 0,
                completionTokens: $usage['completion_tokens'] ?? 0
            );
        } catch (\Throwable $e) {
            if ($e instanceof RuntimeException) {
                throw $e;
            }
            Log::error('LimitRouterProvider exception: '.$e->getMessage());
            throw new RuntimeException('Gagal menghubungi layanan AI: '.$e->getMessage(), 0, $e);
        }
    }

    /**
     * Send chat request with tools and budget cap.
     */
    public function chatWithTools(ChatProviderInput $input, array $tools = [], int $cap = 4): ChatProviderOutput
    {
        // Direct chat completion with system instructions
        return $this->chat($input);
    }
}
