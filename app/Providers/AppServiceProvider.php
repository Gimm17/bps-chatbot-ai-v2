<?php

namespace App\Providers;

use App\Ai\AiProviderInterface;
use App\Ai\ChatService;
use App\Ai\LimitRouterProvider;
use App\Ai\PromptBuilder;
use App\Ai\ScopeGuard;
use App\Bps\BpsAgent;
use App\Bps\BpsApiClient;
use App\Rag\DemoLexicalRetriever;
use App\Rag\KnowledgeLoader;
use App\Rag\RetrieverInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind AI Provider
        $this->app->singleton(AiProviderInterface::class, function () {
            return new LimitRouterProvider(
                apiKey: config('ai.providers.limitrouter.api_key', ''),
                baseUrl: config('ai.providers.limitrouter.base_url', 'https://limitrouter.com/v1'),
                defaultModel: config('ai.providers.limitrouter.default_model', 'gemini-3.7-flash'),
                timeout: config('ai.timeout', 30)
            );
        });

        // Bind Knowledge Loader & Retriever
        $this->app->singleton(KnowledgeLoader::class, function () {
            return new KnowledgeLoader(base_path('data/knowledge'));
        });

        $this->app->singleton(RetrieverInterface::class, function ($app) {
            return new DemoLexicalRetriever($app->make(KnowledgeLoader::class));
        });

        // Bind Scope Guard & Prompt Builder
        $this->app->singleton(ScopeGuard::class, function () {
            return new ScopeGuard;
        });

        $this->app->singleton(PromptBuilder::class, function () {
            return new PromptBuilder;
        });

        // Bind BPS API Client
        $this->app->singleton(BpsApiClient::class, function () {
            return new BpsApiClient(
                apiKey: config('bps.key', ''),
                baseUrl: config('bps.base_url', 'https://webapi.bps.go.id'),
                timeout: config('bps.http.timeout_sec', 15),
                cacheEnabled: config('bps.cache.enabled', true),
                cacheTtlHours: config('bps.cache.ttl_hours', 24)
            );
        });

        // Bind Publication Indexer
        $this->app->singleton(\App\Bps\PublicationIndexer::class, function () {
            return new \App\Bps\PublicationIndexer();
        });

        // Bind BPS Agent
        $this->app->singleton(BpsAgent::class, function ($app) {
            return new BpsAgent(
                apiClient: $app->make(BpsApiClient::class),
                aiProvider: $app->make(AiProviderInterface::class),
                promptBuilder: $app->make(PromptBuilder::class),
                indexer: $app->make(\App\Bps\PublicationIndexer::class)
            );
        });

        // Bind Chat Service (Scoped for request lifecycle)
        $this->app->scoped(ChatService::class, function ($app) {
            return new ChatService(
                provider: $app->make(AiProviderInterface::class),
                retriever: $app->make(RetrieverInterface::class),
                scopeGuard: $app->make(ScopeGuard::class),
                promptBuilder: $app->make(PromptBuilder::class),
                bpsAgent: $app->make(BpsAgent::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $caPath = $this->resolveCaPath();
        if ($caPath !== null) {
            Http::globalOptions(['verify' => $caPath]);
        }
    }

    /**
     * Resolve CA bundle for TLS in Windows/XAMPP environments.
     */
    private function resolveCaPath(): ?string
    {
        $ca = env('CURL_CA_BUNDLE');
        if (empty($ca)) {
            return null;
        }

        // Absolute path check
        $real = realpath($ca);
        if ($real !== false && file_exists($real)) {
            return $real;
        }

        // Relative path from base path
        $relative = realpath(base_path($ca));
        if ($relative !== false && file_exists($relative)) {
            return $relative;
        }

        return null;
    }
}
