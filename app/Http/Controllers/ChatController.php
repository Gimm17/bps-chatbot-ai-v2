<?php

namespace App\Http\Controllers;

use App\Ai\ChatService;
use App\Http\Requests\ChatRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\RateLimiter;

class ChatController extends Controller
{
    public function __construct(
        private readonly ChatService $chatService
    ) {}

    public function handle(ChatRequest $request): JsonResponse
    {
        $ip = $request->ip() ?? '127.0.0.1';
        $rateKey = 'chat-rate:'.$ip;

        // Rate limit: 15 requests per minute per IP for demo
        if (RateLimiter::tooManyAttempts($rateKey, 15)) {
            $seconds = RateLimiter::availableIn($rateKey);

            return response()->json([
                'error' => [
                    'code' => 'RATE_LIMITED',
                    'message' => "Terlalu banyak permintaan. Silakan tunggu {$seconds} detik sebelum mencoba kembali.",
                ],
            ], 429);
        }

        RateLimiter::hit($rateKey, 60);

        $message = (string) $request->input('message');
        $conversationId = $request->input('conversationId');

        $chatResponse = $this->chatService->handle($message, $conversationId);

        $httpCode = match ($chatResponse->status) {
            'provider_error' => 503,
            'rate_limited' => 429,
            default => 200,
        };

        return response()->json($chatResponse->jsonSerialize(), $httpCode);
    }
}
