<?php

namespace App\Http\Controllers;

use App\Ai\AiProviderInterface;
use Illuminate\Http\JsonResponse;

class ModelController extends Controller
{
    public function __construct(
        private readonly AiProviderInterface $provider
    ) {}

    public function index(): JsonResponse
    {
        $models = $this->provider->listModels();

        return response()->json([
            'status' => 'ok',
            'data' => $models,
            'default' => config('ai.providers.limitrouter.default_model', 'gemini-3.7-flash'),
        ]);
    }
}
